<?php

namespace App\Filament\Panels\Admin\Clusters\Management\Resources;

use App\Filament\Panels\Admin\Clusters\Management;
use App\Filament\Panels\Admin\Clusters\Management\Resources\TagResource\Pages;
use App\Models\Tag;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

class TagResource extends Resource
{
    protected static ?string $model = Tag::class;

    protected static ?string $navigationIcon = 'gmdi-sell-o';

    protected static ?string $cluster = Management::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('office_id')
                    ->default(Auth::user()->office_id),
                Forms\Components\Placeholder::make('preview')
                    ->columnSpanFull()
                    ->content(fn ($get) => new HtmlString(
                        Blade::render("<x-filament::badge color=\"{$get('color')}\">".($get('name') ?: 'tag').'</x-filament::badge>')
                    )),
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
