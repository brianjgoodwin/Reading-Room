<x-app-layout>
    <div class="max-w-3xl mx-auto px-8 py-10">

        <a href="{{ route('tags.index') }}"
           class="text-sm text-ink-faint hover:text-ink font-serif border-b border-dotted border-ink-faint hover:border-ink transition-colors">
            &larr; Tags
        </a>

        <h1 class="text-2xl font-serif font-semibold text-ink mt-6 mb-8">
            {{ $tag->name }}
        </h1>

        {{-- Books with this tag --}}
        @if ($shelves->isNotEmpty())
            <section class="mb-10">
                <h2 class="text-xs uppercase tracking-widest text-ink-faint font-serif mb-4">
                    Books ({{ $shelves->count() }})
                </h2>
                <div class="space-y-3">
                    @foreach ($shelves as $shelf)
                        <a href="{{ route('shelf.show', $shelf) }}"
                           class="flex gap-4 items-center border border-parchment-300 bg-parchment-50 p-3 hover:border-ink-faint transition-colors group">
                            <div class="flex-shrink-0 w-10">
                                @if ($shelf->book->display_cover_url)
                                    <img src="{{ $shelf->book->display_cover_url }}"
                                         alt="" class="w-10 shadow-sm">
                                @else
                                    <div class="w-10 h-14 bg-parchment-200"></div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-serif font-semibold text-ink text-sm group-hover:underline decoration-dotted leading-snug">
                                    {{ $shelf->book->title }}
                                </p>
                                <p class="text-xs text-ink-faint">{{ $shelf->book->author }}</p>
                            </div>
                            <span class="text-xs text-ink-faint flex-shrink-0">
                                {{ \App\Models\Shelf::statusLabel($shelf->status) }}
                            </span>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Journal entries with this tag --}}
        @if ($posts->isNotEmpty())
            <section>
                <h2 class="text-xs uppercase tracking-widest text-ink-faint font-serif mb-4">
                    Journal entries ({{ $posts->count() }})
                </h2>
                <div class="space-y-3">
                    @foreach ($posts as $post)
                        <a href="{{ route('posts.show', $post) }}"
                           class="block border border-parchment-300 bg-parchment-50 p-4 hover:border-ink-faint transition-colors group">
                            <p class="text-xs text-ink-faint font-serif mb-1">
                                {{ $post->shelf->book->title }}
                                &middot; {{ $post->created_at->format('M j, Y') }}
                            </p>
                            <p class="text-sm font-serif text-ink line-clamp-2 group-hover:underline decoration-dotted">
                                {{ Str::limit(strip_tags($post->content), 140) }}
                            </p>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        @if ($shelves->isEmpty() && $posts->isEmpty())
            <p class="text-ink-faint font-serif italic">Nothing tagged with &ldquo;{{ $tag->name }}&rdquo; yet.</p>
        @endif

    </div>
</x-app-layout>
