<?php

namespace App\Http\Controllers;

use App\Models\Letter;
use App\Services\ECDSAService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VerificationController extends Controller
{
    public function show($verification_id)
    {
        // Check if letter exists
        $letter = Letter::where('verification_id', $verification_id)->firstOrFail();

        return view('letters.verify', [
            'verification_id' => $verification_id,
            'letter' => $letter
        ]);
    }

    public function verify(Request $request, $verification_id, ECDSAService $ecdsaService)
    {
        try {
            $request->validate([
                'file' => 'required|file|mimes:pdf|max:10240',
            ]);

            // Find letter
            $letter = Letter::where('verification_id', $verification_id)->firstOrFail();

            // Hash uploaded file
            $uploadedHash = hash_file('sha256', $request->file('file')->path());

            // Debug information
            Log::info('Letter Verification Debug', [
                'verification_id' => $verification_id,
                'stored_hash' => $letter->file_hash,
                'uploaded_hash' => $uploadedHash,
                'stored_path' => storage_path('app/public/' . $letter->file_path),
                'uploaded_path' => $request->file('file')->path()
            ]);

            // Compare hashes
            if ($uploadedHash !== $letter->file_hash) {
                return back()->with('error', sprintf(
                    'Letter hash mismatch. The file you uploaded appears to be different from the original signed letter. Please make sure you are uploading the exact same file that was downloaded after signing. (Stored hash: %s... Uploaded hash: %s...)',
                    substr($letter->file_hash, 0, 10),
                    substr($uploadedHash, 0, 10)
                ));
            }

            // Verify all signatures
            $valid = true;
            $invalidSignatures = [];
            $unsignedSignatures = [];

            foreach ($letter->signatures as $signature) {
                if (!$signature->signed_at || !$signature->signature) {
                    $unsignedSignatures[] = $signature->signer->fullname;
                    $valid = false;
                    continue;
                }

                // Get the signer's public key from their user record
                if (
                    !$ecdsaService->verify(
                        $letter->file_hash,
                        $signature->signature,
                        $signature->signer->public_key
                    )
                ) {
                    $valid = false;
                    $invalidSignatures[] = $signature->signer->fullname;
                }
            }

            return view('letters.verify-result', [
                'letter' => $letter,
                'valid' => $valid,
                'invalidSignatures' => $invalidSignatures,
                'unsignedSignatures' => $unsignedSignatures
            ]);
        } catch (\Illuminate\Session\TokenMismatchException $e) {
            return back()->with('error', 'Your session has expired. Please refresh the page and try again.');
        } catch (\Exception $e) {
            Log::error('Verification error: ' . $e->getMessage());
            return back()->with('error', 'An error occurred during verification. Please try again.');
        }
    }
}
