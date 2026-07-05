<x-app-layout>
    <div class="max-w-3xl mx-auto px-8 py-10">

        <div class="flex items-baseline justify-between mb-8">
            <h1 class="text-2xl font-serif font-semibold text-ink">
                Journal
                @if ($activeTag)
                    <span class="text-ink-faint font-normal text-lg">&mdash; {{ $activeTag->name }}</span>
                @endif
            </h1>
            @if ($activeTag)
                <a href="{{ route('posts.index') }}"
                   class="text-sm font-serif text-ink-faint hover:text-ink border-b border-dotted border-ink-faint hover:border-ink transition-colors">
                    Clear filter
                </a>
            @endif
        </div>

        @if ($posts->isEmpty())
            <p class="text-ink-faint font-serif italic">
                @if ($activeTag)
                    No entries tagged &ldquo;{{ $activeTag->name }}&rdquo;.
                @else
                    No journal entries yet. Open a book on your shelf to write your first entry.
                @endif
            </p>
        @else
            <div class="space-y-6">
                @foreach ($posts as $post)
                    <article class="border border-parchment-300 bg-parchment-50 p-5">

                        {{-- Book + date header --}}
                        <div class="flex items-start gap-4 mb-3">
                            <div class="flex-shrink-0 w-8">
                                @if ($post->shelf->book->display_cover_url)
                                    <img src="{{ $post->shelf->book->display_cover_url }}"
                                         alt="" class="w-8 shadow-sm">
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <a href="{{ route('shelf.show', $post->shelf) }}"
                                   class="text-xs font-serif text-ink-faint hover:text-ink border-b border-dotted border-transparent hover:border-ink-faint transition-colors">
                                    {{ $post->shelf->book->title }}
                                </a>
                                <div class="flex items-center gap-3 mt-0.5">
                                    <span class="text-xs text-ink-faint font-serif">
                                        {{ $post->created_at->format('M j, Y') }}
                                    </span>
                                    @if ($post->rating)
                                        <span class="text-amber-700 text-xs tracking-wide">
                                            @for ($i = 1; $i <= 5; $i++)
                                                {{ $i <= $post->rating ? '★' : '☆' }}
                                            @endfor
                                        </span>
                                    @endif
                                    @if ($post->is_private)
                                        <span class="text-xs text-ink-faint font-serif italic">private</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Content preview --}}
                        <a href="{{ route('posts.show', $post) }}" class="block group">
                            <p class="font-serif text-ink text-sm leading-relaxed line-clamp-3 group-hover:underline decoration-dotted underline-offset-2">
                                {{ Str::limit(strip_tags($post->content), 240) }}
                            </p>
                        </a>

                        {{-- Tags --}}
                        @if ($post->tags->isNotEmpty())
                            <div class="flex flex-wrap gap-2 mt-3">
                                @foreach ($post->tags as $tag)
                                    <a href="{{ route('posts.index', ['tag' => $tag->slug]) }}"
                                       class="text-xs font-serif text-ink-faint border border-parchment-300 px-2 py-0.5 hover:text-ink hover:border-ink-faint transition-colors">
                                        {{ $tag->name }}
                                    </a>
                                @endforeach
                            </div>
                        @endif

                    </article>
                @endforeach
            </div>
        @endif

    </div>
</x-app-layout>
