<?php

namespace App\Filament\Clusters\Requests\Resources;

use App\Enums\ActionStatus;
use App\Enums\RequestClass;
use App\Filament\Actions\Tables\ShowRequestAction;
use App\Filament\Actions\Tables\ViewRequestHistoryAction;
use App\Filament\Clusters\Requests;
use App\Models\Request;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class RequestResource extends Resource
{
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $model = Request::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = Requests::class;

    protected static ?RequestClass $class = null;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->extraCellAttributes(['class' => 'font-mono'])
                    ->searchable(),
                Tables\Columns\TextColumn::make('subject')
                    ->sortable()
                    ->searchable()
                    ->limit(36)
                    ->tooltip(fn ($column) => strlen($column->getState()) > $column->getCharacterLimit() ? $column->getState() : null),
                Tables\Columns\TextColumn::make('office.code')
                    ->sortable()
                    ->searchable()
                    ->limit(36)
                    ->extraCellAttributes(['class' => 'font-mono'])
                    ->tooltip(fn (Request $request) => $request->office->name),
                Tables\Columns\TextColumn::make('class')
                    ->badge()
                    ->alignEnd()
                    ->visible(static::class === self::class),
                Tables\Columns\TextColumn::make('action.status')
                    ->label('Status')
                    ->badge()
                    ->alignEnd()
                    ->state(function (Request $request) {
                        return match ($request->action->status) {
                            ActionStatus::RESPONDED => ActionStatus::IN_PROGRESS,
                            default => $request->action->status,
                        };
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->since()
                    ->dateTimeTooltip()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters(static::tableFilters())
            ->actions(static::tableActions())
            ->bulkActions(static::tableBulkActions());
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getEloquentQuery()
            ->whereHas('action', fn ($query) => $query->where('status', ActionStatus::SUBMITTED))
            ->count() ?: null;
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->when(static::$class, fn ($query, $class) => $query->where('class', $class))
            ->whereHas('action', fn ($query) => $query->where('status', '!=', ActionStatus::RETRACTED));

        return match (Filament::getCurrentPanel()->getId()) {
            'moderator', 'admin' => $query->where('office_id', Auth::user()->office_id),
            'agent' => $query->whereHas('assignees', fn ($query) => $query->where('assigned_id', Auth::id())),
            default => $query,
        };
    }

    public static function tableFilters(): array
    {
        if (static::class === self::class) {
            return [
                Tables\Filters\SelectFilter::make('class')
                    ->options(RequestClass::class),
            ];
        }

        return [];
    }

    public static function tableActions(): array
    {
        return [
            ShowRequestAction::make(),
            ViewRequestHistoryAction::make(),
        ];
    }

    public static function tableBulkActions(): array
    {
        return [];
    }
}
