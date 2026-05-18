{{--
    Curriculum sidebar (ArkaLearn/Dicoding-style).
    Required: $course, $enrollment, $currentKind ('material'|'quiz'), $currentId.
--}}
@php
    $user = auth()->user();
@endphp
<aside class="lg:sticky lg:top-24 lg:self-start lg:max-h-[calc(100vh-7rem)] lg:overflow-y-auto">
    <div class="glass-card p-4">
        <p class="font-display font-bold text-white">{{ $course->t('title') }}</p>
        @if($enrollment)
            <div class="mt-3">
                <div class="flex items-center justify-between text-xs text-surface-400 mb-1.5">
                    <span>{{ __('Progress') }}</span>
                    <span class="font-semibold text-white">{{ $enrollment->progress_pct }}%</span>
                </div>
                <div class="h-1.5 w-full rounded-full bg-surface-800 overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-brand-500 to-brand-700" style="width: {{ $enrollment->progress_pct }}%"></div>
                </div>
            </div>
        @endif

        <div class="mt-5 space-y-2">
            @foreach($course->chapters as $chapter)
                @php
                    $progress = $chapter->progressFor($user);
                    $items = $chapter->itemsFor($user);
                    $open = $items->contains(fn ($it) => $it->kind === $currentKind && $it->id === $currentId);
                @endphp
                <details class="group rounded-xl border border-surface-800/70 bg-surface-900/40" {{ $open ? 'open' : '' }}>
                    <summary class="cursor-pointer list-none px-3 py-2.5 flex items-center justify-between gap-2 text-sm">
                        <span class="font-semibold text-white truncate">{{ $chapter->t('title') }}</span>
                        <span class="flex items-center gap-2 shrink-0">
                            <span class="text-xs font-mono text-surface-400">{{ $progress['done'] }}/{{ $progress['total'] }}</span>
                            <svg class="h-4 w-4 text-surface-500 transition group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </span>
                    </summary>
                    <ul class="px-2 pb-2 space-y-1">
                        @foreach($items as $it)
                            @php
                                $isCurrent = $it->kind === $currentKind && $it->id === $currentId;
                                $href = $it->kind === 'quiz'
                                    ? route('elearning.quiz',     [$course->slug, $it->id])
                                    : route('elearning.material', [$course->slug, $it->id]);
                            @endphp
                            <li>
                                @if($it->locked && ! $isCurrent)
                                    <div class="flex items-start gap-2 px-3 py-2 rounded-lg text-xs text-surface-500 cursor-not-allowed select-none" title="{{ __('Finish the previous lesson first') }}">
                                        <svg class="h-3.5 w-3.5 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0-2.21 1.79-4 4-4s4 1.79 4 4v2M5 11h14a2 2 0 012 2v7a2 2 0 01-2 2H5a2 2 0 01-2-2v-7a2 2 0 012-2z"/></svg>
                                        <div class="min-w-0 flex-1">
                                            @if($it->code)
                                                <span class="font-mono text-[10px] text-surface-600">{{ $it->code }}</span>
                                            @endif
                                            <p class="truncate">{{ $it->title }}</p>
                                            <p class="mt-0.5 text-[10px] uppercase tracking-wider text-surface-600">{{ $it->badge }}</p>
                                        </div>
                                    </div>
                                @else
                                    <a href="{{ $href }}"
                                       class="flex items-start gap-2 px-3 py-2 rounded-lg text-xs transition
                                           @if($isCurrent) bg-brand-600/15 border border-brand-500/40 text-white font-semibold
                                           @elseif($it->done) text-surface-300 hover:bg-surface-800/50
                                           @else text-surface-300 hover:bg-surface-800/50 @endif">
                                        @if($it->done)
                                            <svg class="h-3.5 w-3.5 mt-0.5 text-emerald-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                        @else
                                            <span class="h-3.5 w-3.5 mt-0.5 rounded-full border border-surface-600 shrink-0 @if($isCurrent) border-brand-400 @endif"></span>
                                        @endif
                                        <div class="min-w-0 flex-1">
                                            @if($it->code)
                                                <span class="font-mono text-[10px] text-surface-500">{{ $it->code }}</span>
                                            @endif
                                            <p class="truncate">{{ $it->title }}</p>
                                            <p class="mt-0.5 text-[10px] uppercase tracking-wider text-surface-500">{{ $it->badge }}</p>
                                        </div>
                                    </a>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </details>
            @endforeach

            @if($course->finalExam)
                @php
                    $passed = $course->finalExam->isPassedBy($user);
                    $isFinal = $currentKind === 'quiz' && $currentId === $course->finalExam->id;
                @endphp
                <a href="{{ route('elearning.quiz', [$course->slug, $course->finalExam->id]) }}"
                   class="flex items-center gap-2 px-3 py-2.5 rounded-xl text-sm transition border
                       @if($isFinal) bg-brand-600/15 border-brand-500/40 text-white font-semibold
                       @elseif($passed) border-emerald-500/30 bg-emerald-600/10 text-emerald-300
                       @else border-surface-800/70 bg-surface-900/40 text-surface-300 hover:bg-surface-800/50 @endif">
                    <svg class="h-4 w-4 shrink-0 {{ $passed ? 'text-emerald-400' : 'text-brand-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    <span class="font-semibold">{{ __('Final Exam') }}</span>
                    @if($passed)
                        <span class="ml-auto text-[10px] uppercase tracking-wider font-bold">{{ __('Passed') }}</span>
                    @endif
                </a>
            @endif
        </div>
    </div>
</aside>
