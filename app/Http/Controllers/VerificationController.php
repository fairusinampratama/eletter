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

    public function showSignature($verification_id, $signature)
    {
        // Check if letter exists
        $letter = Letter::where('verification_id', $verification_id)->firstOrFail();

        // Find the signature and its signer
        $signatureModel = $letter->signatures()->where('signature', $signature)->first();

        if (!$signatureModel) {
            return back()->with('error', 'Invalid signature for this document.');
        }

        return view('letters.verify-signature', [
            'verification_id' => $verification_id,
            'signature' => $signature,
            'signer' => $signatureModel->signer,
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
            $signatureValidity = [];
            $signatureReasons = [];

            foreach ($letter->signatures as $signature) {
                if (!$signature->signed_at || !$signature->signature) {
                    $unsignedSignatures[] = $signature->signer->fullname;
                    $signatureValidity[$signature->id] = 'unsigned';
                    $valid = false;
                    continue;
                }

                // Use original_file_hash for signature verification
                $verificationResult = $ecdsaService->verify(
                    $letter->original_file_hash,
                    $signature->signature,
                    $signature->signer->public_key
                );

                if (!$verificationResult['valid']) {
                    $valid = false;
                    $invalidSignatures[] = $signature->signer->fullname;
                    $signatureValidity[$signature->id] = 'invalid';
                    $signatureReasons[$signature->id] = $verificationResult['reason'];
                } else {
                    $signatureValidity[$signature->id] = 'valid';
                }
            }

            return view('letters.verify-result', [
                'letter' => $letter,
                'valid' => $valid,
                'invalidSignatures' => $invalidSignatures,
                'unsignedSignatures' => $unsignedSignatures,
                'signatureValidity' => $signatureValidity,
                'signatureReasons' => $signatureReasons,
            ]);
        } catch (\Illuminate\Session\TokenMismatchException $e) {
            return back()->with('error', 'Your session has expired. Please refresh the page and try again.');
        } catch (\Exception $e) {
            Log::error('Verification error: ' . $e->getMessage());
            return back()->with('error', 'An error occurred during verification. Please try again.');
        }
    }

    public function verifySignature(Request $request, $verification_id, $signature, ECDSAService $ecdsaService)
    {
        try {
            $request->validate([
                'file' => 'required|file|mimes:pdf|max:10240',
            ]);

            // Find letter
            $letter = Letter::where('verification_id', $verification_id)->firstOrFail();

            // Find the specific signature
            $signatureModel = $letter->signatures()->where('signature', $signature)->first();

            if (!$signatureModel) {
                return back()->with('error', 'Invalid signature for this document.');
            }

            // Hash uploaded file
            $uploadedHash = hash_file('sha256', $request->file('file')->path());

            // Compare hashes
            if ($uploadedHash !== $letter->file_hash) {
                return back()->with('error', sprintf(
                    'Letter hash mismatch. The file you uploaded appears to be different from the original signed letter. Please make sure you are uploading the exact same file that was downloaded after signing. (Stored hash: %s... Uploaded hash: %s...)',
                    substr($letter->file_hash, 0, 10),
                    substr($uploadedHash, 0, 10)
                ));
            }

            // Verify the specific signature
            $valid = true;
            $invalidSignatures = [];
            $unsignedSignatures = [];
            $signatureValidity = [];
            $signatureReasons = [];

            if (!$signatureModel->signed_at || !$signatureModel->signature) {
                $unsignedSignatures[] = $signatureModel->signer->fullname;
                $signatureValidity[$signatureModel->id] = 'unsigned';
                $valid = false;
            } else {
                // Use original_file_hash for signature verification
                $verificationResult = $ecdsaService->verify(
                    $letter->original_file_hash,
                    $signatureModel->signature,
                    $signatureModel->signer->public_key
                );

                if (!$verificationResult['valid']) {
                    $valid = false;
                    $invalidSignatures[] = $signatureModel->signer->fullname;
                    $signatureValidity[$signatureModel->id] = 'invalid';
                    $signatureReasons[$signatureModel->id] = $verificationResult['reason'];
                } else {
                    $signatureValidity[$signatureModel->id] = 'valid';
                }
            }

            return view('letters.verify-result', [
                'letter' => $letter,
                'valid' => $valid,
                'invalidSignatures' => $invalidSignatures,
                'unsignedSignatures' => $unsignedSignatures,
                'signatureValidity' => $signatureValidity,
                'signatureReasons' => $signatureReasons,
            ]);
        } catch (\Illuminate\Session\TokenMismatchException $e) {
            return back()->with('error', 'Your session has expired. Please refresh the page and try again.');
        } catch (\Exception $e) {
            Log::error('Verification error: ' . $e->getMessage());
            return back()->with('error', 'An error occurred during verification. Please try again.');
        }
    }
}
