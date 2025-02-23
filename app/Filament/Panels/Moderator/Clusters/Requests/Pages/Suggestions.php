<?php

namespace App\Filament\Panels\Moderator\Clusters\Requests\Pages;

use App\Filament\Clusters\Requests\Resources\RequestResource\Pages\ListSuggestions;
use App\Filament\Panels\Moderator\Clusters\Requests\Resources\SuggestionResource;

class Suggestions extends ListSuggestions
{
    protected static string $resource = SuggestionResource::class;
}
