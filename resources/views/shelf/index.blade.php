<x-app-layout>
    <div class="max-w-4xl mx-auto px-8 py-10">

        <div class="flex items-baseline justify-between mb-8">
            <h1 class="text-2xl font-serif font-semibold text-ink">My Shelf</h1>
            <a href="{{ route('books.search') }}"
               class="text-sm font-serif text-ink-faint hover:text-ink border-b border-dotted border-ink-faint hover:border-ink transition-colors">
                + Find a book
            </a>
        </div>

        {{-- Status filter tabs --}}
        <div class="flex gap-6 border-b border-parchment-300 mb-8 text-sm font-serif">
            @foreach ([null => 'All ('.$counts['all'].')', 'to_read' => 'To Read ('.$counts['to_read'].')', 'reading' => 'Reading ('.$counts['reading'].')', 'read' => 'Read ('.$counts['read'].')'] as $value => $label)
                <a href="{{ route('shelf.index', $value ? ['status' => $value] : []) }}"
                   class="pb-2 -mb-px transition-colors {{ ($status ?: null) === ($value ?: null) ? 'border-b-2 border-ink text-ink font-semibold' : 'text-ink-faint hover:text-ink' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        @if ($shelves->isEmpty())
            <p class="text-ink-faint font-serif italic">
                @if ($status)
                    No books with that status yet.
                @else
                    Your shelf is empty. <a href="{{ route('books.search') }}" class="underline decoration-dotted hover:text-ink">Find a book to add.</a>
                @endif
            </p>
        @else
            <div class="space-y-4">
                @foreach ($shelves as $shelf)
                    <a href="{{ route('shelf.show', $shelf) }}"
                       class="flex gap-5 items-start bg-parchment-50 border border-parchment-300 p-4 hover:border-ink-faint transition-colors group">

                        {{-- Cover --}}
                        <div class="flex-shrink-0 w-14">
                            @if ($shelf->book->display_cover_url)
                                <img src="{{ $shelf->book->display_cover_url }}"
                                     alt="Cover of {{ $shelf->book->title }}"
                                     class="w-14 object-cover shadow-sm">
                            @else
                                <div class="w-14 h-20 bg-parchment-200 flex items-center justify-center text-ink-faint text-xs text-center leading-tight p-1">
                                    no cover
                                </div>
                            @endif
                        </div>

                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            <p class="font-serif font-semibold text-ink group-hover:underline decoration-dotted leading-snug">
                                {{ $shelf->book->title }}
                            </p>
                            <p class="text-sm text-ink-faint mt-0.5">{{ $shelf->book->author }}</p>
                            <p class="text-xs text-ink-faint mt-2 uppercase tracking-wide">
                                {{ \App\Models\Shelf::statusLabel($shelf->status) }}
                                @if ($shelf->date_finished)
                                    &middot; finished {{ $shelf->date_finished->format('M j, Y') }}
                                @elseif ($shelf->date_started)
                                    &middot; started {{ $shelf->date_started->format('M j, Y') }}
                                @endif
                            </p>
                        </div>

                        {{-- Post count --}}
                        @if ($shelf->posts_count > 0)
                            <div class="text-xs text-ink-faint flex-shrink-0">
                                {{ $shelf->posts_count }} {{ Str::plural('entry', $shelf->posts_count) }}
                            </div>
                        @endif
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</x-app-layout>
