@extends('layouts.app')

@php
    $values = [
        ['title' => __('Integrity'),        'desc' => __('We are transparent with every cost, every step, every promise — no hidden surprises.')],
        ['title' => __('Professionalism'),  'desc' => __('Our mentors and partners are vetted to meet Japanese workplace standards.')],
        ['title' => __('Care'),             'desc' => __('We accompany students through every milestone, from first kana to first paycheck.')],
        ['title' => __('Growth'),           'desc' => __('We believe in lifelong learning — even after placement, you stay part of the community.')],
    ];

    $stats = [
        ['value' => config('passion.stats.students'),  'label' => __('Trained Students')],
        ['value' => config('passion.stats.workers'),   'label' => __('Workers placed in Japan')],
        ['value' => config('passion.stats.companies'), 'label' => __('Partner Companies')],
        ['value' => '8+',                              'label' => __('Years of Experience')],
    ];
@endphp

@section('title', __('About Us') . ' — Passion Japan Indonesia')
@section('og_title', __('About Passion Japan Indonesia'))
@section('og_description', __('Learn how Passion Japan Indonesia helps Indonesian talent build successful careers in Japan.'))

@section('content')

{{-- HERO --}}
<section class="relative overflow-hidden">
    <div class="pointer-events-none absolute -top-32 left-1/2 -translate-x-1/2 h-[36rem] w-[36rem] rounded-full bg-brand-700/25 blur-3xl"></div>
    <div class="relative mx-auto max-w-7xl px-6 pt-16 pb-12">
        <p class="text-xs uppercase tracking-wider text-brand-400 font-semibold">{{ __('About Us') }}</p>
        <h1 class="mt-3 font-display text-4xl sm:text-5xl lg:text-6xl font-extrabold text-white leading-tight max-w-4xl">
            {{ __('Bridging Indonesian talent with Japanese opportunity.') }}
        </h1>
        <p class="mt-6 max-w-2xl text-lg text-surface-300 leading-relaxed">
            {{ __('Passion Japan Indonesia is a career-and-education platform that prepares Indonesian workers and students for life and work in Japan — from language and culture training, to job placement and post-arrival support.') }}
        </p>
    </div>
</section>

{{-- MISSION + VISION --}}
<section class="py-16 bg-surface-900/40">
    <div class="mx-auto max-w-7xl px-6 grid gap-8 md:grid-cols-2">
        <div class="glass-card p-8">
            <p class="text-xs uppercase tracking-wider text-brand-400 font-semibold">{{ __('Our Mission') }}</p>
            <h2 class="mt-2 font-display text-2xl font-bold text-white">{{ __('Open the path to Japan, end-to-end.') }}</h2>
            <p class="mt-4 text-surface-300 leading-relaxed">
                {{ __('We support every step — from the first Japanese lesson, through interview prep and visa paperwork, to the moment your student lands at Narita. Nobody walks alone.') }}
            </p>
        </div>
        <div class="glass-card p-8">
            <p class="text-xs uppercase tracking-wider text-brand-400 font-semibold">{{ __('Our Vision') }}</p>
            <h2 class="mt-2 font-display text-2xl font-bold text-white">{{ __('Indonesia & Japan: a stronger partnership through people.') }}</h2>
            <p class="mt-4 text-surface-300 leading-relaxed">
                {{ __('We want Indonesia to be the most trusted source of skilled, culturally-fluent talent for Japanese companies — and Japan to be a first-class career destination for ambitious Indonesians.') }}
            </p>
        </div>
    </div>
</section>

{{-- VALUES --}}
<section class="py-16">
    <div class="mx-auto max-w-7xl px-6">
        <div class="text-center max-w-2xl mx-auto">
            <p class="text-xs uppercase tracking-wider text-brand-400 font-semibold">{{ __('Our Values') }}</p>
            <h2 class="mt-2 font-display text-3xl font-bold text-white">{{ __('What we stand for') }}</h2>
        </div>
        <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach($values as $v)
                <div class="glass-card p-6">
                    <h3 class="font-display text-lg font-semibold text-white">{{ $v['title'] }}</h3>
                    <p class="mt-2 text-sm text-surface-400 leading-relaxed">{{ $v['desc'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- STATS --}}
<section class="py-16 bg-surface-900/40">
    <div class="mx-auto max-w-5xl px-6">
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4 text-center">
            @foreach($stats as $s)
                <div class="rounded-2xl border border-surface-700/60 bg-surface-900/40 p-6">
                    <p class="font-display text-3xl font-bold text-white">{{ $s['value'] }}</p>
                    <p class="mt-1 text-xs text-surface-400 uppercase tracking-wider">{{ $s['label'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- OFFICES --}}
<section class="py-16">
    <div class="mx-auto max-w-7xl px-6">
        <div class="text-center max-w-2xl mx-auto">
            <p class="text-xs uppercase tracking-wider text-brand-400 font-semibold">{{ __('Our Offices') }}</p>
            <h2 class="mt-2 font-display text-3xl font-bold text-white">{{ __('Where to find us') }}</h2>
        </div>
        <div class="mt-10 grid gap-4 md:grid-cols-3">
            @foreach(config('passion.contact.offices') as $office)
                <div class="glass-card p-6">
                    <div class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-brand-600/20 text-brand-400">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <h3 class="mt-4 font-display text-lg font-semibold text-white">{{ $office['city'] }}</h3>
                    <p class="text-xs text-surface-400">{{ $office['country'] }}</p>
                    <p class="mt-3 text-sm text-surface-300">{{ $office['address'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- CTA --}}
<section class="py-20">
    <div class="mx-auto max-w-5xl px-6">
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-brand-700 via-brand-800 to-surface-900 p-10 lg:p-14">
            <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_20%_20%,rgba(255,255,255,0.08),transparent_40%)]"></div>
            <div class="relative flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div>
                    <h2 class="font-display text-3xl lg:text-4xl font-bold text-white max-w-xl">{{ __('Have questions? Reach out.') }}</h2>
                    <p class="mt-3 text-brand-100 max-w-xl">{{ __('Drop us a message and our team will reply within 24 hours.') }}</p>
                </div>
                <a href="{{ route('contact') }}" class="inline-flex shrink-0 items-center gap-2 px-7 py-3.5 rounded-xl bg-white text-brand-700 font-semibold hover:bg-brand-50 transition shadow-xl">
                    {{ __('Contact us') }}
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3 10a.75.75 0 01.75-.75h10.638L10.23 5.29a.75.75 0 111.04-1.08l5.5 5.25a.75.75 0 010 1.08l-5.5 5.25a.75.75 0 11-1.04-1.08l4.158-3.96H3.75A.75.75 0 013 10z" clip-rule="evenodd"/></svg>
                </a>
            </div>
        </div>
    </div>
</section>

@endsection
