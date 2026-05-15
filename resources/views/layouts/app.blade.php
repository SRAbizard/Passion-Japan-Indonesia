<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="@yield('meta_description', __('Integrated Japan Career, Recruitment, Internship & E-Learning Platform'))">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="canonical" href="{{ url()->current() }}">

    {{-- Theme: apply saved choice BEFORE paint to avoid flash. --}}
    <script>
        (function () {
            try {
                var saved = localStorage.getItem('pj-theme');
                var html  = document.documentElement;
                if (saved === 'light') { html.classList.remove('dark'); html.classList.add('light'); }
                else                    { html.classList.remove('light'); html.classList.add('dark'); }
            } catch (e) {}
        })();
    </script>

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

    {{-- Livewire scripts so wire:navigate can swap pages without a full
         reload. Audio + splash use @persist below so they survive the swap. --}}
    @livewireStyles
</head>
<body class="min-h-screen bg-surface-950 text-surface-100 antialiased font-sans">

{{-- Loading splash screen — shown once per browser session --}}
<div id="pj-splash" class="pj-splash">
    <x-jp.sakura-petals :count="22" />
    <div class="pj-splash-content">
        <div class="pj-splash-logo">
            <img src="{{ asset('images/logo.png') }}" alt="Passion Japan Indonesia"
                 onerror="this.outerHTML='<span style=&quot;color:#b32510;font-weight:bold;font-size:1.5rem&quot;>PJ</span>';">
        </div>
        <p class="pj-splash-name">Passion Japan</p>
        <p class="pj-splash-subname">ようこそ · Welcome</p>
        <div class="pj-splash-bar"><div class="pj-splash-bar-fill"></div></div>
    </div>
</div>

<header class="sticky top-0 z-40 backdrop-blur-md bg-surface-950/80 border-b border-surface-800/70">
    <nav class="mx-auto max-w-7xl flex items-center justify-between gap-4 px-4 sm:px-6 py-3">
        <a href="{{ url('/') }}" class="flex items-center gap-2 shrink-0">
            <img src="{{ asset('images/logo.png') }}"
                 alt="Passion Japan Indonesia"
                 class="h-9 sm:h-10 w-auto"
                 onerror="this.outerHTML='<span class=\'inline-flex h-9 w-9 items-center justify-center rounded-xl bg-brand-600 font-bold text-white\'>PJ</span><span class=\'ml-2 font-display font-semibold text-white\'>Passion Japan Indonesia</span>';">
        </a>

        <div class="hidden lg:flex items-center gap-7 text-sm text-surface-200">
            <a href="{{ url('/') }}" wire:navigate class="hover:text-white transition">{{ __('Home') }}</a>
            <a href="{{ route('job.index') }}" wire:navigate class="hover:text-white transition">{{ __('Job Vacancies') }}</a>
            <a href="{{ route('elearning.index') }}" wire:navigate class="hover:text-white transition">{{ __('E-Learning') }}</a>
            <a href="{{ route('event.index') }}" wire:navigate class="hover:text-white transition">{{ __('Event') }}</a>
            <a href="{{ route('blog.index') }}" wire:navigate class="hover:text-white transition">{{ __('Blog') }}</a>
            <a href="{{ route('about') }}" wire:navigate class="hover:text-white transition">{{ __('About Us') }}</a>
            <a href="{{ route('contact') }}" wire:navigate class="hover:text-white transition">{{ __('Contact') }}</a>
        </div>

        <div class="flex items-center gap-2">
            {{-- Theme toggle (sun/moon) --}}
            <button type="button" id="theme-toggle"
                    title="{{ __('Toggle dark / light mode') }}"
                    aria-label="{{ __('Toggle dark / light mode') }}"
                    class="btn-ghost text-xs px-2.5 py-2 inline-flex items-center justify-center">
                {{-- Sun shown in dark mode (= click to switch to light) --}}
                <svg class="theme-icon-sun h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                {{-- Moon shown in light mode (= click to switch to dark) --}}
                <svg class="theme-icon-moon h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
            </button>

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
                    <a href="{{ url('/') }}" wire:navigate class="px-3 py-2 rounded-lg hover:bg-surface-700/60">{{ __('Home') }}</a>
                    <a href="{{ route('job.index') }}" wire:navigate class="px-3 py-2 rounded-lg hover:bg-surface-700/60">{{ __('Job Vacancies') }}</a>
                    <a href="{{ route('elearning.index') }}" wire:navigate class="px-3 py-2 rounded-lg hover:bg-surface-700/60">{{ __('E-Learning') }}</a>
                    <a href="{{ route('event.index') }}" wire:navigate class="px-3 py-2 rounded-lg hover:bg-surface-700/60">{{ __('Event') }}</a>
                    <a href="{{ route('blog.index') }}" wire:navigate class="px-3 py-2 rounded-lg hover:bg-surface-700/60">{{ __('Blog') }}</a>
                    <a href="{{ route('about') }}" wire:navigate class="px-3 py-2 rounded-lg hover:bg-surface-700/60">{{ __('About Us') }}</a>
                    <a href="{{ route('contact') }}" wire:navigate class="px-3 py-2 rounded-lg hover:bg-surface-700/60">{{ __('Contact') }}</a>
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
                <a href="{{ \App\Support\SiteSettings::social('instagram') }}" target="_blank" rel="noopener" aria-label="Instagram" class="text-surface-400 hover:text-brand-400 transition">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M7.75 2A5.75 5.75 0 002 7.75v8.5A5.75 5.75 0 007.75 22h8.5A5.75 5.75 0 0022 16.25v-8.5A5.75 5.75 0 0016.25 2h-8.5zm0 1.5h8.5a4.25 4.25 0 014.25 4.25v8.5a4.25 4.25 0 01-4.25 4.25h-8.5A4.25 4.25 0 013.5 16.25v-8.5A4.25 4.25 0 017.75 3.5zM17 6a1 1 0 100 2 1 1 0 000-2zm-5 1.5a4.5 4.5 0 100 9 4.5 4.5 0 000-9zm0 1.5a3 3 0 110 6 3 3 0 010-6z"/></svg>
                </a>
                <a href="{{ \App\Support\SiteSettings::social('facebook') }}" target="_blank" rel="noopener" aria-label="Facebook" class="text-surface-400 hover:text-brand-400 transition">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M13.5 21.95V13.5h2.85l.43-3.3h-3.28V8.1c0-.95.27-1.6 1.64-1.6H17V3.55c-.3-.04-1.34-.13-2.55-.13-2.52 0-4.25 1.54-4.25 4.37V10.2H7.35v3.3h2.85v8.45h3.3z"/></svg>
                </a>
                <a href="{{ \App\Support\SiteSettings::social('tiktok') }}" target="_blank" rel="noopener" aria-label="TikTok" class="text-surface-400 hover:text-brand-400 transition">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19.5 8.2a6.7 6.7 0 01-4.2-1.4v6.7a5.4 5.4 0 11-5.4-5.4c.4 0 .8.04 1.1.12v2.7a2.7 2.7 0 102 2.58V2h2.6a4.1 4.1 0 003.9 3.6v2.6z"/></svg>
                </a>
            </div>
        </div>

        <div>
            <h4 class="text-white font-semibold mb-4">{{ __('Quick Links') }}</h4>
            <ul class="space-y-2 text-sm text-surface-400">
                <li><a href="{{ url('/') }}" wire:navigate class="hover:text-brand-400">{{ __('Home') }}</a></li>
                <li><a href="{{ route('job.index') }}" wire:navigate class="hover:text-brand-400">{{ __('Job Vacancies') }}</a></li>
                <li><a href="{{ route('elearning.index') }}" wire:navigate class="hover:text-brand-400">{{ __('E-Learning') }}</a></li>
                <li><a href="{{ route('event.index') }}" wire:navigate class="hover:text-brand-400">{{ __('Event') }}</a></li>
                <li><a href="{{ route('blog.index') }}" wire:navigate class="hover:text-brand-400">{{ __('Blog') }}</a></li>
                <li><a href="{{ route('about') }}" wire:navigate class="hover:text-brand-400">{{ __('About Us') }}</a></li>
                <li><a href="{{ route('contact') }}" wire:navigate class="hover:text-brand-400">{{ __('Contact') }}</a></li>
            </ul>
        </div>

        <div>
            <h4 class="text-white font-semibold mb-4">{{ __('Popular Programs') }}</h4>
            <ul class="space-y-2 text-sm text-surface-400">
                <li><a href="{{ route('job.index', ['visa' => 'tokutei-ginou']) }}" class="hover:text-brand-400">Tokutei Ginou</a></li>
                <li><a href="{{ route('job.index', ['visa' => 'internship']) }}" class="hover:text-brand-400">Internship</a></li>
                <li><a href="{{ route('job.index', ['visa' => 'engineering']) }}" class="hover:text-brand-400">Gijinkoku / Engineer</a></li>
                <li><a href="{{ route('job.index', ['category' => 'driver']) }}" class="hover:text-brand-400">Driver Jepang</a></li>
                <li><a href="{{ route('elearning.index', ['category' => 'jlpt']) }}" class="hover:text-brand-400">JLPT Class</a></li>
            </ul>
        </div>

        <div>
            <h4 class="text-white font-semibold mb-4">{{ __('Contact') }}</h4>
            <ul class="space-y-3 text-sm text-surface-400">
                <li class="flex items-start gap-2">
                    <svg class="h-4 w-4 mt-0.5 text-brand-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l9 6 9-6M3 8v10a2 2 0 002 2h14a2 2 0 002-2V8M3 8l2-2h14l2 2"/></svg>
                    <a href="mailto:{{ \App\Support\SiteSettings::contact('email') }}" class="hover:text-brand-400">{{ \App\Support\SiteSettings::contact('email') }}</a>
                </li>
                <li class="flex items-start gap-2">
                    <svg class="h-4 w-4 mt-0.5 text-brand-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h2.28a1 1 0 01.95.68l1.5 4.5a1 1 0 01-.5 1.21l-1.96.98a11 11 0 005.36 5.36l.98-1.96a1 1 0 011.21-.5l4.5 1.5a1 1 0 01.68.95V19a2 2 0 01-2 2h-1C9.72 21 3 14.28 3 6V5z"/></svg>
                    <a href="tel:{{ str_replace(' ', '', \App\Support\SiteSettings::contact('phone') ?? '') }}" class="hover:text-brand-400">{{ \App\Support\SiteSettings::contact('phone') }}</a>
                </li>
                @foreach(\App\Support\SiteSettings::offices() as $office)
                    <li>
                        <a href="{{ $office['maps_url'] }}" target="_blank" rel="noopener noreferrer"
                           title="{{ __('Open in Google Maps') }}"
                           class="flex items-start gap-2 hover:text-brand-400 transition group">
                            <svg class="h-4 w-4 mt-0.5 text-brand-500 shrink-0 group-hover:scale-110 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span>{{ trim(($office['city'] ?? '').', '.($office['country'] ?? ''), ', ') }}</span>
                            <svg class="h-3 w-3 mt-1 opacity-40 group-hover:opacity-100 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        </a>
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

@persist('audio-player')
    <x-audio-player />
@endpersist

@livewireScripts

<script>
    (function () {
        // ─── Theme toggle ────────────────────────────────────────────
        var btn = document.getElementById('theme-toggle');
        if (btn) {
            btn.addEventListener('click', function () {
                var html = document.documentElement;
                var goingLight = html.classList.contains('dark');
                html.classList.toggle('dark', ! goingLight);
                html.classList.toggle('light', goingLight);
                try { localStorage.setItem('pj-theme', goingLight ? 'light' : 'dark'); } catch (e) {}
            });
        }

        // ─── Loading splash (once per session) ───────────────────────
        var splash = document.getElementById('pj-splash');
        if (splash) {
            var seen = false;
            try { seen = sessionStorage.getItem('pj-splash-seen') === '1'; } catch (e) {}

            if (seen) {
                splash.classList.add('is-hidden');
                setTimeout(function () { splash.remove(); }, 100);
            } else {
                var hide = function () {
                    splash.classList.add('is-hidden');
                    try { sessionStorage.setItem('pj-splash-seen', '1'); } catch (e) {}
                    setTimeout(function () { splash.remove(); }, 700);
                };
                var minDisplay = 2100;
                var startTime = performance.now();
                var done = function () {
                    var elapsed = performance.now() - startTime;
                    setTimeout(hide, Math.max(0, minDisplay - elapsed));
                };
                if (document.readyState === 'complete') done();
                else window.addEventListener('load', done);
            }
        }

        // ─── Reveal-on-scroll (IntersectionObserver) ─────────────────
        // Wrapped so it can be called again after wire:navigate page swaps
        function pjBindReveal () {
            if (! ('IntersectionObserver' in window)) {
                document.querySelectorAll('.reveal').forEach(function (el) { el.classList.add('is-visible'); });
                return;
            }
            var io = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        io.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
            document.querySelectorAll('.reveal:not(.is-visible)').forEach(function (el) { io.observe(el); });
        }
        pjBindReveal();

        // ─── Section snap scroll (homepage only) ─────────────────────
        // Toggle .snap-sections on <html>+<body> based on path. Re-runs
        // after every Livewire wire:navigate page swap so the snap mode
        // doesn't leak across pages.
        function pjApplySnap () {
            var isHome = location.pathname === '/' || location.pathname === '';
            document.documentElement.classList.toggle('snap-sections', isHome);
            document.body.classList.toggle('snap-sections', isHome);
        }
        pjApplySnap();
        document.addEventListener('livewire:navigated', function () {
            pjApplySnap();
            pjBindReveal();
        });
    })();
</script>

</body>
</html>
