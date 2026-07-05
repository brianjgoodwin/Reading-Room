<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

class OpenLibraryService
{
    private const SEARCH_URL = 'https://openlibrary.org/search.json';

    private const COVER_URL = 'https://covers.openlibrary.org/b/olid/%s-M.jpg';

    private const TIMEOUT = 10;

    /**
     * Search Open Library by title/author/keyword.
     * Returns an array of normalized result items, or [] on failure.
     *
     * Each item: ['title', 'author', 'isbn', 'open_library_id', 'cover_url']
     */
    public function search(string $query, int $limit = 20): array
    {
        try {
            $response = Http::timeout(self::TIMEOUT)
                ->withHeaders(['User-Agent' => 'ReadingRoom/1.0'])
                ->get(self::SEARCH_URL, [
                    'q' => $query,
                    'limit' => $limit,
                    'fields' => 'key,title,author_name,isbn,cover_edition_key',
                ]);

            if (! $response->successful()) {
                return [];
            }

            return collect($response->json('docs', []))
                ->map(fn ($doc) => $this->normalizeResult($doc))
                ->filter()
                ->values()
                ->all();

        } catch (\Throwable) {
            return [];
        }
    }

    /**
     * Download and store a cover image for the given Open Library edition ID.
     * Resizes to 200px wide and saves to storage/public/covers/.
     * Returns the storage-relative path (e.g. "covers/OL123M.jpg"), or null on failure.
     */
    public function downloadCover(string $openLibraryId): ?string
    {
        $path = "covers/{$openLibraryId}.jpg";

        // Already downloaded — don't re-fetch
        if (Storage::disk('public')->exists($path)) {
            return $path;
        }

        try {
            $url = sprintf(self::COVER_URL, $openLibraryId);
            $response = Http::timeout(self::TIMEOUT)
                ->withHeaders(['User-Agent' => 'ReadingRoom/1.0'])
                ->get($url);

            if (! $response->successful()) {
                return null;
            }

            $image = Image::read($response->body())->scale(width: 200);
            Storage::disk('public')->put($path, $image->toJpeg());

            return $path;

        } catch (\Throwable) {
            return null;
        }
    }

    private function normalizeResult(array $doc): ?array
    {
        if (empty($doc['title'])) {
            return null;
        }

        $openLibraryId = $doc['cover_edition_key'] ?? null;

        return [
            'title' => $doc['title'],
            'author' => isset($doc['author_name'])
                ? implode(', ', $doc['author_name'])
                : 'Unknown',
            'isbn' => $doc['isbn'][0] ?? null,
            'open_library_id' => $openLibraryId,
            'cover_url' => $openLibraryId
                ? sprintf(self::COVER_URL, $openLibraryId)
                : null,
        ];
    }
}
