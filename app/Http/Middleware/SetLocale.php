<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $supported = array_keys(config('passion.locales', []));
        $fallback  = config('passion.default_locale', config('app.locale', 'id'));

        $locale = $request->query('lang')
            ?? $request->session()->get('locale')
            ?? optional($request->user())->locale
            ?? $request->getPreferredLanguage($supported)
            ?? $fallback;

        if (! in_array($locale, $supported, true)) {
            $locale = $fallback;
        }

        app()->setLocale($locale);
        $request->session()->put('locale', $locale);

        return $next($request);
    }
}
