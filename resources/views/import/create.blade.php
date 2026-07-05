<x-app-layout>
    <div class="max-w-2xl mx-auto px-8 py-10">

        <h1 class="text-2xl font-serif font-semibold text-ink mb-2">Import from Goodreads</h1>
        <p class="text-sm text-ink-faint font-serif mb-8">
            Export your library from Goodreads under
            <span class="italic">My Books &rarr; Import and export &rarr; Export library</span>.
            Then upload the CSV file here.
        </p>

        {{-- Import results (shown after a successful import) --}}
        @if (session('import_results'))
            @php $r = session('import_results'); @endphp
            <div class="border border-parchment-300 bg-parchment-50 p-5 mb-8">
                <h2 class="text-xs uppercase tracking-widest text-ink-faint font-serif mb-3">Import results</h2>
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <p class="text-2xl font-serif font-semibold text-ink">{{ $r['imported'] }}</p>
                        <p class="text-xs text-ink-faint font-serif uppercase tracking-wide">Imported</p>
                    </div>
                    <div>
                        <p class="text-2xl font-serif font-semibold text-ink">{{ $r['skipped'] }}</p>
                        <p class="text-xs text-ink-faint font-serif uppercase tracking-wide">Skipped</p>
                    </div>
                </div>
                @if (! empty($r['errors']))
                    <div class="border-t border-parchment-300 pt-4">
                        <p class="text-xs uppercase tracking-widest text-ink-faint font-serif mb-2">Skipped rows</p>
                        <ul class="space-y-1">
                            @foreach ($r['errors'] as $error)
                                <li class="text-xs font-serif text-ink-faint">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="border-t border-parchment-300 pt-4 mt-4">
                    <a href="{{ route('shelf.index') }}"
                       class="text-sm font-serif text-ink-light hover:text-ink border-b border-dotted border-ink-faint hover:border-ink transition-colors">
                        Go to your shelf &rarr;
                    </a>
                </div>
            </div>
        @endif

        {{-- Upload form --}}
        <form method="POST" action="{{ route('import.store') }}" enctype="multipart/form-data"
              class="border border-parchment-300 bg-parchment-50 p-6 space-y-6">
            @csrf

            <div>
                <x-input-label for="csv_file" value="Goodreads export file (.csv)" />
                <input type="file" id="csv_file" name="csv_file" accept=".csv,.txt"
                       class="mt-2 block w-full text-sm font-serif text-ink-light
                              file:mr-4 file:py-1.5 file:px-4 file:border file:border-parchment-300
                              file:font-serif file:text-sm file:text-ink-faint file:bg-parchment-100
                              hover:file:text-ink hover:file:border-ink-faint cursor-pointer" />
                <x-input-error :messages="$errors->get('csv_file')" class="mt-1" />
            </div>

            <div class="flex items-center gap-3">
                <input type="hidden" name="import_reviews" value="0">
                <input type="checkbox" id="import_reviews" name="import_reviews" value="1"
                       class="text-ink border-parchment-300 rounded-sm focus:ring-ink">
                <label for="import_reviews" class="font-serif text-sm text-ink-light cursor-pointer">
                    Import Goodreads reviews as private journal entries
                </label>
            </div>

            <div class="border-t border-parchment-300 pt-4">
                <x-primary-button>Import</x-primary-button>
                <p class="mt-3 text-xs text-ink-faint font-serif">
                    Books already on your shelf will be skipped — your existing data won't be overwritten.
                    Maximum file size: 10 MB.
                </p>
            </div>
        </form>

        {{-- Instructions --}}
        <div class="mt-8 border-t border-parchment-300 pt-6">
            <h2 class="text-xs uppercase tracking-widest text-ink-faint font-serif mb-3">What gets imported</h2>
            <ul class="space-y-1.5 text-sm font-serif text-ink-light">
                <li>&middot; Title, author, ISBN</li>
                <li>&middot; Reading status (to read / reading / read)</li>
                <li>&middot; Start and finish dates (if recorded in Goodreads)</li>
                <li>&middot; Your star rating (1&ndash;5)</li>
                <li>&middot; Your written review (optional, imported as a private journal entry)</li>
            </ul>
        </div>

    </div>
</x-app-layout>
