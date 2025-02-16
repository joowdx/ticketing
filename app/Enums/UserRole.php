<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum UserRole: string implements HasLabel
{
    case ROOT = 'root';
    case ADMIN = 'admin';
    case MODERATOR = 'moderator';
    case SUPPORT = 'support';
    case USER = 'user';

    public function getLabel(): ?string
    {

        return match ($this) {
            default => mb_ucfirst($this->value),
        };
    }
}
