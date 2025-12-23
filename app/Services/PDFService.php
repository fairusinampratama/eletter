<?php

namespace App\Services;

use App\Models\Letter;
use Endroid\QrCode\QrCode as EndroidQrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Label\Label;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use setasign\Fpdi\Fpdi;

class PDFService
{
    /**
     * Process and store an uploaded PDF
     *
     * @param UploadedFile $file
     * @return array{path: string, hash: string}
     */
    public function processUpload(UploadedFile $file): array
    {
        $path = $file->store('documents', 'public');
        $hash = $this->hashFile($file);

        return [
            'path' => $path,
            'hash' => $hash,
        ];
    }

    /**
     * Generate a verification ID
     *
     * @return string
     */
    public function generateVerificationId(): string
    {
        return strtoupper(substr(md5(uniqid()), 0, 8));
    }

    /**
     * Embed QR codes in PDF
     *
     * @param Letter $letter
     * @return string
     */
    public function embedQRCodes(Letter $letter): string
    {
        \Log::info("Starting QR code embedding for letter: {$letter->id}");

        // 1. Use Storage Facade to create directories (Safe on Shared Host)
        Storage::disk('public')->makeDirectory('documents');
        Storage::disk('public')->makeDirectory('qr_codes');

        // 2. Get Absolute Paths using the Disk config
        // This ensures consistency with how you saved the file in the Controller
        $originalPdfPath = Storage::disk('public')->path($letter->file_path);

        if (!file_exists($originalPdfPath)) {
            throw new \Exception("Original PDF not found: $originalPdfPath");
        }

        $pdf = new \setasign\Fpdi\Fpdi();
        $pageQRCodes = [];

        // Prepare QR codes
        foreach ($letter->signatures as $signature) {
            $meta = is_string($signature->qr_metadata)
                ? json_decode($signature->qr_metadata, true)
                : $signature->qr_metadata;

            if (!is_array($meta) || !isset($meta['page'], $meta['x'], $meta['y'])) {
                continue;
            }

            $qrContent = url("verify/{$letter->verification_id}/{$signature->signature}");
            $qrCode = new \Endroid\QrCode\QrCode($qrContent);
            $qrCode->setSize(300)->setMargin(10);
            $writer = new PngWriter();
            $result = $writer->write($qrCode);

            // Generate filename and get absolute path
            $qrRelativePath = 'qr_codes/' . $letter->verification_id . '_' . $signature->id . '.png';
            $qrFullPath = Storage::disk('public')->path($qrRelativePath);

            // Write file using standard PHP (fpdi needs local file)
            file_put_contents($qrFullPath, $result->getString());

            $pageQRCodes[$meta['page']][] = [
                'path' => $qrFullPath, // Absolute path required for FPDF/FPDI Image()
                'x' => $meta['x'],
                'y' => $meta['y'],
                'width' => 20,
                'height' => 20,
            ];
        }

        try {
            $pageCount = $pdf->setSourceFile($originalPdfPath);

            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                $templateId = $pdf->importPage($pageNo);
                $size = $pdf->getTemplateSize($templateId);

                // Handle orientation automatically
                $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $pdf->useTemplate($templateId);

                if (isset($pageQRCodes[$pageNo])) {
                    foreach ($pageQRCodes[$pageNo] as $qr) {
                        // Conversion logic (Pixels/Coordinates to mm)
                        // Ensure your input X/Y matches what FPDF expects (mm)
                        $xMm = ($qr['x'] * 0.352777778) - ($qr['width'] / 2);
                        $yMm = ($qr['y'] * 0.352777778) - ($qr['height'] / 2);

                        $pdf->Image($qr['path'], $xMm, $yMm, $qr['width'], $qr['height']);
                    }
                }
            }

            // Output logic
            $signedRelativePath = 'documents/' . $letter->verification_id . '_signed.pdf';
            $signedFullPath = Storage::disk('public')->path($signedRelativePath);

            $pdf->Output($signedFullPath, 'F');

            // Clean up
            foreach ($pageQRCodes as $qrs) {
                foreach ($qrs as $qr) {
                    if (file_exists($qr['path']))
                        unlink($qr['path']);
                }
            }

            return $signedRelativePath; // Return relative path for DB update

        } catch (\Exception $e) {
            // Cleanup on fail
            foreach ($pageQRCodes as $qrs) {
                foreach ($qrs as $qr) {
                    if (file_exists($qr['path']))
                        unlink($qr['path']);
                }
            }
            throw $e;
        }
    }

    /**
     * Add QR code to a PDF page
     *
     * @param Fpdi $pdf
     * @param string $qrPath
     * @param float $x
     * @param float $y
     * @param float $width
     * @param float $height
     * @return void
     */
    private function addQRCodeToPage(Fpdi $pdf, string $qrPath, float $x, float $y, float $width, float $height): void
    {
        $pdf->Image($qrPath, $x, $y, $width, $height);
    }

    /**
     * Hash a file
     *
     * @param UploadedFile $file
     * @return string
     */
    public function hashFile(UploadedFile $file): string
    {
        return hash_file('sha256', $file->path());
    }

    /**
     * Verify uploaded file matches original
     *
     * @param UploadedFile $file
     * @param Letter $document
     * @return bool
     */
    public function verifyFile(UploadedFile $file, Letter $document): bool
    {
        $uploadedHash = $this->hashFile($file);
        return $uploadedHash === $document->file_hash;
    }
}
