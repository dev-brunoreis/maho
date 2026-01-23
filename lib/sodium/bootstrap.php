<?php

if (!extension_loaded('sodium')) {
    
    if (!defined('SODIUM_CRYPTO_SECRETBOX_KEYBYTES')) {
        define('SODIUM_CRYPTO_SECRETBOX_KEYBYTES', 32);
    }
    if (!defined('SODIUM_CRYPTO_SECRETBOX_NONCEBYTES')) {
        define('SODIUM_CRYPTO_SECRETBOX_NONCEBYTES', 24);
    }
    if (!defined('SODIUM_CRYPTO_SECRETBOX_BOXZEROBYTES')) {
        define('SODIUM_CRYPTO_SECRETBOX_BOXZEROBYTES', 16);
    }

    /**
     * Re create the sodium extension functions with native PHP code (without sodium extension)
     */
    function sodium_bin2hex(string $bin): string
    {
        return bin2hex($bin);
    }

    function sodium_hex2bin(string $hex): string
    {
        $bin = hex2bin($hex);
        if ($bin === false) {
            throw new InvalidArgumentException('Invalid hex string');
        }
        return $bin;
    }

    function sodium_crypto_secretbox_keygen(): string
    {
        return random_bytes(SODIUM_CRYPTO_SECRETBOX_KEYBYTES);
    }

    function sodium_crypto_secretbox(string $message, string $nonce, string $key): string
    {
        if (strlen($key) !== SODIUM_CRYPTO_SECRETBOX_KEYBYTES) {
            throw new InvalidArgumentException('Invalid key size');
        }
        if (strlen($nonce) !== SODIUM_CRYPTO_SECRETBOX_NONCEBYTES) {
            throw new InvalidArgumentException('Invalid nonce size');
        }

        $paddedMessage = str_repeat("\0", SODIUM_CRYPTO_SECRETBOX_BOXZEROBYTES) . $message;
        $ciphertext = openssl_encrypt(
            $paddedMessage,
            'aes-256-gcm',
            $key,
            OPENSSL_RAW_DATA,
            $nonce,
            $tag
        );

        if ($ciphertext === false) {
            throw new RuntimeException('Encryption failed');
        }

        return str_repeat("\0", SODIUM_CRYPTO_SECRETBOX_BOXZEROBYTES) . $ciphertext . $tag;
    }

}