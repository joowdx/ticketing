<?php

namespace App\Filament\Panels\Auth\Pages;

use App\Http\Responses\LoginResponse;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Pages\SimplePage;
use Illuminate\Contracts\Support\Htmlable;

class Deactivated extends SimplePage
{
    public ?User $user;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.panels.auth.pages.deactivated';

    public function mount()
    {
        $this->user = Filament::auth()->user();

        if ($this->user->hasActiveAccess()) {
            (new LoginResponse)->toResponse(request());
        }
    }

    public function getTitle(): string|Htmlable
    {
        return 'User access terminated';
    }
}
