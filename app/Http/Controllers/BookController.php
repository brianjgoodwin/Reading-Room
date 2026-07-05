<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Shelf;
use App\Services\OpenLibraryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookController extends Controller
{
    public function search(Request $request): View
    {
        $query = $request->query('q', '');
        $results = [];

        if (strlen(trim($query)) >= 2) {
            $results = (new OpenLibraryService)->search($query);
        }

        return view('books.search', compact('query', 'results'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:500'],
            'author' => ['required', 'string', 'max:500'],
            'isbn' => ['nullable', 'string', 'max:20'],
            'open_library_id' => ['nullable', 'string', 'max:100'],
        ]);

        // Deduplicate: find existing book by open_library_id, then isbn, then create
        $book = null;

        if (! empty($data['open_library_id'])) {
            $book = Book::where('open_library_id', $data['open_library_id'])->first();
        }

        if (! $book && ! empty($data['isbn'])) {
            $book = Book::where('isbn', $data['isbn'])->first();
        }

        if (! $book) {
            $book = Book::create($data);
        }

        // Download cover if we have an Open Library ID and don't have one stored yet
        if ($book->open_library_id && ! $book->cover_path) {
            $coverPath = (new OpenLibraryService)->downloadCover($book->open_library_id);
            if ($coverPath) {
                $book->update(['cover_path' => $coverPath]);
            }
        }

        // Already on shelf — redirect rather than creating a duplicate
        $existing = Shelf::where('user_id', auth()->id())
            ->where('book_id', $book->id)
            ->first();

        if ($existing) {
            return redirect()->route('shelf.show', $existing)
                ->with('success', '"'.$book->title.'" is already on your shelf.');
        }

        $shelf = Shelf::create([
            'user_id' => auth()->id(),
            'book_id' => $book->id,
            'status' => 'to_read',
        ]);

        return redirect()->route('shelf.show', $shelf)
            ->with('success', '"'.$book->title.'" added to your shelf.');
    }
}
