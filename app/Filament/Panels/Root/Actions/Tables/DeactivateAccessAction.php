<?php

namespace App\Filament\Panels\Root\Actions\Tables;

use App\Filament\Panels\Root\Actions\Concerns\DeactivateAccess;
use Filament\Tables\Actions\Action;

class DeactivateAccessAction extends Action
{
    use DeactivateAccess;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bootDeactivateUser();
    }
}
