<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\Url;

interface UrlCacheServiceInterface
{
    /**
     * Retrieve cached URL payload by code or custom alias.
     *
     * @param string $code
     * @return array|null
     */
    public function get(string $code): ?array;

    /**
     * Cache URL payload by code and custom alias.
     *
     * @param Url $url
     * @return void
     */
    public function set(Url $url): void;

    /**
     * Invalidate cached entries for a URL (short code and custom alias).
     *
     * @param Url|string $urlOrCode
     * @param string|null $customAlias
     * @return void
     */
    public function forget(Url|string $urlOrCode, ?string $customAlias = null): void;

    /**
     * Increment click counter in cache or return status.
     *
     * @param int $urlId
     * @return int
     */
    public function incrementClick(int $urlId): int;
}
