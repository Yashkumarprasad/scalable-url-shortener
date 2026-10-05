<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Contracts\UrlClickRepositoryInterface;
use App\Contracts\UrlRepositoryInterface;
use App\DTOs\RecordClickDTO;
use App\Enums\DeviceType;
use App\Jobs\RecordUrlClickJob;
use App\Models\Url;
use App\Models\UrlClick;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnalyticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_view_url_analytics(): void
    {
        $user = User::factory()->create();
        $url = Url::factory()->create(['user_id' => $user->id, 'click_count' => 15]);

        UrlClick::factory()->count(10)->create(['url_id' => $url->id]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson("/api/v1/urls/{$url->id}/analytics");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'url_id' => $url->id,
                    'total_clicks' => 10,
                    'lifetime_clicks' => 15,
                ],
            ])
            ->assertJsonStructure([
                'data' => [
                    'url_id',
                    'short_code',
                    'total_clicks',
                    'lifetime_clicks',
                    'clicks_by_day',
                    'clicks_by_hour',
                    'top_referrers',
                    'device_breakdown',
                    'country_breakdown',
                ],
            ]);
    }

    public function test_stranger_cannot_view_url_analytics(): void
    {
        $owner = User::factory()->create();
        $stranger = User::factory()->create();
        $url = Url::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($stranger, 'sanctum')
            ->getJson("/api/v1/urls/{$url->id}/analytics")
            ->assertStatus(403);
    }

    public function test_record_url_click_job_processes_click_and_increments_counter(): void
    {
        $url = Url::factory()->create(['click_count' => 5]);

        $clickDTO = new RecordClickDTO(
            urlId: $url->id,
            ipAddress: '198.51.100.1',
            userAgent: 'Mozilla/5.0 Test Browser',
            referer: 'https://twitter.com',
            deviceType: DeviceType::DESKTOP,
            countryCode: 'US',
            countryName: 'United States',
            city: 'San Francisco',
            clickedAt: now()
        );

        $job = new RecordUrlClickJob($clickDTO);
        $job->handle(
            app(UrlClickRepositoryInterface::class),
            app(UrlRepositoryInterface::class)
        );

        $this->assertDatabaseHas('url_clicks', [
            'url_id' => $url->id,
            'ip_address' => '198.51.100.1',
            'device_type' => DeviceType::DESKTOP->value,
            'country_code' => 'US',
        ]);

        $this->assertSame(6, $url->fresh()->click_count);
    }
}
