<?php

namespace App\Filament\Panels\Root\Actions\Tables;

use App\Filament\Panels\Root\Actions\Concerns\ApproveAccount;
use Filament\Tables\Actions\BulkAction;

class ApproveAccountBulkAction extends BulkAction
{
    use ApproveAccount;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bootApproveUser();
    }
}
