<x-filament-widgets::widget>
    <x-filament::section>
        <div class="relative overflow-hidden">
            {{-- Decorative wave pattern, top-right --}}
            <div aria-hidden="true" class="pointer-events-none absolute top-0 right-0 h-full w-72 opacity-[0.06] dark:opacity-[0.10]">
                <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg" class="h-full w-full text-primary-600">
                    <defs>
                        <pattern id="seigaiha-admin" x="0" y="0" width="40" height="20" patternUnits="userSpaceOnUse">
                            <path d="M0 20 A 20 20 0 0 1 40 20" fill="none" stroke="currentColor" stroke-width="1.5"/>
                            <path d="M-20 20 A 20 20 0 0 1 20 20" fill="none" stroke="currentColor" stroke-width="1.5"/>
                            <path d="M20 20 A 20 20 0 0 1 60 20" fill="none" stroke="currentColor" stroke-width="1.5"/>
                        </pattern>
                    </defs>
                    <rect width="200" height="200" fill="url(#seigaiha-admin)"/>
                </svg>
            </div>

            <div class="relative">
                <p class="text-xs uppercase tracking-[0.2em] font-bold text-primary-600 dark:text-primary-400">
                    管理画面 · {{ __('Admin Dashboard') }}
                </p>
                <h2 class="mt-2 text-2xl md:text-3xl font-bold text-gray-950 dark:text-white tracking-tight">
                    {{ __('Welcome back, :name', ['name' => $user?->name]) }}
                </h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                    {{ __('Quick overview of what needs your attention today.') }}
                </p>

                <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    {{-- Pending applications --}}
                    <a href="{{ \App\Filament\Resources\Applications\ApplicationResource::getUrl('index') }}"
                       class="group flex items-center gap-4 rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5 hover:ring-primary-500/40 hover:shadow-md transition dark:bg-gray-800/60 dark:ring-white/10">
                        <div class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-primary-50 text-primary-600 dark:bg-primary-500/15 dark:text-primary-400 group-hover:scale-110 transition">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs uppercase tracking-wider font-semibold text-gray-500 dark:text-gray-400">{{ __('Pending applications') }}</p>
                            <p class="text-2xl font-bold text-gray-950 dark:text-white">{{ $pendingApps }}</p>
                        </div>
                    </a>

                    {{-- Pending documents --}}
                    <a href="{{ \App\Filament\Resources\StudentDocuments\StudentDocumentResource::getUrl('index') }}"
                       class="group flex items-center gap-4 rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5 hover:ring-amber-500/40 hover:shadow-md transition dark:bg-gray-800/60 dark:ring-white/10">
                        <div class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-amber-50 text-amber-600 dark:bg-amber-500/15 dark:text-amber-400 group-hover:scale-110 transition">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs uppercase tracking-wider font-semibold text-gray-500 dark:text-gray-400">{{ __('Documents to verify') }}</p>
                            <p class="text-2xl font-bold text-gray-950 dark:text-white">{{ $pendingDocs }}</p>
                        </div>
                    </a>

                    {{-- Unread messages --}}
                    <a href="{{ \App\Filament\Resources\ContactMessages\ContactMessageResource::getUrl('index') }}"
                       class="group flex items-center gap-4 rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5 hover:ring-pink-500/40 hover:shadow-md transition dark:bg-gray-800/60 dark:ring-white/10">
                        <div class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-pink-50 text-pink-600 dark:bg-pink-500/15 dark:text-pink-400 group-hover:scale-110 transition">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l9 6 9-6M3 8v10a2 2 0 002 2h14a2 2 0 002-2V8M3 8l2-2h14l2 2"/></svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs uppercase tracking-wider font-semibold text-gray-500 dark:text-gray-400">{{ __('Unread messages') }}</p>
                            <p class="text-2xl font-bold text-gray-950 dark:text-white">{{ $unreadMessages }}</p>
                        </div>
                    </a>

                    {{-- Total students --}}
                    <a href="{{ \App\Filament\Resources\Students\StudentResource::getUrl('index') }}"
                       class="group flex items-center gap-4 rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5 hover:ring-emerald-500/40 hover:shadow-md transition dark:bg-gray-800/60 dark:ring-white/10">
                        <div class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600 dark:bg-emerald-500/15 dark:text-emerald-400 group-hover:scale-110 transition">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs uppercase tracking-wider font-semibold text-gray-500 dark:text-gray-400">{{ __('Registered students') }}</p>
                            <p class="text-2xl font-bold text-gray-950 dark:text-white">{{ $studentCount }}</p>
                        </div>
                    </a>

                    {{-- Active enrollments --}}
                    <div class="flex items-center gap-4 rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-800/60 dark:ring-white/10">
                        <div class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-sky-50 text-sky-600 dark:bg-sky-500/15 dark:text-sky-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs uppercase tracking-wider font-semibold text-gray-500 dark:text-gray-400">{{ __('Active enrollments') }}</p>
                            <p class="text-2xl font-bold text-gray-950 dark:text-white">{{ $activeEnrollments }}</p>
                        </div>
                    </div>

                    {{-- Certificates issued --}}
                    <div class="flex items-center gap-4 rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-800/60 dark:ring-white/10">
                        <div class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-yellow-50 text-yellow-600 dark:bg-yellow-500/15 dark:text-yellow-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs uppercase tracking-wider font-semibold text-gray-500 dark:text-gray-400">{{ __('Certificates issued') }}</p>
                            <p class="text-2xl font-bold text-gray-950 dark:text-white">{{ $certsIssued }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
