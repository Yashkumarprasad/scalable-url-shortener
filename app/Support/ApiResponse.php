<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ApiResponse
{
    /**
     * Return a standardized success response.
     *
     * @param mixed $data
     * @param string $message
     * @param int $status
     * @return JsonResponse
     */
    public static function success(
        mixed $data = null,
        string $message = 'Operation successful',
        int $status = Response::HTTP_OK
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    /**
     * Return a standardized creation response (201).
     *
     * @param mixed $data
     * @param string $message
     * @return JsonResponse
     */
    public static function created(
        mixed $data = null,
        string $message = 'Resource created successfully'
    ): JsonResponse {
        return self::success($data, $message, Response::HTTP_CREATED);
    }

    /**
     * Return a standardized error response.
     *
     * @param string $message
     * @param int $status
     * @param mixed $data
     * @return JsonResponse
     */
    public static function error(
        string $message = 'An error occurred',
        int $status = Response::HTTP_BAD_REQUEST,
        mixed $data = null
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    /**
     * Return a standardized validation error response (422).
     *
     * @param array<string, mixed> $errors
     * @param string $message
     * @return JsonResponse
     */
    public static function validationError(
        array $errors,
        string $message = 'Validation failed'
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * Return a standardized unauthorized response (401).
     *
     * @param string $message
     * @return JsonResponse
     */
    public static function unauthorized(string $message = 'Unauthenticated'): JsonResponse
    {
        return self::error($message, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Return a standardized forbidden response (403).
     *
     * @param string $message
     * @return JsonResponse
     */
    public static function forbidden(string $message = 'You do not have permission to perform this action'): JsonResponse
    {
        return self::error($message, Response::HTTP_FORBIDDEN);
    }

    /**
     * Return a standardized not found response (404).
     *
     * @param string $message
     * @return JsonResponse
     */
    public static function notFound(string $message = 'Resource not found'): JsonResponse
    {
        return self::error($message, Response::HTTP_NOT_FOUND);
    }

    /**
     * Return a standardized conflict response (409).
     *
     * @param string $message
     * @return JsonResponse
     */
    public static function conflict(string $message = 'Resource conflict'): JsonResponse
    {
        return self::error($message, Response::HTTP_CONFLICT);
    }

    /**
     * Return a standardized too many requests response (429).
     *
     * @param string $message
     * @return JsonResponse
     */
    public static function tooManyRequests(string $message = 'Too many requests. Please slow down.'): JsonResponse
    {
        return self::error($message, Response::HTTP_TOO_MANY_REQUESTS);
    }
}
