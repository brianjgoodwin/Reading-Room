<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-ink text-parchment-50 border border-ink font-serif text-sm hover:bg-ink-light focus:outline-none focus:ring-2 focus:ring-ink focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
