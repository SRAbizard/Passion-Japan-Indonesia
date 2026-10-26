<?php

namespace App\Support;

use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Mpdf\Mpdf;
use Symfony\Component\HttpFoundation\Response;

/**
 * mPDF with the bundled BIZ UDPGothic font (resources/fonts, SIL OFL) so
 * Japanese text renders in every generated document. Pages that use a
 * Latin font fall back to it for Japanese characters.
 */
class Pdf
{
    public static function make(array $config = []): Mpdf
    {
        $fontDirs = (new ConfigVariables())->getDefaults()['fontDir'];
        $fontData = (new FontVariables())->getDefaults()['fontdata'];

        $tempDir = storage_path('app/mpdf');
        if (! is_dir($tempDir)) {
            mkdir($tempDir, 0775, true);
        }

        return new Mpdf($config + [
            'mode'              => 'utf-8',
            'format'            => 'A4',
            'tempDir'           => $tempDir,
            'fontDir'           => [...$fontDirs, resource_path('fonts')],
            'fontdata'          => $fontData + [
                'bizud' => ['R' => 'BIZUDPGothic-Regular.ttf', 'B' => 'BIZUDPGothic-Bold.ttf'],
            ],
            'default_font'      => 'bizud',
            'useSubstitutions'  => true,
            'backupSubsFont'    => ['bizud'],
            'autoLangToFont'    => false,
        ]);
    }

    /** Send a rendered PDF inline (view in the browser) or as a download. */
    public static function respond(string $bytes, string $filename, bool $download = false): Response
    {
        $safe = str_replace(['"', '/', '\\'], ['', '-', '-'], $filename);
        $ascii = preg_replace('/[^\x20-\x7E]/', '_', $safe);

        return response($bytes, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => ($download ? 'attachment' : 'inline')
                .'; filename="'.$ascii.'"; filename*=UTF-8\'\''.rawurlencode($safe),
            'Cache-Control'       => 'private, no-store',
        ]);
    }
}
