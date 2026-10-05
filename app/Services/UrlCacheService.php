<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\UrlCacheServiceInterface;
use App\Models\Url;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

class UrlCacheService implements UrlCacheServiceInterface
{
    private const KEY_PREFIX = 'url:short:';
    private const ALIAS_PREFIX = 'url:alias:';
    private const CLICK_COUNTER_PREFIX = 'url:clicks:';

    private int $ttlSeconds;

    public function __construct(?int $ttl = null)
    {
        $this->ttlSeconds = $ttl ?? (int) config('cache.url_ttl', 86400); // 24h default
    }

    /**
     * Retrieve cached URL payload by code or custom alias.
     */
    public function get(string $code): ?array
    {
        try {
            // First check direct code cache
            $cached = Cache::get(self::KEY_PREFIX . $code);
            if (is_array($cached)) {
                return $cached;
            }

            // Check alias cache
            $cachedAlias = Cache::get(self::ALIAS_PREFIX . $code);
            if (is_array($cachedAlias)) {
                return $cachedAlias;
            }

            return null;
        } catch (Throwable $e) {
            Log::warning('Redis cache read failure: ' . $e->getMessage(), ['code' => $code]);
            return null; // Graceful degradation to database
        }
    }

    /**
     * Cache URL payload by code and custom alias.
     */
    public function set(Url $url): void
    {
        $payload = [
            'id' => $url->id,
            'original_url' => $url->original_url,
            'short_code' => $url->short_code,
            'custom_alias' => $url->custom_alias,
            'is_active' => (bool) $url->is_active,
            'expires_at' => $url->expires_at ? $url->expires_at->toIso8601String() : null,
        ];

        try {
            Cache::put(self::KEY_PREFIX . $url->short_code, $payload, $this->ttlSeconds);

            if (! empty($url->custom_alias)) {
                Cache::put(self::ALIAS_PREFIX . $url->custom_alias, $payload, $this->ttlSeconds);
            }
        } catch (Throwable $e) {
            Log::warning('Redis cache write failure: ' . $e->getMessage(), ['url_id' => $url->id]);
        }
    }

    /**
     * Invalidate cached entries for a URL.
     */
    public function forget(Url|string $urlOrCode, ?string $customAlias = null): void
    {
        try {
            if ($urlOrCode instanceof Url) {
                Cache::forget(self::KEY_PREFIX . $urlOrCode->short_code);
                if (! empty($urlOrCode->custom_alias)) {
                    Cache::forget(self::ALIAS_PREFIX . $urlOrCode->custom_alias);
                }
            } else {
                Cache::forget(self::KEY_PREFIX . $urlOrCode);
                Cache::forget(self::ALIAS_PREFIX . $urlOrCode);
                if (! empty($customAlias)) {
                    Cache::forget(self::ALIAS_PREFIX . $customAlias);
                }
            }
        } catch (Throwable $e) {
            Log::warning('Redis cache eviction failure: ' . $e->getMessage());
        }
    }

    /**
     * Increment click counter in cache or return status.
     */
    public function incrementClick(int $urlId): int
    {
        try {
            return (int) Cache::increment(self::CLICK_COUNTER_PREFIX . $urlId);
        } catch (Throwable $e) {
            return 1;
        }
    }
}
