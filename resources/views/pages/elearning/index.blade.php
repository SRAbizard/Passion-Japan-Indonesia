@extends('layouts.app')
@section('title', __('E-Learning') . ' — Passion Japan Indonesia')
@section('og_title', __('Learn Japanese & job skills — Passion Japan Indonesia'))

@php
    $activeFilterCount = collect(['category','level','q','free'])->filter(fn ($k) => filled(request($k)))->count();
@endphp

@section('content')
<section class="relative overflow-hidden">
    <div class="pointer-events-none absolute -top-32 left-1/2 -translate-x-1/2 h-[28rem] w-[28rem] rounded-full bg-brand-700/20 blur-3xl"></div>
    <x-jp.sakura-petals :count="10" />
    <div class="relative mx-auto max-w-7xl px-6 pt-14 pb-10">
        <p class="text-xs uppercase tracking-wider text-brand-400 font-semibold">{{ __('E-Learning') }}</p>
        <h1 class="mt-3 font-display text-4xl sm:text-5xl font-extrabold text-white">{{ __('Master Japanese on your own pace') }}</h1>
        <p class="mt-4 max-w-2xl text-surface-300">{{ __('Bite-sized lessons, instructor-led courses, and quizzes that prepare you for life and work in Japan.') }}</p>
    </div>
</section>

<section class="mx-auto max-w-7xl px-6 mb-10">
    <form method="GET" id="elearning-filter-form">
        <details class="group glass-card" @if($activeFilterCount > 0) open @endif>
            <summary class="flex items-center justify-between gap-3 cursor-pointer list-none px-5 py-4 select-none">
                <div class="flex items-center gap-3 min-w-0 flex-1">
                    <div class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-brand-600/20 text-brand-400 shrink-0">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L14 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 018 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
                    </div>
                    <div class="min-w-0">
                        <p class="font-display font-semibold text-white">{{ __('Filter & Search') }}</p>
                        <p class="text-xs text-surface-400 truncate">
                            @if($activeFilterCount > 0)
                                {{ trans_choice('{1} :count active filter|[2,*] :count active filters', $activeFilterCount, ['count' => $activeFilterCount]) }}
                            @else
                                {{ __('Narrow results by category, level, or keyword.') }}
                            @endif
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    @if($activeFilterCount > 0)
                        <a href="{{ route('elearning.index') }}"
                           class="text-xs text-surface-300 hover:text-brand-400 px-3 py-1.5 rounded-lg border border-surface-700 hover:border-brand-500/50 transition">
                            {{ __('Reset filter') }}
                        </a>
                    @endif
                    <svg class="h-5 w-5 text-surface-400 transition group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </div>
            </summary>

            <div class="border-t border-surface-800/70 px-5 py-5 grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                <label class="block">
                    <span class="text-xs text-surface-400">{{ __('Category') }}</span>
                    <select name="category" class="mt-1.5 w-full rounded-xl border border-surface-700 bg-surface-900/60 px-3 py-2 text-sm text-white focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
                        <option value="">{{ __('All') }}</option>
                        @foreach($categories as $c)
                            <option value="{{ $c->slug }}" @selected(request('category') === $c->slug)>{{ $c->t('name') }} ({{ $c->courses_count }})</option>
                        @endforeach
                    </select>
                </label>
                <label class="block">
                    <span class="text-xs text-surface-400">{{ __('Level') }}</span>
                    <select name="level" class="mt-1.5 w-full rounded-xl border border-surface-700 bg-surface-900/60 px-3 py-2 text-sm text-white focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
                        <option value="">{{ __('All') }}</option>
                        @foreach($levels as $lvl)
                            <option value="{{ $lvl }}" @selected(request('level') === $lvl)>{{ __('level.'.$lvl) }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="block">
                    <span class="text-xs text-surface-400">{{ __('Keyword') }}</span>
                    <input type="search" name="q" value="{{ request('q') }}" placeholder="{{ __('Search…') }}"
                           class="mt-1.5 w-full rounded-xl border border-surface-700 bg-surface-900/60 px-3 py-2 text-sm text-white placeholder-surface-500 focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
                </label>
                <label class="flex items-center gap-2 mt-6">
                    <input type="checkbox" name="free" value="1" @checked(request()->boolean('free'))
                           class="h-4 w-4 rounded border-surface-700 bg-surface-900 text-brand-600 focus:ring-brand-500">
                    <span class="text-sm text-surface-300">{{ __('Free courses only') }}</span>
                </label>
            </div>

            <div class="flex items-center justify-end gap-2 border-t border-surface-800/70 px-5 py-4">
                <a href="{{ route('elearning.index') }}" class="btn-ghost text-sm">{{ __('Reset filter') }}</a>
                <button class="btn-brand text-sm">{{ __('Apply filter') }}</button>
            </div>
        </details>
    </form>
</section>

<section class="mx-auto max-w-7xl px-6 pb-20">
    @if($courses->isEmpty())
        <div class="glass-card p-10 text-center">
            <p class="text-surface-300">{{ __('No courses match your filters.') }}</p>
            @if($activeFilterCount > 0)
                <a href="{{ route('elearning.index') }}" class="mt-4 inline-block btn-ghost text-sm">{{ __('Reset filter') }}</a>
            @endif
        </div>
    @else
        <div class="flex items-baseline justify-between flex-wrap gap-2 mb-5">
            <p class="text-sm text-surface-400">
                {{ trans_choice('{1} :count course found|[2,*] :count courses found', $courses->total(), ['count' => $courses->total()]) }}
            </p>
        </div>
        <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
            @foreach($courses as $course)
                <a href="{{ route('elearning.show', $course->slug) }}" class="glass-card overflow-hidden block hover:border-brand-500/50 transition group flex flex-col">
                    <div class="aspect-video w-full bg-gradient-to-br from-brand-700 to-brand-900 relative">
                        @if($course->thumbnail_url)
                            <img src="{{ $course->thumbnail_url }}" alt="" class="absolute inset-0 h-full w-full object-cover">
                        @else
                            <div class="absolute inset-0 flex items-center justify-center text-white/30">
                                <svg class="h-16 w-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                            </div>
                        @endif
                        @if($course->is_featured)
                            <span class="absolute top-2 right-2 text-xs font-bold px-2 py-1 rounded-md bg-brand-600 text-white">★ {{ __('Featured') }}</span>
                        @endif
                    </div>
                    <div class="p-5 flex-1 flex flex-col">
                        <div class="flex items-center gap-2 flex-wrap mb-3">
                            @if($course->category)
                                <span class="text-[10px] uppercase tracking-wider font-semibold px-2 py-1 rounded-md bg-surface-800 text-surface-300">{{ $course->category->t('name') }}</span>
                            @endif
                            <span class="text-[10px] uppercase tracking-wider font-semibold px-2 py-1 rounded-md bg-brand-600/15 text-brand-300">{{ __('level.'.$course->level) }}</span>
                            @if($course->is_free)
                                <span class="text-[10px] uppercase tracking-wider font-semibold px-2 py-1 rounded-md bg-emerald-600/15 text-emerald-300">{{ __('Free') }}</span>
                            @endif
                        </div>
                        <h2 class="font-display font-bold text-white text-lg leading-tight group-hover:text-brand-400 transition">{{ $course->t('title') }}</h2>
                        @if($course->t('subtitle'))
                            <p class="mt-2 text-sm text-surface-400 line-clamp-2">{{ $course->t('subtitle') }}</p>
                        @endif
                        <div class="mt-auto pt-4 grid grid-cols-2 gap-2 text-xs text-surface-400 border-t border-surface-800/70 mt-4">
                            <span class="flex items-center gap-1.5">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                                {{ trans_choice('{1} :count chapter|[2,*] :count chapters', $course->chapters_count, ['count' => $course->chapters_count]) }}
                            </span>
                            <span class="flex items-center gap-1.5 text-brand-400 font-semibold justify-end">
                                {{ $course->price_display }}
                            </span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-10">{{ $courses->links() }}</div>
    @endif
</section>
@endsection
