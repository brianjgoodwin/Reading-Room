<x-app-layout>
    <div class="max-w-3xl mx-auto px-8 py-10">

        <h1 class="text-2xl font-serif font-semibold text-ink mb-8">Tags</h1>

        @if ($tags->isEmpty())
            <p class="text-ink-faint font-serif italic">
                No tags yet. Add tags when updating a book's status or writing a journal entry.
            </p>
        @else
            {{-- Tag cloud --}}
            @php
                $sizes = ['text-sm', 'text-base', 'text-lg', 'text-xl', 'text-2xl'];
            @endphp

            <div class="leading-loose">
                @foreach ($tags as $tag)
                    @php
                        $idx  = $max > 1
                            ? (int) round(($tag->total_count / $max) * (count($sizes) - 1))
                            : 0;
                        $size = $sizes[$idx];
                    @endphp
                    <a href="{{ route('tags.show', $tag->slug) }}"
                       class="{{ $size }} font-serif text-ink-light hover:text-ink underline decoration-dotted underline-offset-2 mr-4">
                        {{ $tag->name }}
                        <span class="text-ink-faint text-xs no-underline">({{ $tag->total_count }})</span>
                    </a>
                @endforeach
            </div>

            {{-- Also as a sorted list --}}
            <div class="mt-10 border-t border-parchment-300 pt-8">
                <h2 class="text-xs uppercase tracking-widest text-ink-faint font-serif mb-4">All tags</h2>
                <div class="space-y-1">
                    @foreach ($tags as $tag)
                        <div class="flex items-baseline justify-between">
                            <a href="{{ route('tags.show', $tag->slug) }}"
                               class="font-serif text-ink-light hover:text-ink border-b border-dotted border-transparent hover:border-ink-faint transition-colors">
                                {{ $tag->name }}
                            </a>
                            <span class="text-xs text-ink-faint font-serif">
                                {{ $tag->shelves_count }} {{ Str::plural('book', $tag->shelves_count) }}
                                @if ($tag->posts_count)
                                    &middot; {{ $tag->posts_count }} {{ Str::plural('entry', $tag->posts_count) }}
                                @endif
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    </div>
</x-app-layout>
