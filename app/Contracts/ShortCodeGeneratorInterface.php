<?php

declare(strict_types=1);

namespace App\Contracts;

interface ShortCodeGeneratorInterface
{
    /**
     * Generate a unique short code string.
     *
     * @param int $length
     * @return string
     */
    public function generate(int $length = 6): string;

    /**
     * Validate whether a string conforms to short code rules.
     *
     * @param string $code
     * @return bool
     */
    public function isValid(string $code): bool;
}
