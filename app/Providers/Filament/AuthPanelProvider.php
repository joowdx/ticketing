<?php

namespace App\Providers\Filament;

use App\Filament\Panels\Auth\Pages\Login;
use App\Filament\Panels\Auth\Pages\Registration;
use App\Http\Middleware\Approve;
use App\Http\Middleware\Authenticate;
use App\Http\Middleware\Verify;
use App\Http\Responses\LoginResponse;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AuthPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('auth')
            ->path('auth')
            ->login(Login::class)
            ->registration(Registration::class)
            ->revealablePasswords(false)
            ->colors(['primary' => Color::Green])
            ->discoverPages(in: app_path('Filament/Panels/Auth/Pages'), for: 'App\\Filament\\Panels\\Auth\\Pages')
            ->pages([Redirect::class])
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
            ]);
    }
}

class Redirect extends Dashboard
{
    public function __construct()
    {
        $this->mount();
    }

    public function mount(): void
    {
        (new LoginResponse)->toResponse(request());
    }
}
