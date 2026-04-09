<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SwapRequest extends Model
{
    protected $fillable = [
        'requester_id',
        'receiver_id',
        'skill_to_learn',
        'skill_to_offer',
        'message',
        'proposed_schedule',
        'preferred_format',
        'status',
        'counter_message',
        'counter_schedule',
        'counter_format',
    ];

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function swap(): HasOne
    {
        return $this->hasOne(Swap::class);
    }
}
