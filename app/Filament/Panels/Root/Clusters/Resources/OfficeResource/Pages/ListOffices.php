<?php

namespace App\Filament\Panels\Root\Clusters\Resources\OfficeResource\Pages;

use App\Filament\Panels\Root\Clusters\Resources\OfficeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOffices extends ListRecords
{
    protected static string $resource = OfficeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
