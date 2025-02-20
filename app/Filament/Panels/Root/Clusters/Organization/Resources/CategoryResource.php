<?php

namespace App\Filament\Panels\Root\Clusters\Organization\Resources;

use App\Filament\Filters\OfficeFilter;
use App\Filament\Panels\Root\Clusters\Organization;
use App\Filament\Panels\Root\Clusters\Organization\Resources\CategoryResource\Pages;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Table;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'gmdi-folder-zip-o';

    protected static ?string $cluster = Organization::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('office')
                    ->columnSpanFull()
                    ->relationship('office', 'code')
                    ->searchable()
                    ->preload()
                    ->disabled(fn (string $operation) => $operation === 'edit')
                    ->required(),
                Forms\Components\TextInput::make('name')
                    ->label('Name')
                    ->columnSpanFull()
                    ->dehydrateStateUsing(fn (?string $state) => mb_ucfirst($state ?? ''))
                    ->unique(Category::class, 'name', ignoreRecord: true, modifyRuleUsing: fn ($rule, $get) => $rule->where('office_id', $get('office')))
                    ->maxLength(24)
                    ->rule('required')
                    ->markAsRequired(),
                Forms\Components\Repeater::make('subcategories')
                    ->relationship()
                    ->columnSpanFull()
                    ->addActionLabel('Add subcategory')
                    ->simple(
                        Forms\Components\TextInput::make('name')
                            ->distinct()
                            ->maxLength(24)
                            ->rule('required')
                            ->markAsRequired()
                            ->unique(Category::class, 'name', modifyRuleUsing: fn ($rule, $get) => $rule->withoutTrashed()->where('office_id', $get('office')))
                    ),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(isIndividual: true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('subcategories.name')
                    ->searchable(isIndividual: true)
                    ->bulleted()
                    ->limitList(3)
                    ->expandableLimitedList(),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                OfficeFilter::make()
                    ->withUnaffiliated(false),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->slideOver()
                    ->modalWidth(MaxWidth::Large),
                Tables\Actions\Action::make('subcategories')
                    ->icon('gmdi-folder-special-o')
                    ->url(fn (Category $category) => static::getUrl('subcategories', [$category->id])),
                Tables\Actions\RestoreAction::make(),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\DeleteAction::make()
                        ->modalDescription('Deleting this category will affect all related records associated with it e.g. subcategories under this category.'),
                    Tables\Actions\ForceDeleteAction::make()
                        ->modalDescription(function () {
                            $description = <<<'HTML'
                                <p class="mt-2 text-sm text-gray-500 fi-modal-description dark:text-gray-400">
                                    Deleting this category will affect all related records associated with it e.g. subcategories under this category.
                                </p>

                                <p class="mt-2 text-sm fi-modal-description text-custom-600 dark:text-custom-400" style="--c-400:var(--warning-400);--c-600:var(--warning-600);">
                                    Proceeding with this action will permanently delete the category and all related records associated with it.
                                </p>
                            HTML;

                            return str($description)->toHtmlString();
                        }),
                ]),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make()
                //         ->modalDescription('Deleting selected categories will affect all related records associated with it e.g. subcategories under these categories.'),
                //     Tables\Actions\RestoreBulkAction::make(),
                // ]),
            ])
            ->groups([
                Tables\Grouping\Group::make('office.code')
                    ->label('Office')
                    ->getDescriptionFromRecordUsing(fn (Category $category) => $category->office->name)
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
            'index' => Pages\ListCategories::route('/'),
            'subcategories' => Pages\ListSubcategories::route('/{record}/subcategories'),
        ];
    }
}
