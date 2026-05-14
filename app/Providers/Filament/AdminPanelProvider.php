<?php

namespace App\Providers\Filament;

use Filament\Enums\ThemeMode;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandName('Passion Japan Indonesia')
            ->brandLogo(fn () => file_exists(public_path('images/logo.png')) ? asset('images/logo.png') : null)
            ->brandLogoHeight('2rem')
            ->favicon(asset('images/logo.png'))
            ->colors([
                'primary' => Color::hex('#b32510'),
                'danger'  => Color::Rose,
                'success' => Color::Emerald,
                'warning' => Color::Amber,
                'info'    => Color::Sky,
            ])
            ->defaultThemeMode(ThemeMode::Dark)
            ->sidebarCollapsibleOnDesktop()
            ->userMenuItems(self::buildLocaleMenuItems())
            ->databaseNotifications()
            ->databaseNotificationsPolling('60s')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                \App\Http\Middleware\SetLocale::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }

    /**
     * One MenuItem per supported locale. Clicking switches the locale in the
     * session and redirects back to the admin page the user was on (LocaleController
     * uses redirect()->back()). The currently active locale is marked.
     *
     * NOTE: url() and label() use Closures so that `route()` and `app()->getLocale()`
     * are evaluated on render — at panel-boot time the route registry isn't built yet.
     */
    public static function buildLocaleMenuItems(): array
    {
        return collect(config('passion.locales', []))
            ->map(function ($meta, $code) {
                return MenuItem::make()
                    ->label(function () use ($meta, $code) {
                        $isActive = app()->getLocale() === $code;
                        return ($meta['flag'] ?? '').' '
                             .($meta['native'] ?? strtoupper($code))
                             .($isActive ? '  ✓' : '');
                    })
                    ->icon(fn () => app()->getLocale() === $code ? 'heroicon-o-language' : null)
                    ->color(fn () => app()->getLocale() === $code ? 'primary' : 'gray')
                    ->url(fn () => route('locale.switch', $code));
            })
            ->values()
            ->all();
    }
}
