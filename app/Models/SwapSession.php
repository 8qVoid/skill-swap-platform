<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SwapSession extends Model
{
    protected $fillable = [
        'swap_id',
        'scheduled_for',
        'meeting_link',
        'topic',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_for' => 'datetime',
        ];
    }

    public function swap(): BelongsTo
    {
        return $this->belongsTo(Swap::class);
    }
}
