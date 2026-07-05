<x-app-layout>
    <div class="max-w-3xl mx-auto px-8 py-10">

        <h1 class="text-2xl font-serif font-semibold text-ink mb-10">Reading Stats</h1>

        {{-- Summary row --}}
        <div class="grid grid-cols-4 gap-px bg-parchment-300 border border-parchment-300 mb-10">
            @foreach ([
                ['label' => 'Total books', 'value' => $by_status['total']],
                ['label' => 'Read',        'value' => $by_status['read']],
                ['label' => 'Reading',     'value' => $by_status['reading']],
                ['label' => 'To read',     'value' => $by_status['to_read']],
            ] as $stat)
                <div class="bg-parchment-50 px-5 py-4 text-center">
                    <p class="text-2xl font-serif font-semibold text-ink">{{ $stat['value'] }}</p>
                    <p class="text-xs uppercase tracking-widest text-ink-faint font-serif mt-1">{{ $stat['label'] }}</p>
                </div>
            @endforeach
        </div>

        {{-- Monthly pace --}}
        <section class="mb-10">
            <h2 class="text-xs uppercase tracking-widest text-ink-faint font-serif mb-5">Books finished per month</h2>

            @php
                $maxMonthly = max(collect($monthly_pace)->pluck('count')->max(), 1);
            @endphp

            @if (collect($monthly_pace)->sum('count') === 0)
                <p class="text-ink-faint font-serif italic text-sm">
                    No finished books recorded yet. Add finish dates to books on your shelf.
                </p>
            @else
                <div class="flex items-end gap-1.5 h-28">
                    @foreach ($monthly_pace as $month)
                        @php
                            $height = $month['count'] > 0
                                ? max(8, (int) round(($month['count'] / $maxMonthly) * 100))
                                : 2;
                        @endphp
                        <div class="flex-1 flex flex-col items-center justify-end gap-1">
                            @if ($month['count'] > 0)
                                <span class="text-xs text-ink-faint font-serif">{{ $month['count'] }}</span>
                            @endif
                            <div class="w-full bg-ink-light rounded-sm transition-all"
                                 style="height: {{ $height }}%"
                                 title="{{ $month['month'] }}: {{ $month['count'] }} {{ Str::plural('book', $month['count']) }}">
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="flex gap-1.5 mt-1">
                    @foreach ($monthly_pace as $month)
                        <div class="flex-1 text-center">
                            <span class="text-[10px] text-ink-faint font-serif">
                                {{ \Carbon\Carbon::parse($month['month'])->format('M') }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

        <div class="grid grid-cols-2 gap-8 mb-10">

            {{-- Top authors --}}
            <section>
                <h2 class="text-xs uppercase tracking-widest text-ink-faint font-serif mb-4">Top authors</h2>
                @if (empty($top_authors))
                    <p class="text-ink-faint font-serif italic text-sm">No books on shelf yet.</p>
                @else
                    <div class="space-y-2">
                        @php $maxAuthor = max(collect($top_authors)->pluck('count')->max(), 1); @endphp
                        @foreach ($top_authors as $entry)
                            <div>
                                <div class="flex items-baseline justify-between mb-0.5">
                                    <span class="text-sm font-serif text-ink truncate pr-2">{{ $entry['author'] }}</span>
                                    <span class="text-xs text-ink-faint font-serif flex-shrink-0">{{ $entry['count'] }}</span>
                                </div>
                                <div class="h-1 bg-parchment-200 rounded-sm">
                                    <div class="h-1 bg-ink-faint rounded-sm"
                                         style="width: {{ (int) round(($entry['count'] / $maxAuthor) * 100) }}%">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </section>

            {{-- Streak + rating --}}
            <section class="space-y-6">
                <div>
                    <h2 class="text-xs uppercase tracking-widest text-ink-faint font-serif mb-3">Reading streak</h2>
                    @if ($streak > 0)
                        <p class="font-serif text-ink">
                            <span class="text-3xl font-semibold">{{ $streak }}</span>
                            <span class="text-ink-faint ml-1">{{ Str::plural('month', $streak) }} in a row</span>
                        </p>
                    @else
                        <p class="text-ink-faint font-serif italic text-sm">
                            No streak yet. Finish a book this month to start one.
                        </p>
                    @endif
                </div>

                <div>
                    <h2 class="text-xs uppercase tracking-widest text-ink-faint font-serif mb-3">Average rating</h2>
                    @if ($avg_rating !== null)
                        <p class="font-serif text-ink">
                            <span class="text-3xl font-semibold">{{ $avg_rating }}</span>
                            <span class="text-amber-700 ml-2 tracking-wide">
                                @for ($i = 1; $i <= 5; $i++)
                                    {{ $i <= round($avg_rating) ? '★' : '☆' }}
                                @endfor
                            </span>
                        </p>
                    @else
                        <p class="text-ink-faint font-serif italic text-sm">
                            No rated entries yet.
                        </p>
                    @endif
                </div>
            </section>

        </div>

    </div>
</x-app-layout>
