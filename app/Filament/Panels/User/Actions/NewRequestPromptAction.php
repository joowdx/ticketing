<?php

namespace App\Filament\Panels\User\Actions;

use App\Enums\RequestClass;
use App\Filament\Panels\User\Resources\OfficeResource;
use App\Models\Office;
use Filament\Actions\Action;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\MaxWidth;

class NewRequestPromptAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->name('new-request-prompt');

        $this->label('New request');

        $this->slideOver();

        $this->modal();

        $this->modalIcon('heroicon-o-plus-circle');

        $this->modalSubmitActionLabel('Proceed');

        $this->modalWidth(MaxWidth::ExtraLarge);

        $this->modalFooterActionsAlignment(Alignment::End);

        $this->action(fn (array $data) => $this->redirect(OfficeResource::getUrl('new.'.$data['classification'], [$data['office']])));

        $this->form(function () {
            $offices = Office::query()
                ->get(['name', 'code', 'id'])
                ->mapWithKeys(fn ($office) => [$office->id => "{$office->code} — {$office->name}"])
                ->toArray();

            return [
                Select::make('office')
                    ->options($offices)
                    ->default(count($offices) === 1 ? key($offices) : null)
                    ->required(),
                Radio::make('classification')
                    ->options(RequestClass::class)
                    ->required(),
            ];
        });
    }
}
