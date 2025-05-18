<?php

namespace App\Http\Controllers;

use App\Models\Letter;
use App\Services\ECDSAService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\PDFService;

class SignatureController extends Controller
{
    // Define role mappings as constants
    private const COMMITTEE_ROLE_MAP = [
        'order_to_role' => [
            1 => 4, // Sekretaris Panitia
            2 => 5, // Ketua Panitia
            3 => 2, // Ketua Umum
            4 => 6, // Pembina
        ],
        'role_to_order' => [
            4 => 1, // Sekretaris Panitia
            5 => 2, // Ketua Panitia
            2 => 3, // Ketua Umum
            6 => 4, // Pembina
        ]
    ];

    private const GENERAL_ROLE_MAP = [
        'order_to_role' => [
            1 => 3, // Sekretaris Umum
            2 => 2, // Ketua Umum
            3 => 6, // Pembina
        ],
        'role_to_order' => [
            3 => 1, // Sekretaris Umum
            2 => 2, // Ketua Umum
            6 => 3, // Pembina
        ]
    ];

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
            // Sign using user's private key
            $signatureData = $ecdsaService->sign($letter->file_hash, $currentUser->private_key);

            // If signature record does not exist, create it with proper order
            if (!$signature) {
                $order = $this->getOrderForRole($currentUser->role_id, $letter);
                $signature = $letter->signatures()->create([
                    'signer_id' => $currentUser->id,
                    'order' => $order,
                ]);
            }

            // Update the signature record
            $signature->update([
                'signature' => $signatureData,
                'signed_at' => now(),
            ]);

            // Check if this was the last signature needed
            $allSigned = $letter->signatures()->whereNull('signed_at')->count() === 0;
            if ($allSigned) {
                // Add all QR codes to PDF and update hash
                $pdfService = app(PDFService::class);
                $signedPath = $pdfService->embedQRCodes($letter);

                if ($signedPath) {
                    // Update letter with new file path and hash, preserve original_file_hash
                    $letter->update([
                        'file_path' => $signedPath,
                        'file_hash' => hash_file('sha256', storage_path('app/public/' . $signedPath)),
                        'status' => 'signed',
                        'original_file_hash' => $letter->original_file_hash,
                    ]);
                } else {
                    $letter->update(['status' => 'signed']);
                }
            }

            return back()->with('success', 'Surat berhasil ditandatangani.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menandatangani surat: ' . $e->getMessage());
        }
    }

    protected function getOrderForRole($roleId, $letter)
    {
        $roleMap = $this->getRoleMap($letter);
        return $roleMap['role_to_order'][$roleId] ?? null;
    }

    protected function getRequiredRoleForOrder($order, $letter)
    {
        $roleMap = $this->getRoleMap($letter);
        return $roleMap['order_to_role'][$order] ?? null;
    }

    public function isAuthorizedToSign(Letter $letter, $user)
    {
        // Check if user is in the same institution
        if ($user->institution_id !== $letter->category->institution_id) {
            return false;
        }

        // Get the role map for this letter type
        $roleMap = $this->getRoleMap($letter);

        // Get the order for this user's role
        $order = $roleMap['role_to_order'][$user->role_id] ?? null;

        // If no order found, user is not authorized
        if (!$order) {
            return false;
        }

        // Find user's signature record
        $signature = $letter->signatures()->where('signer_id', $user->id)->first();

        // If no signature record exists and user is first signer, create it
        if (!$signature && $order === 1) {
            $signature = $letter->signatures()->create([
                'signer_id' => $user->id,
                'order' => $order,
            ]);
            return true;
        }

        // If signature exists but not signed
        if ($signature && !$signature->signed_at) {
            // If order is 1, no previous signatures are required
            if ($signature->order === 1) {
                return true;
            }

            // Check if all previous signers have signed
            $previousSignatures = $letter->signatures()
                ->where('order', '<', $signature->order)
                ->whereNotNull('signed_at')
                ->count();

            $requiredPreviousSignatures = $signature->order - 1;

            return $previousSignatures >= $requiredPreviousSignatures;
        }

        return false;
    }

    private function getRoleMap($letter)
    {
        return $letter->category->committee_id !== null
            ? self::COMMITTEE_ROLE_MAP
            : self::GENERAL_ROLE_MAP;
    }
}
