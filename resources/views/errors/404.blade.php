@extends('layouts.app')

@section('title', '404 · ' . __('Page not found'))

@section('content')
<section class="min-h-[60vh] flex items-center">
    <div class="mx-auto max-w-3xl px-6 py-20 text-center">
        <p class="font-display text-7xl sm:text-9xl font-extrabold text-brand-500 leading-none">404</p>
        <h1 class="mt-6 font-display text-3xl sm:text-4xl font-bold text-white">{{ __('Page not found') }}</h1>
        <p class="mt-4 text-surface-300 max-w-xl mx-auto">
            {{ __('The page you are looking for doesn\'t exist or has been moved. Try one of these links instead:') }}
        </p>

        <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
            <a href="{{ url('/') }}" class="btn-brand">{{ __('Back to home') }}</a>
            <a href="{{ url('/about') }}" class="btn-ghost">{{ __('About Us') }}</a>
            <a href="{{ url('/contact') }}" class="btn-ghost">{{ __('Contact') }}</a>
        </div>

        <p class="mt-12 text-xs text-surface-500">{{ __('If you think this is a mistake, please let us know via the contact form.') }}</p>
    </div>
</section>
@endsection
