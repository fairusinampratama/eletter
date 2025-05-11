<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Elliptic\EC;
use kornrunner\Keccak;
use Illuminate\Support\Str;

class ECDSAService
{
    private const CURVE = 'secp256k1';
    private const HASH_ALGO = 'sha256';

    private EC $ec;

    public function __construct()
    {
        $this->ec = new EC(self::CURVE);
    }

    /**
     * Generate a new ECDSA key pair
     *
     * @return array{publicKey: string, privateKey: string}
     * @throws Exception
     */
    public function generateKeyPair(): array
    {
        // Generate a new key pair using the elliptic curve
        $keyPair = $this->ec->genKeyPair();

        // Get the private key in hex format
        $privateKey = $keyPair->getPrivate('hex');

        // Get the public key in hex format
        $publicKey = $keyPair->getPublic('hex');

        return [
            'publicKey' => $publicKey,
            'privateKey' => $privateKey,
        ];
    }

    /**
     * Get the uncompressed public key from a private key
     *
     * @param string $privateKey
     * @return string
     */
    public function getUncompressedPublicKey(string $privateKey): string
    {
        $keyPair = $this->ec->keyFromPrivate($privateKey, 'hex');
        return $keyPair->getPublic(false, 'hex');
    }

    /**
     * Hash a message using Keccak-256
     *
     * @param string $message
     * @return string
     */
    private function hashMessage(string $message): string
    {
        // Prepend Ethereum Signed Message prefix
        $prefix = "\x19Ethereum Signed Message:\n" . strlen($message);
        $prefixedMessage = $prefix . $message;
        return Keccak::hash($prefixedMessage, 256);
    }

    /**
     * Sign a message with a private key
     *
     * @param string $message
     * @param string $privateKey
     * @return string
     */
    public function sign(string $message, string $privateKey): string
    {
        try {
            // Create key pair from private key
            $keyPair = $this->ec->keyFromPrivate($privateKey, 'hex');

            // Hash the message
            $messageHash = $this->hashMessage($message);

            // Sign the message hash
            $signature = $keyPair->sign($messageHash, [
                'canonical' => true,
                'n' => $this->ec->n
            ]);

            // Serialize the signature components
            $r = $signature->r->toString(16);
            $s = $signature->s->toString(16);

            // Pad the components to ensure consistent length
            $r = str_pad($r, 64, '0', STR_PAD_LEFT);
            $s = str_pad($s, 64, '0', STR_PAD_LEFT);

            // Combine the components
            return $r . $s;
        } catch (Exception $e) {
            Log::error('Error signing message: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Verify a signature
     *
     * @param string $message
     * @param string $signature
     * @param string $publicKey
     * @return bool
     */
    public function verify(string $message, string $signature, string $publicKey): bool
    {
        try {
            // Create key pair from public key
            $keyPair = $this->ec->keyFromPublic($publicKey, 'hex');

            // Hash the message
            $messageHash = $this->hashMessage($message);

            // Split the signature into r and s components
            $r = substr($signature, 0, 64);
            $s = substr($signature, 64);

            // Create signature object
            $sig = [
                'r' => $r,
                's' => $s
            ];

            // Verify the signature
            return $keyPair->verify($messageHash, $sig);
        } catch (Exception $e) {
            Log::error('Error verifying signature: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Encrypt a private key
     *
     * @param string $privateKey
     * @return string
     */
    public function encryptPrivateKey(string $privateKey): string
    {
        return Crypt::encryptString($privateKey);
    }

    /**
     * Decrypt a private key
     *
     * @param string $encryptedPrivateKey
     * @return string
     */
    public function decryptPrivateKey(string $encryptedPrivateKey): string
    {
        return Crypt::decryptString($encryptedPrivateKey);
    }

    /**
     * Generate a secure random number
     *
     * @param int $bytes
     * @return string
     */
    private function secureRandom(int $bytes = 32): string
    {
        return bin2hex(random_bytes($bytes));
    }
}
