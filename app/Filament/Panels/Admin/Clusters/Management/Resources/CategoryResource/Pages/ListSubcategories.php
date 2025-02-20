<?php

namespace App\Filament\Panels\Admin\Clusters\Management\Resources\CategoryResource\Pages;

use App\Filament\Panels\Admin\Clusters\Management\Resources\CategoryResource;
use App\Models\Subcategory;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ListSubcategories extends ManageRelatedRecords
{
    protected static string $resource = CategoryResource::class;

    protected static string $relationship = 'subcategories';

    public function getBreadcrumbs(): array
    {
        return array_merge(array_slice(parent::getBreadcrumbs(), 0, -1), ['Subcategories', 'List']);
    }

    public function getSubNavigation(): array
    {
        if (filled($cluster = static::getCluster())) {
            return $this->generateNavigationItems($cluster::getClusteredComponents());
        }

        return [];
    }

    public function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back')
                ->icon('heroicon-o-arrow-left')
                ->url(static::$resource::getUrl()),
            Actions\CreateAction::make()
                ->model(Subcategory::class)
                ->createAnother(false)
                ->slideOver()
                ->modalWidth(MaxWidth::Large)
                ->form([
                    Forms\Components\Hidden::make('category_id')
                        ->default($this->record->getKey()),
                    Forms\Components\TextInput::make('name')
                        ->dehydrateStateUsing(fn (?string $state) => mb_ucfirst($state ?? ''))
                        ->rule('required')
                        ->markAsRequired()
                        ->maxLength(24),
                ]),
        ];
    }

    public function getHeading(): string
    {
        return "{$this->record->name} → Subcategories";
    }

    public function form(Form $form): Form
    {
        return $form
            ->columns(1)
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->dehydrateStateUsing(fn (?string $state) => mb_ucfirst($state ?? ''))
                    ->rule('required')
                    ->markAsRequired()
                    ->maxLength(24),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->searchable()
                    ->sortable(),
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
                Tables\Actions\RestoreAction::make(),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\DeleteAction::make()
                        ->modalDescription('Deleting this subcategory will affect all related records associated with it e.g. requests under this subcategory.'),
                    Tables\Actions\ForceDeleteAction::make()
                        ->modalDescription(function () {
                            $description = <<<'HTML'
                                <p class="mt-2 text-sm text-gray-500 fi-modal-description dark:text-gray-400">
                                    Deleting selected subcategories will affect all related records associated with it e.g. requests under this subcategory.
                                </p>

                                <p class="mt-2 text-sm fi-modal-description text-custom-600 dark:text-custom-400" style="--c-400:var(--warning-400);--c-600:var(--warning-600);">
                                    Proceeding with this action will permanently delete the subcategory and all related records associated with it.
                                </p>
                            HTML;

                            return str($description)->toHtmlString();
                        }),
                ]),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make()
                //         ->modalDescription('Deleting selected subcategories will affect all related records associated with it e.g. requests under this subcategory.'),
                //     Tables\Actions\RestoreBulkAction::make(),
                // ]),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]))
            ->recordAction(null)
            ->recordUrl(null);
    }
}
