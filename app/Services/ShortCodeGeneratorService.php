<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\ShortCodeGeneratorInterface;
use InvalidArgumentException;

class ShortCodeGeneratorService implements ShortCodeGeneratorInterface
{
    /**
     * Standard URL-safe Base62 character set.
     */
    private const ALPHABET = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    /**
     * Reserved short-codes or keywords that cannot be used as codes/aliases.
     */
    private const RESERVED_WORDS = [
        'api', 'admin', 'health', 'login', 'logout', 'register', 'dashboard',
        'auth', 'docs', 'swagger', 'v1', 'v2', 'stats', 'metrics', 'telescope',
        'horizon', 'sanctum', 'oauth', 'settings', 'profile', 'null', 'undefined'
    ];

    private string $alphabet;
    private int $alphabetLength;

    public function __construct(?string $customAlphabet = null)
    {
        $this->alphabet = $customAlphabet ?: self::ALPHABET;
        $this->alphabetLength = strlen($this->alphabet);

        if ($this->alphabetLength < 16) {
            throw new InvalidArgumentException('Short code alphabet must contain at least 16 characters.');
        }
    }

    /**
     * Generate a cryptographically secure random short code string.
     */
    public function generate(int $length = 6): string
    {
        if ($length < 3 || $length > 32) {
            throw new InvalidArgumentException('Short code length must be between 3 and 32 characters.');
        }

        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $randomIndex = random_int(0, $this->alphabetLength - 1);
            $code .= $this->alphabet[$randomIndex];
        }

        // Avoid reserved words
        if ($this->isReserved($code)) {
            return $this->generate($length);
        }

        return $code;
    }

    /**
     * Validate whether a string conforms to short code rules.
     */
    public function isValid(string $code): bool
    {
        if (strlen($code) < 3 || strlen($code) > 64) {
            return false;
        }

        if ($this->isReserved($code)) {
            return false;
        }

        // Must consist of alphanumeric characters, hyphens or underscores
        return (bool) preg_match('/^[a-zA-Z0-9_-]+$/', $code);
    }

    /**
     * Check if code is in the reserved list.
     */
    public function isReserved(string $code): bool
    {
        return in_array(strtolower($code), self::RESERVED_WORDS, true);
    }
}
