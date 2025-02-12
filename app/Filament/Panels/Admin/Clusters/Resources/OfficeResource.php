<?php

namespace App\Filament\Panels\Admin\Clusters\Resources;

use App\Filament\Panels\Admin\Clusters\Organization;
use App\Filament\Panels\Admin\Clusters\Resources\OfficeResource\Pages;
use App\Filament\Panels\Admin\Clusters\Resources\OfficeResource\RelationManagers\CategoriesRelationManager;
use App\Filament\Panels\Admin\Clusters\Resources\OfficeResource\RelationManagers\SubcategoriesRelationManager;
use App\Filament\Panels\Admin\Clusters\Resources\OfficeResource\RelationManagers\UsersRelationManager;
use App\Models\Office;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OfficeResource extends Resource
{
    protected static ?int $navigationSort = -2;

    protected static ?string $model = Office::class;

    protected static ?string $navigationIcon = 'gmdi-domain-o';

    protected static ?string $cluster = Organization::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Information')
                    ->columns([
                        'sm' => 1,
                        'md' => 3,
                    ])
                    ->schema([
                        Forms\Components\FileUpload::make('logo')
                            ->avatar()
                            ->alignCenter()
                            ->directory('logos'),
                        Forms\Components\Group::make()
                            ->columnSpan([
                                'md' => 2,
                            ])
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->unique(ignoreRecord: true)
                                    ->markAsRequired()
                                    ->rule('required')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('code')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255),
                            ]),
                        Forms\Components\TextInput::make('address')
                            ->maxLength(255)
                            ->columnSpan([
                                'sm' => 1,
                                'md' => 3,
                            ]),
                        Forms\Components\TextInput::make('building')
                            ->maxLength(255)
                            ->columnSpan([
                                'sm' => 1,
                                'md' => 2,
                            ]),
                        Forms\Components\TextInput::make('room')
                            ->maxLength(255)
                            ->columnSpan(1),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('logo_url')
                    ->label('Logo')
                    ->circular()
                    ->extraImgAttributes(['loading' => 'lazy']),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('code')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('users.avatar_url')
                    ->circular()
                    ->stacked()
                    ->limit(3)
                    ->limitedRemainingText(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            UsersRelationManager::class,
            CategoriesRelationManager::class,
            SubcategoriesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOffices::route('/'),
            'create' => Pages\CreateOffice::route('/create'),
            'edit' => Pages\EditOffice::route('/{record}/edit'),
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
