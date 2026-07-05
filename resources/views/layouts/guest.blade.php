<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Reading Room') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=lora:400,400i,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-serif text-ink antialiased bg-parchment-100">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
            <div class="mb-6">
                <a href="/" class="font-serif font-semibold text-xl text-ink tracking-tight">
                    Reading Room
                </a>
            </div>

            <div class="w-full sm:max-w-md px-8 py-7 bg-parchment-50 border border-parchment-300 shadow-sm overflow-hidden">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
