@extends('layouts.app')

@section('title', __('Passion Japan Indonesia') . ' — ' . __('Step Toward a Successful Career in Japan'))

@php
    use App\Models\Course;
    use App\Models\Faq;
    use App\Models\JobVacancy;
    use App\Models\Testimonial;
    use App\Models\VisaCategory;
    use App\Support\HomepageDemoData as Demo;

    // Phase 3+4+5: FAQ, Testimonial, Jobs, Courses come from DB; fall back to Demo helper when empty.
    // Benefits/Programs/Workflow still come from Demo until later phases.
    $benefits = Demo::benefits();
    $programs = Demo::programs();
    $visaWorkflows = Demo::visaWorkflows();

    $dbCourses = Course::published()->withCount('chapters')->orderByDesc('is_featured')->orderByDesc('published_at')->limit(4)->get();
    $courses   = $dbCourses->isNotEmpty() ? $dbCourses : Demo::courses();

    $dbFaqs = Faq::published()->get();
    $faqs   = $dbFaqs->isNotEmpty()
        ? $dbFaqs->map(fn ($f) => ['q' => $f->getTranslations('question'), 'a' => $f->getTranslations('answer')])->all()
        : Demo::faqs();

    $companyT = Testimonial::published()->companies()->get();
    $studentT = Testimonial::published()->students()->get();
    $testimonials = ($companyT->isNotEmpty() || $studentT->isNotEmpty())
        ? [
            'company' => $companyT->map(fn ($t) => ['name' => $t->name, 'role' => $t->getTranslations('role'), 'quote' => $t->getTranslations('quote')])->all(),
            'student' => $studentT->map(fn ($t) => ['name' => $t->name, 'role' => $t->getTranslations('role'), 'quote' => $t->getTranslations('quote')])->all(),
        ]
        : Demo::testimonials();

    // Phase 4: homepage Jobs preview from DB, grouped by visa category.
    $dbJobsByVisa = JobVacancy::published()
        ->with('company', 'jobCategory', 'visaCategory')
        ->orderByDesc('is_featured')
        ->orderByDesc('published_at')
        ->get()
        ->groupBy(fn ($v) => $v->visaCategory?->slug);

    if ($dbJobsByVisa->isNotEmpty()) {
        $jobs = VisaCategory::orderBy('sort_order')->get()
            ->map(function ($visa) use ($dbJobsByVisa) {
                $items = ($dbJobsByVisa->get($visa->slug) ?? collect())->take(3)
                    ->map(fn ($v) => [
                        'title'    => $v->getTranslations('title'),
                        'company'  => $v->company->name,
                        'location' => $v->location_display,
                        'salary'   => $v->salary_range ?? '—',
                        'visa'     => $v->visaCategory?->t('name') ?? '',
                        'tag'      => ['id' => $v->jobCategory?->t('name', 'id') ?? '', 'en' => $v->jobCategory?->t('name', 'en') ?? '', 'ja' => $v->jobCategory?->t('name', 'ja') ?? ''],
                        'slug'     => $v->slug,
                    ])->all();

                return ['category' => $visa->t('name'), 'items' => $items];
            })
            ->filter(fn ($section) => count($section['items']) > 0)
            ->values()
            ->all();
    } else {
        $jobs = Demo::jobs();
    }
@endphp

@push('head')
<script>
    // Enable section-by-section snap scrolling on the homepage only.
    document.documentElement.classList.add('snap-sections');
    document.addEventListener('DOMContentLoaded', function () {
        document.body.classList.add('snap-sections');
    });
</script>
@endpush

@section('content')

{{-- ========== HERO ========== --}}
<section class="relative overflow-hidden min-h-[calc(100vh-4rem)] lg:min-h-[calc(100vh-5rem)] flex items-center">
    {{-- Slideshow background — admin-uploaded images, fall back to Japan defaults --}}
    <div class="hero-slideshow" aria-hidden="true">
        @foreach(\App\Support\SiteSettings::heroSlides() as $slide)
            <div class="slide" style="background-color: {{ $slide['color'] }}; background-image: url('{{ $slide['image'] }}');"></div>
        @endforeach
    </div>

    <div class="pointer-events-none absolute -top-40 left-1/2 -translate-x-1/2 h-[40rem] w-[40rem] rounded-full bg-brand-700/25 blur-3xl z-[1]"></div>
    <div class="pointer-events-none absolute -bottom-40 right-0 h-[28rem] w-[28rem] rounded-full bg-brand-900/40 blur-3xl z-[1]"></div>

    {{-- Falling sakura petals across hero --}}
    <x-jp.sakura-petals :count="14" />

    <div class="relative z-10 w-full mx-auto max-w-7xl px-6 py-16 lg:py-24">
        <div class="grid gap-12 lg:grid-cols-2 lg:items-center">
            <div>
                <div class="inline-flex items-center gap-2 rounded-full border border-brand-700/40 bg-brand-700/10 px-3 py-1 text-xs font-medium text-brand-300">
                    <span class="h-1.5 w-1.5 rounded-full bg-brand-400 animate-pulse"></span>
                    {{ __('Japan Career Ecosystem Platform') }}
                </div>

                <h1 class="mt-6 font-display text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-white leading-[1.05]">
                    {{ __('Step Toward a Successful Career in Japan') }}
                </h1>
                <p class="mt-6 max-w-xl text-lg text-surface-300 leading-relaxed">
                    {{ __('We accompany you from language training and work skills, to official job placement in Japan.') }}
                </p>

                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="/dashboard/register" class="btn-brand">
                        {{ __('Get Started') }}
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3 10a.75.75 0 01.75-.75h10.638L10.23 5.29a.75.75 0 111.04-1.08l5.5 5.25a.75.75 0 010 1.08l-5.5 5.25a.75.75 0 11-1.04-1.08l4.158-3.96H3.75A.75.75 0 013 10z" clip-rule="evenodd"/></svg>
                    </a>
                    <a href="#jobs" class="btn-ghost">{{ __('Search jobs') }}</a>
                </div>
            </div>

            <div class="relative">
                <div class="glass-card p-8 lg:p-10">
                    <p class="text-xs uppercase tracking-wider text-brand-400 font-semibold">{{ __('Trusted by') }}</p>
                    <p class="mt-2 font-display text-2xl text-white">{{ __('Thousands of Indonesian workers and dozens of partner companies in Japan.') }}</p>
                    <div class="mt-6 grid grid-cols-3 gap-4 text-center">
                        <div class="rounded-2xl border border-surface-700/60 bg-surface-900/40 p-4">
                            <p class="font-display text-2xl font-bold text-white">{{ \App\Support\SiteSettings::stat('students') }}</p>
                            <p class="mt-1 text-xs text-surface-400 uppercase tracking-wider">{{ __('Trained Students') }}</p>
                        </div>
                        <div class="rounded-2xl border border-surface-700/60 bg-surface-900/40 p-4">
                            <p class="font-display text-2xl font-bold text-white">{{ \App\Support\SiteSettings::stat('workers') }}</p>
                            <p class="mt-1 text-xs text-surface-400 uppercase tracking-wider">{{ __('Workers') }}</p>
                        </div>
                        <div class="rounded-2xl border border-surface-700/60 bg-surface-900/40 p-4">
                            <p class="font-display text-2xl font-bold text-white">{{ \App\Support\SiteSettings::stat('companies') }}</p>
                            <p class="mt-1 text-xs text-surface-400 uppercase tracking-wider">{{ __('Partner Companies') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Scroll-down cue at the bottom of the hero --}}
    <a href="#about" aria-label="{{ __('Scroll down') }}"
       class="absolute bottom-6 left-1/2 -translate-x-1/2 z-10 group inline-flex flex-col items-center gap-1 text-white/70 hover:text-white transition">
        <span class="text-[10px] uppercase tracking-[0.3em] font-semibold">{{ __('Scroll') }}</span>
        <span class="inline-flex h-9 w-6 items-center justify-center rounded-full border-2 border-white/40 group-hover:border-white/70 transition">
            <span class="w-1 h-2 rounded-full bg-white/70 animate-bounce"></span>
        </span>
    </a>
</section>

{{-- ========== BENEFITS — "Mengapa Passion Japan?" ========== --}}
<section id="about" class="reveal relative min-h-screen flex flex-col justify-center py-20 bg-surface-900/40">
    <div class="mx-auto max-w-7xl px-6">
        <div class="text-center max-w-2xl mx-auto">
            <p class="text-xs uppercase tracking-wider text-brand-400 font-semibold">{{ __('Why Us') }}</p>
            <h2 class="mt-2 font-display text-3xl sm:text-4xl font-bold text-white">
                {{ __('Why') }} <span class="text-brand-500">Passion Japan?</span>
            </h2>
            <p class="mt-4 text-surface-400">{{ __('Three reasons thousands of students choose us.') }}</p>
        </div>

        <div class="mt-12 grid gap-6 md:grid-cols-3">
            @foreach($benefits as $benefit)
                <div class="glass-card p-7 hover:border-brand-500/40 transition">
                    <div class="inline-flex h-12 w-12 items-center justify-center rounded-xl bg-brand-600/15 text-brand-400">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $benefit['icon'] }}"/></svg>
                    </div>
                    <h3 class="mt-5 font-display text-lg font-semibold text-white">{{ Demo::pick($benefit['title']) }}</h3>
                    <p class="mt-2 text-sm text-surface-400 leading-relaxed">{{ Demo::pick($benefit['desc']) }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ========== POPULAR PROGRAMS ========== --}}
<section class="reveal min-h-screen flex flex-col justify-center py-20">
    <div class="mx-auto max-w-7xl px-6">
        <div class="flex items-end justify-between flex-wrap gap-4">
            <div>
                <p class="text-xs uppercase tracking-wider text-brand-400 font-semibold">{{ __('Programs') }}</p>
                <h2 class="mt-2 font-display text-3xl sm:text-4xl font-bold text-white">{{ __('Popular Programs') }}</h2>
            </div>
            <a href="#" class="text-sm text-brand-400 hover:text-brand-300">{{ __('See all') }} →</a>
        </div>

        <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($programs as $program)
                <div class="glass-card p-6 hover:border-brand-500/40 transition group">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] uppercase tracking-wider font-semibold px-2 py-1 rounded-md bg-brand-600/15 text-brand-300">{{ Demo::pick($program['tag']) }}</span>
                        <svg class="h-5 w-5 text-surface-500 group-hover:text-brand-400 group-hover:translate-x-1 transition" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3 10a.75.75 0 01.75-.75h10.638L10.23 5.29a.75.75 0 111.04-1.08l5.5 5.25a.75.75 0 010 1.08l-5.5 5.25a.75.75 0 11-1.04-1.08l4.158-3.96H3.75A.75.75 0 013 10z" clip-rule="evenodd"/></svg>
                    </div>
                    <h3 class="mt-3 font-display text-lg font-semibold text-white">{{ $program['name'] }}</h3>
                    <p class="mt-2 text-sm text-surface-400">{{ Demo::pick($program['desc']) }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ========== JOB PORTAL ========== --}}
<section id="jobs" class="reveal min-h-screen flex flex-col justify-center py-20 bg-surface-900/40">
    <div class="mx-auto max-w-7xl px-6">
        <div class="text-center max-w-2xl mx-auto">
            <p class="text-xs uppercase tracking-wider text-brand-400 font-semibold">{{ __('Careers') }}</p>
            <h2 class="mt-2 font-display text-3xl sm:text-4xl font-bold text-white">
                <span class="text-brand-500">Job</span> {{ __('Portal') }}
            </h2>
            <p class="mt-3 text-surface-400">{{ __('Explore Job Opportunities in Japan.') }}</p>
        </div>

        @foreach($jobs as $section)
            <div class="mt-12">
                <div class="flex items-baseline justify-between flex-wrap gap-2">
                    <h3 class="font-display text-xl font-semibold text-white">{{ $section['category'] }}</h3>
                    <a href="{{ route('job.index') }}" class="text-sm text-brand-400 hover:text-brand-300">{{ __('See all') }} →</a>
                </div>

                <div class="mt-6 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    @foreach($section['items'] as $job)
                        <a href="{{ isset($job['slug']) ? route('job.show', $job['slug']) : route('job.index') }}" class="glass-card p-5 block hover:border-brand-500/50 transition group">
                            <div class="flex items-start gap-3">
                                <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-brand-600 to-brand-800 flex items-center justify-center font-display font-bold text-white text-sm shrink-0">
                                    {{ \Illuminate\Support\Str::of($job['company'])->substr(0,1)->upper() }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h4 class="font-semibold text-white truncate group-hover:text-brand-400 transition">{{ Demo::pick($job['title']) }}</h4>
                                    <p class="text-xs text-surface-400 truncate">{{ $job['company'] }}</p>
                                </div>
                            </div>
                            <div class="mt-4 flex flex-wrap gap-1.5">
                                <span class="text-[10px] uppercase tracking-wider font-semibold px-2 py-1 rounded-md bg-surface-800 text-surface-300">{{ Demo::pick($job['tag']) }}</span>
                                <span class="text-[10px] uppercase tracking-wider font-semibold px-2 py-1 rounded-md bg-brand-600/15 text-brand-300">{{ $job['visa'] }}</span>
                            </div>
                            <div class="mt-4 grid grid-cols-2 gap-2 text-xs text-surface-400">
                                <span class="flex items-center gap-1">
                                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    {{ $job['location'] }}
                                </span>
                                <span class="flex items-center gap-1 text-emerald-400">
                                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.66 0-3 .9-3 2s1.34 2 3 2 3 .9 3 2-1.34 2-3 2m0-8v1m0 14v1m0-16a8 8 0 100 16 8 8 0 000-16z"/></svg>
                                    {{ $job['salary'] }}
                                </span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</section>

{{-- ========== E-LEARNING ========== --}}
<section id="learning" class="reveal min-h-screen flex flex-col justify-center py-20">
    <div class="mx-auto max-w-7xl px-6">
        <div class="text-center max-w-2xl mx-auto">
            <p class="text-xs uppercase tracking-wider text-brand-400 font-semibold">{{ __('Learn') }}</p>
            <h2 class="mt-2 font-display text-3xl sm:text-4xl font-bold text-white">
                <span class="text-brand-500">E</span>-Learning
            </h2>
            <p class="mt-3 text-surface-400">{{ __('Master Japanese language and work culture on your own schedule.') }}</p>
        </div>

        @if($dbCourses->isNotEmpty())
            <div class="mt-12 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                @foreach($dbCourses as $course)
                    <a href="{{ route('elearning.show', $course->slug) }}" class="glass-card overflow-hidden block hover:border-brand-500/50 transition group flex flex-col">
                        <div class="aspect-video w-full bg-gradient-to-br from-brand-700 to-brand-900 relative">
                            @if($course->thumbnail_url)
                                <img src="{{ $course->thumbnail_url }}" alt="" class="absolute inset-0 h-full w-full object-cover">
                            @else
                                <div class="absolute inset-0 flex items-center justify-center text-white/30">
                                    <svg class="h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 14l9-5-9-5-9 5 9 5z"/></svg>
                                </div>
                            @endif
                        </div>
                        <div class="p-5 flex-1 flex flex-col">
                            <span class="inline-block self-start text-[10px] uppercase tracking-wider font-semibold px-2 py-1 rounded-md bg-brand-600/15 text-brand-300 mb-2">{{ __('level.'.$course->level) }}</span>
                            <h3 class="font-display font-bold text-white leading-tight group-hover:text-brand-400">{{ $course->t('title') }}</h3>
                            @if($course->t('subtitle'))
                                <p class="mt-2 text-xs text-surface-400 line-clamp-2">{{ $course->t('subtitle') }}</p>
                            @endif
                            <div class="mt-auto pt-3 flex items-center justify-between text-xs text-surface-400">
                                <span>{{ trans_choice('{1} :count chapter|[2,*] :count chapters', $course->chapters_count, ['count' => $course->chapters_count]) }}</span>
                                <span class="text-brand-400 font-semibold">{{ $course->price_display }}</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
            <div class="mt-8 text-center">
                <a href="{{ route('elearning.index') }}" class="btn-ghost text-sm">{{ __('See all') }} →</a>
            </div>
        @else
            <div class="mt-12 grid gap-6 md:grid-cols-2 max-w-4xl mx-auto">
                @foreach($courses as $course)
                    <div class="relative overflow-hidden glass-card p-8 hover:border-brand-500/40 transition">
                        <div class="absolute -top-12 -right-12 h-40 w-40 rounded-full bg-gradient-to-br {{ $course['color'] }} opacity-30 blur-3xl"></div>
                        <div class="relative">
                            <span class="inline-block text-[10px] uppercase tracking-wider font-semibold px-2 py-1 rounded-md bg-brand-600/15 text-brand-300">JLPT</span>
                            <h3 class="mt-3 font-display text-2xl font-bold text-white">{{ $course['name'] }}</h3>
                            <p class="mt-1 text-xs text-surface-400">{{ Demo::pick($course['duration']) }} · {{ Demo::pick($course['chapters']) }}</p>
                            <p class="mt-4 text-sm text-surface-300 leading-relaxed">{{ Demo::pick($course['desc']) }}</p>
                            <a href="{{ route('elearning.index') }}" class="mt-6 btn-brand text-sm">
                                {{ __('Join Now') }}
                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3 10a.75.75 0 01.75-.75h10.638L10.23 5.29a.75.75 0 111.04-1.08l5.5 5.25a.75.75 0 010 1.08l-5.5 5.25a.75.75 0 11-1.04-1.08l4.158-3.96H3.75A.75.75 0 013 10z" clip-rule="evenodd"/></svg>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>

{{-- ========== FAQ ========== --}}
<section class="reveal min-h-screen flex flex-col justify-center py-20 bg-surface-900/40">
    <div class="mx-auto max-w-3xl px-6">
        <div class="text-center">
            <p class="text-xs uppercase tracking-wider text-brand-400 font-semibold">{{ __('FAQ') }}</p>
            <h2 class="mt-2 font-display text-3xl sm:text-4xl font-bold text-white">
                <span class="text-brand-500">FAQ</span> ({{ __('Frequently Asked Questions') }})
            </h2>
        </div>

        <div class="mt-10 space-y-3">
            @foreach($faqs as $faq)
                <details class="group glass-card p-5 transition">
                    <summary class="flex items-center justify-between cursor-pointer list-none">
                        <span class="font-semibold text-white">{{ Demo::pick($faq['q']) }}</span>
                        <svg class="h-5 w-5 text-brand-400 transition group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </summary>
                    <p class="mt-3 text-sm text-surface-400 leading-relaxed">{{ Demo::pick($faq['a']) }}</p>
                </details>
            @endforeach
        </div>
    </div>
</section>

{{-- ========== TESTIMONIALS ========== --}}
<section class="reveal min-h-screen flex flex-col justify-center py-20">
    <div class="mx-auto max-w-7xl px-6">
        <div class="text-center max-w-2xl mx-auto">
            <p class="text-xs uppercase tracking-wider text-brand-400 font-semibold">{{ __('Testimonials') }}</p>
            <h2 class="mt-2 font-display text-3xl sm:text-4xl font-bold text-white">{{ __('What people say about us') }}</h2>
        </div>

        <div class="mt-12 grid gap-10 lg:grid-cols-2">
            <div>
                <h3 class="font-display text-xl font-semibold text-white mb-5">{{ __('Partner Companies') }}</h3>
                <div class="space-y-4">
                    @foreach($testimonials['company'] as $t)
                        <div class="glass-card p-6">
                            <p class="text-sm text-surface-200 leading-relaxed">&ldquo;{{ Demo::pick($t['quote']) }}&rdquo;</p>
                            <div class="mt-4 flex items-center gap-3">
                                <div class="h-10 w-10 rounded-full bg-gradient-to-br from-brand-600 to-brand-800 flex items-center justify-center font-bold text-white">{{ \Illuminate\Support\Str::of($t['name'])->substr(0,1)->upper() }}</div>
                                <div>
                                    <p class="text-sm font-semibold text-white">{{ $t['name'] }}</p>
                                    <p class="text-xs text-surface-400">{{ Demo::pick($t['role']) }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div>
                <h3 class="font-display text-xl font-semibold text-white mb-5">{{ __('Students') }}</h3>
                <div class="space-y-4">
                    @foreach($testimonials['student'] as $t)
                        <div class="glass-card p-6">
                            <p class="text-sm text-surface-200 leading-relaxed">&ldquo;{{ Demo::pick($t['quote']) }}&rdquo;</p>
                            <div class="mt-4 flex items-center gap-3">
                                <div class="h-10 w-10 rounded-full bg-gradient-to-br from-brand-600 to-brand-800 flex items-center justify-center font-bold text-white">{{ \Illuminate\Support\Str::of($t['name'])->substr(0,1)->upper() }}</div>
                                <div>
                                    <p class="text-sm font-semibold text-white">{{ $t['name'] }}</p>
                                    <p class="text-xs text-surface-400">{{ Demo::pick($t['role']) }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ========== ALUR KERJA — per-visa slides ========== --}}
@php
    $badgeColors = [
        'brand'   => ['bg' => 'bg-brand-600/15', 'text' => 'text-brand-300', 'border' => 'border-brand-500/30'],
        'warning' => ['bg' => 'bg-amber-500/15', 'text' => 'text-amber-300', 'border' => 'border-amber-500/30'],
        'info'    => ['bg' => 'bg-sky-500/15',   'text' => 'text-sky-300',   'border' => 'border-sky-500/30'],
        'success' => ['bg' => 'bg-emerald-500/15','text'=> 'text-emerald-300','border'=> 'border-emerald-500/30'],
    ];
@endphp
<section id="workflow" class="reveal min-h-screen flex flex-col justify-center py-20 bg-surface-900/40">
    <div class="mx-auto max-w-7xl px-6">
        <div class="text-center max-w-2xl mx-auto">
            <p class="text-xs uppercase tracking-wider text-brand-400 font-semibold">{{ __('Process') }}</p>
            <h2 class="mt-2 font-display text-3xl sm:text-4xl font-bold text-white">
                {{ __('Alur Bekerja') }} <span class="text-brand-500">{{ __('Ke Jepang') }}</span>
            </h2>
            <p class="mt-3 text-surface-400">{{ __('Pilih jalur visa untuk melihat alur lengkapnya.') }}</p>
        </div>

        {{-- Tab nav --}}
        <div class="mt-10 flex flex-wrap items-center justify-center gap-2" role="tablist" aria-label="{{ __('Visa workflow tabs') }}">
            @foreach($visaWorkflows as $i => $vw)
                <button type="button"
                        data-workflow-tab="{{ $vw['slug'] }}"
                        class="workflow-tab-btn px-5 py-2.5 rounded-xl border text-sm font-semibold transition focus:outline-none focus:ring-2 focus:ring-brand-500/40
                            {{ $i === 0
                                ? 'bg-brand-600 border-brand-600 text-white shadow-lg shadow-brand-600/30'
                                : 'border-surface-700 text-surface-300 hover:border-brand-500/50 hover:text-white' }}"
                        role="tab"
                        aria-selected="{{ $i === 0 ? 'true' : 'false' }}">
                    {{ Demo::pick($vw['name']) }}
                </button>
            @endforeach
        </div>

        {{-- Slides --}}
        <div class="mt-10">
            @foreach($visaWorkflows as $i => $vw)
                <div data-workflow-panel="{{ $vw['slug'] }}"
                     role="tabpanel"
                     class="workflow-panel {{ $i === 0 ? '' : 'hidden' }}">

                    <p class="text-center text-surface-400 text-sm max-w-xl mx-auto mb-8">{{ Demo::pick($vw['tagline']) }}</p>

                    <div class="grid gap-5 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
                        @foreach($vw['steps'] as $step)
                            <div class="relative glass-card p-5 pt-8 hover:border-brand-500/40 transition group flex flex-col items-center text-center">
                                {{-- Step number badge top-left --}}
                                <div class="absolute -top-3 -left-3 h-9 w-9 rounded-xl bg-brand-600 flex items-center justify-center font-display font-bold text-white text-sm shadow-lg shadow-brand-600/40 group-hover:scale-110 transition">
                                    {{ $step['n'] }}
                                </div>

                                {{-- Illustration: uploaded image > heroicon > generic placeholder --}}
                                <div class="h-20 w-20 rounded-full overflow-hidden bg-gradient-to-br from-brand-600/20 to-surface-700/40 ring-2 ring-brand-500/20 flex items-center justify-center mb-4 group-hover:ring-brand-500/50 transition">
                                    @if(! empty($step['icon_url']))
                                        <img src="{{ $step['icon_url'] }}" alt="" class="h-full w-full object-cover">
                                    @elseif(! empty($step['icon']))
                                        @svg($step['icon'], 'h-10 w-10 text-brand-400')
                                    @else
                                        <svg class="h-10 w-10 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9" stroke-width="2"/></svg>
                                    @endif
                                </div>

                                <h3 class="font-semibold text-white text-sm leading-snug min-h-[2.5rem]">{{ Demo::pick($step['title']) }}</h3>

                                @if(! empty($step['badge']))
                                    @php $c = $badgeColors[$step['badge']['color']] ?? $badgeColors['brand']; @endphp
                                    <span class="mt-3 inline-block text-[10px] uppercase tracking-wider font-bold px-2 py-1 rounded-md border {{ $c['bg'] }} {{ $c['text'] }} {{ $c['border'] }}">
                                        {{ Demo::pick($step['badge']['label']) }}
                                    </span>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    @if(! empty($vw['notes']))
                        <div class="mt-8 max-w-3xl mx-auto glass-card p-5 border-amber-500/30 bg-amber-500/[0.04]">
                            <p class="text-xs uppercase tracking-wider text-amber-300 font-bold mb-3 flex items-center gap-2">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                {{ __('Catatan Penting') }}
                            </p>
                            <ul class="space-y-2">
                                @foreach($vw['notes'] as $note)
                                    <li class="flex items-start gap-2 text-sm text-surface-200">
                                        <span class="text-amber-400 mt-1">•</span>
                                        <span>{{ Demo::pick($note) }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</section>

@push('head')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tabs   = document.querySelectorAll('.workflow-tab-btn');
    const panels = document.querySelectorAll('.workflow-panel');
    const ACTIVE_CLASSES   = ['bg-brand-600','border-brand-600','text-white','shadow-lg','shadow-brand-600/30'];
    const INACTIVE_CLASSES = ['border-surface-700','text-surface-300','hover:border-brand-500/50','hover:text-white'];

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            const slug = tab.dataset.workflowTab;

            tabs.forEach(t => {
                const isActive = t === tab;
                t.setAttribute('aria-selected', isActive ? 'true' : 'false');
                ACTIVE_CLASSES.forEach(c => t.classList.toggle(c, isActive));
                INACTIVE_CLASSES.forEach(c => t.classList.toggle(c, ! isActive));
            });

            panels.forEach(p => {
                p.classList.toggle('hidden', p.dataset.workflowPanel !== slug);
            });

            // Smooth-scroll the section into view on small screens after switching
            if (window.innerWidth < 768) {
                document.getElementById('workflow')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });
});
</script>
@endpush

{{-- ========== CTA ========== --}}
{{-- Last section stays a snap target so `mandatory` snap can find it,
     but it's deliberately short (no min-h-screen) so when the user
     snaps to it they see the CTA card AND the footer in one view. --}}
<section class="reveal py-20">
    <div class="mx-auto max-w-5xl px-6">
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-brand-700 via-brand-800 to-surface-900 p-10 lg:p-14">
            <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_20%_20%,rgba(255,255,255,0.08),transparent_40%)]"></div>
            <div class="relative flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div>
                    <h2 class="font-display text-3xl lg:text-4xl font-bold text-white max-w-xl">{{ __('Ready to start your career in Japan?') }}</h2>
                    <p class="mt-3 text-brand-100 max-w-xl">{{ __('Free consultation. Talk to our team and get a clear path within 24 hours.') }}</p>
                </div>
                <a href="https://wa.me/{{ \App\Support\SiteSettings::contact('whatsapp') }}" target="_blank" rel="noopener" class="inline-flex shrink-0 items-center gap-2 px-7 py-3.5 rounded-xl bg-white text-brand-700 font-semibold hover:bg-brand-50 transition shadow-xl">
                    {{ __('Talk to us on WhatsApp') }}
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3 10a.75.75 0 01.75-.75h10.638L10.23 5.29a.75.75 0 111.04-1.08l5.5 5.25a.75.75 0 010 1.08l-5.5 5.25a.75.75 0 11-1.04-1.08l4.158-3.96H3.75A.75.75 0 013 10z" clip-rule="evenodd"/></svg>
                </a>
            </div>
        </div>
    </div>
</section>

{{-- Audio player now lives in layouts/app.blade.php so every public page has it. --}}
@if(false)
    <audio id="pj-theme-audio" src="" loop preload="auto" aria-hidden="true"></audio>

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
        /* Muted state: button breathes to draw attention */
        #pj-audio-toggle:not(.is-playing) {
            animation: pj-audio-breathe 1.6s ease-in-out infinite;
        }
        @keyframes pj-audio-breathe {
            0%, 100% { transform: scale(1); }
            50%      { transform: scale(1.1); }
        }
        /* Playing state: ripples emanate */
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

            // Saved preference: '1' = user wants muted, '0' = user wants playing.
            var saved = null;
            try { saved = localStorage.getItem('pj-audio-muted'); } catch (e) {}

            // Try autoplay WITH SOUND first (works if user has prior site interaction).
            // If browser blocks, fall back to muted autoplay + breathing button + hint.
            audio.muted = saved === '1';
            audio.play().catch(function () {
                // Blocked → mute and try again
                audio.muted = true;
                audio.play().catch(function () {});
                if (saved !== '1') showHint();
            }).finally(updateUI);

            // First user gesture: try to unmute (unless user has explicitly muted)
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

            // Manual toggle button
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

@endsection
