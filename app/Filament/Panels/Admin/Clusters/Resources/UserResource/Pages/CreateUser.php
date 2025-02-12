<?php

namespace App\Filament\Panels\Admin\Clusters\Resources\UserResource\Pages;

use App\Filament\Panels\Admin\Clusters\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
}
