@extends('layouts.app')
@section('title', $quiz->t('title') . ' — ' . $course->t('title'))

@section('content')
<section class="mx-auto max-w-4xl px-6 pt-8 pb-3">
    <a href="{{ route('elearning.quiz', [$course->slug, $quiz->id]) }}" class="inline-flex items-center gap-1 text-sm text-surface-400 hover:text-brand-400 transition">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        {{ $quiz->t('title') }}
    </a>
    <h1 class="mt-3 font-display text-2xl sm:text-3xl font-extrabold text-white">
        {{ $quiz->t('title') }}
    </h1>
    @if($quiz->t('subtitle'))
        <p class="mt-1 text-surface-300">{{ $quiz->t('subtitle') }}</p>
    @endif
</section>

<section class="mx-auto max-w-4xl px-6 pb-12">
    <form action="{{ route('elearning.quiz.submit', [$course->slug, $quiz->id]) }}" method="POST"
          class="glass-card p-6 sm:p-8 space-y-8"
          @if($quiz->time_limit_minutes > 0)
              x-data="{ remaining: {{ $quiz->time_limit_minutes * 60 }} }"
              x-init="
                  let timer = setInterval(() => {
                      remaining--;
                      if (remaining <= 0) { clearInterval(timer); $el.submit(); }
                  }, 1000);
              "
          @endif>
        @csrf

        @if($quiz->time_limit_minutes > 0)
            <div class="sticky top-4 z-10 -mt-4 -mx-4 mb-4 bg-surface-950/90 backdrop-blur border border-brand-500/40 rounded-xl px-4 py-2 flex items-center justify-between text-sm">
                <span class="text-surface-300">{{ __('Time remaining') }}:</span>
                <span class="font-mono font-bold text-brand-400" x-text="Math.floor(remaining/60).toString().padStart(2,'0') + ':' + (remaining%60).toString().padStart(2,'0')"></span>
            </div>
        @endif

        @foreach($quiz->questions as $idx => $q)
            <div>
                <p class="font-semibold text-white">
                    <span class="text-brand-400">{{ $idx + 1 }}.</span> {{ $q->t('question') }}
                </p>

                @if($q->image_path)
                    <img src="{{ asset('storage/'.$q->image_path) }}" alt=""
                         class="mt-3 max-h-64 rounded-lg border border-surface-800 mx-auto">
                @endif

                @if($q->audio_path)
                    <div class="mt-3 rounded-lg border border-surface-800 p-3 bg-surface-900/50">
                        <audio controls class="w-full"
                               @if($q->max_audio_plays > 0)
                                   x-data="{ played: 0, max: {{ $q->max_audio_plays }} }"
                                   @ended="played++; if (played >= max) { $event.target.controls = false; $event.target.style.opacity = 0.4; }"
                               @endif>
                            <source src="{{ asset('storage/'.$q->audio_path) }}">
                        </audio>
                        @if($q->max_audio_plays > 0)
                            <p class="mt-2 text-[10px] uppercase tracking-wider text-surface-500">
                                {{ __('Max plays') }}: {{ $q->max_audio_plays }}
                            </p>
                        @endif
                    </div>
                @endif

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
            <a href="{{ route('elearning.quiz', [$course->slug, $quiz->id]) }}" class="btn-ghost text-sm">{{ __('Cancel') }}</a>
            <button class="btn-brand">{{ __('Submit answers') }}</button>
        </div>
    </form>
</section>
@endsection
