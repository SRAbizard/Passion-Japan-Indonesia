{{-- Japanese-themed full-viewport background for Filament login pages.
     Layered (back → front): dawn sky · big rising sun · distant Fuji ·
     seigaiha wave pattern at the bottom · sakura branches arching from
     the sides · soft scrim. --}}

<div class="pj-login-bg" aria-hidden="true">
    <div class="pj-login-bg-sky"></div>

    {{-- Large red rising sun, centered slightly above middle --}}
    <div class="pj-login-bg-sun"></div>

    {{-- Distant smaller mountains + Mt Fuji silhouette --}}
    <svg class="pj-login-bg-mountains" viewBox="0 0 1440 360" preserveAspectRatio="xMidYEnd slice" xmlns="http://www.w3.org/2000/svg">
        <defs>
            <linearGradient id="m-bg" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0" stop-color="#3a1530" stop-opacity="0.65"/>
                <stop offset="1" stop-color="#1a0a18" stop-opacity="0.85"/>
            </linearGradient>
            <linearGradient id="m-fuji" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0" stop-color="#1a1330" stop-opacity="0.95"/>
                <stop offset="1" stop-color="#0a0612" stop-opacity="1"/>
            </linearGradient>
            <linearGradient id="m-snow" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0" stop-color="#fce4ec" stop-opacity="0.95"/>
                <stop offset="1" stop-color="#fce4ec" stop-opacity="0"/>
            </linearGradient>
        </defs>
        {{-- Furthest hills --}}
        <path d="M0 280 L 220 200 L 380 250 L 580 190 L 780 240 L 980 180 L 1200 230 L 1440 200 L 1440 360 L 0 360 Z" fill="url(#m-bg)"/>
        {{-- Mt Fuji centred --}}
        <path d="M 460 360 L 720 80 L 980 360 Z" fill="url(#m-fuji)"/>
        {{-- Snow cap --}}
        <path d="M 640 180 Q 680 140 720 80 Q 760 140 800 180 Q 780 195 760 178 Q 740 198 720 180 Q 700 198 680 178 Q 660 195 640 180 Z" fill="url(#m-snow)"/>
    </svg>

    {{-- Torii gate silhouette (vermilion red) --}}
    <svg class="pj-login-bg-torii" viewBox="0 0 200 280" xmlns="http://www.w3.org/2000/svg">
        <g fill="#9a1810">
            {{-- Top kasagi (curved roof) --}}
            <path d="M0 28 Q 100 -8 200 28 L 200 48 Q 100 12 0 48 Z"/>
            {{-- Shimaki (second beam) --}}
            <rect x="10" y="56" width="180" height="14"/>
            {{-- Cross beam (nuki) --}}
            <rect x="20" y="120" width="160" height="12"/>
            {{-- Left pillar --}}
            <path d="M 30 80 L 36 80 L 50 280 L 24 280 Z"/>
            {{-- Right pillar --}}
            <path d="M 164 80 L 170 80 L 176 280 L 150 280 Z"/>
            {{-- Center plaque --}}
            <rect x="92" y="76" width="16" height="38"/>
        </g>
    </svg>

    {{-- Seigaiha (overlapping waves) pattern at bottom --}}
    <svg class="pj-login-bg-waves" viewBox="0 0 200 80" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
        <defs>
            <pattern id="seigaiha-login" x="0" y="0" width="40" height="20" patternUnits="userSpaceOnUse">
                <path d="M0 20 A 20 20 0 0 1 40 20" fill="none" stroke="#fce4ec" stroke-width="1.2" opacity="0.55"/>
                <path d="M-20 20 A 20 20 0 0 1 20 20" fill="none" stroke="#fce4ec" stroke-width="1.2" opacity="0.55"/>
                <path d="M20 20 A 20 20 0 0 1 60 20" fill="none" stroke="#fce4ec" stroke-width="1.2" opacity="0.55"/>
            </pattern>
        </defs>
        <rect x="0" y="0" width="200" height="80" fill="url(#seigaiha-login)"/>
    </svg>

    {{-- Sakura branches arcing from the top corners --}}
    <svg class="pj-login-bg-branch pj-login-bg-branch-left" viewBox="0 0 320 200" xmlns="http://www.w3.org/2000/svg">
        <g>
            {{-- Branch curves --}}
            <path d="M0 30 Q 80 50 140 100 T 280 180" stroke="#5b3a1d" stroke-width="3.5" fill="none" stroke-linecap="round"/>
            <path d="M40 60 Q 70 100 110 120" stroke="#5b3a1d" stroke-width="2.5" fill="none" stroke-linecap="round"/>
            <path d="M120 90 Q 160 80 200 90" stroke="#5b3a1d" stroke-width="2" fill="none" stroke-linecap="round"/>
            {{-- Sakura blossoms sprinkled along the branch --}}
            @foreach([[20, 45], [55, 70], [95, 110], [140, 100], [180, 88], [225, 130], [205, 95], [260, 165]] as $blob)
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

    <svg class="pj-login-bg-branch pj-login-bg-branch-right" viewBox="0 0 320 200" xmlns="http://www.w3.org/2000/svg">
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

    {{-- Soft scrim so the login card stays high-contrast --}}
    <div class="pj-login-bg-scrim"></div>
</div>

<style>
    .pj-login-bg {
        position: fixed;
        inset: 0;
        z-index: 0;
        pointer-events: none;
        overflow: hidden;
    }
    .pj-login-bg-sky {
        position: absolute; inset: 0;
        background: linear-gradient(180deg,
            #2e1432 0%,
            #6b1a2c 30%,
            #b32510 60%,
            #f48fb1 88%,
            #fce4ec 100%);
    }
    .pj-login-bg-sun {
        position: absolute;
        left: 50%;
        top: 32%;
        transform: translate(-50%, -50%);
        width: 360px; height: 360px;
        border-radius: 50%;
        background: radial-gradient(circle, #fde68a 0%, #fbbf24 25%, #b32510 60%, transparent 100%);
        opacity: 0.85;
        filter: blur(1px);
        animation: pj-sun-glow 8s ease-in-out infinite;
    }
    @keyframes pj-sun-glow {
        0%, 100% { transform: translate(-50%, -50%) scale(1);    opacity: 0.85; }
        50%      { transform: translate(-50%, -50%) scale(1.04); opacity: 1; }
    }
    .pj-login-bg-mountains {
        position: absolute;
        bottom: 60px; left: 0;
        width: 100%;
        height: 45vh;
        max-height: 420px;
    }
    .pj-login-bg-torii {
        position: absolute;
        bottom: 60px;
        left: 12%;
        width: 110px;
        height: auto;
        opacity: 0.85;
        filter: drop-shadow(0 4px 12px rgba(0,0,0,0.4));
    }
    @media (min-width: 768px) {
        .pj-login-bg-torii {
            width: 140px;
            left: 8%;
        }
    }
    .pj-login-bg-waves {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 60px;
    }
    .pj-login-bg-branch {
        position: absolute;
        top: 0;
        width: 280px;
        height: auto;
        opacity: 0.9;
        filter: drop-shadow(0 4px 12px rgba(0,0,0,0.3));
    }
    .pj-login-bg-branch-left  { left: -20px; }
    .pj-login-bg-branch-right { right: -20px; transform: scaleX(1); }
    @media (min-width: 768px) {
        .pj-login-bg-branch { width: 360px; }
    }
    .pj-login-bg-scrim {
        position: absolute; inset: 0;
        background: radial-gradient(ellipse at center, transparent 30%, rgba(7, 8, 24, 0.55) 100%);
    }

    /* Make sure Filament's body background doesn't cover this */
    body { background: transparent !important; }
    .fi-simple-layout, .fi-body { background: transparent !important; }

    /* Push Filament's login card above the background */
    .fi-simple-layout > main, .fi-simple-main, .fi-simple-page {
        position: relative;
        z-index: 10;
    }
</style>
