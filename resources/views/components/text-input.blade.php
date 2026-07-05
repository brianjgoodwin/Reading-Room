@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'font-serif border-parchment-300 focus:border-ink-light focus:ring-ink-light rounded-sm shadow-sm bg-parchment-50 text-ink']) }}>
