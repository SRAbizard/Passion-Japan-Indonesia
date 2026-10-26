<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\Pdf;
use App\Support\Rirekisho;
use App\Support\RirekishoPdf;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * 履歴書 (Japanese CV) PDF: students download their own from the Student
 * Dashboard; admins download any student's from the student page.
 * ?download=1 downloads, otherwise it opens in the browser.
 */
class CvPdfController extends Controller
{
    public function mine(Request $request): Response
    {
        if (! $request->user()) {
            return redirect()->guest(route('filament.student.auth.login'));
        }

        return $this->send($request, $request->user());
    }

    public function admin(Request $request, User $user): Response
    {
        if (! $request->user()) {
            return redirect()->guest(route('filament.admin.auth.login'));
        }
        abort_unless($request->user()->hasAnyRole(['superadmin', 'admin']), 403);

        return $this->send($request, $user);
    }

    private function send(Request $request, User $user): Response
    {
        $pdf = RirekishoPdf::render($user, $request->string('job')->limit(160)->toString() ?: null);

        return Pdf::respond($pdf, Rirekisho::filename($user), $request->boolean('download'));
    }
}
