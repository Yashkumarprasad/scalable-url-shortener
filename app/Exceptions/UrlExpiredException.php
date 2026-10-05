<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Support\ApiResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UrlExpiredException extends Exception
{
    protected $message = 'The requested short URL has expired.';
    protected $code = Response::HTTP_GONE; // 410 Gone

    public function render(Request $request): JsonResponse
    {
        return ApiResponse::error($this->getMessage(), Response::HTTP_GONE);
    }
}
