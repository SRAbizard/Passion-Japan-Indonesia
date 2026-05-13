<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="@yield('meta_description', __('Integrated Japan Career, Recruitment, Internship & E-Learning Platform'))">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="canonical" href="{{ url()->current() }}">

    {{-- hreflang alternates for SEO multilingual --}}
    @foreach(config('passion.locales') as $code => $meta)
        <link rel="alternate" hreflang="{{ $code }}" href="{{ url()->current() }}?lang={{ $code }}">
    @endforeach
    <link rel="alternate" hreflang="x-default" href="{{ url()->current() }}">

    <title>@yield('title', __('Passion Japan Indonesia'))</title>

    {{-- Open Graph / Twitter --}}
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:site_name" content="Passion Japan Indonesia">
    <meta property="og:locale" content="{{ str_replace('-', '_', app()->getLocale()) }}">
    <meta property="og:title" content="@yield('og_title', __('Passion Japan Indonesia'))">
    <meta property="og:description" content="@yield('og_description', __('Integrated Japan Career, Recruitment, Internship & E-Learning Platform'))">
    <meta property="og:image" content="{{ asset('images/logo.png') }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta name="twitter:card" content="summary_large_image">

    @stack('head')

    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-surface-950 text-surface-100 antialiased font-sans">

<header class="sticky top-0 z-40 backdrop-blur-md bg-surface-950/80 border-b border-surface-800/70">
    <nav class="mx-auto max-w-7xl flex items-center justify-between gap-4 px-4 sm:px-6 py-3">
        <a href="{{ url('/') }}" class="flex items-center gap-2 shrink-0">
            <img src="{{ asset('images/logo.png') }}"
                 alt="Passion Japan Indonesia"
                 class="h-9 sm:h-10 w-auto"
                 onerror="this.outerHTML='<span class=\'inline-flex h-9 w-9 items-center justify-center rounded-xl bg-brand-600 font-bold text-white\'>PJ</span><span class=\'ml-2 font-display font-semibold text-white\'>Passion Japan Indonesia</span>';">
        </a>

        <div class="hidden lg:flex items-center gap-7 text-sm text-surface-200">
            <a href="{{ url('/') }}" class="hover:text-white transition">{{ __('Home') }}</a>
            <a href="{{ route('job.index') }}" class="hover:text-white transition">{{ __('Job Vacancies') }}</a>
            <a href="{{ url('/#learning') }}" class="hover:text-white transition">{{ __('E-Learning') }}</a>
            <a href="{{ route('event.index') }}" class="hover:text-white transition">{{ __('Event') }}</a>
            <a href="{{ route('blog.index') }}" class="hover:text-white transition">{{ __('Blog') }}</a>
            <a href="{{ route('about') }}" class="hover:text-white transition">{{ __('About Us') }}</a>
            <a href="{{ route('contact') }}" class="hover:text-white transition">{{ __('Contact') }}</a>
        </div>

        <div class="flex items-center gap-2">
            {{-- Language switcher --}}
            <div class="relative group">
                <button type="button" class="btn-ghost text-xs px-3 py-2 gap-2">
                    <span>{{ config('passion.locales.'.app()->getLocale().'.flag') }}</span>
                    <span class="uppercase">{{ app()->getLocale() }}</span>
                    <svg class="h-3 w-3 opacity-60" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                </button>
                <div class="absolute right-0 mt-2 hidden group-hover:block group-focus-within:block min-w-[200px] glass-card p-1">
                    @foreach(config('passion.locales') as $code => $meta)
                        <a href="{{ route('locale.switch', $code) }}"
                           class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm hover:bg-surface-700/60 {{ app()->getLocale() === $code ? 'text-brand-400' : 'text-surface-100' }}">
                            <span>{{ $meta['flag'] }}</span>
                            <span>{{ $meta['native'] }}</span>
                        </a>
                    @endforeach
                </div>
            </div>

            @auth
                @php($user = auth()->user())
                @if ($user->hasAnyRole(['superadmin', 'admin']))
                    <a href="/admin" class="btn-ghost text-sm hidden sm:inline-flex">{{ __('Admin Panel') }}</a>
                @else
                    <a href="/dashboard" class="btn-ghost text-sm hidden sm:inline-flex">{{ __('Student Dashboard') }}</a>
                @endif
                <form method="POST" action="/dashboard/logout" class="hidden sm:inline-flex">
                    @csrf
                    <button type="submit" class="btn-ghost text-sm">{{ __('Logout') }}</button>
                </form>
            @else
                <a href="/dashboard/login" class="btn-ghost text-sm hidden sm:inline-flex">{{ __('Login') }}</a>
                <a href="/dashboard/register" class="btn-brand text-sm">{{ __('Get Started') }}</a>
            @endauth

            {{-- Mobile menu toggle (no JS — uses :target via summary/details) --}}
            <details class="lg:hidden relative">
                <summary class="list-none btn-ghost px-2.5 py-2 cursor-pointer">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </summary>
                <div class="absolute right-0 mt-2 min-w-[220px] glass-card p-2 flex flex-col text-sm">
                    <a href="{{ url('/') }}" class="px-3 py-2 rounded-lg hover:bg-surface-700/60">{{ __('Home') }}</a>
                    <a href="{{ route('job.index') }}" class="px-3 py-2 rounded-lg hover:bg-surface-700/60">{{ __('Job Vacancies') }}</a>
                    <a href="{{ url('/#learning') }}" class="px-3 py-2 rounded-lg hover:bg-surface-700/60">{{ __('E-Learning') }}</a>
                    <a href="{{ route('event.index') }}" class="px-3 py-2 rounded-lg hover:bg-surface-700/60">{{ __('Event') }}</a>
                    <a href="{{ route('blog.index') }}" class="px-3 py-2 rounded-lg hover:bg-surface-700/60">{{ __('Blog') }}</a>
                    <a href="{{ route('about') }}" class="px-3 py-2 rounded-lg hover:bg-surface-700/60">{{ __('About Us') }}</a>
                    <a href="{{ route('contact') }}" class="px-3 py-2 rounded-lg hover:bg-surface-700/60">{{ __('Contact') }}</a>
                </div>
            </details>
        </div>
    </nav>
</header>

<main>
    @yield('content')
</main>

<footer class="mt-24 border-t border-surface-800/70 bg-surface-950">
    <div class="mx-auto max-w-7xl px-6 py-14 grid gap-10 md:grid-cols-2 lg:grid-cols-4">
        <div>
            <img src="{{ asset('images/logo.png') }}" alt="Passion Japan Indonesia" class="h-10 w-auto mb-4"
                 onerror="this.outerHTML='<div class=\'font-display font-bold text-white text-lg mb-4\'>Passion Japan Indonesia</div>';">
            <p class="text-sm text-surface-400 leading-relaxed">
                {{ __('We accompany you from language training and work skills, to official job placement in Japan.') }}
            </p>
            <div class="mt-5 flex items-center gap-3">
                <a href="{{ config('passion.contact.instagram') }}" target="_blank" rel="noopener" aria-label="Instagram" class="text-surface-400 hover:text-brand-400 transition">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M7.75 2A5.75 5.75 0 002 7.75v8.5A5.75 5.75 0 007.75 22h8.5A5.75 5.75 0 0022 16.25v-8.5A5.75 5.75 0 0016.25 2h-8.5zm0 1.5h8.5a4.25 4.25 0 014.25 4.25v8.5a4.25 4.25 0 01-4.25 4.25h-8.5A4.25 4.25 0 013.5 16.25v-8.5A4.25 4.25 0 017.75 3.5zM17 6a1 1 0 100 2 1 1 0 000-2zm-5 1.5a4.5 4.5 0 100 9 4.5 4.5 0 000-9zm0 1.5a3 3 0 110 6 3 3 0 010-6z"/></svg>
                </a>
                <a href="{{ config('passion.contact.facebook') }}" target="_blank" rel="noopener" aria-label="Facebook" class="text-surface-400 hover:text-brand-400 transition">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M13.5 21.95V13.5h2.85l.43-3.3h-3.28V8.1c0-.95.27-1.6 1.64-1.6H17V3.55c-.3-.04-1.34-.13-2.55-.13-2.52 0-4.25 1.54-4.25 4.37V10.2H7.35v3.3h2.85v8.45h3.3z"/></svg>
                </a>
                <a href="{{ config('passion.contact.tiktok') }}" target="_blank" rel="noopener" aria-label="TikTok" class="text-surface-400 hover:text-brand-400 transition">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19.5 8.2a6.7 6.7 0 01-4.2-1.4v6.7a5.4 5.4 0 11-5.4-5.4c.4 0 .8.04 1.1.12v2.7a2.7 2.7 0 102 2.58V2h2.6a4.1 4.1 0 003.9 3.6v2.6z"/></svg>
                </a>
            </div>
        </div>

        <div>
            <h4 class="text-white font-semibold mb-4">{{ __('Quick Links') }}</h4>
            <ul class="space-y-2 text-sm text-surface-400">
                <li><a href="{{ url('/') }}" class="hover:text-brand-400">{{ __('Home') }}</a></li>
                <li><a href="{{ route('job.index') }}" class="hover:text-brand-400">{{ __('Job Vacancies') }}</a></li>
                <li><a href="{{ url('/#learning') }}" class="hover:text-brand-400">{{ __('E-Learning') }}</a></li>
                <li><a href="{{ route('event.index') }}" class="hover:text-brand-400">{{ __('Event') }}</a></li>
                <li><a href="{{ route('blog.index') }}" class="hover:text-brand-400">{{ __('Blog') }}</a></li>
                <li><a href="{{ route('about') }}" class="hover:text-brand-400">{{ __('About Us') }}</a></li>
                <li><a href="{{ route('contact') }}" class="hover:text-brand-400">{{ __('Contact') }}</a></li>
            </ul>
        </div>

        <div>
            <h4 class="text-white font-semibold mb-4">{{ __('Popular Programs') }}</h4>
            <ul class="space-y-2 text-sm text-surface-400">
                <li><a href="#" class="hover:text-brand-400">Tokutei Ginou</a></li>
                <li><a href="#" class="hover:text-brand-400">Internship</a></li>
                <li><a href="#" class="hover:text-brand-400">Driver Jepang</a></li>
                <li><a href="#" class="hover:text-brand-400">Engineer Jepang</a></li>
                <li><a href="#" class="hover:text-brand-400">JLPT Class</a></li>
            </ul>
        </div>

        <div>
            <h4 class="text-white font-semibold mb-4">{{ __('Contact') }}</h4>
            <ul class="space-y-3 text-sm text-surface-400">
                <li class="flex items-start gap-2">
                    <svg class="h-4 w-4 mt-0.5 text-brand-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l9 6 9-6M3 8v10a2 2 0 002 2h14a2 2 0 002-2V8M3 8l2-2h14l2 2"/></svg>
                    <a href="mailto:{{ config('passion.contact.email') }}" class="hover:text-brand-400">{{ config('passion.contact.email') }}</a>
                </li>
                <li class="flex items-start gap-2">
                    <svg class="h-4 w-4 mt-0.5 text-brand-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h2.28a1 1 0 01.95.68l1.5 4.5a1 1 0 01-.5 1.21l-1.96.98a11 11 0 005.36 5.36l.98-1.96a1 1 0 011.21-.5l4.5 1.5a1 1 0 01.68.95V19a2 2 0 01-2 2h-1C9.72 21 3 14.28 3 6V5z"/></svg>
                    <a href="tel:{{ str_replace(' ', '', config('passion.contact.phone')) }}" class="hover:text-brand-400">{{ config('passion.contact.phone') }}</a>
                </li>
                @foreach(config('passion.contact.offices') as $office)
                    <li class="flex items-start gap-2">
                        <svg class="h-4 w-4 mt-0.5 text-brand-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span>{{ $office['city'] }}, {{ $office['country'] }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    <div class="border-t border-surface-800/70">
        <div class="mx-auto max-w-7xl px-6 py-5 text-xs text-surface-500 flex flex-col md:flex-row md:items-center md:justify-between gap-2">
            <p>&copy; {{ date('Y') }} Passion Japan Indonesia. {{ __('All rights reserved.') }}</p>
            <p>Laravel {{ \Illuminate\Foundation\Application::VERSION }} · PHP {{ PHP_VERSION }}</p>
        </div>
    </div>
</footer>

<x-whatsapp-float />

</body>
</html>
