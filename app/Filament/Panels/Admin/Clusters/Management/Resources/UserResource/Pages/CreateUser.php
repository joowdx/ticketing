<?php

namespace App\Filament\Panels\Admin\Clusters\Management\Resources\UserResource\Pages;

use App\Filament\Panels\Admin\Clusters\Management\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
}
