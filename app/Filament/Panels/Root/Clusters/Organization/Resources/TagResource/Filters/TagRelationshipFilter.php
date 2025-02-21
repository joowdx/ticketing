<?php

namespace App\Filament\Panels\Root\Clusters\Organization\Resources\TagResource\Filters;

use App\Models\Office;
use Filament\Forms\Components\Select;
use Filament\Tables\Filters\Filter;

class TagRelationshipFilter extends Filter
{
    public static function make(?string $name = null): static
    {
        $filterClass = static::class;

        $name ??= 'tags-filter';

        $static = app($filterClass, ['name' => $name]);

        $static->configure();

        return $static;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->form(function () {
            return [
                Select::make('office')
                    ->reactive()
                    ->searchable()
                    ->options(Office::query()->pluck('code', 'id')->toArray())
                    ->afterStateUpdated(fn ($set) => $set('category', null) || $set('subcategory', null))
                    ->placeholder(null),
                Select::make('category')
                    ->reactive()
                    ->searchable()
                    ->disabled(fn ($get) => ! $get('office'))
                    ->options(fn ($get) => $get('office') ? Office::find($get('office'))->categories->pluck('name', 'id')->toArray() : [])
                    ->afterStateUpdated(fn ($set) => $set('subcategory', null)),
                Select::make('subcategory')
                    ->reactive()
                    ->searchable()
                    ->disabled(fn ($get) => ! $get('office') || ! $get('category'))
                    ->options(function ($get) {
                        if (! $get('office') || ! $get('category')) {
                            return [];
                        }

                        return Office::find($get('office'))->categories->find($get('category'))->subcategories->pluck('name', 'id')->toArray();
                    }),
            ];
        });
    }
}
