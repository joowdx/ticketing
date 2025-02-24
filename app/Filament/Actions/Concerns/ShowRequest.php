<?php

namespace App\Filament\Actions\Concerns;

use App\Enums\RequestClass;
use App\Models\Request;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
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

        $this->infolist(fn (Request $request) => [
            ViewEntry::make('body')
                ->label('Inquiry')
                ->hiddenLabel(false)
                ->view('filament.requests.action', [
                    'content' => $request->body,
                    'chat' => true,
                ]),
            ViewEntry::make('responses')
                ->visible($request->class === RequestClass::INQUIRY)
                ->view('filament.requests.history', [
                    'request' => $request,
                    'chat' => true,
                    'descending' => false,
                ]),
        ]);

        $this->hidden(fn (Request $request) => $request->trashed());
    }
}
