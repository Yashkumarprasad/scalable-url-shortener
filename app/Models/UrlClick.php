<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\DeviceType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $url_id
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property string|null $referer
 * @property DeviceType $device_type
 * @property string|null $country_code
 * @property string|null $country_name
 * @property string|null $city
 * @property Carbon $clicked_at
 * @property Carbon|null $created_at
 * @property-read Url $url
 */
class UrlClick extends Model
{
    use HasFactory;

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'url_id',
        'ip_address',
        'user_agent',
        'referer',
        'device_type',
        'country_code',
        'country_name',
        'city',
        'clicked_at',
        'created_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'clicked_at' => 'datetime',
        'created_at' => 'datetime',
        'device_type' => DeviceType::class,
        'url_id' => 'integer',
    ];

    /**
     * Get the URL that this click belongs to.
     */
    public function url(): BelongsTo
    {
        return $this->belongsTo(Url::class);
    }
}
