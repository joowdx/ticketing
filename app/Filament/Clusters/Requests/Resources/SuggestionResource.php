<?php

namespace App\Filament\Clusters\Requests\Resources;

use App\Enums\RequestClass;
use App\Filament\Clusters\Requests\Resources\RequestResource\Pages\ListSuggestions;

class SuggestionResource extends RequestResource
{
    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $navigationIcon = 'heroicon-o-light-bulb';

    protected static ?string $label = 'Suggestions';

    protected static ?RequestClass $class = RequestClass::SUGGESTION;

    public static function getPages(): array
    {
        return [
            'index' => ListSuggestions::route('/'),
        ];
    }
}
