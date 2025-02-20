<?php

namespace App\Filament\Panels\User\Resources\RequestResource\Pages;

use App\Enums\RequestClass;
use App\Filament\Panels\User\Actions\NewRequestPromptAction;
use App\Filament\Panels\User\Resources\RequestResource;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListRequests extends ListRecords
{
    protected static string $resource = RequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            NewRequestPromptAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'requests' => Tab::make('Requests')
                ->icon('heroicon-o-lifebuoy'),
            'inquiry' => Tab::make('Inquiry')
                ->icon(RequestClass::INQUIRY->getIcon())
                ->modifyQueryUsing(fn ($query) => $query->where('class', RequestClass::INQUIRY)),
            'suggestion' => Tab::make('Suggestion')
                ->icon(RequestClass::SUGGESTION->getIcon())
                ->modifyQueryUsing(fn ($query) => $query->where('class', RequestClass::SUGGESTION)),
            'ticket' => Tab::make('Ticket')
                ->icon(RequestClass::TICKET->getIcon())
                ->modifyQueryUsing(fn ($query) => $query->where('class', RequestClass::TICKET)),
        ];
    }
}
