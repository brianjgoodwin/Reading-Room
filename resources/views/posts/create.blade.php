<x-app-layout>
    <div class="max-w-3xl mx-auto px-8 py-10">

        <a href="{{ route('shelf.show', $shelf) }}"
           class="text-sm text-ink-faint hover:text-ink font-serif border-b border-dotted border-ink-faint hover:border-ink transition-colors">
            &larr; {{ $shelf->book->title }}
        </a>

        <h1 class="text-2xl font-serif font-semibold text-ink mt-6 mb-8">New journal entry</h1>

        <form method="POST" action="{{ route('posts.store', $shelf) }}" class="space-y-6">
            @csrf

            @include('posts.partials.form-fields', ['post' => null, 'tagString' => ''])

            <div class="flex items-center gap-4 pt-2">
                <x-primary-button>Save entry</x-primary-button>
                <a href="{{ route('shelf.show', $shelf) }}"
                   class="text-sm font-serif text-ink-faint hover:text-ink transition-colors">
                    Cancel
                </a>
            </div>
        </form>

    </div>
</x-app-layout>
