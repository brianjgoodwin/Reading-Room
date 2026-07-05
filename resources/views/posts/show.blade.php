<x-app-layout>
    <div class="max-w-3xl mx-auto px-8 py-10">

        {{-- Back to book --}}
        <a href="{{ route('shelf.show', $post->shelf) }}"
           class="text-sm text-ink-faint hover:text-ink font-serif border-b border-dotted border-ink-faint hover:border-ink transition-colors">
            &larr; {{ $post->shelf->book->title }}
        </a>

        {{-- Header --}}
        <div class="flex items-start gap-5 mt-6 mb-8">
            <div class="flex-shrink-0 w-16">
                @if ($post->shelf->book->display_cover_url)
                    <img src="{{ $post->shelf->book->display_cover_url }}"
                         alt="Cover of {{ $post->shelf->book->title }}"
                         class="w-16 shadow-sm">
                @else
                    <div class="w-16 h-24 bg-parchment-200"></div>
                @endif
            </div>
            <div class="flex-1">
                <p class="text-xs uppercase tracking-widest text-ink-faint font-serif mb-1">
                    {{ $post->created_at->format('F j, Y') }}
                    @if ($post->is_private)
                        &middot; <span class="italic normal-case">private</span>
                    @endif
                </p>
                <h1 class="text-xl font-serif font-semibold text-ink leading-snug">
                    {{ $post->shelf->book->title }}
                </h1>
                <p class="text-sm text-ink-faint font-serif mt-0.5">{{ $post->shelf->book->author }}</p>

                @if ($post->rating)
                    <p class="text-amber-700 mt-2 tracking-wide">
                        @for ($i = 1; $i <= 5; $i++)
                            {{ $i <= $post->rating ? '★' : '☆' }}
                        @endfor
                    </p>
                @endif
            </div>
        </div>

        {{-- Rendered Markdown --}}
        {{-- Safe: CommonMark is configured with html_input=strip and allow_unsafe_links=false --}}
        <div class="prose prose-reading max-w-none">
            {!! $rendered !!}
        </div>

        {{-- Tags --}}
        @if ($post->tags->isNotEmpty())
            <div class="flex flex-wrap gap-2 mt-8 pt-6 border-t border-parchment-300">
                @foreach ($post->tags as $tag)
                    <a href="{{ route('posts.index', ['tag' => $tag->slug]) }}"
                       class="text-xs font-serif text-ink-faint border border-parchment-300 px-2 py-0.5 hover:text-ink hover:border-ink-faint transition-colors">
                        {{ $tag->name }}
                    </a>
                @endforeach
            </div>
        @endif

        {{-- Actions --}}
        <div class="flex items-center gap-6 mt-8 pt-6 border-t border-parchment-300">
            <a href="{{ route('posts.edit', $post) }}"
               class="text-sm font-serif text-ink-faint hover:text-ink border-b border-dotted border-ink-faint hover:border-ink transition-colors">
                Edit entry
            </a>
            <a href="{{ route('posts.create', $post->shelf) }}"
               class="text-sm font-serif text-ink-faint hover:text-ink border-b border-dotted border-ink-faint hover:border-ink transition-colors">
                + New entry for this book
            </a>
        </div>

    </div>
</x-app-layout>
