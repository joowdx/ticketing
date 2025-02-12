<?php

namespace App\Filament\Panels\Admin\Clusters\Resources\OfficeResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class CategoriesRelationManager extends RelationManager
{
    protected static string $relationship = 'categories';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->columnSpanFull()
                    ->markAsRequired()
                    ->rule('required'),
                Forms\Components\Fieldset::make('Tags')
                    ->schema([
                        Forms\Components\Repeater::make('tag')
                            ->relationship('tags')
                            ->columnSpanFull()
                            ->hiddenLabel()
                            ->grid(3)
                            ->simple(
                                Forms\Components\TextInput::make('name')
                                    ->distinct()
                                    ->markAsRequired()
                                    ->rule('required')
                                    ->maxLength(15),
                            ),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tags.name')
                    ->limit(20),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->slideOver(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->slideOver(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->recordAction(null);
    }
}
