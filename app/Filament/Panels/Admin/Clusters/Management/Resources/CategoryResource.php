<?php

namespace App\Filament\Panels\Admin\Clusters\Management\Resources;

use App\Filament\Panels\Admin\Clusters\Management;
use App\Filament\Panels\Admin\Clusters\Management\Resources\CategoryResource\Pages;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationIcon = 'gmdi-folder-zip-o';

    protected static ?string $cluster = Management::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('office_id')
                    ->default(Auth::user()->office_id),
                Forms\Components\TextInput::make('name')
                    ->label('Name')
                    ->columnSpanFull()
                    ->dehydrateStateUsing(fn (?string $state) => mb_ucfirst($state ?? ''))
                    ->unique(Category::class, 'name', ignoreRecord: true, modifyRuleUsing: fn ($rule) => $rule->where('office_id', Auth::user()->office_id))
                    ->maxLength(24)
                    ->rule('required')
                    ->markAsRequired(),
                Forms\Components\Repeater::make('subcategories')
                    ->relationship()
                    ->columnSpanFull()
                    ->addActionLabel('Add subcategory')
                    ->deletable(fn (string $operation) => $operation === 'create')
                    ->addable(fn (string $operation) => $operation === 'create')
                    ->simple(
                        Forms\Components\TextInput::make('name')
                            ->distinct()
                            ->maxLength(24)
                            ->rule('required')
                            ->markAsRequired()
                            ->unique(Category::class, 'name', modifyRuleUsing: fn ($rule) => $rule->withoutTrashed()->where('office_id', Auth::user()->office_id))
                    ),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('subcategories.name')
                    ->searchable()
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
                        ->modalDescription('Deleting this category will affect all related records associated with it e.g. subcategories and requests under this category.'),
                    Tables\Actions\ForceDeleteAction::make()
                        ->modalDescription(function () {
                            $description = <<<'HTML'
                                <p class="mt-2 text-sm text-gray-500 fi-modal-description dark:text-gray-400">
                                    Deleting selected categories will affect all related records associated with it e.g. subcategories and requests under this category.
                                </p>

                                <p class="mt-2 text-sm fi-modal-description text-custom-600 dark:text-custom-400" style="--c-400:var(--warning-400);--c-600:var(--warning-600);">
                                    Proceeding with this action will permanently delete the category and all related records associated with it.
                                </p>
                            HTML;

                            return str($description)->toHtmlString();
                        }),
                ]),
            ])
            ->recordAction(null)
            ->recordUrl(null);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'subcategories' => Pages\ListSubcategories::route('/{record}/subcategories'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('office_id', Auth::user()->office_id)
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
