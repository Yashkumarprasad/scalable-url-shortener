<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Support\ApiResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InvalidOperationException extends Exception
{
    protected $message = 'Invalid operation requested.';
    protected $code = Response::HTTP_BAD_REQUEST;

    public function render(Request $request): JsonResponse
    {
        return ApiResponse::error($this->getMessage(), Response::HTTP_BAD_REQUEST);
    }
}
