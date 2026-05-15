<?php

namespace App\Providers;

use App\Listeners\AssignDefaultRoleOnRegistration;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Route post-login redirects by role so /admin/login and
        // /dashboard/login behave identically for both admins and students.
        $this->app->bind(
            \Filament\Auth\Http\Responses\Contracts\LoginResponse::class,
            \App\Filament\Auth\RoleAwareLoginResponse::class,
        );
    }

    public function boot(): void
    {
        Event::listen(Registered::class, AssignDefaultRoleOnRegistration::class);
    }
}
