@extends('layouts.app')
@section('title', __('Gallery') . ' — Passion Japan Indonesia')
@section('og_title', __('Gallery — Passion Japan Indonesia'))
@section('og_description', __('Photos and videos from our trainings, events, and student journeys.'))

@section('content')
<x-jp.page-hero
    :eyebrow="__('Documentation')"
    :title="__('Moments from our journey')"
    :subtitle="__('Photos and videos from our trainings, events, and student journeys.')"
    :petals="10" />

<section class="mx-auto max-w-7xl px-6 pb-20">
    @if($items->isEmpty())
        <div class="glass-card p-12 text-center">
            <svg class="h-12 w-12 mx-auto text-surface-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <p class="mt-4 text-surface-300">{{ __('No gallery items published yet.') }}</p>
        </div>
    @else
        {{-- Lightbox-style grid. Click an item → modal-ish reveal via <dialog>. --}}
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @foreach($items as $item)
                <button type="button"
                        data-gallery-trigger
                        data-id="{{ $item->id }}"
                        class="group relative aspect-square overflow-hidden rounded-2xl bg-surface-900 border border-surface-800 hover:border-brand-500/50 transition text-left">
                    @if($item->thumbnail_url)
                        <img src="{{ $item->thumbnail_url }}" alt="{{ $item->t('title') ?? '' }}"
                             loading="lazy"
                             class="absolute inset-0 h-full w-full object-cover group-hover:scale-105 transition duration-500">
                    @else
                        <div class="absolute inset-0 flex items-center justify-center text-surface-600">
                            <svg class="h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        </div>
                    @endif

                    @if($item->type !== 'image')
                        {{-- Play overlay for video/youtube --}}
                        <div class="absolute inset-0 flex items-center justify-center bg-black/30 group-hover:bg-black/50 transition">
                            <span class="inline-flex h-14 w-14 items-center justify-center rounded-full bg-brand-600/95 text-white shadow-xl group-hover:scale-110 transition">
                                <svg class="h-6 w-6 ml-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                            </span>
                        </div>
                    @endif

                    @if($item->t('title'))
                        <div class="absolute bottom-0 inset-x-0 p-3 bg-gradient-to-t from-black/80 via-black/40 to-transparent">
                            <p class="text-sm font-semibold text-white truncate">{{ $item->t('title') }}</p>
                            @if($item->taken_at)
                                <p class="text-[10px] text-surface-300 mt-0.5">{{ $item->taken_at->translatedFormat('d M Y') }}</p>
                            @endif
                        </div>
                    @endif

                    {{-- Hidden JSON payload — read by the lightbox script --}}
                    @php
                        $payload = [
                            'id'        => $item->id,
                            'title'     => $item->t('title'),
                            'caption'   => $item->t('caption'),
                            'type'      => $item->type,
                            'image'     => $item->image_url,
                            'video'     => $item->video_url,
                            'youtube'   => $item->youtube_embed_url,
                            'taken_at'  => $item->taken_at?->translatedFormat('d M Y'),
                        ];
                    @endphp
                    <script type="application/json" class="gallery-payload">{!! json_encode($payload) !!}</script>
                </button>
            @endforeach
        </div>

        <div class="mt-10">{{ $items->links() }}</div>
    @endif
</section>

{{-- Lightbox modal --}}
<dialog id="pj-gallery-lightbox" class="bg-transparent backdrop:bg-black/85 backdrop:backdrop-blur-sm p-0 m-0 w-full h-full max-w-full max-h-full">
    <div class="relative w-full h-full flex items-center justify-center p-4 sm:p-10">
        <button type="button" data-close
                class="absolute top-4 right-4 z-10 inline-flex h-10 w-10 items-center justify-center rounded-full bg-surface-900/80 text-white hover:bg-surface-800 backdrop-blur">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
        <div id="pj-gallery-content" class="w-full max-w-5xl max-h-[90vh] flex flex-col items-center justify-center"></div>
    </div>
</dialog>

@push('head')
<style>
    #pj-gallery-lightbox[open] { display: flex; }
    #pj-gallery-lightbox::backdrop { cursor: pointer; }
</style>
@endpush

<script>
(function () {
    var dialog  = document.getElementById('pj-gallery-lightbox');
    var content = document.getElementById('pj-gallery-content');
    if (! dialog || ! content) return;

    document.querySelectorAll('[data-gallery-trigger]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var payloadEl = btn.querySelector('.gallery-payload');
            if (! payloadEl) return;
            var data;
            try { data = JSON.parse(payloadEl.textContent); } catch (e) { return; }

            var html = '';
            if (data.type === 'image' && data.image) {
                html = '<img src="' + data.image + '" alt="" class="max-h-[80vh] w-auto rounded-2xl shadow-2xl">';
            } else if (data.type === 'video' && data.video) {
                html = '<video src="' + data.video + '" controls autoplay class="max-h-[80vh] w-auto rounded-2xl shadow-2xl bg-black"></video>';
            } else if (data.type === 'youtube' && data.youtube) {
                html = '<div class="w-full aspect-video max-w-4xl"><iframe src="' + data.youtube + '?autoplay=1" class="h-full w-full rounded-2xl" allow="autoplay; encrypted-media" allowfullscreen></iframe></div>';
            }

            if (data.title || data.caption || data.taken_at) {
                html += '<div class="mt-4 max-w-3xl text-center text-white">';
                if (data.title)    html += '<p class="font-display text-xl font-bold">' + data.title + '</p>';
                if (data.taken_at) html += '<p class="mt-1 text-xs text-surface-300">' + data.taken_at + '</p>';
                if (data.caption)  html += '<p class="mt-3 text-sm text-surface-200">' + data.caption + '</p>';
                html += '</div>';
            }

            content.innerHTML = html;
            dialog.showModal();
        });
    });

    // Click backdrop to close
    dialog.addEventListener('click', function (e) {
        if (e.target === dialog) dialog.close();
    });

    // Close button
    dialog.querySelectorAll('[data-close]').forEach(function (b) {
        b.addEventListener('click', function () { dialog.close(); });
    });

    // Stop video when closing
    dialog.addEventListener('close', function () {
        content.innerHTML = '';
    });
})();
</script>
@endsection
