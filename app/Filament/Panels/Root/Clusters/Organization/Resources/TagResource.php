<?php

namespace App\Filament\Panels\Root\Clusters\Organization\Resources;

use App\Filament\Filters\OfficeFilter;
use App\Filament\Panels\Root\Clusters\Organization;
use App\Filament\Panels\Root\Clusters\Organization\Resources\TagResource\Filters\TagRelationshipFilter;
use App\Filament\Panels\Root\Clusters\Organization\Resources\TagResource\Pages;
use App\Models\Tag;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\MaxWidth;
use Filament\Support\Facades\FilamentColor;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

class TagResource extends Resource
{
    protected static ?string $model = Tag::class;

    protected static ?string $navigationIcon = 'gmdi-sell-o';

    protected static ?string $cluster = Organization::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Placeholder::make('preview')
                    ->columnSpanFull()
                    ->content(fn ($get) => new HtmlString(Blade::render("<x-filament::badge color=\"{$get('color')}\">{$get('name')}</x-filament::badge>"))),
                Forms\Components\TextInput::make('name')
                    ->maxLength(24)
                    ->columnSpanFull()
                    ->live(onBlur: true)
                    ->rules('required')
                    ->markAsRequired(),
                Forms\Components\Select::make('color')
                    ->columnSpanFull()
                    ->options(array_combine(array_keys(Color::all()), array_map('ucfirst', array_keys(Color::all()))))
                    ->live(onBlur: true)
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('office_id')
                    ->columnSpanFull()
                    ->relationship('office', 'code')
                    ->searchable()
                    ->preload()
                    ->live(onBlur: true)
                    ->required()
                    ->placeholder('Select office'),
                Forms\Components\Select::make('category_id')
                    ->columnSpanFull()
                    ->relationship('category', 'name', fn ($get, $query) => $query->where('office_id', $get('office_id')))
                    ->searchable()
                    ->preload()
                    ->live(onBlur: true)
                    ->placeholder('Select category'),
                Forms\Components\Select::make('subcategory_id')
                    ->columnSpanFull()
                    ->relationship('subcategory', 'name', fn ($get, $query) => $query->where('category_id', $get('category_id')))
                    ->searchable()
                    ->preload()
                    ->placeholder('Select subcategory'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Tag')
                    ->color(fn (Tag $tag) => $tag->color ?? 'gray')
                    ->badge()
                    ->searchable(isIndividual: true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->placeholder('--------------')
                    ->searchable(isIndividual: true),
                Tables\Columns\TextColumn::make('subcategory.name')
                    ->placeholder('--------------')
                    ->searchable(isIndividual: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TagRelationshipFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->slideOver()
                    ->modalWidth(MaxWidth::Large),
                Tables\Actions\DeleteAction::make()
                    ->form([
                        Forms\Components\TextInput::make('password')
                            ->rule('required')
                            ->markAsRequired()
                            ->password()
                            ->currentPassword(),
                    ]),
            ])
            ->groups([
                Tables\Grouping\Group::make('office.code')
                    ->label('Office')
                    ->getDescriptionFromRecordUsing(fn (Tag $tag) => $tag->office->name)
                    ->titlePrefixedWithLabel(false),
            ])
            ->groupingSettingsHidden()
            ->defaultGroup('office.code')
            ->recordAction(null)
            ->recordUrl(null);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTags::route('/'),
        ];
    }
}
