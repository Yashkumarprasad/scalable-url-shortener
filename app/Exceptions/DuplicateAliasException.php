<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Support\ApiResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DuplicateAliasException extends Exception
{
    protected $message = 'The specified custom alias is already in use.';
    protected $code = Response::HTTP_CONFLICT;

    public function render(Request $request): JsonResponse
    {
        return ApiResponse::conflict($this->getMessage());
    }
}
