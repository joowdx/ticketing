<?php

namespace App\Filament\Actions\Tables;

use App\Models\Request;
use Filament\Tables\Actions\Action;

class ViewRequestHistoryAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->name('view-request-history');

        $this->icon('gmdi-route-o');

        $this->slideOver();

        $this->modalWidth('lg');

        $this->modalSubmitAction(false);

        $this->modalCancelAction(false);

        $this->modalContent(fn (Request $request) => view('filament.requests.history', ['request' => $request]));
    }
}
