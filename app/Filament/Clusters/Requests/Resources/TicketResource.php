<?php

namespace App\Filament\Clusters\Requests\Resources;

use App\Enums\RequestClass;
use App\Filament\Actions\Tables\CloseRequestAction;
use App\Filament\Actions\Tables\ReclassifyRequestAction;
use App\Filament\Actions\Tables\ShowRequestAction;
use App\Filament\Actions\Tables\ViewRequestHistoryAction;
use App\Filament\Clusters\Requests\Resources\RequestResource\Pages\ListTickets;
use App\Filament\Panels\Agent\Actions\Tables\RequeueRequestAction;
use App\Filament\Panels\Agent\Actions\Tables\StartRequestAction;
use Filament\Facades\Filament;
use Filament\Tables\Actions\ActionGroup;

class TicketResource extends RequestResource
{
    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $label = 'Tickets';

    protected static ?RequestClass $class = RequestClass::TICKET;

    public static function getPages(): array
    {
        return [
            'index' => ListTickets::route('/'),
        ];
    }

    public static function tableActions(): array
    {
        return match (Filament::getCurrentPanel()->getId()) {
            'agent' => [
                ShowRequestAction::make(),
                StartRequestAction::make(),
                RequeueRequestAction::make(),
                ViewRequestHistoryAction::make(),
                ActionGroup::make([
                    ReclassifyRequestAction::make(),
                    CloseRequestAction::make(),
                ]),
            ],
            'moderator' => [
                ShowRequestAction::make(),
                StartRequestAction::make(),
                RequeueRequestAction::make(),
                ViewRequestHistoryAction::make(),
                ActionGroup::make([
                    ReclassifyRequestAction::make(),
                    CloseRequestAction::make(),
                ]),
            ],
            default => parent::tableActions(),
        };
    }
}
