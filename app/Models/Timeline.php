<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;

/**
 * App\Models\Timeline
 *
 * @property int $id
 * @property float $seconds
 * @property bool $is_paused
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Message|null $message
 * @method static \Illuminate\Database\Eloquent\Builder|Timeline newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Timeline newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Timeline query()
 * @method static \Illuminate\Database\Eloquent\Builder|Timeline whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Timeline whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Timeline whereIsPaused($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Timeline whereSeconds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Timeline whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Timeline extends Model
{
    use HasFactory;

    protected $fillable = [
        'seconds',
        'is_paused',
    ];

    protected $casts = [
        'seconds' => 'float',
        'is_paused' => 'boolean',
    ];

    public function message(): HasOne
    {
        return $this->hasOne(Message::class);
    }
}
