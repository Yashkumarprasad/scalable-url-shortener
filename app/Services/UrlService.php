<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\ShortCodeGeneratorInterface;
use App\Contracts\UrlCacheServiceInterface;
use App\Contracts\UrlRepositoryInterface;
use App\DTOs\CreateUrlDTO;
use App\DTOs\UpdateUrlDTO;
use App\Exceptions\DuplicateAliasException;
use App\Exceptions\InvalidOperationException;
use App\Exceptions\UrlDisabledException;
use App\Exceptions\UrlExpiredException;
use App\Exceptions\UrlNotFoundException;
use App\Models\Url;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class UrlService
{
    private const MAX_COLLISION_RETRIES = 5;

    public function __construct(
        private readonly UrlRepositoryInterface $urlRepository,
        private readonly ShortCodeGeneratorInterface $codeGenerator,
        private readonly UrlCacheServiceInterface $cacheService
    ) {
    }

    /**
     * Create a new short URL.
     *
     * @throws DuplicateAliasException
     * @throws InvalidOperationException
     */
    public function createUrl(CreateUrlDTO $dto): Url
    {
        // 1. Validate custom alias uniqueness if provided
        if (! empty($dto->customAlias)) {
            if (! $this->codeGenerator->isValid($dto->customAlias)) {
                throw new InvalidOperationException('The provided custom alias is invalid or reserved.');
            }

            if ($this->urlRepository->codeExists($dto->customAlias)) {
                throw new DuplicateAliasException("The custom alias '{$dto->customAlias}' is already in use.");
            }
        }

        // 2. Generate collision-free unique short code
        $shortCode = $this->generateUniqueShortCode();

        // 3. Persist atomically in repository
        $url = $this->urlRepository->create($dto, $shortCode);

        // 4. Pre-warm Redis cache for ultra-low latency on initial access
        $this->cacheService->set($url);

        Log::info('Short URL created', [
            'id' => $url->id,
            'short_code' => $url->short_code,
            'user_id' => $url->user_id,
        ]);

        return $url;
    }

    /**
     * Update an existing short URL.
     *
     * @throws DuplicateAliasException
     * @throws InvalidOperationException
     */
    public function updateUrl(Url $url, UpdateUrlDTO $dto): Url
    {
        $oldAlias = $url->custom_alias;

        // Check custom alias if changing
        if ($dto->customAlias !== null && $dto->customAlias !== $url->custom_alias) {
            if (! $this->codeGenerator->isValid($dto->customAlias)) {
                throw new InvalidOperationException('The custom alias is invalid or reserved.');
            }

            if ($this->urlRepository->codeExists($dto->customAlias, $url->id)) {
                throw new DuplicateAliasException("The custom alias '{$dto->customAlias}' is already in use.");
            }
        }

        $updated = $this->urlRepository->update($url, $dto);

        // Evict old cache keys and refresh with new data
        $this->cacheService->forget($updated->short_code, $oldAlias);

        if ($updated->is_active && ! $updated->isExpired()) {
            $this->cacheService->set($updated);
        }

        Log::info('Short URL updated', ['id' => $updated->id]);

        return $updated;
    }

    /**
     * Delete a URL (soft-delete and cache invalidation).
     */
    public function deleteUrl(Url $url): bool
    {
        $this->cacheService->forget($url);
        $deleted = $this->urlRepository->delete($url);

        Log::info('Short URL deleted', ['id' => $url->id]);

        return $deleted;
    }

    /**
     * Resolve destination URL from short code or custom alias.
     * High performance path: Redis Cache Hit -> Fallback DB -> Cache Set.
     *
     * @throws UrlNotFoundException
     * @throws UrlExpiredException
     * @throws UrlDisabledException
     */
    public function resolveDestination(string $code): array
    {
        // 1. Try Redis cache first
        $cached = $this->cacheService->get($code);
        if ($cached !== null) {
            $this->validateAvailability(
                $cached['is_active'],
                $cached['expires_at'] ? new \DateTimeImmutable($cached['expires_at']) : null
            );

            return [
                'id' => (int) $cached['id'],
                'original_url' => (string) $cached['original_url'],
                'short_code' => (string) $cached['short_code'],
                'is_cached' => true,
            ];
        }

        // 2. Cache Miss: Query Database
        $url = $this->urlRepository->findByCode($code);
        if (! $url) {
            throw new UrlNotFoundException("No URL found matching '{$code}'.");
        }

        // 3. Validate status & expiration
        $this->validateAvailability((bool) $url->is_active, $url->expires_at);

        // 4. Populate Redis cache for future requests
        $this->cacheService->set($url);

        return [
            'id' => (int) $url->id,
            'original_url' => (string) $url->original_url,
            'short_code' => (string) $url->short_code,
            'is_cached' => false,
        ];
    }

    /**
     * Get paginated URLs for a user.
     */
    public function getUserUrls(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->urlRepository->getPaginatedForUser($userId, $perPage);
    }

    /**
     * Get all paginated URLs (admin).
     */
    public function getAllUrls(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return $this->urlRepository->getPaginatedAll($perPage, $filters);
    }

    /**
     * Validate whether a URL is active and unexpired.
     */
    private function validateAvailability(bool $isActive, ?\DateTimeInterface $expiresAt): void
    {
        if (! $isActive) {
            throw new UrlDisabledException('This short URL has been disabled or deactivated.');
        }

        if ($expiresAt !== null) {
            $now = new \DateTimeImmutable('now', $expiresAt->getTimezone());
            if ($expiresAt < $now) {
                throw new UrlExpiredException('This short URL has expired.');
            }
        }
    }

    /**
     * Generate a unique random short code with collision handling.
     */
    private function generateUniqueShortCode(int $length = 6): string
    {
        for ($i = 0; $i < self::MAX_COLLISION_RETRIES; $i++) {
            $code = $this->codeGenerator->generate($length);

            if (! $this->urlRepository->codeExists($code)) {
                return $code;
            }
        }

        // If high collision rate, incrementally increase code length
        return $this->generateUniqueShortCode($length + 1);
    }
}
