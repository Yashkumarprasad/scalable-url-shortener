<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\HealthService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class HealthController extends Controller
{
    public function __construct(
        private readonly HealthService $healthService
    ) {
    }

    /**
     * Perform health check probe.
     */
    public function check(): JsonResponse
    {
        $health = $this->healthService->check();

        $statusCode = $health['status'] === 'ok'
            ? Response::HTTP_OK
            : Response::HTTP_SERVICE_UNAVAILABLE;

        return response()->json($health, $statusCode);
    }
}
