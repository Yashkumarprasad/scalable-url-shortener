<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /**
     * Register a new user account.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $dto = $request->toDTO();

        $user = User::create([
            'name' => $dto->name,
            'email' => $dto->email,
            'password' => Hash::make($dto->password),
            'role' => UserRole::USER,
            'status' => UserStatus::ACTIVE,
            'email_verified_at' => now(),
        ]);

        $token = $user->createToken('auth-token')->plainTextToken;

        Log::info('User registered successfully', ['user_id' => $user->id, 'email' => $user->email]);

        return ApiResponse::created([
            'user' => new UserResource($user),
            'token' => $token,
            'token_type' => 'Bearer',
        ], 'User registered successfully');
    }

    /**
     * Authenticate user and issue API access token.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $dto = $request->toDTO();

        $user = User::where('email', $dto->email)->first();

        if (! $user || ! Hash::check($dto->password, $user->password)) {
            Log::warning('Failed login attempt', ['email' => $dto->email, 'ip' => $request->ip()]);
            return ApiResponse::unauthorized('Invalid email or password credentials.');
        }

        if ($user->status === UserStatus::SUSPENDED) {
            Log::warning('Login attempt by suspended user', ['user_id' => $user->id]);
            return ApiResponse::forbidden('Your account has been suspended. Please contact support.');
        }

        if ($user->status === UserStatus::INACTIVE) {
            Log::warning('Login attempt by inactive user', ['user_id' => $user->id]);
            return ApiResponse::forbidden('Your account is currently inactive.');
        }

        $deviceName = $dto->deviceName ?: 'api-token';
        $token = $user->createToken($deviceName)->plainTextToken;

        Log::info('User logged in successfully', ['user_id' => $user->id]);

        return ApiResponse::success([
            'user' => new UserResource($user),
            'token' => $token,
            'token_type' => 'Bearer',
        ], 'Logged in successfully');
    }

    /**
     * Revoke current authentication token (logout).
     */
    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user && $user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
        }

        return ApiResponse::success(null, 'Successfully logged out and revoked access token');
    }

    /**
     * Get currently authenticated user details.
     */
    public function me(Request $request): JsonResponse
    {
        return ApiResponse::success(
            new UserResource($request->user()),
            'User profile retrieved successfully'
        );
    }
}
