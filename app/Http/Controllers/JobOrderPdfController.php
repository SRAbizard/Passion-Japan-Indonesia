<?php

namespace App\Http\Controllers;

use App\Models\JobVacancy;
use App\Support\JobOrder;
use App\Support\JobOrderPdf;
use App\Support\Pdf;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * The generated Job Order PDF: always available to admins (from the
 * vacancy edit page), and on the public job page when the vacancy is
 * marked "show the Job Order to candidates". ?download=1 downloads it,
 * otherwise it opens in the browser.
 */
class JobOrderPdfController extends Controller
{
    public function admin(Request $request, JobVacancy $vacancy): Response
    {
        if (! $request->user()) {
            return redirect()->guest(route('filament.admin.auth.login'));
        }
        abort_unless($request->user()?->hasAnyRole(['superadmin', 'admin']), 403);

        return $this->send($request, $vacancy);
    }

    public function public(Request $request, string $slug): Response
    {
        $vacancy = JobVacancy::published()->where('slug', $slug)->firstOrFail();
        abort_unless($vacancy->job_order_public && JobOrder::filled($vacancy), 404);

        return $this->send($request, $vacancy);
    }

    private function send(Request $request, JobVacancy $vacancy): Response
    {
        return Pdf::respond(JobOrderPdf::render($vacancy), JobOrderPdf::filename($vacancy), $request->boolean('download'));
    }
}
