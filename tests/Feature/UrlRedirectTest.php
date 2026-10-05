<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Jobs\RecordUrlClickJob;
use App\Models\Url;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class UrlRedirectTest extends TestCase
{
    use RefreshDatabase;

    public function test_short_code_redirects_to_original_url(): void
    {
        Queue::fake();

        $url = Url::factory()->create([
            'original_url' => 'https://laravel.com/docs/10.x',
            'short_code' => 'lar10x',
            'is_active' => true,
        ]);

        $response = $this->get('/lar10x');

        $response->assertStatus(302)
            ->assertRedirect('https://laravel.com/docs/10.x');

        Queue::assertPushed(RecordUrlClickJob::class, function ($job) use ($url) {
            return $job->clickDTO->urlId === $url->id;
        });
    }

    public function test_custom_alias_redirects_to_original_url(): void
    {
        Queue::fake();

        $url = Url::factory()->create([
            'original_url' => 'https://redis.io/commands',
            'short_code' => 'rdsxyz',
            'custom_alias' => 'redis-commands',
            'is_active' => true,
        ]);

        $response = $this->get('/redis-commands');

        $response->assertStatus(302)
            ->assertRedirect('https://redis.io/commands');

        Queue::assertPushed(RecordUrlClickJob::class);
    }

    public function test_expired_url_returns_410_gone(): void
    {
        Url::factory()->expired()->create([
            'short_code' => 'expi01',
        ]);

        $response = $this->getJson('/expi01');

        $response->assertStatus(410)
            ->assertJson([
                'success' => false,
                'message' => 'This short URL has expired.',
            ]);
    }

    public function test_deactivated_url_returns_403_forbidden(): void
    {
        Url::factory()->inactive()->create([
            'short_code' => 'disa01',
        ]);

        $response = $this->getJson('/disa01');

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'This short URL has been disabled or deactivated.',
            ]);
    }

    public function test_non_existent_url_returns_404(): void
    {
        $response = $this->getJson('/nonexistent99');

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => "No URL found matching 'nonexistent99'.",
            ]);
    }

    public function test_redirect_populates_and_utilizes_cache(): void
    {
        Queue::fake();

        $url = Url::factory()->create([
            'original_url' => 'https://github.com/laravel/framework',
            'short_code' => 'laragk',
            'is_active' => true,
        ]);

        // First access: Cache Miss -> Populate Cache
        $res1 = $this->get('/laragk');
        $res1->assertStatus(302);
        $res1->assertHeader('X-Redirect-Cache', 'MISS');

        // Check cache exists
        $cached = Cache::get('url:short:laragk');
        $this->assertNotNull($cached);
        $this->assertSame('https://github.com/laravel/framework', $cached['original_url']);

        // Second access: Cache Hit
        $res2 = $this->get('/laragk');
        $res2->assertStatus(302);
        $res2->assertHeader('X-Redirect-Cache', 'HIT');
    }
}
