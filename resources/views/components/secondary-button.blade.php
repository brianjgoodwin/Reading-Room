<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-4 py-2 bg-parchment-50 border border-parchment-300 font-serif text-sm text-ink-faint hover:text-ink hover:border-ink-faint focus:outline-none focus:ring-2 focus:ring-ink focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
