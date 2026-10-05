<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Support\ApiResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UrlNotFoundException extends Exception
{
    protected $message = 'The requested short URL was not found.';
    protected $code = 404;

    public function render(Request $request): JsonResponse
    {
        return ApiResponse::notFound($this->getMessage());
    }
}
