<?php

namespace App\Filament\Auth;

use Filament\Auth\Http\Responses\Contracts\LoginResponse as Responsable;
use Filament\Facades\Filament;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;

/**
 * After login, route the user to the panel that matches their role:
 *   - superadmin / admin → /admin
 *   - student (or anything else) → /dashboard
 *
 * This means /admin/login and /dashboard/login both work for both user
 * types — students who land on the admin URL still end up in their own
 * dashboard, and admins who land on the student URL end up in the admin
 * panel. One login URL is enough.
 */
class RoleAwareLoginResponse implements Responsable
{
    public function toResponse($request): RedirectResponse | Redirector
    {
        $user = $request->user();

        $targetPanel = ($user && $user->hasAnyRole(['superadmin', 'admin']))
            ? 'admin'
            : 'student';

        $url = Filament::getPanel($targetPanel)->getUrl();

        return redirect()->intended($url);
    }
}
