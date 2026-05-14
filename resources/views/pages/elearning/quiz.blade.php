@extends('layouts.app')
@section('title', __('Final quiz') . ' — ' . $course->t('title'))

@section('content')
<section class="mx-auto max-w-4xl px-6 pt-10 pb-6">
    <a href="{{ route('elearning.show', $course->slug) }}" class="inline-flex items-center gap-1 text-sm text-surface-400 hover:text-brand-400 transition">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        {{ $course->t('title') }}
    </a>
    <h1 class="mt-3 font-display text-3xl font-extrabold text-white">{{ __('Final Quiz') }}</h1>
    <p class="mt-2 text-surface-300">{{ $course->quiz->t('title') }}</p>
    @if($course->quiz->t('description'))
        <p class="mt-2 text-sm text-surface-400">{{ $course->quiz->t('description') }}</p>
    @endif
</section>

@if(session('status'))
    <div class="mx-auto max-w-4xl px-6 mb-4">
        <div class="glass-card border-brand-500/40 px-5 py-3 text-sm text-brand-300">{{ session('status') }}</div>
    </div>
@endif

<section class="mx-auto max-w-4xl px-6 pb-12 grid gap-6">
    <div class="glass-card p-5 grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
        <div>
            <p class="text-xs uppercase tracking-wider text-surface-400">{{ __('Pass score') }}</p>
            <p class="mt-1 font-display font-bold text-white">{{ $course->quiz->passing_score }}%</p>
        </div>
        <div>
            <p class="text-xs uppercase tracking-wider text-surface-400">{{ __('Questions') }}</p>
            <p class="mt-1 font-display font-bold text-white">{{ $course->quiz->questions->count() }}</p>
        </div>
        <div>
            <p class="text-xs uppercase tracking-wider text-surface-400">{{ __('Attempts') }}</p>
            <p class="mt-1 font-display font-bold text-white">{{ $attempts->count() }}{{ $course->quiz->max_attempts > 0 ? ' / '.$course->quiz->max_attempts : '' }}</p>
        </div>
        <div>
            <p class="text-xs uppercase tracking-wider text-surface-400">{{ __('Best score') }}</p>
            <p class="mt-1 font-display font-bold text-white">{{ $bestAttempt?->score ?? '—' }}%</p>
        </div>
    </div>

    @php
        $alreadyPassed = $attempts->contains(fn ($a) => $a->passed);
        $reachedMax = $course->quiz->max_attempts > 0 && $attempts->count() >= $course->quiz->max_attempts;
    @endphp

    @if($alreadyPassed)
        <div class="glass-card p-8 text-center">
            <div class="inline-flex h-16 w-16 items-center justify-center rounded-full bg-emerald-500/20 text-emerald-400 mb-4">
                <svg class="h-8 w-8" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            </div>
            <h2 class="font-display text-2xl font-bold text-white">{{ __('You already passed this quiz!') }}</h2>
            <p class="mt-2 text-surface-300">{{ __('Best score: :score%', ['score' => $bestAttempt->score]) }}</p>
            @php
                $cert = \App\Models\Certificate::where('user_id', auth()->id())->where('course_id', $course->id)->first();
            @endphp
            @if($cert)
                <a href="{{ route('certificate.show', $cert->certificate_number) }}" class="mt-5 btn-brand inline-flex items-center gap-2">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/></svg>
                    {{ __('View certificate') }}
                </a>
            @endif
        </div>
    @elseif($reachedMax)
        <div class="glass-card p-8 text-center">
            <p class="text-surface-300">{{ __('You have reached the maximum number of attempts.') }}</p>
        </div>
    @else
        <form action="{{ route('elearning.quiz.submit', $course->slug) }}" method="POST" class="glass-card p-6 sm:p-8 space-y-8">
            @csrf
            @foreach($course->quiz->questions as $idx => $q)
                <div>
                    <p class="font-semibold text-white">
                        <span class="text-brand-400">{{ $idx + 1 }}.</span> {{ $q->t('question') }}
                    </p>
                    <div class="mt-3 space-y-2">
                        @foreach($q->choices ?? [] as $choice)
                            @php $key = $choice['key'] ?? null; @endphp
                            @if($key)
                                <label class="flex items-start gap-3 px-4 py-3 rounded-xl border border-surface-700 hover:border-brand-500/50 cursor-pointer transition has-[:checked]:bg-brand-600/10 has-[:checked]:border-brand-500/60">
                                    <input type="radio" name="answers[{{ $q->id }}]" value="{{ $key }}" required
                                           class="mt-0.5 h-4 w-4 text-brand-600 border-surface-700 bg-surface-900 focus:ring-brand-500">
                                    <span class="text-sm text-surface-200">
                                        <span class="font-mono text-surface-500 mr-1">{{ strtoupper($key) }}.</span>
                                        {{ $q->choiceText($key) }}
                                    </span>
                                </label>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endforeach

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-surface-800">
                <a href="{{ route('elearning.show', $course->slug) }}" class="btn-ghost text-sm">{{ __('Cancel') }}</a>
                <button class="btn-brand">{{ __('Submit answers') }}</button>
            </div>
        </form>
    @endif

    @if($attempts->isNotEmpty())
        <div class="glass-card p-6">
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
</section>
@endsection
