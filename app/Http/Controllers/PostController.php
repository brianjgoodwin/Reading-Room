<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Shelf;
use App\Services\TagService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use League\CommonMark\CommonMarkConverter;

class PostController extends Controller
{
    private CommonMarkConverter $markdown;

    public function __construct()
    {
        $this->markdown = new CommonMarkConverter([
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);
    }

    public function index(Request $request): View
    {
        $tagSlug = $request->query('tag');

        $posts = Post::forUser(auth()->id())
            ->with(['shelf.book', 'tags'])
            ->when($tagSlug, function ($q) use ($tagSlug) {
                $q->whereHas('tags', fn ($t) => $t->where('slug', $tagSlug)
                    ->where('user_id', auth()->id()));
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $activeTag = $tagSlug
            ? auth()->user()->tags()->where('slug', $tagSlug)->first()
            : null;

        return view('posts.index', compact('posts', 'activeTag'));
    }

    public function create(Shelf $shelf): View
    {
        abort_unless($shelf->user_id === auth()->id(), 403);

        $shelf->load('book');

        return view('posts.create', compact('shelf'));
    }

    public function store(Request $request, Shelf $shelf): RedirectResponse
    {
        abort_unless($shelf->user_id === auth()->id(), 403);

        $data = $request->validate([
            'content' => ['required', 'string', 'max:100000'],
            'rating' => ['nullable', 'integer', 'min:0', 'max:5'],
            'is_private' => ['boolean'],
            'tags' => ['nullable', 'string', 'max:500'],
        ]);

        $post = $shelf->posts()->create([
            'content' => $data['content'],
            'rating' => $data['rating'] ?? null,
            'is_private' => $data['is_private'] ?? false,
        ]);

        $tagIds = (new TagService)->syncFromString($data['tags'] ?? '', auth()->id());
        $post->tags()->sync($tagIds);

        return redirect()->route('posts.show', $post)
            ->with('success', 'Journal entry saved.');
    }

    public function show(Post $post): View
    {
        abort_unless($post->shelf->user_id === auth()->id(), 403);

        $post->load(['shelf.book', 'tags']);
        $rendered = $this->markdown->convert($post->content)->getContent();

        return view('posts.show', compact('post', 'rendered'));
    }

    public function edit(Post $post): View
    {
        abort_unless($post->shelf->user_id === auth()->id(), 403);

        $post->load(['shelf.book', 'tags']);
        $tagString = (new TagService)->tagsToString($post->tags);

        return view('posts.edit', compact('post', 'tagString'));
    }

    public function update(Request $request, Post $post): RedirectResponse
    {
        abort_unless($post->shelf->user_id === auth()->id(), 403);

        $data = $request->validate([
            'content' => ['required', 'string', 'max:100000'],
            'rating' => ['nullable', 'integer', 'min:0', 'max:5'],
            'is_private' => ['boolean'],
            'tags' => ['nullable', 'string', 'max:500'],
        ]);

        $post->update([
            'content' => $data['content'],
            'rating' => $data['rating'] ?? null,
            'is_private' => $data['is_private'] ?? false,
        ]);

        $tagIds = (new TagService)->syncFromString($data['tags'] ?? '', auth()->id());
        $post->tags()->sync($tagIds);

        return redirect()->route('posts.show', $post)
            ->with('success', 'Entry updated.');
    }

    public function destroy(Post $post): RedirectResponse
    {
        abort_unless($post->shelf->user_id === auth()->id(), 403);

        $shelf = $post->shelf;
        $post->delete();

        return redirect()->route('shelf.show', $shelf)
            ->with('success', 'Entry deleted.');
    }
}
