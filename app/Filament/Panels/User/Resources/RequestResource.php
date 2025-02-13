<?php

namespace App\Filament\Panels\User\Resources;

use App\Filament\Actions\Tables\ShowRequest;
use App\Filament\Filters\OfficeFilter;
use App\Filament\Panels\User\Resources\RequestResource\Pages;
use App\Models\Request;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

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
                    ->limit(50)
                    ->description(fn (Request $request) => $request->classification->getLabel(), 'above')
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();

                        if (strlen($state) <= $column->getCharacterLimit()) {
                            return null;
                        }

                        return $state;
                    }),
                Tables\Columns\TextColumn::make('office.code')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->since()
                    ->dateTimeTooltip()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                OfficeFilter::make()
                    ->setModel(static::$model),
            ])
            ->actions([
                ShowRequest::make()
                    ->label('Show'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRequests::route('/'),
        ];
    }
}
