<x-app-layout>
    <div class="max-w-2xl mx-auto px-8 py-10">

        <h1 class="text-2xl font-serif font-semibold text-ink mb-8">Profile</h1>

        <div class="space-y-8">
            <div class="border border-parchment-300 bg-parchment-50 p-6">
                @include('profile.partials.update-profile-information-form')
            </div>

            <div class="border border-parchment-300 bg-parchment-50 p-6">
                @include('profile.partials.update-password-form')
            </div>

            <div class="border border-parchment-300 bg-parchment-50 p-6">
                @include('profile.partials.delete-user-form')
            </div>
        </div>

    </div>
</x-app-layout>
