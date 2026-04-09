<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Swap extends Model
{
    protected $fillable = [
        'swap_request_id',
        'requester_id',
        'receiver_id',
        'skill_to_learn',
        'skill_to_offer',
        'format',
        'status',
        'progress_notes',
        'progress_percent',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'completed_at' => 'datetime',
        ];
    }

    public function request(): BelongsTo
    {
        return $this->belongsTo(SwapRequest::class, 'swap_request_id');
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(SwapMessage::class)->latest();
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(SwapSession::class)->orderBy('scheduled_for');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }
}
