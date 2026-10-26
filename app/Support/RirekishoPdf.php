<?php

namespace App\Support;

use App\Models\User;

/** Renders a student's 履歴書 (Japanese CV) PDF from their profile. */
class RirekishoPdf
{
    public static function render(User $user, ?string $desiredJob = null): string
    {
        $cv = Rirekisho::data($user, $desiredJob);
        $photo = $cv['photo'] ? self::passportCrop($cv['photo']) : null;
        $cv['photo'] = $photo;

        $mpdf = Pdf::make([
            'margin_left'   => 15,
            'margin_right'  => 15,
            'margin_top'    => 13,
            'margin_bottom' => 16,
            'margin_footer' => 8,
        ]);
        $mpdf->SetTitle('履歴書 — '.$cv['name']);
        $mpdf->SetAuthor('PT. Passion Japan Indonesia');
        $mpdf->SetHTMLFooter('<table width="100%" style="border-top: 0.6pt solid #999; font-family: bizud; font-size: 7.5pt;"><tr>'
            .'<td style="padding-top: 3pt; color: #9F1B32; font-weight: bold;">PASSION JAPAN INDONESIA</td>'
            .'<td style="padding-top: 3pt; text-align: right; color: #999;">{PAGENO} / {nbpg}</td></tr></table>');

        try {
            $mpdf->WriteHTML(view('pdf.rirekisho', ['cv' => $cv])->render());

            return $mpdf->OutputBinaryData();
        } finally {
            if ($photo) {
                @unlink($photo);
            }
        }
    }

    /**
     * Centre-crop the profile photo to 3:4 (the 3 × 4 cm photo box), so
     * mPDF does not stretch it. Returns a temporary JPEG path.
     */
    private static function passportCrop(string $path): ?string
    {
        $data = @file_get_contents($path);
        $src  = $data !== false ? @imagecreatefromstring($data) : false;
        if (! $src) {
            return null;
        }
        [$w, $h] = [imagesx($src), imagesy($src)];
        $cw = (int) min($w, $h * 3 / 4);
        $ch = (int) min($h, $w * 4 / 3);
        $out = imagecreatetruecolor(450, 600);
        imagecopyresampled($out, $src, 0, 0, (int) (($w - $cw) / 2), (int) (($h - $ch) / 2), 450, 600, $cw, $ch);
        $dir = storage_path('app/mpdf');
        is_dir($dir) || mkdir($dir, 0775, true);
        $base = tempnam($dir, 'cv');
        @unlink($base);
        $tmp = $base.'.jpg';
        imagejpeg($out, $tmp, 88);

        return $tmp;
    }
}
