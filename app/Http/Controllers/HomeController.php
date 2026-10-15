<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Models\Gallery;
use App\Models\JobCategory;
use App\Models\JobVacancy;
use App\Models\Testimonial;
use App\Models\VisaCategory;
use App\Support\HomepageDemoData;
use App\Support\JapanRegions;
use App\Support\SiteSettings;
use Illuminate\View\View;

/**
 * Homepage — "Misi ke Jepang". Everything the interactive pieces need
 * (finder, map, match quiz, job cards) is serialised once into the
 * page as JSON and driven client-side by resources/js/home.js.
 */
class HomeController extends Controller
{
    public function index(): View
    {
        $vacancies = JobVacancy::published()
            ->with('company', 'jobCategory', 'visaCategory')
            ->orderByDesc('is_featured')
            ->orderByDesc('published_at')
            ->limit(300)
            ->get();

        $jobs = $vacancies->map(fn (JobVacancy $v) => [
            'title'    => $v->t('title'),
            'company'  => $v->company?->name,
            'location' => $v->location_display,
            'region'   => JapanRegions::keyFor($v->location_prefecture, $v->location_city),
            'category' => $v->jobCategory?->slug,
            'categoryName' => $v->jobCategory?->t('name'),
            'visa'     => $v->visaCategory?->slug,
            'visaName' => $v->visaCategory?->t('name'),
            'min'      => $v->salary_min,
            'max'      => $v->salary_max,
            'currency' => $v->salary_currency,
            'period'   => $v->salary_period,
            'featured' => (bool) $v->is_featured,
            'new'      => $v->published_at?->gt(now()->subDays(14)) ?? false,
            'url'      => route('job.show', $v->slug),
        ])->values();

        $regions = collect(JapanRegions::all())->map(fn ($r, $key) => [
            'key'     => $key,
            'name'    => __($r['name']),
            'jp'      => $r['jp'],
            'x'       => $r['x'],
            'y'       => $r['y'],
            'airport' => $r['airport'],
            'count'   => $jobs->where('region', $key)->count(),
        ])->values();

        $faqs = Faq::published()->get();
        $testimonials = Testimonial::published()->get();

        return view('welcome', [
            'jobs'          => $jobs,
            'regions'       => $regions,
            'categories'    => JobCategory::orderBy('sort_order')->get(),
            'visas'         => VisaCategory::orderBy('sort_order')->get(),
            'workflows'     => HomepageDemoData::visaWorkflows(),
            'faqs'          => $faqs->isNotEmpty()
                ? $faqs->map(fn ($f) => ['q' => $f->t('question'), 'a' => $f->t('answer')])->all()
                : collect(HomepageDemoData::faqs())->map(fn ($f) => ['q' => HomepageDemoData::pick($f['q']), 'a' => HomepageDemoData::pick($f['a'])])->all(),
            'testimonials'  => $testimonials,
            'albums'        => Gallery::published()
                ->withCount(['publishedItems as items_count'])
                ->whereHas('publishedItems')
                ->orderBy('sort_order')->orderByDesc('id')
                ->limit(8)->get(),
            'heroSlides'    => SiteSettings::heroSlides(),
            'companyVideo'  => SiteSettings::companyVideo(),
            'academyUrl'    => SiteSettings::academyUrl(),
            'stats'         => [
                ['value' => SiteSettings::stat('students'),  'label' => __('students trained')],
                ['value' => SiteSettings::stat('workers'),   'label' => __('workers placed in Japan')],
                ['value' => SiteSettings::stat('companies'), 'label' => __('partner companies in Japan')],
            ],
        ]);
    }
}
