<?php

namespace App\Filament\Actions\Tables;

use App\Filament\Actions\Concerns\ShowRequest;
use Filament\Tables\Actions\Action;

class ShowRequestAction extends Action
{
    use ShowRequest;

    protected function setUp(): void
    {
        $this->bootShowRequest();
    }
}
