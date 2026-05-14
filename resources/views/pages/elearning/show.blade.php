@extends('layouts.app')
@section('title', $course->t('title') . ' — ' . __('E-Learning'))

@section('content')
<section class="relative mx-auto max-w-7xl px-6 pt-10 pb-6">
    <a href="{{ route('elearning.index') }}" class="inline-flex items-center gap-1 text-sm text-surface-400 hover:text-brand-400 transition">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        {{ __('Back to courses') }}
    </a>
</section>

@if(session('status'))
    <div class="mx-auto max-w-7xl px-6 mb-4">
        <div class="glass-card border-brand-500/40 px-5 py-3 text-sm text-brand-300">{{ session('status') }}</div>
    </div>
@endif

<section class="mx-auto max-w-7xl px-6 pb-12 grid gap-8 lg:grid-cols-3">
    <div class="lg:col-span-2 space-y-6">
        <div class="glass-card overflow-hidden">
            <div class="aspect-video bg-gradient-to-br from-brand-700 to-brand-900 relative">
                @if($course->intro_video_url)
                    <iframe src="{{ $course->intro_video_url }}" class="absolute inset-0 h-full w-full" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                @elseif($course->thumbnail_url)
                    <img src="{{ $course->thumbnail_url }}" alt="" class="absolute inset-0 h-full w-full object-cover">
                @else
                    <div class="absolute inset-0 flex items-center justify-center text-white/30">
                        <svg class="h-24 w-24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 14l9-5-9-5-9 5 9 5z"/></svg>
                    </div>
                @endif
            </div>
            <div class="p-6 sm:p-8">
                <div class="flex items-center gap-2 flex-wrap mb-3">
                    @if($course->category)
                        <span class="text-[10px] uppercase tracking-wider font-semibold px-2 py-1 rounded-md bg-surface-800 text-surface-300">{{ $course->category->t('name') }}</span>
                    @endif
                    <span class="text-[10px] uppercase tracking-wider font-semibold px-2 py-1 rounded-md bg-brand-600/15 text-brand-300">{{ __('level.'.$course->level) }}</span>
                    @if($course->is_free)
                        <span class="text-[10px] uppercase tracking-wider font-semibold px-2 py-1 rounded-md bg-emerald-600/15 text-emerald-300">{{ __('Free') }}</span>
                    @endif
                </div>
                <h1 class="font-display text-3xl sm:text-4xl font-extrabold text-white">{{ $course->t('title') }}</h1>
                @if($course->t('subtitle'))
                    <p class="mt-3 text-lg text-surface-300">{{ $course->t('subtitle') }}</p>
                @endif
                <div class="mt-5 flex flex-wrap items-center gap-4 text-sm text-surface-400">
                    @if($course->instructor)
                        <span class="flex items-center gap-2">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            {{ $course->instructor->name }}
                        </span>
                    @endif
                    <span class="flex items-center gap-2">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                        {{ trans_choice('{1} :count chapter|[2,*] :count chapters', $course->chapters_count, ['count' => $course->chapters_count]) }}
                    </span>
                    <span class="flex items-center gap-2">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        {{ trans_choice('{1} :count lesson|[2,*] :count lessons', $course->materials_count, ['count' => $course->materials_count]) }}
                    </span>
                    <span class="flex items-center gap-2">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        {{ trans_choice('{1} :count student enrolled|[2,*] :count students enrolled', $course->enrollments_count, ['count' => $course->enrollments_count]) }}
                    </span>
                </div>
            </div>
        </div>

        <div class="glass-card p-6 sm:p-8">
            <h2 class="font-display text-xl font-bold text-white mb-4">{{ __('About this course') }}</h2>
            <div class="prose prose-invert max-w-none text-surface-300 prose-headings:text-white">{!! $course->t('description') !!}</div>
        </div>

        @if($course->t('what_you_learn'))
            <div class="glass-card p-6 sm:p-8">
                <h2 class="font-display text-xl font-bold text-white mb-4">{{ __('What you will learn') }}</h2>
                <ul class="grid gap-2 sm:grid-cols-2">
                    @foreach(explode("\n", $course->t('what_you_learn')) as $line)
                        @if(trim($line))
                            <li class="flex items-start gap-2 text-surface-300 text-sm">
                                <svg class="h-5 w-5 shrink-0 text-brand-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                {{ trim($line) }}
                            </li>
                        @endif
                    @endforeach
                </ul>
            </div>
        @endif

        @if($course->t('prerequisites'))
            <div class="glass-card p-6 sm:p-8">
                <h2 class="font-display text-xl font-bold text-white mb-4">{{ __('Prerequisites') }}</h2>
                <p class="text-surface-300 whitespace-pre-line">{{ $course->t('prerequisites') }}</p>
            </div>
        @endif

        <div class="glass-card p-6 sm:p-8">
            <h2 class="font-display text-xl font-bold text-white mb-4">{{ __('Course content') }}</h2>
            <div class="space-y-4">
                @foreach($course->chapters as $chIdx => $chapter)
                    <details class="group border border-surface-800 rounded-xl" @if($chIdx === 0) open @endif>
                        <summary class="flex items-center justify-between gap-3 cursor-pointer list-none px-4 py-3 select-none">
                            <div class="flex items-center gap-3 min-w-0">
                                <span class="text-xs font-mono text-surface-500">{{ str_pad($chIdx + 1, 2, '0', STR_PAD_LEFT) }}</span>
                                <div class="min-w-0">
                                    <p class="font-semibold text-white truncate">{{ $chapter->t('title') }}</p>
                                    <p class="text-xs text-surface-400">{{ trans_choice('{1} :count lesson|[2,*] :count lessons', $chapter->materials->count(), ['count' => $chapter->materials->count()]) }}</p>
                                </div>
                            </div>
                            <svg class="h-4 w-4 text-surface-400 transition group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </summary>
                        <ul class="border-t border-surface-800">
                            @foreach($chapter->materials as $mat)
                                @php
                                    $accessible = $enrollment || $mat->is_free_preview;
                                    $done = in_array($mat->id, $completedIds);
                                @endphp
                                <li class="px-4 py-3 flex items-center gap-3 text-sm border-b border-surface-800/50 last:border-0">
                                    @if($done)
                                        <svg class="h-4 w-4 text-emerald-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                    @elseif($mat->type === 'video')
                                        <svg class="h-4 w-4 text-surface-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    @elseif($mat->type === 'pdf')
                                        <svg class="h-4 w-4 text-surface-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    @else
                                        <svg class="h-4 w-4 text-surface-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                    @endif
                                    @if($accessible)
                                        <a href="{{ route('elearning.material', [$course->slug, $mat->id]) }}" class="flex-1 text-surface-200 hover:text-brand-400 truncate">{{ $mat->t('title') }}</a>
                                    @else
                                        <span class="flex-1 text-surface-500 truncate">{{ $mat->t('title') }}</span>
                                    @endif
                                    @if($mat->is_free_preview && ! $enrollment)
                                        <span class="text-[10px] uppercase tracking-wider font-semibold px-2 py-0.5 rounded-md bg-emerald-600/15 text-emerald-300">{{ __('Preview') }}</span>
                                    @elseif(! $accessible)
                                        <svg class="h-4 w-4 text-surface-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                    @endif
                                    @if($mat->duration_minutes > 0)
                                        <span class="text-xs text-surface-500 shrink-0">{{ $mat->duration_minutes }}m</span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </details>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Sidebar: enroll/progress card --}}
    <aside class="lg:col-span-1">
        <div class="glass-card p-6 sticky top-24">
            <p class="text-3xl font-display font-extrabold text-brand-400">{{ $course->price_display }}</p>

            @if($enrollment)
                <div class="mt-5 space-y-4">
                    <div>
                        <div class="flex items-center justify-between text-xs text-surface-400 mb-1.5">
                            <span>{{ __('Your progress') }}</span>
                            <span class="font-semibold text-white">{{ $enrollment->progress_pct }}%</span>
                        </div>
                        <div class="h-2 w-full rounded-full bg-surface-800 overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-brand-500 to-brand-700" style="width: {{ $enrollment->progress_pct }}%"></div>
                        </div>
                    </div>

                    @php
                        $firstMaterial = $course->chapters->flatMap->materials->first();
                    @endphp
                    @if($firstMaterial)
                        <a href="{{ route('elearning.material', [$course->slug, $firstMaterial->id]) }}" class="btn-brand w-full text-center">
                            {{ $enrollment->progress_pct > 0 ? __('Continue learning') : __('Start course') }}
                        </a>
                    @endif

                    @if($course->hasQuiz() && $enrollment->progress_pct >= 100)
                        <a href="{{ route('elearning.quiz', $course->slug) }}" class="btn-ghost w-full text-center">{{ __('Take final quiz') }}</a>
                    @endif

                    @if($hasCertificate)
                        <a href="{{ route('certificate.show', $hasCertificate->certificate_number) }}" class="block w-full rounded-xl bg-emerald-600/15 border border-emerald-500/40 px-4 py-2.5 text-sm text-emerald-300 text-center font-semibold hover:bg-emerald-600/25">
                            {{ __('View your certificate') }} →
                        </a>
                    @endif
                </div>
            @else
                <form action="{{ route('elearning.enroll', $course->slug) }}" method="POST" class="mt-5">
                    @csrf
                    <button class="btn-brand w-full">
                        {{ $course->is_free ? __('Enroll for free') : __('Enroll now') }}
                    </button>
                </form>
                @auth
                    @if(! auth()->user()->hasRole('student'))
                        <p class="mt-3 text-xs text-amber-400">{{ __('Only student accounts can enroll.') }}</p>
                    @endif
                @else
                    <p class="mt-3 text-xs text-surface-400 text-center">{{ __('Login or sign up to enroll.') }}</p>
                @endauth
            @endif

            <ul class="mt-6 space-y-2.5 text-sm text-surface-300 border-t border-surface-800 pt-5">
                <li class="flex items-center gap-2"><svg class="h-4 w-4 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> {{ trans_choice('{1} :count day access|[2,*] :count days access', $course->duration_days, ['count' => $course->duration_days]) }}</li>
                <li class="flex items-center gap-2"><svg class="h-4 w-4 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/></svg> {{ trans_choice('{1} :count lesson|[2,*] :count lessons', $course->materials_count, ['count' => $course->materials_count]) }}</li>
                @if($course->hasQuiz())
                    <li class="flex items-center gap-2"><svg class="h-4 w-4 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg> {{ __('Includes final quiz') }}</li>
                @endif
                <li class="flex items-center gap-2"><svg class="h-4 w-4 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/></svg> {{ __('Certificate of completion') }}</li>
            </ul>
        </div>
    </aside>
</section>

@if($related->isNotEmpty())
    <section class="mx-auto max-w-7xl px-6 pb-20">
        <h2 class="font-display text-2xl font-bold text-white mb-5">{{ __('Related courses') }}</h2>
        <div class="grid gap-4 md:grid-cols-3">
            @foreach($related as $r)
                <a href="{{ route('elearning.show', $r->slug) }}" class="glass-card p-5 block hover:border-brand-500/50 transition group">
                    <h3 class="font-display font-bold text-white group-hover:text-brand-400">{{ $r->t('title') }}</h3>
                    <p class="mt-2 text-xs text-surface-400">{{ __('level.'.$r->level) }} · {{ $r->price_display }}</p>
                </a>
            @endforeach
        </div>
    </section>
@endif
@endsection
