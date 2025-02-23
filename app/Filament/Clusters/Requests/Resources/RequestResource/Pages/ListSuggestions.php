<?php

namespace App\Filament\Clusters\Requests\Resources\RequestResource\Pages;

use App\Filament\Clusters\Requests\Resources\SuggestionResource;
use Filament\Resources\Pages\ListRecords;

class ListSuggestions extends ListRecords
{
    protected static string $resource = SuggestionResource::class;
}
