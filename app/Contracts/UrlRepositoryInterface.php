<?php

declare(strict_types=1);

namespace App\Contracts;

use App\DTOs\CreateUrlDTO;
use App\DTOs\UpdateUrlDTO;
use App\DTOs\UrlFilterDTO;
use App\Models\Url;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface UrlRepositoryInterface
{
    /**
     * Find URL by short code or custom alias (including soft deleted or inactive for validation).
     *
     * @param string $code
     * @return Url|null
     */
    public function findByCode(string $code): ?Url;

    /**
     * Find active URL by ID.
     *
     * @param int $id
     * @return Url|null
     */
    public function findById(int $id): ?Url;

    /**
     * Check if a short code or custom alias exists in database.
     *
     * @param string $code
     * @param int|null $excludeId
     * @return bool
     */
    public function codeExists(string $code, ?int $excludeId = null): bool;

    /**
     * Create a new URL record atomically.
     *
     * @param CreateUrlDTO $dto
     * @param string $shortCode
     * @return Url
     */
    public function create(CreateUrlDTO $dto, string $shortCode): Url;

    /**
     * Update an existing URL record.
     *
     * @param Url $url
     * @param UpdateUrlDTO $dto
     * @return Url
     */
    public function update(Url $url, UpdateUrlDTO $dto): Url;

    /**
     * Delete a URL record (soft delete).
     *
     * @param Url $url
     * @return bool
     */
    public function delete(Url $url): bool;

    /**
     * Get paginated URLs for a specific user.
     *
     * @param int $userId
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPaginatedForUser(int $userId, int $perPage = 15): LengthAwarePaginator;

    /**
     * Get paginated URLs across the system (admin).
     *
     * @param int $perPage
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function getPaginatedAll(int $perPage = 15, array $filters = []): LengthAwarePaginator;

    /**
     * Increment click count atomically on URL record.
     *
     * @param int $urlId
     * @param int $amount
     * @return void
     */
    public function incrementClickCount(int $urlId, int $amount = 1): void;
}
