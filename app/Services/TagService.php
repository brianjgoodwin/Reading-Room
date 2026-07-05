<?php

namespace App\Services;

use App\Models\Tag;
use Illuminate\Support\Str;

class TagService
{
    /**
     * Parse a comma-separated tag string, normalize each tag, get or create
     * Tag records for this user, and return an array of Tag IDs.
     *
     * Used to sync tags onto a shelf entry or post via ->tags()->sync($ids).
     */
    public function syncFromString(string $tagInput, int $userId): array
    {
        $names = $this->parseTagString($tagInput);

        return collect($names)->map(function (string $name) use ($userId) {
            $slug = Str::slug($name);

            return Tag::firstOrCreate(
                ['user_id' => $userId, 'slug' => $slug],
                ['name' => $name]
            )->id;
        })->all();
    }

    /**
     * Parse a comma-separated string into a clean array of unique tag names.
     * "Fiction, sci-fi, fiction" → ['Fiction', 'sci-fi']
     * Deduplication is slug-based so "Fiction" and "fiction" are the same tag.
     */
    public function parseTagString(string $input): array
    {
        return collect(explode(',', $input))
            ->map(fn ($t) => trim($t))
            ->filter()
            ->unique(fn ($t) => Str::slug($t))
            ->values()
            ->all();
    }

    /**
     * Convert a collection of Tag models to a comma-separated string
     * for pre-populating a form input field.
     */
    public function tagsToString(iterable $tags): string
    {
        return collect($tags)->pluck('name')->implode(', ');
    }
}
