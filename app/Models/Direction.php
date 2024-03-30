<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $from
 * @property int $to
 * @property int $city
 * @property int $usage
 * @property bool $enabled
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property Collection $executionHistory
 */
class Direction extends Model
{
    use HasFactory;

    protected $table = 'directions';

    protected $casts = [
        'enabled' => 'boolean',
    ];

    // TODO: cronjob to disable unused directions(1+ days)

    public function uid(): string
    {
        return self::buildUid($this->from, $this->to, $this->city);
    }

    public static function buildUid(int $from, int $to, int $city): string
    {
        return $from . ':' . $to . ':' . $city;
    }

    public function executionHistory(): HasMany
    {
        return $this->hasMany(ExecutionHistoryItem::class);
    }
}
