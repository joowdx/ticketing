<?php

namespace App\Filament\Panels\Root\Clusters\Resources\UserResource\Pages;

use App\Filament\Panels\Root\Clusters\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
}
