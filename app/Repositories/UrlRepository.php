<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\UrlRepositoryInterface;
use App\DTOs\CreateUrlDTO;
use App\DTOs\UpdateUrlDTO;
use App\Models\Url;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class UrlRepository implements UrlRepositoryInterface
{
    /**
     * Find URL by short code or custom alias.
     */
    public function findByCode(string $code): ?Url
    {
        return Url::query()
            ->where('short_code', $code)
            ->orWhere('custom_alias', $code)
            ->first();
    }

    /**
     * Find active URL by ID.
     */
    public function findById(int $id): ?Url
    {
        return Url::query()->find($id);
    }

    /**
     * Check if a short code or custom alias exists in database.
     */
    public function codeExists(string $code, ?int $excludeId = null): bool
    {
        $query = Url::withTrashed()
            ->where(function ($q) use ($code) {
                $q->where('short_code', $code)
                  ->orWhere('custom_alias', $code);
            });

        if ($excludeId !== null) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    /**
     * Create a new URL record atomically.
     */
    public function create(CreateUrlDTO $dto, string $shortCode): Url
    {
        return DB::transaction(function () use ($dto, $shortCode) {
            return Url::create([
                'user_id' => $dto->userId,
                'original_url' => $dto->originalUrl,
                'short_code' => $shortCode,
                'custom_alias' => $dto->customAlias,
                'title' => $dto->title,
                'expires_at' => $dto->expiresAt,
                'is_active' => true,
                'click_count' => 0,
            ]);
        });
    }

    /**
     * Update an existing URL record.
     */
    public function update(Url $url, UpdateUrlDTO $dto): Url
    {
        return DB::transaction(function () use ($url, $dto) {
            $attributes = [];

            if ($dto->originalUrl !== null) {
                $attributes['original_url'] = $dto->originalUrl;
            }
            if ($dto->title !== null) {
                $attributes['title'] = $dto->title;
            }
            if ($dto->customAlias !== null) {
                $attributes['custom_alias'] = $dto->customAlias;
            }
            if ($dto->isActive !== null) {
                $attributes['is_active'] = $dto->isActive;
            }
            if ($dto->clearExpiresAt) {
                $attributes['expires_at'] = null;
            } elseif ($dto->expiresAt !== null) {
                $attributes['expires_at'] = $dto->expiresAt;
            }

            $url->update($attributes);
            return $url->fresh();
        });
    }

    /**
     * Delete a URL record (soft delete).
     */
    public function delete(Url $url): bool
    {
        return (bool) $url->delete();
    }

    /**
     * Get paginated URLs for a specific user.
     */
    public function getPaginatedForUser(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return Url::query()
            ->where('user_id', $userId)
            ->latest('id')
            ->paginate($perPage);
    }

    /**
     * Get paginated URLs across the system (admin).
     */
    public function getPaginatedAll(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = Url::query()->with('user')->latest('id');

        if (! empty($filters['search'])) {
            $search = '%' . $filters['search'] . '%';
            $query->where(function ($q) use ($search) {
                $q->where('original_url', 'like', $search)
                  ->orWhere('short_code', 'like', $search)
                  ->orWhere('custom_alias', 'like', $search)
                  ->orWhere('title', 'like', $search);
            });
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', (bool) $filters['is_active']);
        }

        if (isset($filters['user_id'])) {
            $query->where('user_id', (int) $filters['user_id']);
        }

        return $query->paginate($perPage);
    }

    /**
     * Increment click count atomically on URL record.
     */
    public function incrementClickCount(int $urlId, int $amount = 1): void
    {
        Url::where('id', $urlId)->increment('click_count', $amount);
    }
}
