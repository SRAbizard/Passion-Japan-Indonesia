@extends('layouts.app')

@section('title', __('Passion Japan Indonesia') . ' — ' . __('Step Toward a Successful Career in Japan'))

@php
    use Illuminate\Support\Str;

    $rate = (float) config('passion.jpy_to_idr', 105);

    // Visa slugs differ per install; resolve the three quiz routes by name.
    $routeSlug = function (array $needles) use ($visas) {
        return $visas->first(fn ($v) => Str::contains(Str::lower($v->slug.' '.$v->t('name', 'en')), $needles))?->slug;
    };
    $visaRoutes = [
        'ssw'      => ['slug' => $routeSlug(['tokutei', 'ssw', 'specified']), 'name' => 'Tokutei Ginou (SSW)'],
        'intern'   => ['slug' => $routeSlug(['intern', 'magang', 'jisshu', 'trainee']), 'name' => __('Internship (Ginou Jisshu)')],
        'engineer' => ['slug' => $routeSlug(['engineer', 'gijinkoku']), 'name' => __('Engineer (Gijinkoku)')],
    ];

    // Most-used categories become the quick chips + marquee.
    $catCounts = $jobs->countBy('category');
    $topCats = $categories->sortByDesc(fn ($c) => $catCounts[$c->slug] ?? 0)->take(3);

    $fmtYen = fn (?int $n) => $n ? '¥'.number_format($n, 0, ',', '.') : null;
    $fmtRp = fn (?int $n) => $n ? 'Rp'.rtrim(rtrim(number_format($n * $rate / 1e6, 1, ',', '.'), '0'), ',').' '.__('mil') : null;
    $periodLabel = ['monthly' => __('/ month'), 'yearly' => __('/ year'), 'hourly' => __('/ hour')];

    $featuredJobs = $jobs->take(6);

    $payload = [
        'jobs'      => $jobs,
        'regions'   => $regions,
        'rate'      => $rate,
        'jobsUrl'   => route('job.index'),
        'academy'   => $academyUrl,
        'routes'    => $visaRoutes,
        'i18n'      => [
            'allFields'   => __('All fields'),
            'jobsInRegion'=> __('jobs in this region'),
            'noJobsRegion'=> __('No openings here yet. New jobs come in every week.'),
            'seeRegion'   => __('See all in :region', ['region' => ':region']),
            'question'    => __('QUESTION'),
            'done'        => __('MISSION COMPLETE'),
            'bestRoute'   => __('BEST ROUTE FOR YOU'),
            'seeJobs'     => __('See these jobs'),
            'again'       => __('Try again'),
            'academyNote' => __('Raise your Japanese to N4 first at Passion Japan Academy, then come back here to apply.'),
            'academyLink' => __('Open Passion Japan Academy'),
            'noMatch'     => __('No matching openings right now. We will tell you as soon as one opens.'),
            'whyEngineer' => __('Your engineering/IT education opens the Engineer visa route.'),
            'whySsw'      => __('Your Japanese level already meets the Tokutei Ginou requirement.'),
            'whyIntern'   => __('The internship route lets you start from the basics; you are trained before departure.'),
            'whySswPrep'  => __('Get your Japanese to N4 so you can apply through Tokutei Ginou.'),
            'perMonth'    => __('/ month'),
            'estimate'    => __('estimated rate'),
            'mil'         => __('mil'),
        ],
        'quiz' => [
            ['q' => __('How old are you?'), 'k' => 'age', 'o' => [
                [__('18–22 years'), __('Fresh graduate'), 0], [__('23–27 years'), '', 1], [__('28–32 years'), '', 2], [__('33 or older'), '', 3]]],
            ['q' => __('What is your Japanese level?'), 'k' => 'lang', 'o' => [
                [__('None yet'), __('Starting from zero'), 0], [__('N5 / beginner'), __('Hiragana & katakana'), 1], [__('N4 / JFT A2'), __('Basic conversation'), 2], [__('N3 or higher'), __('Fluent'), 3]]],
            ['q' => __('Which field interests you?'), 'k' => 'field', 'o' => [
                [__('Caregiving'), 'Kaigo', ['kaigo']],
                [__('Factory'), __('Manufacturing, machining'), ['pabrik', 'kikai-kakou']],
                [__('Restaurant & hotel'), __('Hospitality'), ['restoran', 'perhotelan']],
                [__('Driver & logistics'), '', ['driver', 'logistics']],
                [__('Engineering & IT'), __('Engineer, CAD, programmer'), ['engineer']],
                [__('Other fields'), __('Cleaning, farming, livestock'), ['building-cleaning', 'peternakan']]]],
            ['q' => __('Your highest education?'), 'k' => 'edu', 'o' => [
                [__('High school / vocational'), 'SMA / SMK', 0], [__('Diploma (D3)'), '', 1], [__('Bachelor in engineering / IT'), '', 2], [__('Other bachelor'), '', 3]]],
        ],
    ];
@endphp

@section('content')
<div data-pj-home>

{{-- ========== HERO ========== --}}
<section class="pj-hero">
    <canvas data-pj-petals aria-hidden="true"></canvas>
    <div class="pj-wrap pj-hero-grid">
        <div class="pj-hero-copy">
            <span class="pj-sticker">日本へのミッション · {{ __('Mission to Japan') }}</span>
            <h1>
                <span class="l">{{ __('Your mission:') }}</span>
                <span class="l">{{ __('work in') }}</span>
                <span class="flag">{{ __('Japan.') }}</span>
            </h1>
            <p class="pj-lede">{{ __('Official job openings from partner companies in Japan. We guide you from applying and mensetsu to your first day at work.') }}</p>

            <form class="pj-finder pj-card" method="GET" action="{{ route('job.index') }}" data-pj-finder>
                <div class="pj-finder-row">
                    <div class="pj-field">
                        <label for="pj-f-category">{{ __('Field') }}</label>
                        <select id="pj-f-category" name="category">
                            <option value="">{{ __('All fields') }}</option>
                            @foreach($categories as $c)
                                <option value="{{ $c->slug }}">{{ $c->t('name') }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="pj-field">
                        <label for="pj-f-visa">{{ __('Visa route') }}</label>
                        <select id="pj-f-visa" name="visa">
                            <option value="">{{ __('All routes') }}</option>
                            @foreach($visas as $v)
                                <option value="{{ $v->slug }}">{{ $v->t('name') }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="pj-field">
                        <label for="pj-f-region">{{ __('Region') }}</label>
                        <select id="pj-f-region" name="region">
                            <option value="">{{ __('All of Japan') }}</option>
                            @foreach($regions as $r)
                                <option value="{{ $r['key'] }}">{{ $r['name'] }} · {{ $r['jp'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="pj-finder-foot">
                    <div class="pj-chips" role="group" aria-label="{{ __('Popular fields') }}">
                        @foreach($topCats as $c)
                            <button type="button" class="pj-chip" data-pj-chip="{{ $c->slug }}" aria-pressed="false">{{ $c->t('name') }}</button>
                        @endforeach
                    </div>
                    <button class="pj-btn is-red" type="submit">
                        {!! __('Show :count jobs', ['count' => '<span class="pj-count" data-pj-count>'.$jobs->count().'</span>']) !!} →
                    </button>
                </div>
            </form>
        </div>

        <div class="pj-scene" data-pj-scene aria-hidden="true">
            <div class="sun" data-depth="6"></div>
            <svg class="crane" data-depth="22" viewBox="0 0 100 70"><path d="M5 40 L45 30 L60 5 L62 32 L95 26 L66 42 L70 62 L50 46 L20 52 Z" fill="var(--pj-kami)" stroke="var(--pj-line)" stroke-width="3" stroke-linejoin="round"/><path d="M45 30 L50 46 M62 32 L66 42" stroke="var(--pj-line)" stroke-width="2"/></svg>
            <svg class="cloud c1" data-depth="16" viewBox="0 0 120 50"><path d="M15 42 Q2 42 4 31 Q6 21 18 23 Q20 8 38 10 Q48 0 62 9 Q78 4 82 19 Q100 15 104 29 Q118 31 114 42 Z" fill="var(--pj-kami)" stroke="var(--pj-line)" stroke-width="3" stroke-linejoin="round"/></svg>
            <svg class="cloud c2" data-depth="10" viewBox="0 0 120 50"><path d="M15 42 Q2 42 4 31 Q6 21 18 23 Q20 8 38 10 Q48 0 62 9 Q78 4 82 19 Q100 15 104 29 Q118 31 114 42 Z" fill="var(--pj-kami)" stroke="var(--pj-line)" stroke-width="3" stroke-linejoin="round"/></svg>
            <svg class="fuji" data-depth="4" viewBox="0 0 400 200"><path d="M0 200 L120 70 Q150 40 170 38 L230 38 Q250 40 280 70 L400 200 Z" fill="#2B4795" stroke="var(--pj-line)" stroke-width="3" stroke-linejoin="round"/><path d="M148 58 Q160 40 172 38 L228 38 Q240 40 252 58 L236 72 L222 60 L208 76 L196 60 L182 74 L166 62 Z" fill="#FFFAF2" stroke="var(--pj-line)" stroke-width="3" stroke-linejoin="round"/></svg>
            @if(count($heroSlides))
                <div class="photo" data-depth="18">
                    <div class="frame" data-pj-slides>
                        @foreach($heroSlides as $i => $slide)
                            <img src="{{ $slide['image'] }}" alt="" loading="{{ $i ? 'lazy' : 'eager' }}" class="{{ $i === 0 ? 'is-on' : '' }}" onerror="this.remove()">
                        @endforeach
                    </div>
                </div>
            @endif
            <div class="pj-pass pj-card" data-depth="28" data-base-rotate="-4">
                <div class="pj-pass-main">
                    <div class="pj-pass-top"><span>搭乗券 · BOARDING PASS</span><span>PJ {{ date('Y') }}</span></div>
                    <div class="pj-pass-route">
                        <div><b>CGK</b><small>Jakarta</small></div>
                        <div class="line"><svg viewBox="0 0 24 24"><path d="M21 16v-2l-8-5V3.5a1.5 1.5 0 0 0-3 0V9l-8 5v2l8-2.5V19l-2 1.5V22l3.5-1 3.5 1v-1.5L13 19v-5.5z" fill="var(--pj-red)"/></svg></div>
                        <div class="text-right"><b data-pj-bp-code>NRT</b><small data-pj-bp-city>Tokyo</small></div>
                    </div>
                    <div class="pj-pass-meta">
                        <span>{{ __('Passenger') }}<b>{{ auth()->user()?->name ? Str::limit(auth()->user()->name, 12) : __('You') }}</b></span>
                        <span>{{ __('Seat') }}<b data-pj-bp-seat>{{ __('All fields') }}</b></span>
                        <span>Gate<b>Mensetsu</b></span>
                    </div>
                </div>
                <div class="pj-pass-stub">出発</div>
            </div>
        </div>
    </div>
</section>

{{-- ========== MARQUEE ========== --}}
<div class="pj-marquees" aria-hidden="true">
    @php
        $catNames = $categories->map(fn ($c) => trim(($c->t('name', 'ja') !== $c->t('name') ? $c->t('name', 'ja').' ' : '').$c->t('name')))->all();
        $promises = [__('Official job openings'), __('Verified partner companies'), __('Online & offline mensetsu'), __('Guided until departure'), __('Transparent costs')];
    @endphp
    <div class="pj-band is-red"><div class="pj-track">@foreach([...$catNames, ...$catNames] as $t)<span>{{ $t }}</span>@endforeach</div></div>
    <div class="pj-band is-ink"><div class="pj-track">@foreach([...$promises, ...$promises] as $t)<span>{{ $t }}</span>@endforeach</div></div>
</div>

{{-- ========== STATEMENT ========== --}}
<section class="pj-statement">
    <div class="pj-wrap">
        <p class="big" data-pj-statement>
            <span data-words>{{ __('Hundreds of Indonesians have already departed with Passion Japan.') }}</span>
            <span data-words data-hl>{{ __('The next mission is yours.') }}</span>
        </p>
        <div class="pj-stats">
            @foreach($stats as $s)
                <div class="pj-stat pj-card"><b data-pj-countup>{{ $s['value'] }}</b><span>{{ $s['label'] }}</span></div>
            @endforeach
        </div>
    </div>
</section>

{{-- ========== MISSION ROUTE ========== --}}
<section class="pj-route pj-section" id="rute">
    <div class="pj-wrap">
        <div class="pj-head">
            <h2><span class="jp">ミッションルート</span>{{ __('Your mission route') }}</h2>
            <p class="pj-lede">{{ __('Five checkpoints from Jakarta to Japan. Every checkpoint you pass gets a stamp.') }}</p>
        </div>
        <div data-pj-route-stage>
            <svg class="pj-route-svg" viewBox="0 0 1000 150" aria-hidden="true">
                <path data-pj-route-path class="pj-route-path" d="M20 110 C 140 20, 220 20, 300 80 S 470 140, 520 70 S 680 10, 740 80 S 900 130, 980 40"/>
                <path data-pj-route-done class="pj-route-done" d="M20 110 C 140 20, 220 20, 300 80 S 470 140, 520 70 S 680 10, 740 80 S 900 130, 980 40" style="stroke-dasharray: 1400; stroke-dashoffset: 1400"/>
                <g data-pj-route-plane transform="translate(20 110)"><circle r="22" fill="#F1AF2E" stroke="#F5EDE0" stroke-width="3"/><path d="M12 4v-2l-8-5V-8.5a1.5 1.5 0 0 0-3 0V-3l-8 5v2l8-2.5V7l-2 1.5V10l3.5-1 3.5 1V8.5L1 7V1.5z" fill="#1D1730" transform="rotate(90) translate(-2 -1)"/></g>
            </svg>
            <ol class="pj-steps">
                @php
                    $steps = [
                        ['登録', __('Create your profile'), __('Fill in your biodata and education, and upload documents from your dashboard.')],
                        ['学習', __('Learn the language'), __('Level up your JLPT/JFT at Passion Japan Academy.')],
                        ['面接', 'Mensetsu', __('Online or offline interviews with Japanese companies.')],
                        ['ビザ', __('Visa & COE'), __('We handle the paperwork until your Certificate of Eligibility is issued.')],
                        ['出発', __('Depart!'), __('Fly to Japan and start your first day at work.')],
                    ];
                @endphp
                @foreach($steps as $i => [$jp, $title, $desc])
                    <li class="pj-step" data-pj-step>
                        <div class="pj-stamp">{{ $jp }}<small>0{{ $i + 1 }}</small></div>
                        <div>
                            <span class="tag">{{ __('CHECKPOINT') }} 0{{ $i + 1 }}</span>
                            <h3>{{ $title }}</h3>
                            <p>
                                {{ $desc }}
                                @if($i === 1 && $academyUrl)
                                    <a href="{{ $academyUrl }}" target="_blank" rel="noopener">{{ __('Open Academy') }} ↗</a>
                                @endif
                            </p>
                        </div>
                    </li>
                @endforeach
            </ol>
        </div>

        @if(count($workflows))
            <div class="pj-route-detail" data-pj-tabs>
                <h3 class="font-display text-2xl sm:text-3xl">{{ __('Step by step, per visa route') }}</h3>
                <div class="pj-tabs" role="tablist" aria-label="{{ __('Visa workflow tabs') }}">
                    @foreach($workflows as $i => $wf)
                        <button type="button" class="pj-tab" role="tab" id="pj-tab-{{ $wf['slug'] }}" aria-controls="pj-panel-{{ $wf['slug'] }}"
                                aria-selected="{{ $i === 0 ? 'true' : 'false' }}" data-pj-tab="{{ $wf['slug'] }}">{{ \App\Support\HomepageDemoData::pick($wf['name']) }}</button>
                    @endforeach
                </div>
                @foreach($workflows as $i => $wf)
                    <div role="tabpanel" id="pj-panel-{{ $wf['slug'] }}" aria-labelledby="pj-tab-{{ $wf['slug'] }}" data-pj-panel="{{ $wf['slug'] }}" @if($i) hidden @endif>
                        @if($tagline = \App\Support\HomepageDemoData::pick($wf['tagline']))
                            <p class="mb-4 text-[#d6ccbd] max-w-2xl">{{ $tagline }}</p>
                        @endif
                        <div class="pj-tickets">
                            @foreach($wf['steps'] as $step)
                                <div class="pj-ticket">
                                    <span class="n">{{ str_pad($step['n'], 2, '0', STR_PAD_LEFT) }}</span>
                                    <h4>{{ \App\Support\HomepageDemoData::pick($step['title']) }}</h4>
                                    @if(! empty($step['badge']))
                                        <span class="badge {{ $step['badge']['color'] }}">{{ \App\Support\HomepageDemoData::pick($step['badge']['label']) }}</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        @if(! empty($wf['notes']))
                            <div class="pj-notes">
                                <b class="text-[#F1AF2E] text-xs tracking-widest uppercase">{{ __('Catatan Penting') }}</b>
                                @foreach($wf['notes'] as $note)
                                    <p>• {{ \App\Support\HomepageDemoData::pick($note) }}</p>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>

{{-- ========== MATCH QUIZ ========== --}}
<section class="pj-section" id="cek">
    <div class="pj-wrap">
        <div class="pj-head">
            <span class="pj-sticker is-pink">{{ __('Mission 01 · 1 minute') }}</span>
            <h2><span class="jp">相性チェック</span>{{ __('Check your match') }}</h2>
            <p class="pj-lede">{{ __('Answer 4 questions. We will show the visa route that fits you best and matching jobs.') }}</p>
        </div>
        <div class="pj-quiz">
            <div class="pj-game pj-card">
                <div class="pj-game-bar"><span data-pj-q-label>{{ __('QUESTION') }} 1 / 4</span><div class="pj-xp" aria-hidden="true"><i data-pj-xp></i></div><span data-pj-q-xp>0 XP</span></div>
                <div class="pj-game-body" data-pj-game aria-live="polite">
                    <span class="pj-q-num">{{ __('QUESTION') }} 1</span>
                    <p class="pj-q-title">{{ $payload['quiz'][0]['q'] }}</p>
                    <div class="pj-opts">
                        @foreach($payload['quiz'][0]['o'] as $o)
                            <button type="button" class="pj-opt">{{ $o[0] }}@if($o[1])<small>{{ $o[1] }}</small>@endif</button>
                        @endforeach
                    </div>
                </div>
            </div>
            <aside class="grid gap-4">
                <div class="pj-side-card pj-card is-tilt">
                    <h3>{{ __('Three routes to Japan') }}</h3>
                    <ul>
                        <li><b>Tokutei Ginou (SSW)</b>: {{ __('needs Japanese N4/JFT A2 and a skills test.') }}</li>
                        <li><b>{{ __('Internship (Ginou Jisshu)') }}</b>: {{ __('suits beginners; you are trained from the start.') }}</li>
                        <li><b>{{ __('Engineer (Gijinkoku)') }}</b>: {{ __('for D3/S1 graduates in engineering or IT.') }}</li>
                    </ul>
                </div>
                <p class="pj-disclaimer">{{ __('This check is a simulation for initial guidance. The final decision follows each job’s requirements and a consultation with our team.') }}</p>
            </aside>
        </div>
    </div>
</section>

{{-- ========== JOB MAP ========== --}}
<section class="pj-section pt-0!" id="peta">
    <div class="pj-wrap">
        <div class="pj-head">
            <h2><span class="jp">求人マップ</span>{{ __('Where in Japan do you want to work?') }}</h2>
            <p class="pj-lede">{{ __('Tap a region on the map to see its openings.') }}</p>
        </div>
        <div class="pj-map">
            <div class="pj-map-box pj-card">
                <svg class="pj-japan" viewBox="0 0 400 440" role="group" aria-label="{{ __('Job map of Japan') }}">
                    <path class="land" d="M292 34 Q318 18 344 26 Q372 30 384 54 Q378 80 356 92 Q338 108 314 104 Q294 96 286 74 Q282 50 292 34 Z"/>
                    <path class="land" d="M284 120 Q300 126 310 146 Q314 176 304 204 Q296 232 280 252 Q262 272 236 284 Q206 296 176 310 Q146 324 116 334 Q96 340 84 334 Q78 324 92 316 Q118 304 146 292 Q178 278 206 264 Q232 250 248 228 Q262 204 266 174 Q268 144 284 120 Z"/>
                    <path class="land" d="M132 344 Q158 336 184 336 Q198 342 192 356 Q172 366 146 366 Q130 360 132 344 Z"/>
                    <path class="land" d="M58 342 Q78 340 92 350 Q100 370 92 392 Q80 412 64 410 Q48 402 44 382 Q42 358 58 342 Z"/>
                    <path class="land" d="M14 424 q8 -6 14 0 q-6 8 -14 0 Z"/>
                    @foreach($regions as $r)
                        @php $left = $r['x'] < 120; @endphp
                        <g class="pj-pin {{ $r['count'] ? '' : 'is-empty' }}" tabindex="0" role="button" data-pj-pin="{{ $r['key'] }}"
                           aria-label="{{ $r['name'] }}: {{ $r['count'] }} {{ __('jobs in this region') }}">
                            <circle class="pulse" cx="{{ $r['x'] }}" cy="{{ $r['y'] }}" r="9"/>
                            <circle class="dot" cx="{{ $r['x'] }}" cy="{{ $r['y'] }}" r="8"/>
                            <text x="{{ $left ? $r['x'] + 14 : $r['x'] - 14 }}" y="{{ $r['y'] + 4 }}" text-anchor="{{ $left ? 'start' : 'end' }}">{{ $r['name'] }}{{ $r['count'] ? ' · '.$r['count'] : '' }}</text>
                        </g>
                    @endforeach
                </svg>
            </div>
            <div class="pj-city pj-card" data-pj-city aria-live="polite">
                <p class="pj-lede">{{ __('Tap a region on the map to see its openings.') }}</p>
            </div>
        </div>
    </div>
</section>

{{-- ========== FEATURED JOBS ========== --}}
<section class="pj-section pt-0!" id="lowongan">
    <div class="pj-wrap">
        <div class="pj-head">
            <h2><span class="jp">おすすめ求人</span>{{ __('Featured jobs') }}</h2>
        </div>
        <div class="pj-jobs-tools">
            <a href="{{ route('job.index') }}" wire:navigate class="font-bold underline decoration-2 underline-offset-4">{{ __('See all :count jobs', ['count' => $jobs->count()]) }} →</a>
            <div class="pj-toggle" role="group" aria-label="{{ __('Salary currency') }}">
                <button type="button" data-pj-cur="jpy" aria-pressed="true">¥ Yen</button>
                <button type="button" data-pj-cur="idr" aria-pressed="false">Rp Rupiah</button>
            </div>
        </div>
        @if($featuredJobs->isEmpty())
            <div class="pj-empty pj-card"><p class="pj-lede mx-auto">{{ __('New openings are being prepared. Check back soon or talk to us on WhatsApp.') }}</p></div>
        @else
            <div class="pj-jobs">
                @foreach($featuredJobs as $j)
                    @php $convertible = $j['currency'] === 'JPY' && $j['min']; @endphp
                    <a href="{{ $j['url'] }}" wire:navigate class="pj-job pj-card" data-pj-tilt>
                        <div class="pj-job-top">
                            @if($j['featured'])<span class="pj-badge">★ {{ __('Featured') }}</span>
                            @elseif($j['new'])<span class="pj-badge is-new">{{ __('New') }}</span>
                            @else<span></span>@endif
                            @if($j['categoryName'])<span class="pj-job-tags"><span>{{ $j['categoryName'] }}</span></span>@endif
                        </div>
                        <h3>{{ $j['title'] }}</h3>
                        <p class="co">{{ $j['company'] }} · {{ $j['location'] }}</p>
                        <div class="pj-job-tags">
                            @if($j['visaName'])<span>{{ $j['visaName'] }}</span>@endif
                        </div>
                        <div class="pj-salary" @if($convertible) data-pj-salary data-min="{{ $j['min'] }}" data-max="{{ $j['max'] ?? $j['min'] }}" data-period="{{ $periodLabel[$j['period']] ?? '' }}" @endif>
                            @if($convertible)
                                <b>{{ $fmtYen($j['min']) }}@if($j['max'] && $j['max'] !== $j['min']) – {{ $fmtYen($j['max']) }}@endif</b>
                                <small>≈ {{ $fmtRp($j['min']) }}@if($j['max'] && $j['max'] !== $j['min']) – {{ $fmtRp($j['max']) }}@endif {{ $periodLabel[$j['period']] ?? '' }}</small>
                            @else
                                <b>{{ $j['min'] ? number_format($j['min']).($j['max'] ? ' – '.number_format($j['max']) : '').' '.$j['currency'] : __('Salary on request') }}</b>
                            @endif
                        </div>
                        <span class="pj-hanko" title="{{ __('Partner company') }}">認定</span>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</section>

{{-- ========== COMPANY PROFILE VIDEO ========== --}}
@if($companyVideo)
<section class="pj-section pt-0!">
    <div class="pj-wrap">
        <div class="pj-head">
            <h2><span class="jp">会社紹介</span>{{ __('Get to know Passion Japan') }}</h2>
        </div>
        <div class="pj-video">
            <div class="pj-video-frame pj-card">
                @if($companyVideo['type'] === 'youtube')
                    <div data-pj-lite-yt data-embed-url="{{ $companyVideo['url'] }}" class="group absolute inset-0 cursor-pointer">
                        <img src="{{ $companyVideo['poster'] }}" alt="{{ __('Company Profile') }}" loading="lazy">
                        <span class="absolute inset-0 grid place-items-center">
                            <span class="pj-btn is-red h-20 w-20 p-0! group-hover:scale-110 transition"><svg class="h-9 w-9 ml-1" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg></span>
                        </span>
                    </div>
                @else
                    <video src="{{ $companyVideo['url'] }}" @if($companyVideo['poster']) poster="{{ $companyVideo['poster'] }}" @endif controls preload="metadata"></video>
                @endif
            </div>
            <div class="pj-points">
                @php
                    $points = [
                        ['M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', __('Real Job Placement'), __('Direct partnership with companies in Japan — students go straight to verified job offers, not theory.')],
                        ['M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', __('Transparent Process, No Hidden Costs'), __('Every fee, every step, every promise — written, signed, and explained before you commit.')],
                        ['M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', __('Guided from start to finish'), __('From documents and mensetsu to visa and departure, one team accompanies you.')],
                    ];
                @endphp
                @foreach($points as [$icon, $title, $desc])
                    <div class="pj-point pj-card">
                        <span class="ico"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/></svg></span>
                        <div><h3>{{ $title }}</h3><p>{{ $desc }}</p></div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif

{{-- ========== GALLERY ========== --}}
@if($albums->isNotEmpty())
<section class="pj-section pj-gallery" id="galeri">
    <div class="pj-scallop absolute top-0 inset-x-0" aria-hidden="true"></div>
    <div class="pj-wrap">
        <div class="pj-head">
            <h2><span class="jp">ギャラリー</span>{{ __('From our albums') }}</h2>
            <p class="pj-lede">{{ __('Real moments from our candidates, from the mensetsu room to campuses across Indonesia.') }}</p>
        </div>
        <div class="pj-polaroids">
            @foreach($albums as $album)
                <a href="{{ route('gallery.show', $album->slug) }}" wire:navigate class="pj-polaroid">
                    <div class="ph">
                        <span class="cnt">{{ trans_choice('{1} :count item|[2,*] :count items', $album->items_count, ['count' => $album->items_count]) }}</span>
                        @if($album->cover_url)
                            <img src="{{ $album->cover_url }}" alt="" loading="lazy" onerror="this.remove()">
                        @endif
                    </div>
                    <p class="cap">{{ $album->t('title') }}@if($album->t('caption'))<small>{{ Str::limit($album->t('caption'), 70) }}</small>@endif</p>
                </a>
            @endforeach
        </div>
        <a href="{{ route('gallery.index') }}" wire:navigate class="pj-btn">{{ __('See all') }} →</a>
    </div>
</section>
@endif

{{-- ========== TESTIMONIALS ========== --}}
@if($testimonials->isNotEmpty())
<section class="pj-section">
    <div class="pj-wrap">
        <div class="pj-head">
            <h2><span class="jp">みんなの声</span>{{ __('What people say about us') }}</h2>
        </div>
        <div class="pj-notes-grid" style="--cols: {{ in_array($testimonials->count(), [2, 4]) ? 2 : 3 }}">
            @foreach($testimonials as $t)
                <figure class="pj-quote pj-card">
                    <span class="kind">{{ $t->kind === 'company' ? __('Partner Companies') : __('Students') }}</span>
                    <blockquote>“{{ $t->t('quote') }}”</blockquote>
                    <figcaption class="who">
                        <span class="av">{{ Str::of($t->name)->substr(0, 1)->upper() }}</span>
                        <span><b>{{ $t->name }}</b><span>{{ $t->t('role') }}</span></span>
                    </figcaption>
                </figure>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ========== FAQ ========== --}}
<section class="pj-section pt-0!">
    <div class="pj-wrap">
        <div class="pj-head">
            <h2><span class="jp">よくある質問</span>{{ __('Frequently Asked Questions') }}</h2>
        </div>
        <div class="pj-faq">
            @foreach($faqs as $faq)
                <details class="pj-card">
                    <summary>{{ $faq['q'] }}</summary>
                    <p>{{ $faq['a'] }}</p>
                </details>
            @endforeach
        </div>
    </div>
</section>

</div>

@php
    $homeJson = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP);
@endphp
<script type="application/json" id="pj-home-data">{!! $homeJson !!}</script>
@endsection
