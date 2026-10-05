<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int|null $user_id
 * @property string $original_url
 * @property string $short_code
 * @property string|null $custom_alias
 * @property string|null $title
 * @property Carbon|null $expires_at
 * @property bool $is_active
 * @property int $click_count
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read string $short_url
 * @property-read User|null $user
 */
class Url extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'original_url',
        'short_code',
        'custom_alias',
        'title',
        'expires_at',
        'is_active',
        'click_count',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'expires_at' => 'datetime',
        'click_count' => 'integer',
        'user_id' => 'integer',
    ];

    /**
     * Get the user that owns the URL.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the click analytics for the URL.
     */
    public function clicks(): HasMany
    {
        return $this->hasMany(UrlClick::class);
    }

    /**
     * Scope a query to only include active URLs.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include unexpired URLs.
     */
    public function scopeNotExpired(Builder $query): Builder
    {
        return $query->where(function (Builder $q) {
            $q->whereNull('expires_at')
                ->orWhere('expires_at', '>', Carbon::now());
        });
    }

    /**
     * Determine if the URL is expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    /**
     * Check whether the URL can currently be redirected.
     */
    public function isAvailable(): bool
    {
        return $this->is_active && ! $this->isExpired();
    }

    /**
     * Get the public short URL string.
     */
    public function getShortUrlAttribute(): string
    {
        $code = $this->custom_alias ?: $this->short_code;
        return url('/' . $code);
    }
}
