<?php

namespace App\Filament\Panels\Auth\Pages;

use App\Http\Responses\LoginResponse;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Pages\SimplePage;
use Illuminate\Contracts\Support\Htmlable;

class Approval extends SimplePage
{
    protected static string $layout = 'filament-panels::components.layout.base';

    protected static string $view = 'filament.panels.auth.pages.approval';

    public function mount(): void
    {
        /** @var User */
        $user = Filament::auth()->user();

        if ($user->hasApprovedAccount()) {
            (new LoginResponse)->toResponse(request());
        }
    }

    public function getTitle(): string|Htmlable
    {
        return 'Account review in progress';
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
