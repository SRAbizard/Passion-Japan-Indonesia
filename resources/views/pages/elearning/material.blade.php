@extends('layouts.app')
@section('title', $material->t('title') . ' — ' . $course->t('title'))

@section('content')
<section class="mx-auto max-w-7xl px-6 pt-8 pb-3">
    <a href="{{ route('elearning.show', $course->slug) }}" class="inline-flex items-center gap-1 text-sm text-surface-400 hover:text-brand-400 transition">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        {{ $course->t('title') }}
    </a>
</section>

@if(session('status'))
    <div class="mx-auto max-w-7xl px-6 mb-4">
        <div class="glass-card border-brand-500/40 px-5 py-3 text-sm text-brand-300">{{ session('status') }}</div>
    </div>
@endif

<section class="mx-auto max-w-7xl px-6 pb-12 grid gap-6 lg:grid-cols-[280px_1fr]">
    {{-- Sidebar: chapter/material outline --}}
    <aside class="lg:sticky lg:top-24 lg:self-start lg:max-h-[calc(100vh-7rem)] lg:overflow-y-auto">
        <div class="glass-card p-4">
            @if($enrollment)
                <div class="mb-4">
                    <div class="flex items-center justify-between text-xs text-surface-400 mb-1.5">
                        <span>{{ __('Progress') }}</span>
                        <span class="font-semibold text-white">{{ $enrollment->progress_pct }}%</span>
                    </div>
                    <div class="h-1.5 w-full rounded-full bg-surface-800 overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-brand-500 to-brand-700" style="width: {{ $enrollment->progress_pct }}%"></div>
                    </div>
                </div>
            @endif

            @foreach($course->chapters as $chIdx => $chapter)
                <div class="mb-4">
                    <p class="text-xs font-display font-bold uppercase tracking-wider text-surface-400 mb-2">
                        {{ str_pad($chIdx + 1, 2, '0', STR_PAD_LEFT) }}. {{ $chapter->t('title') }}
                    </p>
                    <ul class="space-y-1">
                        @foreach($chapter->materials as $mat)
                            @php $done = in_array($mat->id, $completedIds); $current = $mat->id === $material->id; @endphp
                            <li>
                                <a href="{{ route('elearning.material', [$course->slug, $mat->id]) }}"
                                   class="flex items-center gap-2 px-2 py-1.5 rounded-lg text-xs transition
                                       @if($current) bg-brand-600/15 text-brand-300 font-semibold
                                       @elseif($done) text-surface-300 hover:bg-surface-800/50
                                       @else text-surface-400 hover:bg-surface-800/50 @endif">
                                    @if($done)
                                        <svg class="h-3.5 w-3.5 text-emerald-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                    @else
                                        <span class="h-3.5 w-3.5 rounded-full border border-surface-600 shrink-0 @if($current) border-brand-400 @endif"></span>
                                    @endif
                                    <span class="truncate">{{ $mat->t('title') }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach

            @if($course->hasQuiz())
                <div class="mt-5 pt-5 border-t border-surface-800">
                    <a href="{{ route('elearning.quiz', $course->slug) }}" class="flex items-center gap-2 px-2 py-2 rounded-lg text-sm text-surface-300 hover:bg-surface-800/50 transition">
                        <svg class="h-4 w-4 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <span class="font-semibold">{{ __('Final quiz') }}</span>
                    </a>
                </div>
            @endif
        </div>
    </aside>

    <div>
        <div class="glass-card p-6 sm:p-8">
            <p class="text-xs text-surface-400">{{ $material->chapter->t('title') }}</p>
            <h1 class="mt-1 font-display text-2xl sm:text-3xl font-extrabold text-white">{{ $material->t('title') }}</h1>

            <div class="mt-6">
                @if($material->type === 'video' && $material->video_url)
                    <div class="aspect-video w-full rounded-xl overflow-hidden bg-surface-900">
                        <iframe src="{{ $material->video_url }}" class="h-full w-full" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                    </div>
                @elseif($material->type === 'pdf' && $material->pdf_url)
                    <div class="aspect-[4/3] w-full rounded-xl overflow-hidden bg-surface-900">
                        <iframe src="{{ $material->pdf_url }}" class="h-full w-full"></iframe>
                    </div>
                    <a href="{{ $material->pdf_url }}" target="_blank" class="mt-3 inline-flex items-center gap-1 text-sm text-brand-400 hover:underline">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        {{ __('Download PDF') }}
                    </a>
                @else
                    <div class="prose prose-invert max-w-none text-surface-300 prose-headings:text-white">
                        {!! $material->t('content') !!}
                    </div>
                @endif
            </div>

            <div class="mt-8 pt-6 border-t border-surface-800 flex items-center justify-between gap-4 flex-wrap">
                <div>
                    @if($prev)
                        <a href="{{ route('elearning.material', [$course->slug, $prev->id]) }}" class="btn-ghost text-sm inline-flex items-center gap-1">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            {{ __('Previous') }}
                        </a>
                    @endif
                </div>

                <div class="flex items-center gap-2">
                    @if($enrollment && ! $isCompleted)
                        <form action="{{ route('elearning.complete', [$course->slug, $material->id]) }}" method="POST">
                            @csrf
                            <button class="btn-brand text-sm inline-flex items-center gap-2">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                {{ __('Mark as complete') }}
                            </button>
                        </form>
                    @elseif($isCompleted)
                        <span class="inline-flex items-center gap-2 text-sm text-emerald-400 font-semibold">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            {{ __('Completed') }}
                        </span>
                    @endif

                    @if($next)
                        <a href="{{ route('elearning.material', [$course->slug, $next->id]) }}" class="btn-ghost text-sm inline-flex items-center gap-1">
                            {{ __('Next') }}
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    @elseif($course->hasQuiz())
                        <a href="{{ route('elearning.quiz', $course->slug) }}" class="btn-brand text-sm inline-flex items-center gap-1">
                            {{ __('Take final quiz') }}
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
