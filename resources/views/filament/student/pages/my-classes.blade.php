<x-filament-panels::page>
    @php
        $enrollments = $this->getEnrollments();
    @endphp

    @if ($enrollments->isEmpty())
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-10 text-center">
            <div class="mx-auto inline-flex h-16 w-16 items-center justify-center rounded-full bg-primary-50 text-primary-600 dark:bg-primary-500/10 dark:text-primary-400 mb-4">
                <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-950 dark:text-white">{{ __('You haven\'t enrolled in any courses yet') }}</h3>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ __('Browse the catalog to find a course.') }}</p>
            <a href="{{ route('elearning.index') }}"
               class="mt-5 fi-btn relative grid-flow-col items-center justify-center font-semibold outline-none transition duration-75 focus-visible:ring-2 rounded-lg fi-color-primary fi-btn-color-primary fi-size-md fi-btn-size-md gap-1.5 px-3 py-2 text-sm inline-grid shadow-sm bg-primary-600 text-white hover:bg-primary-500 focus-visible:ring-primary-500/50">
                {{ __('Browse courses') }}
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
    @else
        <div class="grid gap-4 md:grid-cols-2">
            @foreach ($enrollments as $enrollment)
                @php
                    $course = $enrollment->course;
                    $firstMaterial = $course?->chapters->flatMap->materials->first();
                @endphp
                <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 overflow-hidden flex flex-col">
                    <div class="aspect-video bg-gradient-to-br from-primary-600 to-primary-800 relative">
                        @if ($course?->thumbnail_url)
                            <img src="{{ $course->thumbnail_url }}" alt="" class="absolute inset-0 h-full w-full object-cover">
                        @endif
                        <div class="absolute top-3 left-3">
                            <span class="text-[10px] uppercase tracking-wider font-bold px-2 py-1 rounded-md bg-white/90 dark:bg-gray-900/90 text-primary-600 dark:text-primary-400 backdrop-blur">
                                {{ $course?->category?->t('name') ?? '—' }}
                            </span>
                        </div>
                    </div>
                    <div class="p-5 flex-1 flex flex-col">
                        <h3 class="font-semibold text-gray-950 dark:text-white">{{ $course?->t('title') ?? '—' }}</h3>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('level.'.$course?->level) }}</p>

                        <div class="mt-4">
                            <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400 mb-1.5">
                                <span>{{ __('Progress') }}</span>
                                <span class="font-semibold text-gray-950 dark:text-white">{{ $enrollment->progress_pct }}%</span>
                            </div>
                            <div class="h-2 w-full rounded-full bg-gray-200 dark:bg-gray-700 overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-primary-500 to-primary-700" style="width: {{ $enrollment->progress_pct }}%"></div>
                            </div>
                        </div>

                        <div class="mt-3 text-xs text-gray-500 dark:text-gray-400">
                            <span class="px-2 py-0.5 rounded-md bg-gray-100 dark:bg-gray-800">{{ __('enrollment.status.'.$enrollment->status) }}</span>
                            @if ($enrollment->last_activity_at)
                                · {{ __('Last active') }} {{ $enrollment->last_activity_at->diffForHumans() }}
                            @endif
                        </div>

                        <div class="mt-5 pt-4 border-t border-gray-100 dark:border-white/5 flex items-center justify-between gap-2">
                            @if ($firstMaterial)
                                <a href="{{ route('elearning.material', [$course->slug, $firstMaterial->id]) }}"
                                   class="fi-btn fi-color-primary fi-size-sm gap-1.5 px-2.5 py-1.5 text-sm inline-grid grid-flow-col items-center font-semibold rounded-lg shadow-sm bg-primary-600 text-white hover:bg-primary-500">
                                    {{ $enrollment->progress_pct > 0 ? __('Continue learning') : __('Start course') }} →
                                </a>
                            @endif
                            <a href="{{ route('elearning.show', $course->slug) }}"
                               class="fi-btn fi-size-sm gap-1.5 px-2.5 py-1.5 text-sm inline-grid grid-flow-col items-center font-medium rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-white/5">
                                {{ __('Course details') }}
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @php $certs = auth()->user()->certificates()->with('course')->get(); @endphp
        @if ($certs->isNotEmpty())
            <div class="mt-8">
                <h3 class="font-semibold text-gray-950 dark:text-white text-lg mb-4">{{ __('Your certificates') }}</h3>
                <div class="grid gap-3 sm:grid-cols-2 md:grid-cols-3">
                    @foreach ($certs as $cert)
                        <a href="{{ route('certificate.show', $cert->certificate_number) }}"
                           class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-4 flex items-center gap-3 hover:ring-primary-500/40 transition">
                            <div class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400 shrink-0">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-semibold text-gray-950 dark:text-white truncate">{{ $cert->course?->t('title') ?? '—' }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 font-mono">{{ $cert->certificate_number }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    @endif
</x-filament-panels::page>
