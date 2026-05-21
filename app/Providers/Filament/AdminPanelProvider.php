<?php

namespace App\Providers\Filament;

use Filament\Enums\ThemeMode;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationGroup;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
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
            // Declare nav groups explicitly so we control:
            //  - the order (Recruitment → Learning → Students → CMS)
            //  - collapsed-by-default state (Filament's default is expanded,
            //    which made the sidebar feel busy when admin opened the
            //    dashboard with no active group). Groups auto-expand when
            //    the current page lives inside them, so context isn't lost.
            ->navigationGroups([
                NavigationGroup::make()->label(fn () => __('Recruitment'))->collapsed(),
                NavigationGroup::make()->label(fn () => __('Learning'))->collapsed(),
                NavigationGroup::make()->label(fn () => __('Students'))->collapsed(),
                NavigationGroup::make()->label(fn () => __('CMS'))->collapsed(),
            ])
            ->userMenuItems(array_merge(
                [
                    MenuItem::make()
                        ->label(__('Back to website'))
                        ->url(fn () => url('/'))
                        ->icon('heroicon-o-globe-alt')
                        ->openUrlInNewTab(false),
                ],
                self::buildLocaleMenuItems()
            ))
            ->databaseNotifications()
            ->databaseNotificationsPolling('60s')
            ->viteTheme('resources/css/filament/passion-theme.css')
            ->renderHook(
                PanelsRenderHook::AUTH_LOGIN_FORM_BEFORE,
                fn (): string => view('filament.partials.login-jp-decoration')->render(),
            )
            ->renderHook(
                PanelsRenderHook::AUTH_LOGIN_FORM_AFTER,
                fn (): string => view('filament.partials.back-to-home-login')->render(),
            )
            ->renderHook(
                PanelsRenderHook::TOPBAR_END,
                fn (): string => view('filament.partials.back-to-home-topbar')->render(),
            )
            ->renderHook(
                PanelsRenderHook::BODY_START,
                function (): string {
                    $route = request()->route()?->getName() ?? '';

                    // Auth pages (login / register / password-reset / email-verification)
                    // get the Mt Fuji background + falling sakura BEFORE the main content
                    // so they sit behind the form. BODY_END would render them on top.
                    if (str_contains($route, '.auth.')) {
                        return view('filament.partials.login-background')->render()
                            . view('filament.partials.login-sakura')->render();
                    }

                    // Authenticated admin pages: reset cached nav-group state
                    // BEFORE Filament's sidebar Livewire boots. BODY_END is
                    // too late — Filament has already read localStorage by
                    // then and rendered the sidebar in the old state.
                    return view('filament.partials.sidebar-nav-init')->render();
                },
            )
            ->renderHook(
                PanelsRenderHook::BODY_END,
                fn (): string => view('filament.partials.sidebar-exclusive-groups')->render()
                    . view('filament.partials.audio-player')->render(),
            )
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                \App\Filament\Widgets\AdminWelcomeWidget::class,
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
