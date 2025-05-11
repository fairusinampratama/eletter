<?php

namespace App\Http\Controllers;

use App\Models\Letter;
use App\Services\ECDSAService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SignatureController extends Controller
{
    public function sign(Request $request, ECDSAService $ecdsaService)
    {
        $request->validate([
            'letter_id' => 'required|exists:letters,id',
        ]);

        $letter = Letter::findOrFail($request->letter_id);
        $currentUser = Auth::user();

        // Find the signature record for this letter and signer
        $signature = $letter->signatures()->where('signer_id', $currentUser->id)->first();

        // Check if already signed
        if ($signature && $signature->signed_at) {
            return back()->with('error', 'Anda sudah menandatangani surat ini.');
        }

        // Check if user is authorized to sign this letter
        if (!$this->isAuthorizedToSign($letter, $currentUser)) {
            return back()->with('error', 'Anda tidak memiliki akses untuk menandatangani surat ini.');
        }

        try {
            // Generate ECDSA key pair
            $keyPair = $ecdsaService->generateKeyPair();

            // Sign the letter's file hash
            $signatureData = $ecdsaService->sign($letter->file_hash, $keyPair['privateKey']);

            // If signature record does not exist, create it with proper order
            if (!$signature) {
                $order = $this->getOrderForRole($currentUser->role_id);
                $signature = $letter->signatures()->create([
                    'signer_id' => $currentUser->id,
                    'order' => $order,
                ]);
            }

            // Update the signature record
            $signature->update([
                'signature' => $signatureData,
                'public_key' => $keyPair['publicKey'],
                'signed_at' => now(),
            ]);

            // If all signatures are collected, update letter status
            $allSigned = $letter->signatures()->whereNull('signed_at')->count() === 0;
            if ($allSigned) {
                $letter->update(['status' => 'signed']);
            }

            return back()->with('success', 'Surat berhasil ditandatangani.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menandatangani surat: ' . $e->getMessage());
        }
    }

    protected function getOrderForRole($roleId)
    {
        // Define the order for each role
        $roleOrderMap = [
            3 => 1, // Sekretaris Umum: First
            2 => 2, // Ketua Umum: Second
            6 => 3, // Pembina: Third
        ];

        return $roleOrderMap[$roleId] ?? null;
    }

    protected function isAuthorizedToSign(Letter $letter, $user)
    {
        // Check if user is in the same institution
        if ($user->institution_id !== $letter->category->institution_id) {
            return false;
        }

        // Check if user is one of the designated signers
        $isDesignatedSigner = $letter->signatures()
            ->where('signer_id', $user->id)
            ->exists();

        if (!$isDesignatedSigner) {
            return false;
        }

        // Check if user's role matches the required role for this signature
        $signature = $letter->signatures()->where('signer_id', $user->id)->first();
        if (!$signature) {
            return false;
        }

        // Get the required role for this signature order
        $requiredRole = $this->getRequiredRoleForOrder($signature->order);
        if ($user->role_id !== $requiredRole) {
            return false;
        }

        return true;
    }

    protected function getRequiredRoleForOrder($order)
    {
        // Define the role requirements for each signature order
        $roleMap = [
            1 => 3, // First signature: Sekretaris Umum (role_id: 3)
            2 => 2, // Second signature: Ketua Umum (role_id: 2)
            3 => 6, // Third signature: Pembina (role_id: 6)
        ];

        return $roleMap[$order] ?? null;
    }
}
