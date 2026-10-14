@props([
    'eyebrow'  => null,   // small sticker above the title (e.g. "Careers")
    'title'    => null,
    'subtitle' => null,
    'petals'   => 10,     // sakura petal density
    'minH'     => '',     // optional extra min-height utility
])

{{--
    Reusable hero band for internal pages, in the "Misi ke Jepang" style:
    washi paper, a hinomaru sun with a hard ink shadow, a Fuji line
    drawing, falling sakura, and a chunky title. Pass eyebrow / title /
    subtitle as attributes; anything in the slot renders under the title.
--}}
<section {{ $attributes->class(['pj-page-hero', $minH]) }}>
    <div class="pj-sun" aria-hidden="true"></div>
    <svg class="pj-fuji" viewBox="0 0 560 170" aria-hidden="true">
        <path d="M0 170 L190 52 Q214 34 232 33 L296 33 Q316 34 340 52 L560 170 Z" fill="#2B4795" stroke="var(--pj-line)" stroke-width="3" stroke-linejoin="round"/>
        <path d="M206 46 Q220 34 234 33 L294 33 Q308 34 322 46 L306 58 L292 47 L278 61 L265 47 L251 60 L236 48 Z" fill="#FFFAF2" stroke="var(--pj-line)" stroke-width="3" stroke-linejoin="round"/>
    </svg>

    <x-jp.sakura-petals :count="$petals" />

    <div class="relative mx-auto max-w-7xl px-6 pt-10 pb-14 sm:pt-16 sm:pb-20">
        @if($eyebrow)
            <span class="pj-sticker">{{ $eyebrow }}</span>
        @endif
        @if($title)
            <h1 class="mt-5 font-display text-4xl sm:text-6xl leading-[1.02] max-w-3xl text-balance">
                {{ $title }}
            </h1>
        @endif
        @if($subtitle)
            <p class="mt-5 max-w-2xl text-lg pj-lede">{{ $subtitle }}</p>
        @endif

        {{ $slot }}
    </div>
</section>
