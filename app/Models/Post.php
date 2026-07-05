<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Post extends Model
{
    protected $fillable = [
        'shelf_id',
        'content',
        'rating',
        'is_private',
    ];

    protected $casts = [
        'is_private' => 'boolean',
        'rating' => 'integer',
    ];

    public function shelf(): BelongsTo
    {
        return $this->belongsTo(Shelf::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->whereHas('shelf', fn ($q) => $q->where('user_id', $userId));
    }

    public function scopePublic(Builder $query): Builder
    {
        return $query->where('is_private', false);
    }
}
