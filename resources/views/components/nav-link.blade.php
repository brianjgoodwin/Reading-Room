@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block font-serif text-ink font-semibold py-1 underline decoration-1 underline-offset-2'
            : 'block font-serif text-ink-faint hover:text-ink py-1 transition-colors duration-150';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
