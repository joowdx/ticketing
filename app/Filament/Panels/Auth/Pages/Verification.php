<?php

namespace App\Filament\Panels\Auth\Pages;

use App\Http\Responses\LoginResponse;
use Filament\Facades\Filament;
use Filament\Pages\Auth\EmailVerification\EmailVerificationPrompt;

class Verification extends EmailVerificationPrompt
{
    public function mount(): void
    {
        /** @var User */
        $user = Filament::auth()->user();

        if ($user->hasVerifiedEmail()) {
            (new LoginResponse)->toResponse(request());
        }
    }
}
