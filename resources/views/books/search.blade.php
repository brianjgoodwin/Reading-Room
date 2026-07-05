<x-app-layout>
    <div class="max-w-3xl mx-auto px-8 py-10">

        <h1 class="text-2xl font-serif font-semibold text-ink mb-6">Find a Book</h1>

        {{-- Search form --}}
        <form method="GET" action="{{ route('books.search') }}" class="flex gap-3 mb-8">
            <x-text-input
                type="search"
                name="q"
                value="{{ $query }}"
                placeholder="Title, author, or keyword&hellip;"
                class="flex-1"
                autofocus />
            <x-primary-button>Search</x-primary-button>
        </form>

        {{-- Results --}}
        @if ($query && empty($results))
            <p class="text-ink-faint font-serif italic">No results for &ldquo;{{ $query }}&rdquo;.</p>

        @elseif (! empty($results))
            <p class="text-xs text-ink-faint uppercase tracking-wide mb-4 font-serif">
                {{ count($results) }} results for &ldquo;{{ $query }}&rdquo;
            </p>

            <div class="space-y-3">
                @foreach ($results as $book)
                    <div class="flex gap-4 items-start border border-parchment-300 bg-parchment-50 p-4">

                        {{-- Cover --}}
                        <div class="flex-shrink-0 w-12">
                            @if ($book['cover_url'])
                                <img src="{{ $book['cover_url'] }}"
                                     alt="Cover of {{ $book['title'] }}"
                                     class="w-12 shadow-sm"
                                     loading="lazy">
                            @else
                                <div class="w-12 h-16 bg-parchment-200 flex items-center justify-center text-ink-faint text-xs text-center leading-tight p-1">
                                    —
                                </div>
                            @endif
                        </div>

                        {{-- Info + Add button --}}
                        <div class="flex-1 min-w-0">
                            <p class="font-serif font-semibold text-ink leading-snug">{{ $book['title'] }}</p>
                            <p class="text-sm text-ink-faint mt-0.5">{{ $book['author'] }}</p>
                            @if ($book['isbn'])
                                <p class="text-xs text-ink-faint mt-0.5">ISBN {{ $book['isbn'] }}</p>
                            @endif
                        </div>

                        {{-- Add to shelf --}}
                        <form method="POST" action="{{ route('books.store') }}" class="flex-shrink-0">
                            @csrf
                            <input type="hidden" name="title"           value="{{ $book['title'] }}">
                            <input type="hidden" name="author"          value="{{ $book['author'] }}">
                            <input type="hidden" name="isbn"            value="{{ $book['isbn'] ?? '' }}">
                            <input type="hidden" name="open_library_id" value="{{ $book['open_library_id'] ?? '' }}">
                            <button type="submit"
                                    class="text-sm font-serif text-ink-faint hover:text-ink border border-parchment-300 hover:border-ink-faint px-3 py-1.5 transition-colors">
                                Add to shelf
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        @endif

    </div>
</x-app-layout>
