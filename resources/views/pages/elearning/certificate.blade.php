@extends('layouts.app')
@section('title', __('Certificate') . ' — ' . $certificate->course->t('title'))

@section('content')
<section class="mx-auto max-w-4xl px-6 pt-12 pb-12">
    <div class="text-center mb-8">
        <p class="text-xs uppercase tracking-wider text-brand-400 font-semibold">{{ __('Verified Certificate') }}</p>
        <h1 class="mt-3 font-display text-4xl font-extrabold text-white">{{ __('Certificate of Completion') }}</h1>
    </div>

    <div class="glass-card p-8 sm:p-12 text-center border-brand-500/30">
        <div class="text-brand-400 mb-6">
            <svg class="mx-auto h-20 w-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
        </div>

        <p class="text-sm uppercase tracking-wider text-surface-400">{{ __('This is to certify that') }}</p>
        <h2 class="mt-3 font-display text-3xl sm:text-4xl font-extrabold text-white">{{ $certificate->user->name }}</h2>
        <p class="mt-5 text-sm uppercase tracking-wider text-surface-400">{{ __('has successfully completed the course') }}</p>
        <h3 class="mt-3 font-display text-xl sm:text-2xl font-bold text-brand-400">{{ $certificate->course->t('title') }}</h3>

        @if($certificate->final_score)
            <p class="mt-5 text-surface-300">{{ __('Final score: :score%', ['score' => $certificate->final_score]) }}</p>
        @endif

        <div class="mt-8 pt-8 border-t border-surface-800 grid grid-cols-2 gap-4 text-sm text-surface-400">
            <div>
                <p class="text-xs uppercase tracking-wider">{{ __('Issued on') }}</p>
                <p class="mt-1 font-semibold text-white">{{ $certificate->issued_at->format('d F Y') }}</p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-wider">{{ __('Certificate number') }}</p>
                <p class="mt-1 font-mono text-white">{{ $certificate->certificate_number }}</p>
            </div>
        </div>

        <div class="mt-8">
            <a href="{{ route('certificate.download', $certificate->certificate_number) }}" class="btn-brand inline-flex items-center gap-2">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                {{ __('Download PDF') }}
            </a>
        </div>
    </div>

    <p class="mt-6 text-center text-xs text-surface-500">
        {{ __('Verify this certificate at') }} {{ url('/certificate/'.$certificate->certificate_number) }}
    </p>
</section>
@endsection
