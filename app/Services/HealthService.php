<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Throwable;

class HealthService
{
    /**
     * Perform deep health check on vital application infrastructure.
     *
     * @return array{status: string, timestamp: string, services: array<string, string>}
     */
    public function check(): array
    {
        $dbStatus = $this->checkDatabase();
        $redisStatus = $this->checkRedis();

        $overallStatus = ($dbStatus === 'ok' && $redisStatus === 'ok') ? 'ok' : 'degraded';

        return [
            'status' => $overallStatus,
            'timestamp' => now()->toIso8601String(),
            'services' => [
                'database' => $dbStatus,
                'redis' => $redisStatus,
                'queue' => config('queue.default', 'redis'),
            ],
        ];
    }

    private function checkDatabase(): string
    {
        try {
            DB::connection()->getPdo();
            DB::select('SELECT 1');
            return 'ok';
        } catch (Throwable $e) {
            return 'unavailable';
        }
    }

    private function checkRedis(): string
    {
        try {
            Cache::store('redis')->get('health-check-probe');
            return 'ok';
        } catch (Throwable $e) {
            // Fallback check on default cache
            try {
                Cache::get('health-check-probe');
                return 'ok';
            } catch (Throwable) {
                return 'unavailable';
            }
        }
    }
}
