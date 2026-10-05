<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\UserStatus;
use App\Models\Url;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UrlManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_short_url(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/urls', [
            'original_url' => 'https://example.com/some/long/path?param=123',
            'title' => 'Example Path',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'URL created successfully',
            ])
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'original_url',
                    'short_code',
                    'short_url',
                    'is_active',
                    'click_count',
                ],
            ]);

        $this->assertDatabaseHas('urls', [
            'user_id' => $user->id,
            'original_url' => 'https://example.com/some/long/path?param=123',
            'title' => 'Example Path',
        ]);
    }

    public function test_authenticated_user_can_create_url_with_custom_alias(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/urls', [
            'original_url' => 'https://laravel.com',
            'custom_alias' => 'my-laravel-link',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => [
                    'custom_alias' => 'my-laravel-link',
                ],
            ]);

        $this->assertDatabaseHas('urls', [
            'custom_alias' => 'my-laravel-link',
        ]);
    }

    public function test_cannot_create_duplicate_custom_alias(): void
    {
        $user = User::factory()->create();
        Url::factory()->create(['custom_alias' => 'duplicate-alias']);

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/urls', [
            'original_url' => 'https://google.com',
            'custom_alias' => 'duplicate-alias',
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure(['errors' => ['custom_alias']]);
    }

    public function test_rejects_dangerous_url_schemes(): void
    {
        $user = User::factory()->create();

        $dangerousUrls = [
            'javascript:alert(1)',
            'data:text/html;base64,PHNjcmlwdD5hbGVydCgxKTwvc2NyaXB0Pg==',
            'file:///etc/passwd',
            'vbscript:msgbox(1)',
        ];

        foreach ($dangerousUrls as $dangerous) {
            $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/urls', [
                'original_url' => $dangerous,
            ]);

            $response->assertStatus(422);
        }
    }

    public function test_user_can_list_their_own_urls(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        Url::factory()->count(3)->create(['user_id' => $user->id]);
        Url::factory()->count(2)->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/v1/urls');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data.items')
            ->assertJson([
                'data' => [
                    'pagination' => [
                        'total' => 3,
                    ],
                ],
            ]);
    }

    public function test_user_cannot_view_or_modify_another_users_url(): void
    {
        $owner = User::factory()->create();
        $stranger = User::factory()->create();

        $url = Url::factory()->create(['user_id' => $owner->id]);

        // Attempt show
        $this->actingAs($stranger, 'sanctum')->getJson("/api/v1/urls/{$url->id}")
            ->assertStatus(403);

        // Attempt update
        $this->actingAs($stranger, 'sanctum')->putJson("/api/v1/urls/{$url->id}", [
            'title' => 'Hacked Title',
        ])->assertStatus(403);

        // Attempt delete
        $this->actingAs($stranger, 'sanctum')->deleteJson("/api/v1/urls/{$url->id}")
            ->assertStatus(403);
    }

    public function test_user_can_update_their_own_url(): void
    {
        $user = User::factory()->create();
        $url = Url::factory()->create([
            'user_id' => $user->id,
            'title' => 'Old Title',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user, 'sanctum')->putJson("/api/v1/urls/{$url->id}", [
            'title' => 'Updated Title',
            'is_active' => false,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'title' => 'Updated Title',
                    'is_active' => false,
                ],
            ]);

        $this->assertDatabaseHas('urls', [
            'id' => $url->id,
            'title' => 'Updated Title',
            'is_active' => false,
        ]);
    }

    public function test_user_can_delete_their_own_url(): void
    {
        $user = User::factory()->create();
        $url = Url::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user, 'sanctum')->deleteJson("/api/v1/urls/{$url->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'URL deleted successfully',
            ]);

        $this->assertSoftDeleted('urls', ['id' => $url->id]);
    }

    public function test_suspended_user_cannot_create_urls(): void
    {
        $suspendedUser = User::factory()->suspended()->create();

        $response = $this->actingAs($suspendedUser, 'sanctum')->postJson('/api/v1/urls', [
            'original_url' => 'https://example.com',
        ]);

        $response->assertStatus(403);
    }
}
