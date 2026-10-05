<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Support\ApiResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UrlDisabledException extends Exception
{
    protected $message = 'The requested short URL is deactivated or disabled.';
    protected $code = Response::HTTP_FORBIDDEN;

    public function render(Request $request): JsonResponse
    {
        return ApiResponse::forbidden($this->getMessage());
    }
}
