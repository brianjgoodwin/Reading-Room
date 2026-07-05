<?php

namespace App\Services;

use App\Models\Post;
use App\Models\Shelf;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class StatsService
{
    /**
     * Compile all reading stats for the given user.
     * Returns an array consumed directly by the stats view.
     */
    public function compute(int $userId): array
    {
        return [
            'by_status' => $this->byStatus($userId),
            'monthly_pace' => $this->monthlyPace($userId),
            'top_authors' => $this->topAuthors($userId),
            'streak' => $this->readingStreak($userId),
            'avg_rating' => $this->averageRating($userId),
        ];
    }

    /**
     * Count shelf entries by status.
     * Returns ['to_read' => int, 'reading' => int, 'read' => int, 'total' => int]
     */
    private function byStatus(int $userId): array
    {
        $counts = Shelf::where('user_id', $userId)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return [
            'to_read' => $counts['to_read'] ?? 0,
            'reading' => $counts['reading'] ?? 0,
            'read' => $counts['read'] ?? 0,
            'total' => array_sum($counts),
        ];
    }

    /**
     * Books finished per month for the last 12 months.
     * Returns array of ['month' => 'Jul 2026', 'count' => int], oldest first.
     */
    private function monthlyPace(int $userId): array
    {
        $finished = Shelf::where('user_id', $userId)
            ->where('status', 'read')
            ->whereNotNull('date_finished')
            ->where('date_finished', '>=', now()->subMonths(11)->startOfMonth())
            ->select('date_finished')
            ->get()
            ->groupBy(fn ($shelf) => Carbon::parse($shelf->date_finished)->format('Y-m'));

        // Build a complete 12-month spine so months with 0 books appear
        $months = [];
        for ($i = 11; $i >= 0; $i--) {
            $key = now()->subMonths($i)->format('Y-m');
            $label = now()->subMonths($i)->format('M Y');
            $months[$key] = ['month' => $label, 'count' => $finished->get($key)?->count() ?? 0];
        }

        return array_values($months);
    }

    /**
     * Top 5 authors by number of books on shelf (any status).
     * Returns array of ['author' => string, 'count' => int], descending.
     */
    private function topAuthors(int $userId): array
    {
        return Shelf::where('shelves.user_id', $userId)
            ->join('books', 'books.id', '=', 'shelves.book_id')
            ->select('books.author', DB::raw('count(*) as count'))
            ->groupBy('books.author')
            ->orderByDesc('count')
            ->limit(5)
            ->get()
            ->map(fn ($row) => ['author' => $row->author, 'count' => $row->count])
            ->all();
    }

    /**
     * Current reading streak: the number of consecutive calendar months
     * (ending with the current month) in which at least one book was finished.
     *
     * Days-based streaks require precise date data that users may not have entered.
     * Month-based streaks are more forgiving and still meaningful.
     * Returns 0 if the current month has no finished books.
     */
    private function readingStreak(int $userId): int
    {
        $finishedMonths = Shelf::where('user_id', $userId)
            ->where('status', 'read')
            ->whereNotNull('date_finished')
            ->orderByDesc('date_finished')
            ->pluck('date_finished')
            ->map(fn ($d) => Carbon::parse($d)->format('Y-m'))
            ->unique()
            ->values();

        if ($finishedMonths->isEmpty()) {
            return 0;
        }

        $streak = 0;
        $expected = now()->format('Y-m');

        foreach ($finishedMonths as $month) {
            if ($month === $expected) {
                $streak++;
                $expected = Carbon::parse($month)->subMonth()->format('Y-m');
            } else {
                break;
            }
        }

        return $streak;
    }

    /**
     * Average rating across all rated posts (rating not null, not 0).
     * Returns float rounded to 1 decimal, or null if no rated posts.
     */
    private function averageRating(int $userId): ?float
    {
        $avg = Post::forUser($userId)
            ->whereNotNull('rating')
            ->where('rating', '>', 0)
            ->avg('rating');

        return $avg !== null ? round((float) $avg, 1) : null;
    }
}
