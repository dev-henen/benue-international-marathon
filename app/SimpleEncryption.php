<?php

namespace App;

class SimpleEncryption
{
    private string $encryptionKey;
    private string $cipherMethod;

    public function __construct(string $encryptionKey, string $cipherMethod = 'AES-256-CBC')
    {
        $this->encryptionKey = hash('sha256', $encryptionKey); // Generate a secure key
        $this->cipherMethod = $cipherMethod;
    }

    /**
     * Encrypt a given string.
     *
     * @param string $data The plaintext to encrypt.
     * @return string The encrypted string, base64 encoded.
     */
    public function encrypt(string $data): string
    {
        $ivLength = openssl_cipher_iv_length($this->cipherMethod);
        $iv = openssl_random_pseudo_bytes($ivLength); // Generate a random IV
        $encrypted = openssl_encrypt($data, $this->cipherMethod, $this->encryptionKey, 0, $iv);

        if ($encrypted === false) {
            throw new \Exception('Encryption failed.');
        }

        // Return base64-encoded string containing both IV and encrypted data
        return base64_encode($iv . $encrypted);
    }

    /**
     * Decrypt a given encrypted string.
     *
     * @param string $data The encrypted string (base64 encoded).
     * @return string The decrypted plaintext.
     */
    public function decrypt(string $data): string
    {
        $decodedData = base64_decode($data);
        $ivLength = openssl_cipher_iv_length($this->cipherMethod);
        $iv = substr($decodedData, 0, $ivLength); // Extract the IV
        $encryptedData = substr($decodedData, $ivLength); // Extract the encrypted text

        $decrypted = openssl_decrypt($encryptedData, $this->cipherMethod, $this->encryptionKey, 0, $iv);

        if ($decrypted === false) {
            throw new \Exception('Decryption failed.');
        }

        return $decrypted;
    }
}
