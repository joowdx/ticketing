<?php

namespace App\Filament\Panels\Root\Clusters\Organization\Resources\CategoryResource\Pages;

use App\Filament\Panels\Root\Clusters\Organization\Resources\CategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\MaxWidth;

class ListCategories extends ListRecords
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->createAnother(false)
                ->slideOver()
                ->modalWidth(MaxWidth::Large),
        ];
    }
}
