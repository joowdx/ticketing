<?php

namespace App\Filament\Panels\Admin\Actions\Tables;

use App\Filament\Panels\Admin\Actions\Concerns\ApproveAccount;
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
