<?php

namespace App\Filament\Panels\Admin\Clusters\Management\Resources\CategoryResource\Pages;

use App\Filament\Panels\Admin\Clusters\Management\Resources\CategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateCategory extends CreateRecord
{
    protected static string $resource = CategoryResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return [
            ...$data,
            'office_id' => Auth::user()->office_id,
        ];
    }
}
