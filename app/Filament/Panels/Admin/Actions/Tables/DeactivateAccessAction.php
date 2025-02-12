<?php

namespace App\Filament\Panels\Admin\Actions\Tables;

use App\Filament\Panels\Admin\Actions\Concerns\DeactivateAccess;
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
