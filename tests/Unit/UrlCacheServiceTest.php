<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Url;
use App\Services\UrlCacheService;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class UrlCacheServiceTest extends TestCase
{
    private UrlCacheService $cacheService;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
        $this->cacheService = new UrlCacheService(3600);
    }

    public function test_it_caches_and_retrieves_url(): void
    {
        $url = new Url([
            'id' => 42,
            'original_url' => 'https://laravel.com',
            'short_code' => 'lar42',
            'custom_alias' => 'laravel-alias',
            'is_active' => true,
            'expires_at' => null,
        ]);
        $url->id = 42;

        $this->cacheService->set($url);

        $cachedByCode = $this->cacheService->get('lar42');
        $this->assertNotNull($cachedByCode);
        $this->assertSame('https://laravel.com', $cachedByCode['original_url']);
        $this->assertSame(42, $cachedByCode['id']);

        $cachedByAlias = $this->cacheService->get('laravel-alias');
        $this->assertNotNull($cachedByAlias);
        $this->assertSame('https://laravel.com', $cachedByAlias['original_url']);
    }

    public function test_it_invalidates_cache_on_forget(): void
    {
        $url = new Url([
            'id' => 99,
            'original_url' => 'https://example.org',
            'short_code' => 'org99',
            'custom_alias' => 'my-org',
            'is_active' => true,
        ]);
        $url->id = 99;

        $this->cacheService->set($url);
        $this->assertNotNull($this->cacheService->get('org99'));
        $this->assertNotNull($this->cacheService->get('my-org'));

        $this->cacheService->forget($url);
        $this->assertNull($this->cacheService->get('org99'));
        $this->assertNull($this->cacheService->get('my-org'));
    }
}
