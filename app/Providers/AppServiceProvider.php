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
        //
    }

    public function boot(): void
    {
        Event::listen(Registered::class, AssignDefaultRoleOnRegistration::class);
    }
}
