<?php

namespace App\Filament\Panels\Root\Actions\Tables;

use App\Filament\Panels\Root\Actions\Concerns\ChangePassword;
use Filament\Actions\Action;

class ChangePasswordAction extends Action
{
    use ChangePassword;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bootChangePassword();
    }
}
