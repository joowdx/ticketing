<?php

namespace App\Filament\Panels\User\Resources\OfficeResource\Pages;

use App\Enums\RequestClass;
use App\Filament\Panels\User\Resources\OfficeResource;
use App\Filament\Panels\User\Resources\OfficeResource\Concerns\NewRequest;
use Filament\Resources\Pages\EditRecord;

class NewSuggestion extends EditRecord
{
    use NewRequest;

    protected static string $resource = OfficeResource::class;

    protected static ?string $breadcrumb = 'New Suggestion';

    protected static RequestClass $classification = RequestClass::SUGGESTION;
}
