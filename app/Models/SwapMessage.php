<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SwapMessage extends Model
{
    protected $fillable = [
        'swap_id',
        'user_id',
        'body',
    ];

    public function swap(): BelongsTo
    {
        return $this->belongsTo(Swap::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
