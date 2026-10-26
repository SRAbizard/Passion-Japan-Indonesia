<?php

namespace App\Support;

use Illuminate\Http\Request;

/**
 * Honeypot for public forms: a visually hidden "website" field that people
 * never see but naive bots fill in, plus a minimum time between rendering
 * the form and submitting it. Pair with <x-honeypot /> in the form and a
 * throttle middleware on the route.
 */
class SpamGuard
{
    public const FIELD = 'website';
    public const TIME_FIELD = 'form_started';
    private const MIN_SECONDS = 3;

    public static function isBot(Request $request): bool
    {
        if (filled($request->input(self::FIELD))) {
            return true;
        }

        $started = (int) $request->input(self::TIME_FIELD);

        return $started > 0 && (time() - $started) < self::MIN_SECONDS;
    }
}
