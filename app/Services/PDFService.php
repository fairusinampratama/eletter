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
        \Log::info('Starting QR code embedding for letter: ' . $letter->id);

        // Ensure directories exist with proper permissions
        $documentsPath = Storage::disk('public')->path('documents');
        $qrCodesPath = Storage::disk('public')->path('qr_codes');

        \Log::info('Directories:', [
            'documents' => $documentsPath,
            'qr_codes' => $qrCodesPath
        ]);

        if (!file_exists($documentsPath)) {
            mkdir($documentsPath, 0755, true);
            \Log::info('Created documents directory');
        }
        if (!file_exists($qrCodesPath)) {
            mkdir($qrCodesPath, 0755, true);
            \Log::info('Created qr_codes directory');
        }

        $originalPdfPath = Storage::disk('public')->path($letter->file_path);
        \Log::info('Original PDF path: ' . $originalPdfPath);

        $pdf = new \setasign\Fpdi\Fpdi();
        $pageQRCodes = [];

        // Prepare QR codes for each signature
        foreach ($letter->signatures as $signature) {
            \Log::info('Processing signature:', [
                'id' => $signature->id,
                'metadata' => $signature->qr_metadata
            ]);

            $meta = $signature->qr_metadata;
            if (is_string($meta)) {
                $meta = json_decode($meta, true);
            }
            if (!is_array($meta) || !isset($meta['page'], $meta['x'], $meta['y'])) {
                \Log::warning('Skipping signature due to missing metadata', [
                    'signature_id' => $signature->id,
                    'metadata' => $meta
                ]);
                continue;
            }

            // Generate QR code content
            $qrContent = url("verify/{$letter->verification_id}/{$signature->signature}");
            \Log::info('QR Content: ' . $qrContent);

            $qrCode = new \Endroid\QrCode\QrCode($qrContent);
            $qrCode->setSize(300);
            $qrCode->setMargin(10);

            $writer = new \Endroid\QrCode\Writer\PngWriter();
            $result = $writer->write($qrCode);

            // Use absolute path for QR code storage
            $qrPath = 'qr_codes/' . $letter->verification_id . '_' . $signature->id . '.png';
            $fullQrPath = Storage::disk('public')->path($qrPath);

            \Log::info('Saving QR code:', [
                'relative_path' => $qrPath,
                'full_path' => $fullQrPath
            ]);

            // Ensure the QR code is saved
            if (!Storage::disk('public')->put($qrPath, $result->getString())) {
                \Log::error('Failed to save QR code', [
                    'path' => $qrPath,
                    'full_path' => $fullQrPath
                ]);
                throw new \Exception("Failed to save QR code to: {$qrPath}");
            }

            // Group QR codes by page
            $pageQRCodes[$meta['page']][] = [
                'path' => $fullQrPath,
                'x' => $meta['x'],
                'y' => $meta['y'],
                'width' => 20,
                'height' => 20,
            ];

            \Log::info('QR code added to page', [
                'page' => $meta['page'],
                'x' => $meta['x'],
                'y' => $meta['y']
            ]);
        }

        try {
            $pageCount = $pdf->setSourceFile($originalPdfPath);
            \Log::info('PDF page count: ' . $pageCount);

            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                $templateId = $pdf->importPage($pageNo);
                
                // Get the original page size
                $size = $pdf->getTemplateSize($templateId);
                
                // Add page with original size
                $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $pdf->useTemplate($templateId);

                // Get actual page size in points (pixels at 72dpi)
                $pageSize = $pdf->getTemplateSize($templateId);
                $pageWidthPx = $pageSize['width'];
                $pageHeightPx = $pageSize['height'];
                $pageWidthMm = $pdf->GetPageWidth();
                $pageHeightMm = $pdf->GetPageHeight();

                \Log::info('Page dimensions:', [
                    'page' => $pageNo,
                    'width_px' => $pageWidthPx,
                    'height_px' => $pageHeightPx,
                    'width_mm' => $pageWidthMm,
                    'height_mm' => $pageHeightMm
                ]);

                // Place all QR codes for this page
                if (isset($pageQRCodes[$pageNo])) {
                    foreach ($pageQRCodes[$pageNo] as $qr) {
                        // Convert points (72dpi) to mm and adjust for center positioning
                        $xMm = ($qr['x'] * 0.352777778) - ($qr['width'] / 2);
                        $yMm = ($qr['y'] * 0.352777778) - ($qr['height'] / 2);

                        \Log::info('Placing QR code:', [
                            'page' => $pageNo,
                            'original_x' => $qr['x'],
                            'original_y' => $qr['y'],
                            'x_mm' => $xMm,
                            'y_mm' => $yMm,
                            'path' => $qr['path']
                        ]);

                        $this->addQRCodeToPage($pdf, $qr['path'], $xMm, $yMm, $qr['width'], $qr['height']);
                    }
                }
            }

            $modifiedPdfPath = 'documents/' . $letter->verification_id . '_signed.pdf';
            $fullModifiedPath = Storage::disk('public')->path($modifiedPdfPath);

            \Log::info('Saving modified PDF:', [
                'path' => $modifiedPdfPath,
                'full_path' => $fullModifiedPath
            ]);

            $pdf->Output($fullModifiedPath, 'F');

            // Clean up QR images
            foreach ($pageQRCodes as $qrs) {
                foreach ($qrs as $qr) {
                    Storage::disk('public')->delete($qr['path']);
                }
            }

            return $modifiedPdfPath;
        } catch (\Exception $e) {
            \Log::error('Error in PDF processing:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

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
