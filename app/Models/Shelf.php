<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shelf extends Model
{
    protected $fillable = [
        'user_id',
        'book_id',
        'status',
        'date_started',
        'date_finished',
    ];

    protected $casts = [
        'date_started' => 'date',
        'date_finished' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    // Filter to only the current user's shelf entries
    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    // Filter by reading status
    public function scopeWithStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    public static function statusLabel(string $status): string
    {
        return match ($status) {
            'to_read' => 'To Read',
            'reading' => 'Reading',
            'read' => 'Read',
            default => $status,
        };
    }
}
