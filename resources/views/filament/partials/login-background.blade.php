{{-- Japanese-themed full-viewport background for Filament login pages.
     Fixed-position so it survives any ancestor transforms. Sits at z-index 0
     behind the login card. --}}

<div class="pj-login-bg" aria-hidden="true">
    {{-- Layer 1: dusk gradient sky --}}
    <div class="pj-login-bg-sky"></div>

    {{-- Layer 2: rising sun (large red disc) --}}
    <div class="pj-login-bg-sun"></div>

    {{-- Layer 3: Mt Fuji silhouette at the bottom --}}
    <svg class="pj-login-bg-fuji" viewBox="0 0 1440 360" preserveAspectRatio="xMidYEnd slice" xmlns="http://www.w3.org/2000/svg">
        <defs>
            <linearGradient id="fuji-grad" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0" stop-color="#1a1e30" stop-opacity="0.95"/>
                <stop offset="1" stop-color="#0e1124" stop-opacity="1"/>
            </linearGradient>
            <linearGradient id="fuji-snow" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0" stop-color="#fce4ec" stop-opacity="0.9"/>
                <stop offset="1" stop-color="#fce4ec" stop-opacity="0"/>
            </linearGradient>
        </defs>
        {{-- Distant smaller mountains --}}
        <path d="M0 320 L 280 240 L 460 290 L 640 230 L 820 280 L 1020 220 L 1200 270 L 1440 230 L 1440 360 L 0 360 Z"
              fill="#0e1124" opacity="0.6"/>
        {{-- Mt Fuji center --}}
        <path d="M 380 360 L 720 90 L 1060 360 Z" fill="url(#fuji-grad)"/>
        {{-- Snow cap --}}
        <path d="M 620 200 Q 660 160 720 90 Q 780 160 820 200 Q 800 215 780 195 Q 760 215 740 200 Q 720 215 700 200 Q 680 215 660 200 Q 640 215 620 200 Z"
              fill="url(#fuji-snow)"/>
    </svg>

    {{-- Layer 4: subtle sashiko dot pattern at top-right --}}
    <div class="pj-login-bg-pattern"></div>

    {{-- Layer 5: dark overlay so the login card stays readable --}}
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
    /* Push Filament's login card above the background */
    .fi-simple-layout > main, .fi-simple-main, .fi-simple-page {
        position: relative;
        z-index: 10;
    }
    .pj-login-bg-sky {
        position: absolute; inset: 0;
        background: linear-gradient(180deg,
            #2a1530 0%,
            #4a1f3d 25%,
            #6d160a 60%,
            #b32510 90%,
            #f48fb1 100%);
    }
    .pj-login-bg-sun {
        position: absolute;
        right: 12%;
        top: 18%;
        width: 220px; height: 220px;
        border-radius: 50%;
        background: radial-gradient(circle, #fde68a 0%, #fbbf24 30%, #b32510 70%, transparent 100%);
        opacity: 0.85;
        filter: blur(2px);
        animation: pj-sun-glow 6s ease-in-out infinite;
    }
    @keyframes pj-sun-glow {
        0%, 100% { transform: scale(1);    opacity: 0.85; }
        50%      { transform: scale(1.05); opacity: 1; }
    }
    .pj-login-bg-fuji {
        position: absolute;
        bottom: 0; left: 0;
        width: 100%;
        height: 50vh;
        max-height: 460px;
    }
    .pj-login-bg-pattern {
        position: absolute; inset: 0;
        background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,0.06) 1px, transparent 0);
        background-size: 28px 28px;
    }
    .pj-login-bg-scrim {
        position: absolute; inset: 0;
        background: radial-gradient(ellipse at center, transparent 0%, rgba(7, 8, 24, 0.55) 100%);
    }

    /* Make sure Filament's body background doesn't cover this */
    body { background: transparent !important; }
    .fi-simple-layout, .fi-body { background: transparent !important; }
</style>
