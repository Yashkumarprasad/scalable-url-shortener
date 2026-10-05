<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\Url;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_regular_user_cannot_access_admin_endpoints(): void
    {
        $regularUser = User::factory()->create(['role' => UserRole::USER]);

        $this->actingAs($regularUser, 'sanctum')->getJson('/api/v1/admin/stats')
            ->assertStatus(403);

        $this->actingAs($regularUser, 'sanctum')->getJson('/api/v1/admin/users')
            ->assertStatus(403);

        $this->actingAs($regularUser, 'sanctum')->getJson('/api/v1/admin/urls')
            ->assertStatus(403);
    }

    public function test_admin_can_view_system_stats(): void
    {
        $admin = User::factory()->admin()->create();
        Url::factory()->count(5)->create();

        $response = $this->actingAs($admin, 'sanctum')->getJson('/api/v1/admin/stats');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'users' => ['total', 'active', 'suspended'],
                    'urls' => ['total', 'active', 'expired'],
                    'clicks' => ['total', 'last_24_hours'],
                    'top_performing_urls',
                ],
            ]);
    }

    public function test_admin_can_list_and_moderate_users(): void
    {
        $admin = User::factory()->admin()->create();
        $targetUser = User::factory()->create(['status' => UserStatus::ACTIVE]);

        // List users
        $response = $this->actingAs($admin, 'sanctum')->getJson('/api/v1/admin/users');
        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['items', 'pagination']]);

        // Suspend user
        $suspendResponse = $this->actingAs($admin, 'sanctum')
            ->patchJson("/api/v1/admin/users/{$targetUser->id}/status", [
                'status' => UserStatus::SUSPENDED->value,
            ]);

        $suspendResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $targetUser->id,
                    'status' => UserStatus::SUSPENDED->value,
                ],
            ]);

        $this->assertSame(UserStatus::SUSPENDED, $targetUser->fresh()->status);
    }

    public function test_admin_can_toggle_url_status(): void
    {
        $admin = User::factory()->admin()->create();
        $url = Url::factory()->create(['is_active' => true]);

        $response = $this->actingAs($admin, 'sanctum')
            ->patchJson("/api/v1/admin/urls/{$url->id}/status", [
                'is_active' => false,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $url->id,
                    'is_active' => false,
                ],
            ]);

        $this->assertFalse($url->fresh()->is_active);
    }

    public function test_admin_can_delete_any_url(): void
    {
        $admin = User::factory()->admin()->create();
        $url = Url::factory()->create();

        $response = $this->actingAs($admin, 'sanctum')
            ->deleteJson("/api/v1/admin/urls/{$url->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'URL deleted successfully by admin',
            ]);

        $this->assertSoftDeleted('urls', ['id' => $url->id]);
    }
}
