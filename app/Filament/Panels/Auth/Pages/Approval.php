<?php

namespace App\Filament\Panels\Auth\Pages;

use App\Http\Responses\LoginResponse;
use Filament\Facades\Filament;
use Filament\Pages\SimplePage;
use Illuminate\Contracts\Support\Htmlable;

class Approval extends SimplePage
{
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
}
