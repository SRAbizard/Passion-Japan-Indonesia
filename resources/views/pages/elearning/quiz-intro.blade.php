@extends('layouts.app')
@section('title', $quiz->t('title') . ' — ' . $course->t('title'))

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
        <div class="glass-card p-8 sm:p-12 text-center">
            <p class="text-xs uppercase tracking-wider text-brand-400 font-semibold">{{ __('Quiz') }}</p>
            @if($quiz->code)
                <p class="mt-2 font-mono text-xs text-surface-500">{{ $quiz->code }}</p>
            @endif
            <h1 class="mt-2 font-display text-3xl sm:text-4xl font-extrabold text-white">{{ $quiz->t('title') }}</h1>
            @if($quiz->t('subtitle'))
                <p class="mt-2 text-surface-300">{{ $quiz->t('subtitle') }}</p>
            @endif

            {{-- 3 stats chips (Dicoding style) --}}
            <div class="mt-8 flex flex-wrap items-center justify-center gap-6 text-sm">
                <div class="flex items-center gap-2 text-surface-300">
                    <svg class="h-5 w-5 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M12 22a10 10 0 110-20 10 10 0 010 20z"/></svg>
                    <span>{{ __('Total questions') }}:</span>
                    <span class="font-display font-bold text-white">{{ $quiz->questions->count() }}</span>
                </div>
                <div class="flex items-center gap-2 text-surface-300">
                    <svg class="h-5 w-5 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093M12 17h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ __('Pass score') }}:</span>
                    <span class="font-display font-bold text-white">{{ $quiz->passing_score }}%</span>
                </div>
                <div class="flex items-center gap-2 text-surface-300">
                    <svg class="h-5 w-5 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ __('Time limit') }}:</span>
                    <span class="font-display font-bold text-white">
                        {{ $quiz->time_limit_minutes > 0 ? $quiz->time_limit_minutes.'m' : __('No limit') }}
                    </span>
                </div>
            </div>

            {{-- Action --}}
            <div class="mt-8">
                @php $alreadyPassed = $attempts->contains(fn ($a) => $a->passed); @endphp

                @if($alreadyPassed && $quiz->isFinalExam() && $certificate)
                    <div class="inline-flex flex-col items-center gap-3">
                        <p class="text-emerald-400 font-semibold">{{ __('You already passed!') }} ({{ $bestAttempt->score }}%)</p>
                        <a href="{{ route('certificate.show', $certificate->certificate_number) }}" class="btn-brand inline-flex items-center gap-2">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/></svg>
                            {{ __('View certificate') }}
                        </a>
                    </div>
                @elseif($alreadyPassed)
                    <div class="inline-flex flex-col items-center gap-3">
                        <p class="text-emerald-400 font-semibold">{{ __('You already passed!') }} ({{ $bestAttempt->score }}%)</p>
                        <a href="{{ route('elearning.quiz.take', [$course->slug, $quiz->id]) }}" class="btn-ghost text-sm">
                            {{ __('Retake quiz') }}
                        </a>
                    </div>
                @elseif($reachedMax)
                    <p class="text-surface-400">{{ __('You have reached the maximum number of attempts.') }}</p>
                @else
                    <a href="{{ route('elearning.quiz.take', [$course->slug, $quiz->id]) }}" class="btn-brand inline-flex items-center gap-2 text-base px-8 py-3">
                        {{ __('Start Quiz') }}
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                @endif
            </div>

            {{-- Stats — attempts taken --}}
            @if($attempts->isNotEmpty())
                <div class="mt-8 pt-6 border-t border-surface-800 text-xs text-surface-400">
                    {{ __('Attempts so far') }}:
                    <span class="font-semibold text-white">{{ $attempts->count() }}{{ $quiz->max_attempts > 0 ? ' / '.$quiz->max_attempts : '' }}</span>
                    · {{ __('Best score') }}:
                    <span class="font-semibold text-white">{{ $bestAttempt?->score ?? '—' }}%</span>
                </div>
            @endif
        </div>

        {{-- Attempt history --}}
        @if($attempts->isNotEmpty())
            <div class="glass-card p-6 mt-6">
                <h3 class="font-display font-bold text-white mb-3">{{ __('Attempt history') }}</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="text-xs uppercase tracking-wider text-surface-400">
                            <tr class="border-b border-surface-800">
                                <th class="text-left py-2 pr-4">#</th>
                                <th class="text-left py-2 pr-4">{{ __('Date') }}</th>
                                <th class="text-left py-2 pr-4">{{ __('Score') }}</th>
                                <th class="text-left py-2">{{ __('Result') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($attempts as $i => $att)
                                <tr class="border-b border-surface-800/50 last:border-0 text-surface-300">
                                    <td class="py-2 pr-4">{{ $attempts->count() - $i }}</td>
                                    <td class="py-2 pr-4">{{ $att->finished_at?->format('d M Y H:i') ?? '—' }}</td>
                                    <td class="py-2 pr-4 font-semibold {{ $att->passed ? 'text-emerald-400' : 'text-surface-300' }}">{{ $att->score }}%</td>
                                    <td class="py-2">
                                        @if($att->passed)
                                            <span class="text-xs uppercase tracking-wider font-bold px-2 py-1 rounded-md bg-emerald-600/15 text-emerald-300">{{ __('Passed') }}</span>
                                        @else
                                            <span class="text-xs uppercase tracking-wider font-bold px-2 py-1 rounded-md bg-surface-800 text-surface-400">{{ __('Failed') }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- Prev/Next nav --}}
        <div class="mt-6 flex items-center justify-between gap-4">
            @if($prev)
                @php $prevUrl = $prev->kind === 'quiz' ? route('elearning.quiz', [$course->slug, $prev->id]) : route('elearning.material', [$course->slug, $prev->id]); @endphp
                <a href="{{ $prevUrl }}" class="btn-ghost text-sm inline-flex items-center gap-1">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    {{ __('Previous') }}
                </a>
            @else
                <span></span>
            @endif
            @if($next)
                @php $nextUrl = $next->kind === 'quiz' ? route('elearning.quiz', [$course->slug, $next->id]) : route('elearning.material', [$course->slug, $next->id]); @endphp
                <a href="{{ $nextUrl }}" class="btn-ghost text-sm inline-flex items-center gap-1">
                    {{ __('Next') }}
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            @endif
        </div>
    </div>
</section>
@endsection
