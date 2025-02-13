<?php

namespace App\Filament\Actions\Concerns;

use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\MaxWidth;

trait UpdateRequest
{
    protected function bootUpdateRequest(): void
    {
        $this->name('update-request');

        $this->icon('heroicon-o-pencil-square');

        $this->modal();

        $this->modalHeading('Update request');

        $this->modalFooterActionsAlignment(Alignment::End);

        $this->modalSubmitActionLabel('Save');

        $this->modalCancelActionLabel('Cancel');

        $this->modalWidth(MaxWidth::ExtraLarge);

        $this->form([

        ]);
    }
}
