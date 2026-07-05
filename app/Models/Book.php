<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Book extends Model
{
    protected $fillable = [
        'title',
        'author',
        'isbn',
        'open_library_id',
        'cover_path',
    ];

    public function shelves(): HasMany
    {
        return $this->hasMany(Shelf::class);
    }

    // Returns a URL to the locally stored cover, or null if not downloaded yet
    public function getCoverUrlAttribute(): ?string
    {
        return $this->cover_path ? Storage::disk('public')->url($this->cover_path) : null;
    }

    // Returns an Open Library cover URL as a fallback for display before download
    public function getOpenLibraryCoverUrlAttribute(): ?string
    {
        return $this->open_library_id
            ? "https://covers.openlibrary.org/b/olid/{$this->open_library_id}-M.jpg"
            : null;
    }

    // The best available cover URL: local first, Open Library fallback
    public function getDisplayCoverUrlAttribute(): ?string
    {
        return $this->cover_url ?? $this->open_library_cover_url;
    }
}
