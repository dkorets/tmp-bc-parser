<?php

declare(strict_types=1);

namespace App\Models;

use App\Services\Dto\TableRow;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $direction_id
 * @property int $response_time_ms
 * @property int $response_status
 * @property TableRow[] $table_rows
 * @property string $used_proxy
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property Direction $direction
 */
class ExecutionHistoryItem extends Model
{
    protected $table = 'execution_history';

    protected $casts = [
        'table_rows' => 'json',
    ];

    public function direction(): BelongsTo
    {
        return $this->belongsTo(Direction::class);
    }

    public function getTableRowsAttribute()
    {
        // todo
    }
}
