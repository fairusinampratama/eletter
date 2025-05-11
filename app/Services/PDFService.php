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
     * Embed QR code in PDF
     *
     * @param Letter $document
     * @return string
     */
    public function embedQRCode(Letter $document): string
    {
        // Ensure storage directories exist
        Storage::disk('public')->makeDirectory('documents');
        Storage::disk('public')->makeDirectory('qr_codes');

        // Generate QR code
        $qrCode = new EndroidQrCode(route('verify', $document->verification_id));
        $qrCode->setSize(300);
        $qrCode->setMargin(10);

        // Create writer
        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        // Save QR code temporarily
        $qrPath = 'qr_codes/' . $document->verification_id . '.png';
        Storage::disk('public')->put($qrPath, $result->getString());

        // Get the original PDF path
        $originalPdfPath = Storage::disk('public')->path($document->file_path);

        // Create new PDF with QR code
        $pdf = new Fpdi();

        try {
            // Get the number of pages in the original PDF
            $pageCount = $pdf->setSourceFile($originalPdfPath);

            // Process each page
            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                // Import page
                $templateId = $pdf->importPage($pageNo);
                $pdf->AddPage();
                $pdf->useTemplate($templateId);

                // Add QR code to the last page
                if ($pageNo === $pageCount) {
                    $this->addQRCodeToPage($pdf, $qrPath);
                }
            }

            // Save the modified PDF
            $modifiedPdfPath = 'documents/' . $document->verification_id . '_signed.pdf';
            $pdf->Output(Storage::disk('public')->path($modifiedPdfPath), 'F');

            // Clean up temporary QR code
            Storage::disk('public')->delete($qrPath);

            return $modifiedPdfPath;
        } catch (\Exception $e) {
            // Clean up on error
            Storage::disk('public')->delete($qrPath);
            throw $e;
        }
    }

    /**
     * Add QR code to a PDF page
     *
     * @param Fpdi $pdf
     * @param string $qrPath
     * @return void
     */
    private function addQRCodeToPage(Fpdi $pdf, string $qrPath): void
    {
        // Get page dimensions
        $pageWidth = $pdf->GetPageWidth();
        $pageHeight = $pdf->GetPageHeight();

        // QR code dimensions
        $qrWidth = 25; // mm
        $qrHeight = 25; // mm

        // Position QR code in bottom left corner with margin
        $margin = 10; // mm
        $x = $margin;
        $y = $pageHeight - $qrHeight - $margin;

        // Add QR code image
        $pdf->Image(Storage::disk('public')->path($qrPath), $x, $y, $qrWidth, $qrHeight);

        // Calculate where the text would go
        $textY = $y + $qrHeight + 4; // 4mm below QR code

        // If the text would go out of the page, put it above the QR code instead
        if ($textY + 4 > $pageHeight - $margin) {
            $textY = $y - 6; // 6mm above the QR code
        }

        // Add verification text
        $pdf->SetFont('Helvetica', '', 7);
        $pdf->SetXY($x, $textY);
        $pdf->Cell($qrWidth, 4, 'Scan to verify document', 0, 1, 'C');
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
