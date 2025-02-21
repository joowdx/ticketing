<?php

namespace App\Filament\Panels\Auth\Pages;

use App\Http\Responses\LoginResponse;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Pages\Auth\EmailVerification\EmailVerificationPrompt;

class Verification extends EmailVerificationPrompt
{
    protected static string $layout = 'filament-panels::components.layout.base';

    protected static string $view = 'filament.panels.auth.pages.verification';

    public function mount(): void
    {
        /** @var User */
        $user = Filament::auth()->user();

        if ($user->hasVerifiedEmail()) {
            (new LoginResponse)->toResponse(request());
        }
    }

    public function logoutAction(): Action
    {
        return Action::make('logout')
            ->outlined()
            ->icon('gmdi-logout-o')
            ->action(function () {
                Filament::auth()->logout();

                return redirect('/');
            });
    }
}
