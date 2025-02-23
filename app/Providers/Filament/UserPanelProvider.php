<?php

namespace App\Providers\Filament;

use App\Http\Middleware\Active;
use App\Http\Middleware\Approve;
use App\Http\Middleware\Authenticate;
use App\Http\Middleware\Verify;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\MaxWidth;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class UserPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('user')
            ->path('user')
            ->homeUrl('/')
            ->brandLogo(fn () => view('banner'))
            ->font('Urbanist')
            ->colors([...Color::all(), 'gray' => Color::Neutral])
            ->discoverResources(in: app_path('Filament/Panels/User/Resources'), for: 'App\\Filament\\Panels\\User\\Resources')
            ->discoverPages(in: app_path('Filament/Panels/User/Pages'), for: 'App\\Filament\\Panels\\User\\Pages')
            ->discoverWidgets(in: app_path('Filament/Panels/User/Widgets'), for: 'App\\Filament\\Panels\\User\\Widgets')
            ->pages([Pages\Dashboard::class])
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
                Verify::class,
                Approve::class,
                Active::class,
            ])
            ->globalSearch(false)
            ->maxContentWidth(MaxWidth::ScreenTwoExtraLarge)
            ->databaseTransactions()
            ->topNavigation()
            ->spa();
    }
}
