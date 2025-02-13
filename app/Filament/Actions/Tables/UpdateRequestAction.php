<?php

namespace App\Filament\Actions\Tables;

use App\Filament\Actions\Concerns\UpdateRequest;
use Filament\Tables\Actions\Action;

class UpdateRequestAction extends Action
{
    use UpdateRequest;

    protected function setUp(): void
    {
        $this->bootUpdateRequest();
    }
}
