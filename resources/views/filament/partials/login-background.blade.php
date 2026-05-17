{{-- Photo-based full-viewport background for Filament login pages.
     Admin can upload a custom photo via Site Settings → Login background.
     A default Mt Fuji + sakura photo is used when no upload is set. A
     dark gradient scrim keeps the white login card readable, and two
     small sakura branches arc in from the corners for a subtle JP touch
     without competing with the photo. --}}

@php($pjLoginBg = \App\Support\SiteSettings::loginBackgroundUrl())

<div class="pj-login-bg" aria-hidden="true">
    {{-- The photo itself, cover-filled. --}}
    <div class="pj-login-bg-photo" style="background-image: url('{{ $pjLoginBg }}');"></div>

    {{-- Dark gradient scrim — darker at top + bottom, lets photo breathe
         in the middle while keeping the login card high-contrast. --}}
    <div class="pj-login-bg-scrim"></div>

    {{-- Brand-tinted vignette anchored at the form's centre. --}}
    <div class="pj-login-bg-vignette"></div>

    {{-- Subtle sakura branches in the top corners (kept small so they
         don't fight the photo). --}}
    <svg class="pj-login-bg-branch pj-login-bg-branch-left" viewBox="0 0 320 200" xmlns="http://www.w3.org/2000/svg">
        <g>
            <path d="M0 30 Q 80 50 140 100 T 280 180" stroke="#5b3a1d" stroke-width="3" fill="none" stroke-linecap="round"/>
            <path d="M40 60 Q 70 100 110 120" stroke="#5b3a1d" stroke-width="2" fill="none" stroke-linecap="round"/>
            @foreach([[20, 45], [55, 70], [95, 110], [140, 100], [180, 88], [225, 130], [260, 165]] as $blob)
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
            <path d="M320 30 Q 240 50 180 100 T 40 180" stroke="#5b3a1d" stroke-width="3" fill="none" stroke-linecap="round"/>
            <path d="M280 60 Q 250 100 210 120" stroke="#5b3a1d" stroke-width="2" fill="none" stroke-linecap="round"/>
            @foreach([[300, 45], [265, 70], [225, 110], [180, 100], [140, 88], [95, 130], [60, 165]] as $blob)
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
</div>

<style>
    .pj-login-bg {
        position: fixed;
        inset: 0;
        z-index: 0;
        pointer-events: none;
        overflow: hidden;
        background: #0a0612; /* shows through before the photo loads */
    }
    .pj-login-bg-photo {
        position: absolute;
        inset: 0;
        background-position: center;
        background-size: cover;
        background-repeat: no-repeat;
        /* gentle Ken-Burns drift so the photo isn't static */
        animation: pj-login-bg-drift 30s ease-in-out infinite alternate;
    }
    @keyframes pj-login-bg-drift {
        0%   { transform: scale(1.04) translate(-1.5%, -1%); }
        100% { transform: scale(1.08) translate(1.5%,  1%); }
    }
    /* Top + bottom dark gradient → keeps card readable while letting
       the centre of the photo show through. */
    .pj-login-bg-scrim {
        position: absolute; inset: 0;
        background: linear-gradient(180deg,
            rgba(7, 8, 24, 0.55) 0%,
            rgba(7, 8, 24, 0.20) 35%,
            rgba(7, 8, 24, 0.20) 65%,
            rgba(7, 8, 24, 0.75) 100%);
    }
    /* Subtle brand-red vignette behind the card. */
    .pj-login-bg-vignette {
        position: absolute; inset: 0;
        background: radial-gradient(ellipse at center,
            rgba(179, 37, 16, 0.12) 0%,
            transparent 55%);
    }
    .pj-login-bg-branch {
        position: absolute;
        top: 0;
        width: 220px;
        height: auto;
        opacity: 0.85;
        filter: drop-shadow(0 4px 12px rgba(0,0,0,0.4));
    }
    .pj-login-bg-branch-left  { left: -10px; }
    .pj-login-bg-branch-right { right: -10px; transform: scaleX(1); }
    @media (min-width: 768px) {
        .pj-login-bg-branch { width: 300px; }
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
