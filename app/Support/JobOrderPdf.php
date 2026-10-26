<?php

namespace App\Support;

use App\Models\JobVacancy;

/** Renders a vacancy's Job Order as the "Terjemahan Job Order" PDF. */
class JobOrderPdf
{
    public static function render(JobVacancy $vacancy): string
    {
        $vacancy->loadMissing('company');

        $mpdf = Pdf::make([
            'margin_left'   => 22,
            'margin_right'  => 22,
            'margin_top'    => 34,
            'margin_bottom' => 20,
            'margin_header' => 10,
            'margin_footer' => 9,
        ]);
        $title = JobOrder::documentTitle($vacancy);
        $mpdf->SetTitle($title);
        $mpdf->SetAuthor('PT. Passion Japan Indonesia');

        $logo = resource_path('images/logo-pdf.png');
        $mpdf->SetHTMLHeader('<img src="'.$logo.'" style="height: 17mm;">');
        $mpdf->SetHTMLFooter('<table width="100%" style="font-family: freeserif; font-size: 8pt; color: #888;"><tr>'
            .'<td>PT. Passion Japan Indonesia</td><td style="text-align: right;">{PAGENO} / {nbpg}</td></tr></table>');

        $mpdf->WriteHTML(view('pdf.job-order', [
            'title'    => $title,
            'sections' => JobOrder::forDocument($vacancy),
        ])->render());

        return $mpdf->OutputBinaryData();
    }

    public static function filename(JobVacancy $vacancy): string
    {
        return JobOrder::documentTitle($vacancy).'.pdf';
    }
}
