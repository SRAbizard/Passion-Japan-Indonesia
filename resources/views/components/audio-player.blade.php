@php $themeAudioUrl = \App\Support\SiteSettings::themeAudioUrl(); @endphp
@if($themeAudioUrl)
    <audio id="pj-theme-audio" src="{{ $themeAudioUrl }}" loop preload="auto" aria-hidden="true"></audio>

    {{-- Prominent floating speaker — pulses while muted to advertise the music --}}
    <div class="fixed bottom-6 left-6 z-50 inline-flex items-center gap-3" id="pj-audio-wrap">
        <button id="pj-audio-toggle"
                type="button"
                title="{{ __('Toggle background music') }}"
                aria-label="{{ __('Toggle background music') }}"
                class="relative inline-flex h-14 w-14 items-center justify-center rounded-full bg-brand-600 shadow-xl shadow-brand-600/40 hover:bg-brand-700 hover:shadow-brand-500/60 transition">
            <svg class="audio-icon-on h-6 w-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/></svg>
            <svg class="audio-icon-off h-6 w-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15zM17 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2"/></svg>
            <span class="audio-pulse absolute inset-0 rounded-full ring-2 ring-brand-500/70"></span>
            <span class="audio-pulse audio-pulse-2 absolute inset-0 rounded-full ring-2 ring-brand-500/70"></span>
        </button>
        <span id="pj-audio-hint"
              class="hidden md:inline-flex items-center gap-1.5 px-3 py-2 rounded-full text-xs font-semibold text-white bg-surface-900/90 backdrop-blur border border-brand-500/40 shadow-lg whitespace-nowrap">
            🎵 {{ __('Tap to enable music') }}
        </span>
    </div>

    <style>
        #pj-audio-toggle .audio-icon-on, #pj-audio-toggle .audio-icon-off { display: none; }
        #pj-audio-toggle.is-playing .audio-icon-on  { display: block; }
        #pj-audio-toggle:not(.is-playing) .audio-icon-off { display: block; }
        #pj-audio-toggle:not(.is-playing) {
            animation: pj-audio-breathe 1.6s ease-in-out infinite;
        }
        @keyframes pj-audio-breathe {
            0%, 100% { transform: scale(1); }
            50%      { transform: scale(1.1); }
        }
        #pj-audio-toggle.is-playing .audio-pulse {
            animation: pj-audio-ripple 1.8s ease-out infinite;
        }
        #pj-audio-toggle.is-playing .audio-pulse-2 { animation-delay: 0.6s; }
        #pj-audio-toggle:not(.is-playing) .audio-pulse { display: none; }
        @keyframes pj-audio-ripple {
            0%   { transform: scale(1);    opacity: 0.7; }
            100% { transform: scale(1.7);  opacity: 0; }
        }
        #pj-audio-hint {
            opacity: 0;
            transform: translateX(-8px);
            transition: opacity 0.3s ease, transform 0.3s ease;
            pointer-events: none;
        }
        #pj-audio-hint.is-visible { opacity: 1; transform: none; }
    </style>

    <script>
        (function () {
            var audio  = document.getElementById('pj-theme-audio');
            var toggle = document.getElementById('pj-audio-toggle');
            var hint   = document.getElementById('pj-audio-hint');
            if (! audio || ! toggle) return;
            if (window.__pjAudioInit) return;
            window.__pjAudioInit = true;

            var saved = null;
            try { saved = localStorage.getItem('pj-audio-muted'); } catch (e) {}

            // Resume position across navigation (sessionStorage, expires after 60s)
            var resumeAt = 0;
            try {
                var rawTime  = sessionStorage.getItem('pj-audio-time');
                var rawSaved = sessionStorage.getItem('pj-audio-time-at');
                if (rawTime && rawSaved) {
                    var age = (Date.now() - parseInt(rawSaved, 10)) / 1000;
                    if (age < 60) resumeAt = parseFloat(rawTime) || 0;
                }
            } catch (e) {}
            var seekToResume = function () {
                if (resumeAt > 0 && resumeAt < (audio.duration || Infinity)) {
                    try { audio.currentTime = resumeAt; } catch (e) {}
                }
            };
            if (audio.readyState >= 1) seekToResume();
            else audio.addEventListener('loadedmetadata', seekToResume, { once: true });

            audio.muted = saved === '1';
            audio.play().catch(function () {
                audio.muted = true;
                audio.play().catch(function () {});
                if (saved !== '1') showHint();
            }).finally(updateUI);

            // Save current position so the next page picks up where this left off
            var savePos = function () {
                if (audio.currentTime > 0 && ! audio.paused) {
                    try {
                        sessionStorage.setItem('pj-audio-time',    String(audio.currentTime));
                        sessionStorage.setItem('pj-audio-time-at', String(Date.now()));
                    } catch (e) {}
                }
            };
            setInterval(savePos, 2000);
            window.addEventListener('pagehide',     savePos);
            window.addEventListener('beforeunload', savePos);

            var unmuteOnce = function () {
                if (saved !== '1' && audio.muted) {
                    audio.muted = false;
                    audio.play().then(updateUI).catch(function () {});
                }
                hideHint();
                ['click','keydown','touchstart','wheel'].forEach(function (e) {
                    window.removeEventListener(e, unmuteOnce);
                });
            };
            ['click','keydown','touchstart','wheel'].forEach(function (e) {
                window.addEventListener(e, unmuteOnce, { passive: true });
            });

            toggle.addEventListener('click', function (ev) {
                ev.stopPropagation();
                audio.muted = ! audio.muted;
                if (! audio.muted) audio.play().catch(function () {});
                try { localStorage.setItem('pj-audio-muted', audio.muted ? '1' : '0'); } catch (e) {}
                updateUI();
                hideHint();
            });

            audio.addEventListener('play',  updateUI);
            audio.addEventListener('pause', updateUI);

            function updateUI () {
                toggle.classList.toggle('is-playing', ! audio.muted && ! audio.paused);
            }
            function showHint () { if (hint) { hint.classList.add('is-visible'); setTimeout(hideHint, 6000); } }
            function hideHint () { if (hint) hint.classList.remove('is-visible'); }
        })();
    </script>
@endif
