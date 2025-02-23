<?php

namespace App\Filament\Actions\Concerns;

use App\Models\Request;
use Filament\Infolists\Components\TextEntry;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\MaxWidth;

trait ShowRequest
{
    protected function bootShowRequest(): void
    {
        $this->icon('heroicon-o-eye');

        $this->label('Show');

        $this->color('gray');

        $this->slideOver();

        $this->modalIconColor(fn (Request $request) => $request->class->getColor());

        $this->modalHeading(fn (Request $request) => $request->subject);

        $this->modalDescription(fn (Request $request) => "{$request->user?->name} {$request->created_at->diffForHumans()} ({$request->created_at->format('F j, Y H:i')})");

        $this->modalFooterActionsAlignment(Alignment::End);

        $this->modalSubmitAction(false);

        $this->modalCancelAction(false);

        $this->modalWidth(MaxWidth::ExtraLarge);

        $this->infolist([
            TextEntry::make('body')
                ->hiddenLabel()
                ->getStateUsing(fn (Request $request) => str($request->body)->markdown()->toHtmlString())
                ->markdown(),
        ]);

        $this->hidden(fn (Request $request) => $request->trashed());
    }
}
