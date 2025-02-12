<?php

namespace App\Http\Responses;

use App\Enums\UserRole;
use App\Models\User;
use Filament\Http\Responses\Auth\Contracts\LoginResponse as Responsable;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;

class LoginResponse implements Responsable
{
    public function toResponse($request): RedirectResponse|Redirector
    {
        /** @var User $user */
        $user = $request->user();

        if (! $user->hasVerifiedEmail()) {
            return redirect()->route('filament.auth.auth.email-verification.prompt');
        }

        $route = match ($user->role) {
            UserRole::ADMIN => 'filament.admin.pages.dashboard',
            // UserRole::USER => 'filament.user.pages.dashboard',
            // UserRole::OFFICER => 'filament.officer.pages.dashboard',
            // UserRole::SUPPORT => 'filament.support.pages.dashboard',
            default => 'home',
        };

        return redirect()->route($route);
    }
}
