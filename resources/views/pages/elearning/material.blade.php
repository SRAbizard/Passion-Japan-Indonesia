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

<section class="mx-auto max-w-7xl px-6 pb-12 grid gap-6 lg:grid-cols-[300px_1fr]">
    @include('pages.elearning.partials.curriculum-sidebar')

    <div>
        <div class="glass-card p-6 sm:p-8">
            <p class="text-xs text-surface-400">
                @if($material->code) <span class="font-mono">{{ $material->code }}</span> · @endif
                {{ $material->chapter->t('title') }}
            </p>
            <h1 class="mt-1 font-display text-2xl sm:text-3xl font-extrabold text-white">{{ $material->t('title') }}</h1>

            <div class="mt-6">
                @if($material->type === 'video' && $material->video_url)
                    <div class="aspect-video w-full rounded-xl overflow-hidden bg-surface-900">
                        <iframe src="{{ $material->video_url }}" class="h-full w-full" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                    </div>
                @elseif($material->type === 'embed' && $material->embed_url)
                    <div class="aspect-video w-full rounded-xl overflow-hidden bg-surface-900">
                        <iframe src="{{ $material->embed_url }}" class="h-full w-full" frameborder="0" allowfullscreen allow="fullscreen *"></iframe>
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
                        @php $prevUrl = $prev->kind === 'quiz' ? route('elearning.quiz', [$course->slug, $prev->id]) : route('elearning.material', [$course->slug, $prev->id]); @endphp
                        <a href="{{ $prevUrl }}" class="btn-ghost text-sm inline-flex items-center gap-1">
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
                                {{ __('Complete & Next') }}
                            </button>
                        </form>
                    @elseif($isCompleted && $next)
                        @php $nextUrl = $next->kind === 'quiz' ? route('elearning.quiz', [$course->slug, $next->id]) : route('elearning.material', [$course->slug, $next->id]); @endphp
                        <a href="{{ $nextUrl }}" class="btn-brand text-sm inline-flex items-center gap-1">
                            {{ __('Next') }}
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    @elseif($isCompleted)
                        <span class="inline-flex items-center gap-2 text-sm text-emerald-400 font-semibold">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            {{ __('Completed') }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
