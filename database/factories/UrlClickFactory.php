<?php

namespace Database\Factories;

use App\Enums\DeviceType;
use App\Models\Url;
use App\Models\UrlClick;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UrlClick>
 */
class UrlClickFactory extends Factory
{
    protected $model = UrlClick::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $devices = [DeviceType::DESKTOP, DeviceType::MOBILE, DeviceType::TABLET];
        $referrers = ['https://google.com', 'https://twitter.com', 'https://linkedin.com', 'https://github.com', null];
        $countries = [
            ['US', 'United States', 'New York'],
            ['GB', 'United Kingdom', 'London'],
            ['IN', 'India', 'Bengaluru'],
            ['DE', 'Germany', 'Berlin'],
            ['CA', 'Canada', 'Toronto'],
        ];

        $country = fake()->randomElement($countries);

        return [
            'url_id' => Url::factory(),
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
            'referer' => fake()->randomElement($referrers),
            'device_type' => fake()->randomElement($devices),
            'country_code' => $country[0],
            'country_name' => $country[1],
            'city' => $country[2],
            'clicked_at' => fake()->dateTimeBetween('-30 days', 'now'),
            'created_at' => now(),
        ];
    }
}
