<?php

namespace App\Http\Controllers;

use App\Models\Shelf;
use App\Services\TagService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShelfController extends Controller
{
    public function index(Request $request): View
    {
        // Normalize: absent or unrecognized status = null (show all)
        $validStatuses = ['to_read', 'reading', 'read'];
        $status = in_array($request->query('status'), $validStatuses)
            ? $request->query('status')
            : null;

        $shelves = auth()->user()
            ->shelves()
            ->with('book')
            ->withCount('posts')
            ->when($status, fn ($q) => $q->withStatus($status))
            ->join('books', 'books.id', '=', 'shelves.book_id')
            ->orderBy('books.title')
            ->select('shelves.*')
            ->get();

        $counts = [
            'all' => auth()->user()->shelves()->count(),
            'to_read' => auth()->user()->shelves()->withStatus('to_read')->count(),
            'reading' => auth()->user()->shelves()->withStatus('reading')->count(),
            'read' => auth()->user()->shelves()->withStatus('read')->count(),
        ];

        return view('shelf.index', compact('shelves', 'counts', 'status'));
    }

    public function show(Shelf $shelf): View
    {
        abort_unless($shelf->user_id === auth()->id(), 403);

        $shelf->load(['book', 'tags', 'posts' => fn ($q) => $q->orderBy('created_at', 'desc')]);

        $tagString = (new TagService)->tagsToString($shelf->tags);

        return view('shelf.show', compact('shelf', 'tagString'));
    }

    public function update(Request $request, Shelf $shelf): RedirectResponse
    {
        abort_unless($shelf->user_id === auth()->id(), 403);

        $data = $request->validate([
            'status' => ['required', 'in:to_read,reading,read'],
            'date_started' => ['nullable', 'date'],
            'date_finished' => ['nullable', 'date', 'after_or_equal:date_started'],
            'tags' => ['nullable', 'string', 'max:500'],
        ]);

        $shelf->update($data);

        $tagIds = (new TagService)->syncFromString($data['tags'] ?? '', auth()->id());
        $shelf->tags()->sync($tagIds);

        return redirect()->route('shelf.show', $shelf)
            ->with('success', 'Shelf entry updated.');
    }

    public function destroy(Shelf $shelf): RedirectResponse
    {
        abort_unless($shelf->user_id === auth()->id(), 403);

        $shelf->delete();

        return redirect()->route('shelf.index')
            ->with('success', 'Book removed from your shelf.');
    }
}
