<?php

namespace App\Filament\Auth;

use Filament\Auth\Http\Responses\Contracts\LogoutResponse as Responsable;
use Filament\Facades\Filament;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;

/**
 * After logout, send everyone — admin or student — to the same login URL
 * (the student panel's), so the user-facing flow stays consistent with
 * the unified login URL exposed by the public navbar.
 */
class UnifiedLogoutResponse implements Responsable
{
    public function toResponse($request): RedirectResponse | Redirector
    {
        $studentPanel = Filament::getPanel('student', isStrict: false);

        $url = $studentPanel?->hasLogin()
            ? $studentPanel->getLoginUrl()
            : url('/');

        return redirect()->to($url);
    }
}
