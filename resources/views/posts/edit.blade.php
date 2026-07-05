<x-app-layout>
    <div class="max-w-3xl mx-auto px-8 py-10">

        <a href="{{ route('posts.show', $post) }}"
           class="text-sm text-ink-faint hover:text-ink font-serif border-b border-dotted border-ink-faint hover:border-ink transition-colors">
            &larr; Back to entry
        </a>

        <h1 class="text-2xl font-serif font-semibold text-ink mt-6 mb-2">Edit entry</h1>
        <p class="text-sm text-ink-faint font-serif mb-8">{{ $post->shelf->book->title }}</p>

        <form method="POST" action="{{ route('posts.update', $post) }}" class="space-y-6">
            @csrf
            @method('PATCH')

            @include('posts.partials.form-fields', ['post' => $post, 'tagString' => $tagString])

            <div class="flex items-center gap-4 pt-2">
                <x-primary-button>Save changes</x-primary-button>
                <a href="{{ route('posts.show', $post) }}"
                   class="text-sm font-serif text-ink-faint hover:text-ink transition-colors">
                    Cancel
                </a>
            </div>
        </form>

        {{-- Delete --}}
        <div class="mt-10 pt-6 border-t border-parchment-300">
            <form method="POST" action="{{ route('posts.destroy', $post) }}"
                  onsubmit="return confirm('Delete this entry? This cannot be undone.')">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="text-sm font-serif text-ink-faint hover:text-red-700 transition-colors">
                    Delete entry
                </button>
            </form>
        </div>

    </div>
</x-app-layout>
