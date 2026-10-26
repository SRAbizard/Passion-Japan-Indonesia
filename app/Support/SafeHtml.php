<?php

namespace App\Support;

use Illuminate\Support\HtmlString;
use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

/**
 * Cleans rich-text HTML written in the admin (blog body, event and job
 * descriptions) before it is printed unescaped on public pages: keeps
 * formatting, links and images; drops scripts, inline event handlers,
 * iframes and javascript: URLs, so a compromised or careless editor
 * account cannot inject code into visitors' browsers.
 */
class SafeHtml
{
    private static ?HtmlSanitizer $sanitizer = null;

    public static function clean(?string $html): HtmlString
    {
        if (blank($html)) {
            return new HtmlString('');
        }

        self::$sanitizer ??= new HtmlSanitizer(
            (new HtmlSanitizerConfig())
                ->allowSafeElements()
                ->allowAttribute('class', '*')
                ->allowAttribute('style', ['span', 'p', 'td', 'th'])
                ->allowLinkSchemes(['https', 'http', 'mailto', 'tel'])
                ->allowMediaSchemes(['https', 'http', 'data'])
                ->allowRelativeLinks()
                ->allowRelativeMedias()
                ->forceAttribute('a', 'rel', 'noopener noreferrer')
                ->withMaxInputLength(500_000)
        );

        return new HtmlString(self::$sanitizer->sanitize($html));
    }
}
