<x-app-layout>
    <div class="max-w-3xl mx-auto px-8 py-10">

        {{-- Back --}}
        <a href="{{ route('shelf.index') }}"
           class="text-sm text-ink-faint hover:text-ink font-serif border-b border-dotted border-ink-faint hover:border-ink transition-colors">
            &larr; Shelf
        </a>

        {{-- Book header --}}
        <div class="flex gap-6 mt-6 mb-8">
            <div class="flex-shrink-0 w-24">
                @if ($shelf->book->display_cover_url)
                    <img src="{{ $shelf->book->display_cover_url }}"
                         alt="Cover of {{ $shelf->book->title }}"
                         class="w-24 shadow-sm">
                @else
                    <div class="w-24 h-36 bg-parchment-200 flex items-center justify-center text-ink-faint text-xs text-center p-2 leading-tight">
                        no cover
                    </div>
                @endif
            </div>
            <div class="flex-1">
                <h1 class="text-2xl font-serif font-semibold text-ink leading-tight">
                    {{ $shelf->book->title }}
                </h1>
                <p class="text-ink-faint font-serif mt-1">{{ $shelf->book->author }}</p>
                @if ($shelf->book->isbn)
                    <p class="text-xs text-ink-faint mt-1">ISBN {{ $shelf->book->isbn }}</p>
                @endif
            </div>
        </div>

        {{-- Status form --}}
        <section class="border border-parchment-300 bg-parchment-50 p-5 mb-8">
            <h2 class="text-xs uppercase tracking-widest text-ink-faint font-serif mb-4">Reading status</h2>
            <form method="POST" action="{{ route('shelf.update', $shelf) }}" class="space-y-4">
                @csrf
                @method('PATCH')

                <div class="flex gap-6 font-serif text-sm">
                    @foreach (['to_read' => 'To Read', 'reading' => 'Reading', 'read' => 'Read'] as $value => $label)
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="status" value="{{ $value }}"
                                   {{ $shelf->status === $value ? 'checked' : '' }}
                                   class="text-ink border-parchment-300 focus:ring-ink">
                            {{ $label }}
                        </label>
                    @endforeach
                </div>

                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <x-input-label for="date_started" value="Date started" />
                        <x-text-input id="date_started" name="date_started" type="date"
                                      class="mt-1 block w-full text-sm"
                                      :value="old('date_started', $shelf->date_started?->format('Y-m-d'))" />
                        <x-input-error :messages="$errors->get('date_started')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="date_finished" value="Date finished" />
                        <x-text-input id="date_finished" name="date_finished" type="date"
                                      class="mt-1 block w-full text-sm"
                                      :value="old('date_finished', $shelf->date_finished?->format('Y-m-d'))" />
                        <x-input-error :messages="$errors->get('date_finished')" class="mt-1" />
                    </div>
                </div>

                <div>
                    <x-input-label for="tags" value="Tags (comma-separated)" />
                    <x-text-input id="tags" name="tags" type="text"
                                  class="mt-1 block w-full text-sm"
                                  :value="old('tags', $tagString)"
                                  placeholder="e.g. fiction, favourites, 2026" />
                </div>

                <div class="flex items-center gap-4">
                    <x-primary-button>Save</x-primary-button>
                </div>
            </form>
        </section>

        {{-- Tags --}}
        @if ($shelf->tags->isNotEmpty())
            <div class="flex flex-wrap gap-2 mb-6">
                @foreach ($shelf->tags as $tag)
                    <a href="{{ route('tags.show', $tag->slug) }}"
                       class="text-xs font-serif text-ink-faint border border-parchment-300 px-2 py-0.5 hover:text-ink hover:border-ink-faint transition-colors">
                        {{ $tag->name }}
                    </a>
                @endforeach
            </div>
        @endif

        {{-- Journal entries --}}
        <section>
            <div class="flex items-baseline justify-between mb-4">
                <h2 class="text-xs uppercase tracking-widest text-ink-faint font-serif">Journal entries</h2>
                <a href="{{ route('posts.create', $shelf) }}"
                   class="text-sm font-serif text-ink-faint hover:text-ink border-b border-dotted border-ink-faint hover:border-ink transition-colors">
                    + New entry
                </a>
            </div>

            @if ($shelf->posts->isEmpty())
                <p class="text-ink-faint font-serif italic text-sm">No entries yet.</p>
            @else
                <div class="space-y-4">
                    @foreach ($shelf->posts as $post)
                        <a href="{{ route('posts.show', $post) }}"
                           class="block border border-parchment-300 bg-parchment-50 p-4 hover:border-ink-faint transition-colors group">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-serif text-ink line-clamp-2 group-hover:underline decoration-dotted">
                                        {{ Str::limit(strip_tags($post->content), 140) }}
                                    </p>
                                    <p class="text-xs text-ink-faint mt-2">
                                        {{ $post->created_at->format('M j, Y') }}
                                        @if ($post->is_private)
                                            &middot; <span class="italic">private</span>
                                        @endif
                                    </p>
                                </div>
                                @if ($post->rating)
                                    <div class="text-amber-700 flex-shrink-0 text-sm tracking-wide">
                                        @for ($i = 1; $i <= 5; $i++)
                                            {{ $i <= $post->rating ? '★' : '☆' }}
                                        @endfor
                                    </div>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </section>

        {{-- Remove from shelf --}}
        <div class="mt-10 pt-6 border-t border-parchment-300">
            <form method="POST" action="{{ route('shelf.destroy', $shelf) }}"
                  onsubmit="return confirm('Remove this book from your shelf? Journal entries will also be deleted.')">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="text-sm font-serif text-ink-faint hover:text-red-700 transition-colors">
                    Remove from shelf
                </button>
            </form>
        </div>

    </div>
</x-app-layout>
