<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Url;
use App\Models\User;

class UrlPolicy
{
    /**
     * Perform pre-authorization checks.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isActive();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Url $url): bool
    {
        return $user->isActive() && $url->user_id === $user->id;
    }

    /**
     * Determine whether the user can view analytics for the model.
     */
    public function viewAnalytics(User $user, Url $url): bool
    {
        return $user->isActive() && $url->user_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isActive();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Url $url): bool
    {
        return $user->isActive() && $url->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Url $url): bool
    {
        return $user->isActive() && $url->user_id === $user->id;
    }
}
