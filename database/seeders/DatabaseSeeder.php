<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\Url;
use App\Models\UrlClick;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('password123'),
                'role' => UserRole::ADMIN,
                'status' => UserStatus::ACTIVE,
                'email_verified_at' => now(),
            ]
        );

        // 2. Create Regular Demo user
        $user = User::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'John Doe',
                'password' => Hash::make('password123'),
                'role' => UserRole::USER,
                'status' => UserStatus::ACTIVE,
                'email_verified_at' => now(),
            ]
        );

        // 3. Create Inactive user
        User::firstOrCreate(
            ['email' => 'inactive@example.com'],
            [
                'name' => 'Inactive User',
                'password' => Hash::make('password123'),
                'role' => UserRole::USER,
                'status' => UserStatus::INACTIVE,
                'email_verified_at' => null,
            ]
        );

        // 4. Create sample URLs for regular user
        $demoUrls = [
            [
                'original_url' => 'https://laravel.com/docs/10.x',
                'short_code' => 'larav10',
                'custom_alias' => 'laravel-docs',
                'title' => 'Laravel 10 Documentation',
                'click_count' => 45,
            ],
            [
                'original_url' => 'https://github.com/torvalds/linux',
                'short_code' => 'lnxgit',
                'custom_alias' => 'linux-kernel',
                'title' => 'Linux Kernel GitHub',
                'click_count' => 120,
            ],
            [
                'original_url' => 'https://redis.io/documentation',
                'short_code' => 'rdsdoc',
                'custom_alias' => null,
                'title' => 'Redis Official Docs',
                'click_count' => 18,
            ],
        ];

        foreach ($demoUrls as $urlData) {
            $clickCount = $urlData['click_count'];
            unset($urlData['click_count']);

            $url = Url::firstOrCreate(
                ['short_code' => $urlData['short_code']],
                array_merge($urlData, [
                    'user_id' => $user->id,
                    'is_active' => true,
                    'click_count' => $clickCount,
                ])
            );

            // Generate clicks for analytics testing
            if ($url->clicks()->count() === 0) {
                UrlClick::factory()->count($clickCount)->create([
                    'url_id' => $url->id,
                ]);
            }
        }
    }
}
