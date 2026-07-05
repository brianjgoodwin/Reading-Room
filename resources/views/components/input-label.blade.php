@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-serif text-sm text-ink-light']) }}>
    {{ $value ?? $slot }}
</label>
