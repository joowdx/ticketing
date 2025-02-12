<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum UserRole: string implements HasLabel
{
    case ADMIN = 'admin';
    case OFFICER = 'officer';
    case SUPPORT = 'support';
    case USER = 'user';

    public function getLabel(): ?string
    {
        return mb_ucfirst($this->value);
    }
}
