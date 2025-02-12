<?php

namespace App\Filament\Panels\Admin\Clusters\Resources\OfficeResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class UsersRelationManager extends RelationManager
{
    protected static string $relationship = 'users';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\ImageColumn::make('avatar_url')
                    ->label('Avatar')
                    ->circular()
                    ->extraImgAttributes(['loading' => 'lazy']),
                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('number')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('role')
                    ->sortable()
                    ->searchable(),
            ])
            ->headerActions([
                Tables\Actions\AssociateAction::make()
                    ->color('primary')
                    ->recordSelectOptionsQuery(fn (Builder $query) => $query->whereNot('id', Auth::id()))
                    ->recordSelectSearchColumns(['name', 'email', 'number'])
                    ->multiple()
                    ->label('Assign user')
                    ->modalHeading('Assign user')
                    ->modalSubmitActionLabel('Assign')
                    ->successNotificationTitle('Assigned')
                    ->associateAnother(false),
            ])
            ->filters([
                // Tables\Filters\SelectFilter::make('role'),
            ])
            ->actions([
                Tables\Actions\DissociateAction::make()
                    ->label('Remove')
                    ->modalHeading('Unassign user')
                    ->successNotificationTitle('Unassigned'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DissociateBulkAction::make()
                        ->label('Remove selected')
                        ->modalHeading('Unassign users')
                        ->successNotificationTitle('Unassigned'),
                ]),
            ]);
    }
}
