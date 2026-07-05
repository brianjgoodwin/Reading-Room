<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\View\View;

class TagController extends Controller
{
    public function index(): View
    {
        $tags = auth()->user()
            ->tags()
            ->withCount(['shelves', 'posts'])
            ->orderBy('name')
            ->get()
            ->map(function ($tag) {
                $tag->total_count = $tag->shelves_count + $tag->posts_count;

                return $tag;
            });

        $max = $tags->max('total_count') ?: 1;

        return view('tags.index', compact('tags', 'max'));
    }

    public function show(Tag $tag): View
    {
        abort_unless($tag->user_id === auth()->id(), 403);

        $shelves = $tag->shelves()
            ->where('user_id', auth()->id())
            ->with('book')
            ->orderBy('created_at', 'desc')
            ->get();

        $posts = $tag->posts()
            ->forUser(auth()->id())
            ->with(['shelf.book'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('tags.show', compact('tag', 'shelves', 'posts'));
    }
}
