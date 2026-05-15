<div class="text-center mb-6">
    {{-- Sakura branch SVG decoration above the form --}}
    <svg class="mx-auto h-12 w-auto" viewBox="0 0 120 48" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
        <g fill="none">
            {{-- Branch curve --}}
            <path d="M0 38 Q 30 30 60 32 T 120 24" stroke="#8b5e3c" stroke-width="1.5" stroke-linecap="round" opacity="0.65"/>
            {{-- Sakura blossoms (5-petal) at intervals --}}
            @foreach([15, 35, 60, 85, 105] as $cx)
                @php $cy = $cx === 60 ? 28 : ($cx % 30 === 5 ? 30 : 32); @endphp
                <g transform="translate({{ $cx }} {{ $cy }})">
                    <g fill="#f48fb1" opacity="0.9">
                        <ellipse cx="0" cy="-3" rx="2" ry="3.5" transform="rotate(0)"/>
                        <ellipse cx="0" cy="-3" rx="2" ry="3.5" transform="rotate(72)"/>
                        <ellipse cx="0" cy="-3" rx="2" ry="3.5" transform="rotate(144)"/>
                        <ellipse cx="0" cy="-3" rx="2" ry="3.5" transform="rotate(216)"/>
                        <ellipse cx="0" cy="-3" rx="2" ry="3.5" transform="rotate(288)"/>
                    </g>
                    <circle cx="0" cy="0" r="1" fill="#fbbf24"/>
                </g>
            @endforeach
        </g>
    </svg>
    <p class="mt-2 text-xs uppercase tracking-[0.3em] text-primary-600 dark:text-primary-400 font-bold">
        ようこそ
    </p>
</div>
