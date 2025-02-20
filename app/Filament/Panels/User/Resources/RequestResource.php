<?php

namespace App\Filament\Panels\User\Resources;

use App\Filament\Actions\Tables\DeleteRequestAction;
use App\Filament\Actions\Tables\RestoreRequestAction;
use App\Filament\Actions\Tables\ResubmitRequestAction;
use App\Filament\Actions\Tables\RetractRequestAction;
use App\Filament\Actions\Tables\ShowRequestAction;
use App\Filament\Actions\Tables\UpdateRequestAction;
use App\Filament\Actions\Tables\ViewRequestHistoryAction;
use App\Filament\Filters\OfficeFilter;
use App\Filament\Panels\User\Resources\RequestResource\Pages;
use App\Models\Request;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RequestResource extends Resource
{
    protected static ?string $model = Request::class;

    protected static ?string $navigationIcon = 'heroicon-o-lifebuoy';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
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
                    ->visible(fn (HasTable $livewire) => $livewire->activeTab === 'requests'),
                Tables\Columns\TextColumn::make('action.status')
                    ->label('Status')
                    ->badge()
                    ->alignEnd(),
                Tables\Columns\TextColumn::make('created_at')
                    ->since()
                    ->dateTimeTooltip()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                OfficeFilter::make()
                    ->withUnaffiliated(false),
                Tables\Filters\TrashedFilter::make()
                    ->label('Deleted'),
            ])
            ->actions([
                ResubmitRequestAction::make()
                    ->label('Resubmit'),
                ShowRequestAction::make()
                    ->label('Show')
                    ->infolist([
                        TextEntry::make('body')
                            ->hiddenLabel()
                            ->getStateUsing(fn (Request $request) => str($request->body)->markdown()->toHtmlString())
                            ->markdown(),
                    ]),
                ViewRequestHistoryAction::make()
                    ->label('History'),
                RestoreRequestAction::make(),
                Tables\Actions\ActionGroup::make([
                    UpdateRequestAction::make(),
                    RetractRequestAction::make()
                        ->label('Retract'),
                    DeleteRequestAction::make(),
                    Tables\Actions\ForceDeleteAction::make()
                        ->label('Delete'),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRequests::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
