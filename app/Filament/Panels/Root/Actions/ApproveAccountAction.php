<?php

namespace App\Filament\Panels\Root\Actions;

use App\Filament\Panels\Root\Actions\Concerns\ApproveAccount;
use Filament\Actions\Action;

class ApproveAccountAction extends Action
{
    use ApproveAccount;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bootApproveUser();
    }
}
