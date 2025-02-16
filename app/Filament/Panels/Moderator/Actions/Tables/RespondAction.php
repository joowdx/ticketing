<?php

namespace App\Filament\Panels\Moderator\Actions\Tables;

use Filament\Tables\Actions\ActionGroup;

class RespondAction extends ActionGroup
{
    public static function make(array $actions = []): static
    {
        $static = app(static::class, ['actions' => $actions]);

        $static->configure();

        return $static;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->actions([
            AssignRequestAction::make(),
        ]);
    }
}
