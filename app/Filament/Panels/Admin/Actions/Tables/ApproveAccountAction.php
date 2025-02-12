<?php

namespace App\Filament\Panels\Admin\Actions\Tables;

use App\Filament\Panels\Admin\Actions\Concerns\ApproveAccount;
use Filament\Tables\Actions\Action;

class ApproveAccountAction extends Action
{
    use ApproveAccount;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bootApproveUser();
    }
}
