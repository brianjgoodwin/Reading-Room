<?php

namespace App\Services;

use App\Models\Book;
use App\Models\Post;
use App\Models\Shelf;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class GoodreadsImporter
{
    // Maps Goodreads exclusive shelf names to our status enum
    private const SHELF_MAP = [
        'read' => 'read',
        'currently-reading' => 'reading',
        'to-read' => 'to_read',
    ];

    // Date formats Goodreads uses in CSV exports
    private const DATE_FORMATS = [
        'Y/m/d',
        'Y-m-d',
        'm/d/Y',
        'M d, Y',
    ];

    /**
     * Import books from a Goodreads CSV export.
     *
     * @param  string  $csvPath  Absolute path to the uploaded CSV file
     * @param  int  $userId  The authenticated user's ID
     * @param  bool  $importReviews  Whether to create journal posts from Goodreads reviews
     * @return array ['imported' => int, 'skipped' => int, 'errors' => string[]]
     */
    public function import(string $csvPath, int $userId, bool $importReviews = false): array
    {
        $rows = $this->parseCsv($csvPath);
        $results = ['imported' => 0, 'skipped' => 0, 'errors' => []];

        foreach ($rows as $index => $row) {
            try {
                DB::transaction(function () use ($row, $userId, $importReviews, &$results) {
                    $book = $this->findOrCreateBook($row);
                    $shelf = $this->findOrCreateShelf($book, $row, $userId);

                    if ($importReviews && ! empty(trim($row['My Review'] ?? ''))) {
                        $this->createPostFromReview($shelf, $row);
                    }

                    $results['imported']++;
                });
            } catch (\Throwable $e) {
                $results['skipped']++;
                $title = $row['Title'] ?? "Row {$index}";
                $results['errors'][] = "\"{$title}\": {$e->getMessage()}";
            }
        }

        return $results;
    }

    /**
     * Parse a Goodreads CSV file into an array of associative rows.
     * Tries UTF-8 first, falls back to Latin-1 for older exports.
     */
    private function parseCsv(string $path): array
    {
        $rows = [];
        $handle = fopen($path, 'r');

        if ($handle === false) {
            throw new \RuntimeException('Could not open CSV file.');
        }

        $headers = null;

        while (($line = fgetcsv($handle)) !== false) {
            if ($headers === null) {
                // First row is the header
                $headers = array_map('trim', $line);

                continue;
            }

            // Pad short rows to match header count (Goodreads CSV can be inconsistent)
            while (count($line) < count($headers)) {
                $line[] = '';
            }

            $row = array_combine($headers, array_slice($line, 0, count($headers)));

            // Convert Latin-1 encoded text if needed
            foreach ($row as $key => $value) {
                if (! mb_check_encoding($value, 'UTF-8')) {
                    $row[$key] = mb_convert_encoding($value, 'UTF-8', 'Latin-1');
                }
            }

            $rows[] = $row;
        }

        fclose($handle);

        return $rows;
    }

    /**
     * Find an existing book by ISBN, then title+author. Creates a new one if not found.
     */
    private function findOrCreateBook(array $row): Book
    {
        $isbn = $this->normalizeIsbn($row['ISBN13'] ?? $row['ISBN'] ?? '');
        $title = trim($row['Title'] ?? '');
        $author = trim($row['Author'] ?? $row['Authors'] ?? 'Unknown');

        if (empty($title)) {
            throw new \InvalidArgumentException('Row has no title.');
        }

        // Try ISBN first (most reliable deduplication)
        if ($isbn) {
            $book = Book::where('isbn', $isbn)->first();
            if ($book) {
                return $book;
            }
        }

        // Try title + author match
        $book = Book::where('title', $title)
            ->where('author', $author)
            ->first();

        if ($book) {
            return $book;
        }

        return Book::create([
            'title' => $title,
            'author' => $author,
            'isbn' => $isbn ?: null,
        ]);
    }

    /**
     * Find the user's existing shelf entry for this book, or create one.
     * If an entry already exists, we leave it untouched (don't overwrite
     * user's own status or dates with Goodreads data).
     */
    private function findOrCreateShelf(Book $book, array $row, int $userId): Shelf
    {
        $existing = Shelf::where('user_id', $userId)
            ->where('book_id', $book->id)
            ->first();

        if ($existing) {
            return $existing;
        }

        $goodreadsShelf = trim($row['Exclusive Shelf'] ?? 'to-read');
        $status = self::SHELF_MAP[$goodreadsShelf] ?? 'to_read';
        $dateStarted = $this->parseDate($row['Date Started'] ?? '');
        $dateFinished = $this->parseDate($row['Date Read'] ?? '');

        return Shelf::create([
            'user_id' => $userId,
            'book_id' => $book->id,
            'status' => $status,
            'date_started' => $dateStarted?->toDateString(),
            'date_finished' => $dateFinished?->toDateString(),
        ]);
    }

    /**
     * Create a journal post from a Goodreads review text.
     * Maps Goodreads 1-5 rating to our rating field (import as-is).
     * Posts created from import are marked private by default.
     * Only creates one post per shelf entry — skips if one already exists.
     */
    private function createPostFromReview(Shelf $shelf, array $row): void
    {
        if ($shelf->posts()->exists()) {
            return;
        }

        $reviewText = trim($row['My Review'] ?? '');
        $rating = (int) ($row['My Rating'] ?? 0);

        $shelf->posts()->create([
            'content' => $reviewText,
            'rating' => $rating > 0 ? $rating : null,
            'is_private' => true,
        ]);
    }

    /**
     * Parse a date string trying multiple formats. Returns Carbon or null.
     */
    private function parseDate(string $dateStr): ?Carbon
    {
        $dateStr = trim($dateStr);

        if (empty($dateStr) || $dateStr === '0000-00-00' || $dateStr === 'not set') {
            return null;
        }

        foreach (self::DATE_FORMATS as $format) {
            try {
                return Carbon::createFromFormat($format, $dateStr);
            } catch (\Throwable) {
                continue;
            }
        }

        return null;
    }

    /**
     * Strip Goodreads' ISBN formatting: ="0060934344" → 0060934344
     */
    private function normalizeIsbn(string $isbn): string
    {
        return preg_replace('/[^0-9X]/', '', $isbn);
    }
}
