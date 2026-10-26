@props([
    'eyebrow'  => null,   // small sticker above the title (e.g. "Careers")
    'title'    => null,
    'subtitle' => null,
    'petals'   => 10,     // sakura petal density
    'minH'     => '',     // optional extra min-height utility
])

{{--
    Reusable hero band for internal pages, in the "Misi ke Jepang" style:
    washi paper, a Japan scene (sun, Fuji, clouds, pagoda, sakura branch),
    falling sakura, and a chunky title. Pass eyebrow / title /
    subtitle as attributes; anything in the slot renders under the title.
--}}
<section {{ $attributes->class(['pj-page-hero', $minH]) }}>
    <x-jp.hero-art />

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
