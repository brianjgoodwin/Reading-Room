<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? config('app.name', 'Reading Room') }}</title>

        <!-- Fonts: Lora (serif body), JetBrains Mono (code) -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=lora:400,400i,600,700|jetbrains-mono:400&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-serif antialiased bg-parchment-100 text-ink">
        <div class="flex min-h-screen">

            {{-- Left sidebar --}}
            <aside class="w-52 flex-shrink-0 bg-parchment-50 border-r border-parchment-300 flex flex-col py-8 px-5">
                <div class="mb-8">
                    <a href="{{ route('shelf.index') }}" class="font-serif font-semibold text-lg text-ink leading-tight hover:text-ink-light">
                        Reading Room
                    </a>
                </div>

                <nav class="flex flex-col gap-1 text-sm flex-1">
                    <x-nav-link :href="route('shelf.index')" :active="request()->routeIs('shelf.*')">
                        Shelf
                    </x-nav-link>
                    <x-nav-link :href="route('posts.index')" :active="request()->routeIs('posts.*')">
                        Journal
                    </x-nav-link>
                    <x-nav-link :href="route('tags.index')" :active="request()->routeIs('tags.*')">
                        Tags
                    </x-nav-link>
                    <x-nav-link :href="route('stats.index')" :active="request()->routeIs('stats.*')">
                        Stats
                    </x-nav-link>
                    <x-nav-link :href="route('books.search')" :active="request()->routeIs('books.*')">
                        Find a Book
                    </x-nav-link>
                </nav>

                <div class="flex flex-col gap-1 text-sm border-t border-parchment-300 pt-4 mt-4">
                    <x-nav-link :href="route('import.create')" :active="request()->routeIs('import.*')">
                        Import
                    </x-nav-link>
                    <x-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.*')">
                        Profile
                    </x-nav-link>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="w-full text-left font-serif text-ink-faint hover:text-ink py-1 transition-colors duration-150">
                            Sign out
                        </button>
                    </form>
                </div>
            </aside>

            {{-- Main content --}}
            <main class="flex-1 overflow-y-auto">
                {{-- Flash messages --}}
                @if (session('success'))
                    <div class="bg-green-50 border-l-4 border-green-400 text-green-800 text-sm px-6 py-3">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="bg-red-50 border-l-4 border-red-400 text-red-800 text-sm px-6 py-3">
                        {{ session('error') }}
                    </div>
                @endif

                {{ $slot }}
            </main>

        </div>
    </body>
</html>
