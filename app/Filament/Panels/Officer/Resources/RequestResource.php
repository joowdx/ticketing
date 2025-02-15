<?php

namespace App\Filament\Panels\Officer\Resources;

use App\Enums\ActionStatus;
use App\Filament\Actions\Tables\ShowRequestAction;
use App\Filament\Actions\Tables\ViewRequestHistoryAction;
use App\Filament\Panels\Officer\Actions\Tables\RespondAction;
use App\Filament\Panels\Officer\Resources\RequestResource\Pages;
use App\Models\Request;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class RequestResource extends Resource
{
    protected static ?string $model = Request::class;

    protected static ?string $navigationIcon = 'heroicon-o-lifebuoy';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

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
                    ->visible(fn (HasTable $livewire) => in_array($livewire->activeTab, ['requests', 'received'])),
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
                //
            ])
            ->actions([
                ShowRequestAction::make(),
                ViewRequestHistoryAction::make()
                    ->label('History'),
                RespondAction::make()
                    ->label('Respond'),
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
            ->where('office_id', Auth::user()->office_id)
            ->whereDoesntHave('action', fn ($query) => $query->where('status', ActionStatus::RETRACTED));
    }
}
