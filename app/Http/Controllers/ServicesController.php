<?php

namespace App\Http\Controllers;

use App\Support\HomepageDemoData;
use App\Support\MatchingData;
use Illuminate\View\View;

/**
 * /services — the three visa routes (who each fits, key requirements, the
 * admin-managed step-by-step workflow, open jobs) and the support services.
 */
class ServicesController extends Controller
{
    public function __invoke(MatchingData $matching): View
    {
        $workflows = collect(HomepageDemoData::visaWorkflows())->keyBy('slug');
        $jobCounts = $matching->jobs()->countBy('visa');
        // Map each visa category to a route type for the requirement copy.
        $typeBySlug = collect($matching->visaRoutes())->mapWithKeys(fn ($r, $type) => [$r['slug'] => $type]);

        $routes = $matching->visas()->map(fn ($v) => [
            'slug'     => $v->slug,
            'name'     => $v->t('name'),
            'jp'       => $v->t('name', 'ja'),
            'desc'     => $v->t('description'),
            'type'     => $typeBySlug[$v->slug] ?? null,
            'jobs'     => (int) ($jobCounts[$v->slug] ?? 0),
            'workflow' => $workflows[$v->slug] ?? null,
        ]);

        return view('pages.services', ['routes' => $routes]);
    }
}
