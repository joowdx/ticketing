<?php

namespace App\Filament\Panels\Moderator\Resources\RequestResource\Pages;

use App\Enums\ActionStatus;
use App\Filament\Panels\Moderator\Resources\RequestResource;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListRequests extends ListRecords
{
    protected static string $resource = RequestResource::class;

    public function getTabs(): array
    {
        return [
            'requests' => Tab::make('Requests')
                ->icon('heroicon-o-lifebuoy'),
            'received' => Tab::make('Received')
                ->icon('heroicon-o-inbox')
                ->modifyQueryUsing(fn ($query) => $query->whereHas('action', fn ($query) => $query->where('status', ActionStatus::SUBMITTED))),
            'queued' => Tab::make('Queued')
                ->icon('heroicon-o-clock')
                ->modifyQueryUsing(fn ($query) => $query->whereHas('action', fn ($query) => $query->where('status', ActionStatus::QUEUED))),
            'pending' => Tab::make('Pending')
                ->icon('heroicon-o-user-group')
                ->modifyQueryUsing(fn ($query) => $query->whereHas('action', fn ($query) => $query->where('status', ActionStatus::ASSIGNED))),
        ];
    }
}
