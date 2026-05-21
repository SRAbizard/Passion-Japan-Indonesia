@props([
    'eyebrow'  => null,   // small label above the title (e.g. "E-Learning")
    'title'    => null,
    'subtitle' => null,
    'petals'   => 10,     // sakura petal density
    'minH'     => 'min-h-[42vh]', // tweak per page if you want it shorter
])

{{--
    Reusable hero band for internal pages.

    Visual layers (back → front):
      1. Dawn-sky radial gradient (warm Japanese sunrise)
      2. Mt Fuji silhouette SVG, low-opacity, centred at the bottom
      3. Sakura branch curl in the top-right corner
      4. Falling sakura petals (count tunable via $petals)
      5. Bottom fade-out to the page surface so the body content
         doesn't get a hard edge against the hero

    Pass eyebrow / title / subtitle as slots or as attributes — both work.
--}}
<section {{ $attributes->class([
    'pj-page-hero relative isolate overflow-hidden',
    $minH,
]) }}>
    {{-- Layer 1: gradient sky --}}
    <div class="absolute inset-0 -z-10 pj-page-hero-sky"></div>

    {{-- Layer 2: Mt Fuji silhouette --}}
    <svg class="absolute inset-x-0 bottom-0 -z-10 w-full h-[32%] opacity-60 pointer-events-none"
         viewBox="0 0 1440 240" preserveAspectRatio="xMidYEnd slice" xmlns="http://www.w3.org/2000/svg">
        <defs>
            <linearGradient id="pj-hero-hills" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0" stop-color="#3a1530" stop-opacity="0.7"/>
                <stop offset="1" stop-color="#1a0a18" stop-opacity="0.95"/>
            </linearGradient>
            <linearGradient id="pj-hero-fuji" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0" stop-color="#22183a" stop-opacity="0.95"/>
                <stop offset="1" stop-color="#0a0612" stop-opacity="1"/>
            </linearGradient>
            <linearGradient id="pj-hero-snow" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0" stop-color="#fce4ec" stop-opacity="0.9"/>
                <stop offset="1" stop-color="#fce4ec" stop-opacity="0"/>
            </linearGradient>
        </defs>
        <path d="M0 190 L 220 130 L 380 175 L 580 120 L 780 165 L 980 115 L 1200 160 L 1440 130 L 1440 240 L 0 240 Z" fill="url(#pj-hero-hills)"/>
        <path d="M 520 240 L 720 50 L 920 240 Z" fill="url(#pj-hero-fuji)"/>
        <path d="M 660 130 Q 690 95 720 50 Q 750 95 780 130 Q 765 142 750 128 Q 735 145 720 130 Q 705 145 690 128 Q 675 142 660 130 Z" fill="url(#pj-hero-snow)"/>
    </svg>

    {{-- Layer 3: top-right sakura branch --}}
    <svg class="absolute -top-4 -right-6 w-72 h-44 opacity-80 pointer-events-none -z-10"
         viewBox="0 0 320 200" xmlns="http://www.w3.org/2000/svg">
        <g>
            <path d="M320 30 Q 240 50 180 100 T 40 180" stroke="#5b3a1d" stroke-width="3.5" fill="none" stroke-linecap="round"/>
            <path d="M280 60 Q 250 100 210 120" stroke="#5b3a1d" stroke-width="2.5" fill="none" stroke-linecap="round"/>
            <path d="M200 90 Q 160 80 120 90" stroke="#5b3a1d" stroke-width="2" fill="none" stroke-linecap="round"/>
            @foreach([[300, 45], [265, 70], [225, 110], [180, 100], [140, 88], [95, 130], [115, 95], [60, 165]] as $blob)
                <g transform="translate({{ $blob[0] }} {{ $blob[1] }})">
                    <g fill="#f48fb1">
                        <ellipse cx="0" cy="-4" rx="3" ry="5" transform="rotate(0)"/>
                        <ellipse cx="0" cy="-4" rx="3" ry="5" transform="rotate(72)"/>
                        <ellipse cx="0" cy="-4" rx="3" ry="5" transform="rotate(144)"/>
                        <ellipse cx="0" cy="-4" rx="3" ry="5" transform="rotate(216)"/>
                        <ellipse cx="0" cy="-4" rx="3" ry="5" transform="rotate(288)"/>
                    </g>
                    <circle cx="0" cy="0" r="1.5" fill="#fbbf24"/>
                </g>
            @endforeach
        </g>
    </svg>

    {{-- Layer 4: falling petals --}}
    <x-jp.sakura-petals :count="$petals" />

    {{-- Layer 5: bottom fade so the hero blends into the page surface --}}
    <div class="absolute inset-x-0 bottom-0 h-32 -z-10 bg-gradient-to-b from-transparent via-surface-950/40 to-surface-950"></div>

    {{-- Centred copy --}}
    <div class="relative mx-auto max-w-7xl px-6 pt-14 pb-12 sm:pt-20 sm:pb-16">
        @if($eyebrow)
            <p class="text-xs uppercase tracking-[0.25em] text-brand-300 font-semibold">{{ $eyebrow }}</p>
        @endif
        @if($title)
            <h1 class="mt-3 font-display text-3xl sm:text-5xl font-extrabold text-white leading-tight">
                {{ $title }}
            </h1>
        @endif
        @if($subtitle)
            <p class="mt-4 max-w-2xl text-surface-200">{{ $subtitle }}</p>
        @endif

        {{-- Allow custom slot content below the headline (CTA buttons, search, etc.) --}}
        {{ $slot }}
    </div>
</section>

@once
    @push('head')
        <style>
            .pj-page-hero-sky {
                background:
                    radial-gradient(ellipse at 80% 0%, rgba(244,143,177,0.20) 0%, transparent 55%),
                    radial-gradient(ellipse at 15% 30%, rgba(179,37,16,0.18) 0%, transparent 50%),
                    linear-gradient(180deg,
                        #1a0a18 0%,
                        #2e1432 35%,
                        #4a1828 80%,
                        rgba(74,24,40,0) 100%);
            }
        </style>
    @endpush
@endonce
