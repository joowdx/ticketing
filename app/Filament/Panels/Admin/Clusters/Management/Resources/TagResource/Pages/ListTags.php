<?php

namespace App\Filament\Panels\Admin\Clusters\Management\Resources\TagResource\Pages;

use App\Filament\Panels\Admin\Clusters\Management\Resources\TagResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\MaxWidth;

class ListTags extends ListRecords
{
    protected static string $resource = TagResource::class;

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
