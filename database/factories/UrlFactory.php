<?php

namespace Database\Factories;

use App\Models\Url;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Url>
 */
class UrlFactory extends Factory
{
    protected $model = Url::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'original_url' => fake()->url(),
            'short_code' => Str::random(6),
            'custom_alias' => null,
            'title' => fake()->sentence(3),
            'expires_at' => null,
            'is_active' => true,
            'click_count' => 0,
        ];
    }

    /**
     * URL with custom alias.
     */
    public function withCustomAlias(string $alias = null): static
    {
        return $this->state(fn (array $attributes) => [
            'custom_alias' => $alias ?? fake()->unique()->slug(2),
        ]);
    }

    /**
     * URL expired.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'expires_at' => now()->subDay(),
        ]);
    }

    /**
     * URL inactive/disabled.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
