<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Support\ApiResponse;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        UrlNotFoundException::class,
        UrlExpiredException::class,
        UrlDisabledException::class,
        DuplicateAliasException::class,
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->renderable(function (ValidationException $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return ApiResponse::validationError(
                    $e->errors(),
                    $e->getMessage() ?: 'Validation failed'
                );
            }
        });

        $this->renderable(function (AuthenticationException $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return ApiResponse::unauthorized($e->getMessage() ?: 'Unauthenticated');
            }
        });

        $this->renderable(function (AuthorizationException|AccessDeniedHttpException $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return ApiResponse::forbidden($e->getMessage() ?: 'This action is unauthorized.');
            }
        });

        $this->renderable(function (ModelNotFoundException|NotFoundHttpException $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return ApiResponse::notFound('The requested endpoint or resource was not found.');
            }
        });

        $this->renderable(function (ThrottleRequestsException|TooManyRequestsHttpException $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return ApiResponse::tooManyRequests('Too many requests. Please slow down.');
            }
        });

        $this->renderable(function (MethodNotAllowedHttpException $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return ApiResponse::error('HTTP method not allowed for this route.', Response::HTTP_METHOD_NOT_ALLOWED);
            }
        });
    }
}
