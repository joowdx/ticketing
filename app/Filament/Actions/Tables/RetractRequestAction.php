<?php

namespace App\Filament\Actions\Tables;

use App\Filament\Actions\Concerns\RetractRequest;
use Filament\Tables\Actions\Action;

class RetractRequestAction extends Action
{
    use RetractRequest;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bootRetractRequest();
    }
}
