<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Computed;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'name',
    'email',
    'password',
    'role',
    'profile_photo',
    'bio',
    'location',
    'timezone',
    'availability',
    'skill_level',
    'teach_skills',
    'learn_skills',
    'formats',
    'portfolio_links',
    'saved_users',
    'onboarding_completed',
    'is_verified',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'teach_skills' => 'array',
            'learn_skills' => 'array',
            'formats' => 'array',
            'portfolio_links' => 'array',
            'saved_users' => 'array',
            'onboarding_completed' => 'boolean',
            'is_verified' => 'boolean',
        ];
    }

    public function sentSwapRequests(): HasMany
    {
        return $this->hasMany(SwapRequest::class, 'requester_id');
    }

    public function receivedSwapRequests(): HasMany
    {
        return $this->hasMany(SwapRequest::class, 'receiver_id');
    }

    public function initiatedSwaps(): HasMany
    {
        return $this->hasMany(Swap::class, 'requester_id');
    }

    public function partnerSwaps(): HasMany
    {
        return $this->hasMany(Swap::class, 'receiver_id');
    }

    public function reviewsWritten(): HasMany
    {
        return $this->hasMany(Review::class, 'reviewer_id');
    }

    public function reviewsReceived(): HasMany
    {
        return $this->hasMany(Review::class, 'reviewee_id');
    }

    public function blockedUsers(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'blocked_users',
            'blocker_id',
            'blocked_user_id'
        )->withTimestamps();
    }

    public function blockedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'blocked_users',
            'blocked_user_id',
            'blocker_id'
        )->withTimestamps();
    }

    protected function savedUsers(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (is_array($value)) {
                    return $value;
                }

                if (is_string($value) && $value !== '') {
                    $decoded = json_decode($value, true);

                    return is_array($decoded) ? $decoded : [];
                }

                return [];
            },
        );
    }

    #[Computed]
    public function averageRating(): float
    {
        return round((float) $this->reviewsReceived()->avg('rating'), 1);
    }

    public function getProfilePhotoUrlAttribute(): string
    {
        $photo = $this->profile_photo;

        if (is_string($photo) && $photo !== '') {
            if (str_starts_with($photo, 'http://') || str_starts_with($photo, 'https://')) {
                return $photo;
            }

            return Storage::disk('public')->url($photo);
        }

        return 'https://ui-avatars.com/api/?name='.urlencode($this->name ?: 'Skill Swap').'&background=163a5f&color=ffffff&bold=true&size=256';
    }

    #[Computed]
    public function completedSwapCount(): int
    {
        return Swap::query()
            ->where('status', 'completed')
            ->where(fn ($query) => $query
                ->where('requester_id', $this->id)
                ->orWhere('receiver_id', $this->id))
            ->count();
    }
}
