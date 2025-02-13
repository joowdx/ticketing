<?php

namespace App\Filament\Actions\Tables;

use App\Models\Request;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\TextEntry\TextEntrySize;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables\Actions\Action;

class ShowRequest extends Action
{
    protected function setUp(): void
    {
        $this->name('show-request');

        $this->icon('heroicon-o-eye');

        $this->modal();

        $this->modalIcon(fn (Request $request) => $request->classification->getIcon());

        $this->modalIconColor(fn (Request $request) => $request->classification->getColor());

        $this->modalHeading(fn (Request $request) => "{$request->classification->getLabel()} for {$request->office->code}");

        $this->modalFooterActionsAlignment(Alignment::End);

        $this->modalSubmitAction(false);

        $this->modalCancelActionLabel('Close');

        $this->modalWidth(MaxWidth::ExtraLarge);

        $this->infolist([
            TextEntry::make('created_at')
                ->hiddenLabel()
                ->since(),
            TextEntry::make('subject')
                ->hiddenLabel()
                ->weight(FontWeight::Bold)
                ->size(TextEntrySize::Large),
            TextEntry::make('body')
                ->hiddenLabel()
                ->markdown(),
        ]);
    }
}
