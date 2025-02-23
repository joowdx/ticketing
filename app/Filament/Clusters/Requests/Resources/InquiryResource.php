<?php

namespace App\Filament\Clusters\Requests\Resources;

use App\Enums\RequestClass;
use App\Enums\UserRole;
use App\Filament\Actions\Tables\CloseRequestAction;
use App\Filament\Actions\Tables\RejectRequestAction;
use App\Filament\Actions\Tables\RespondRequestAction;
use App\Filament\Actions\Tables\ShowRequestAction;
use App\Filament\Actions\Tables\ViewRequestHistoryAction;
use App\Filament\Clusters\Requests\Resources\RequestResource\Pages\ListInquiries;
use App\Filament\Panels\Moderator\Actions\Tables\AssignRequestAction;
use Filament\Tables\Actions\ActionGroup;
use Illuminate\Support\Facades\Auth;

class InquiryResource extends RequestResource
{
    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $navigationIcon = 'heroicon-o-question-mark-circle';

    protected static ?string $label = 'Inquiries';

    protected static ?RequestClass $class = RequestClass::INQUIRY;

    public static function getPages(): array
    {
        return [
            'index' => ListInquiries::route('/'),
        ];
    }

    public static function tableActions(): array
    {
        return match(Auth::user()->role) {
            UserRole::MODERATOR => [
                RespondRequestAction::make(),
                AssignRequestAction::make(),
                ShowRequestAction::make(),
                ViewRequestHistoryAction::make(),
                ActionGroup::make([
                    RejectRequestAction::make(),
                    CloseRequestAction::make()
                        ->requireRemarks(false),
                ]),
            ],
            UserRole::AGENT => [
                RespondRequestAction::make(),
                ShowRequestAction::make(),
                ViewRequestHistoryAction::make(),
                ActionGroup::make([
                    RejectRequestAction::make(),
                    CloseRequestAction::make()
                        ->requireRemarks(false),
                ]),
            ],
            default => [],
        };
    }
}
