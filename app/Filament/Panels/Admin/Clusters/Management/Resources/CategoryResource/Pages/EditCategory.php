<?php

namespace App\Filament\Panels\Admin\Clusters\Management\Resources\CategoryResource\Pages;

use App\Filament\Panels\Admin\Clusters\Management\Resources\CategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCategory extends EditRecord
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\RestoreAction::make(),
            Actions\ActionGroup::make([
                Actions\ForceDeleteAction::make(),
            ]),
        ];
    }
}
