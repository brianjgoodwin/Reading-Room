{{--
    Shared form fields for create and edit post views.
    Expects: $post (may be null for create), $tagString (string)
--}}

{{-- Content --}}
<div>
    <x-input-label for="content" value="Entry" />
    <textarea id="content" name="content" rows="14"
              class="mt-1 block w-full font-serif border-parchment-300 focus:border-ink-light focus:ring-ink-light rounded-sm shadow-sm bg-parchment-50 text-ink text-sm leading-relaxed"
              placeholder="Write in Markdown&hellip;" required>{{ old('content', $post?->content ?? '') }}</textarea>
    <p class="mt-1 text-xs text-ink-faint font-serif">Markdown is supported.</p>
    <x-input-error :messages="$errors->get('content')" class="mt-1" />
</div>

{{-- Rating --}}
<div>
    <x-input-label value="Rating" />
    <div class="flex gap-4 mt-2 font-serif text-sm">
        <label class="flex items-center gap-1.5 cursor-pointer">
            <input type="radio" name="rating" value=""
                   {{ old('rating', $post?->rating) === null ? 'checked' : '' }}
                   class="text-ink border-parchment-300 focus:ring-ink">
            <span class="text-ink-faint italic">No rating</span>
        </label>
        @for ($i = 1; $i <= 5; $i++)
            <label class="flex items-center gap-1.5 cursor-pointer">
                <input type="radio" name="rating" value="{{ $i }}"
                       {{ (int) old('rating', $post?->rating) === $i ? 'checked' : '' }}
                       class="text-ink border-parchment-300 focus:ring-ink">
                <span class="text-amber-700 tracking-wide">
                    @for ($s = 1; $s <= 5; $s++)
                        {{ $s <= $i ? '★' : '☆' }}
                    @endfor
                </span>
            </label>
        @endfor
    </div>
    <x-input-error :messages="$errors->get('rating')" class="mt-1" />
</div>

{{-- Tags --}}
<div>
    <x-input-label for="tags" value="Tags (comma-separated)" />
    <x-text-input id="tags" name="tags" type="text"
                  class="mt-1 block w-full text-sm"
                  :value="old('tags', $tagString)"
                  placeholder="e.g. fiction, favourites, 2026" />
    <x-input-error :messages="$errors->get('tags')" class="mt-1" />
</div>

{{-- Privacy --}}
<div class="flex items-center gap-3">
    <input type="hidden" name="is_private" value="0">
    <input type="checkbox" id="is_private" name="is_private" value="1"
           {{ old('is_private', $post?->is_private) ? 'checked' : '' }}
           class="text-ink border-parchment-300 rounded-sm focus:ring-ink">
    <label for="is_private" class="font-serif text-sm text-ink-light cursor-pointer">
        Private entry (only visible to you)
    </label>
</div>
