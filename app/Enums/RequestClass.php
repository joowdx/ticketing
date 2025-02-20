<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasDescription;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum RequestClass: string implements HasColor, HasDescription, HasIcon, HasLabel
{
    case INQUIRY = 'inquiry';
    case TICKET = 'ticket';
    case SUGGESTION = 'suggestion';

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::INQUIRY => 'success',
            self::TICKET => 'warning',
            self::SUGGESTION => 'info',
            default => null,
        };
    }

    public function getDescription(): ?string
    {
        return match ($this) {
            self::INQUIRY => 'An inquiry is a request for information or clarification on a topic or service, typically not requiring immediate action e.g. "Can you explain how this feature works?"',
            self::TICKET => 'A ticket is a request for technical support or assistance that needs resolving e.g. "My account is locked, I need help recovering it."',
            self::SUGGESTION => 'A suggestion is a request for a new feature or improvement e.g. "I suggest that we work on improving the performance of the application."',
            default => null,
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::INQUIRY => 'heroicon-o-question-mark-circle',
            self::TICKET => 'heroicon-o-ticket',
            self::SUGGESTION => 'heroicon-o-light-bulb',
            default => 'heroicon-o-lifebuoy',
        };
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::INQUIRY => 'Inquiry',
            self::TICKET => 'Ticket',
            self::SUGGESTION => 'Suggestion',
            default => 'Request',
        };
    }
}
