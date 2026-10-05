<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\UserStatus;
use App\Support\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsActive
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->status !== UserStatus::ACTIVE) {
            $message = $user->status === UserStatus::SUSPENDED
                ? 'Your account has been suspended. Please contact support.'
                : 'Your account is inactive. Please activate your account.';

            return ApiResponse::forbidden($message);
        }

        return $next($request);
    }
}
