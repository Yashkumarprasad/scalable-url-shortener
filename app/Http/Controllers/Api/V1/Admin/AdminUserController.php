<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateUserStatusRequest;
use App\Http\Resources\AdminUserResource;
use App\Models\User;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminUserController extends Controller
{
    /**
     * List all users with filtering and pagination.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = max(1, min(100, (int) $request->query('per_page', '15')));
        $search = $request->query('search');
        $status = $request->query('status');

        $query = User::query()->latest('id');

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $term = "%{$search}%";
                $q->where('name', 'like', $term)
                  ->orWhere('email', 'like', $term);
            });
        }

        if (! empty($status)) {
            $query->where('status', $status);
        }

        $users = $query->paginate($perPage);

        return ApiResponse::success([
            'items' => AdminUserResource::collection($users->items()),
            'pagination' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
            ],
        ], 'Users retrieved successfully');
    }

    /**
     * Get specific user profile and statistics.
     */
    public function show(User $user): JsonResponse
    {
        return ApiResponse::success(
            new AdminUserResource($user),
            'User retrieved successfully'
        );
    }

    /**
     * Update user status or role (e.g. suspend or reactivate).
     */
    public function updateStatus(UpdateUserStatusRequest $request, User $user): JsonResponse
    {
        $validated = $request->validated();

        if (isset($validated['status'])) {
            $user->status = $validated['status'];
        }

        if (isset($validated['role'])) {
            $user->role = $validated['role'];
        }

        $user->save();

        Log::info('Admin updated user status', [
            'admin_id' => $request->user()->id,
            'target_user_id' => $user->id,
            'status' => $user->status,
        ]);

        return ApiResponse::success(
            new AdminUserResource($user),
            'User status updated successfully'
        );
    }
}
