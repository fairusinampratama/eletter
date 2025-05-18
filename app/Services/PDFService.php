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
        Storage::disk('public')->makeDirectory('documents');
        Storage::disk('public')->makeDirectory('qr_codes');

        $originalPdfPath = Storage::disk('public')->path($letter->file_path);
        $pdf = new \setasign\Fpdi\Fpdi();
        $pageQRCodes = [];

        // Prepare QR codes for each signature
        foreach ($letter->signatures as $signature) {
            $meta = $signature->qr_metadata;
            if (is_string($meta)) {
                $meta = json_decode($meta, true);
            }
            if (!is_array($meta) || !isset($meta['page'], $meta['x'], $meta['y'])) {
                continue; // skip if metadata is missing
            }

            // Generate QR code content
            $qrContent = url("verify/{$letter->verification_id}/{$signature->signature}");
            $qrCode = new \Endroid\QrCode\QrCode($qrContent);
            $qrCode->setSize(300);
            $qrCode->setMargin(10);

            $writer = new \Endroid\QrCode\Writer\PngWriter();
            $result = $writer->write($qrCode);

            $qrPath = 'qr_codes/' . $letter->verification_id . '_' . $signature->id . '.png';
            Storage::disk('public')->put($qrPath, $result->getString());

            // Group QR codes by page
            $pageQRCodes[$meta['page']][] = [
                'path' => $qrPath,
                'x' => $meta['x'],
                'y' => $meta['y'],
                'width' => 20,  // fixed size in mm
                'height' => 20, // fixed size in mm
            ];
        }

        try {
            $pageCount = $pdf->setSourceFile($originalPdfPath);

            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                $templateId = $pdf->importPage($pageNo);
                $pdf->AddPage();
                $pdf->useTemplate($templateId);

                // Get page size in mm
                $pageWidthMm = $pdf->GetPageWidth();
                $pageHeightMm = $pdf->GetPageHeight();
                // Default A4 size in points (pixels at 72dpi)
                $pageWidthPx = 595;
                $pageHeightPx = 842;

                // Place all QR codes for this page
                if (isset($pageQRCodes[$pageNo])) {
                    foreach ($pageQRCodes[$pageNo] as $qr) {
                        // Convert pixel to mm and adjust for center positioning
                        $xMm = ($qr['x'] / $pageWidthPx * $pageWidthMm) - ($qr['width'] / 2);
                        $yMm = ($qr['y'] / $pageHeightPx * $pageHeightMm) - ($qr['height'] / 2);
                        $this->addQRCodeToPage($pdf, $qr['path'], $xMm, $yMm, $qr['width'], $qr['height']);
                    }
                }
            }

            $modifiedPdfPath = 'documents/' . $letter->verification_id . '_signed.pdf';
            $pdf->Output(Storage::disk('public')->path($modifiedPdfPath), 'F');

            // Clean up QR images
            foreach ($pageQRCodes as $qrs) {
                foreach ($qrs as $qr) {
                    Storage::disk('public')->delete($qr['path']);
                }
            }

            return $modifiedPdfPath;
        } catch (\Exception $e) {
            // Clean up on error
            foreach ($pageQRCodes as $qrs) {
                foreach ($qrs as $qr) {
                    Storage::disk('public')->delete($qr['path']);
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
        $pdf->Image(Storage::disk('public')->path($qrPath), $x, $y, $width, $height);
        // Optionally add text below QR
        $pdf->SetXY($x, $y + $height + 2);
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
