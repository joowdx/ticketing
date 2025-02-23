<?php

namespace App\Filament\Clusters\Requests\Resources\RequestResource\Pages;

use App\Filament\Clusters\Requests\Resources\TicketResource;
use Filament\Resources\Pages\ListRecords;

class ListTickets extends ListRecords
{
    protected static string $resource = TicketResource::class;
}
