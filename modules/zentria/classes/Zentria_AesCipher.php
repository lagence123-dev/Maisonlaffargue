<?php
/**
 *  Copyright 2025 Zentria
 *
 *  @author    Zentria <zentria@profileo.com>
 *  @copyright 2025 Zentria
 *  @license   Commercial
 *  link      https://zentria.profileo.com
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class Zentria_AesCipher
{
    const OPENSSL_CIPHER_NAME = 'aes-128-cbc';

    const CIPHER_KEY_LEN = 16;

    // 128 bits
    private static function fixKey($key)
    {
        if (strlen($key) < self::CIPHER_KEY_LEN) {
            // 0 pad to len 16
            return str_pad((string) $key, self::CIPHER_KEY_LEN, '0');
        }

        if (strlen($key) > self::CIPHER_KEY_LEN) {
            // truncate to 16 bytes
            return substr($key, 0, self::CIPHER_KEY_LEN);
        }
        return $key;
    }

    /**
     * Encrypt data using AES Cipher (CBC) with 128-bit key
     *
     * @param string $key key to use should be 16 bytes long (128-bit)
     * @param string $iv initialization vector
     * @param string $data data to encrypt
     *
     * @return string encrypted data in base64 encoding with iv attached at end after a :
     */
    public static function encryption($key, $iv, $data)
    {
        $encodedEncryptedData = base64_encode(openssl_encrypt($data, self::OPENSSL_CIPHER_NAME, self::fixKey($key), OPENSSL_RAW_DATA, $iv));
        $encodedIV = base64_encode($iv);
        $encryptedPayload = $encodedEncryptedData . ':' . $encodedIV;
        return $encryptedPayload;
    }

    /**
     * Decrypt data using AES Cipher (CBC) with 128-bit key
     *
     * @param string $key key to use should be 16 bytes long (128-bit)
     * @param string $data data to be decrypted in base64 encoding with iv attached at the end after a :
     *
     * @return string decrypted data
     */
    public static function decryption($key, $data)
    {
        $parts = explode(':', $data); // Separate Encrypted data from iv.
        $encrypted = $parts[0];

        if (!isset($parts[1])) {
            // No IV, return empty string
            return '';
        }
        $iv = $parts[1];
        $decryptedData = openssl_decrypt(base64_decode($encrypted), self::OPENSSL_CIPHER_NAME, self::fixKey($key), OPENSSL_RAW_DATA, base64_decode($iv));
        return $decryptedData;
    }

    public static function encrypt($to_encode, $encryptionToken)
    {
        $secure = null;
        return self::encryption(substr($encryptionToken, 0, 16), openssl_random_pseudo_bytes(16, $secure), $to_encode);
    }

    public static function decrypt($to_decode, $encryptionToken)
    {
        return self::decryption(substr($encryptionToken, 0, 16), $to_decode);
    }
}
