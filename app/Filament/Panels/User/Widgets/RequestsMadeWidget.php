<?php

namespace App\Filament\Panels\User\Widgets;

use App\Enums\RequestClass;
use App\Models\Request;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class RequestsMadeWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $id = Auth::id();

        return [
            Stat::make('Requests', Request::where('user_id', $id)->count()),
            Stat::make('Inquiries', Request::where('user_id', $id)->where('class', RequestClass::INQUIRY)->count()),
            Stat::make('Suggestions', Request::where('user_id', $id)->where('class', RequestClass::SUGGESTION)->count()),
            Stat::make('Tickets', Request::where('user_id', $id)->where('class', RequestClass::TICKET)->count()),
        ];
    }
}
