<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    protected $fillable = ['user_id', 'name', 'slug'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function shelves(): BelongsToMany
    {
        return $this->belongsToMany(Shelf::class);
    }

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class);
    }
}
