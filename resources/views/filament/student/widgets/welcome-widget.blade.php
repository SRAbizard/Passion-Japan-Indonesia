<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
            <div class="flex-1">
                <p class="text-xs uppercase tracking-wider font-semibold text-primary-500">{{ __('Profile completion') }}</p>
                <h2 class="mt-1 text-2xl font-bold">
                    {{ __('Hello, :name', ['name' => $user?->name ?? __('Student')]) }} <span aria-hidden="true">👋</span>
                </h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('Complete your profile so we can match you with the best opportunities in Japan.') }}
                </p>

                <div class="mt-5 max-w-md">
                    <div class="flex items-center justify-between text-xs font-medium">
                        <span class="text-gray-600 dark:text-gray-300">{{ __('Profile is :percent% complete', ['percent' => $percent]) }}</span>
                        <span class="text-primary-500">{{ $percent }}%</span>
                    </div>
                    <div class="mt-2 h-2 w-full overflow-hidden rounded-full bg-gray-200 dark:bg-gray-800">
                        <div class="h-full rounded-full bg-primary-500 transition-all" style="width: {{ $percent }}%"></div>
                    </div>
                </div>

                @unless ($verified)
                    <div class="mt-5 inline-flex items-center gap-2 rounded-lg border border-warning-500/40 bg-warning-500/10 px-3 py-1.5 text-xs font-medium text-warning-600 dark:text-warning-400">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ __('Please verify your email address.') }}
                    </div>
                @endunless
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="{{ filament()->getProfileUrl() }}" class="fi-btn fi-btn-color-primary inline-flex items-center gap-2 rounded-lg bg-primary-600 px-4 py-2 text-sm font-semibold text-white hover:bg-primary-700 transition">
                    {{ __('Edit profile') }}
                </a>
                <a href="{{ url('/') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-100 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-800 transition">
                    {{ __('Back to website') }}
                </a>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
