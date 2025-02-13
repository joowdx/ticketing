<?php

namespace App\Filament\Panels\User\Resources;

use App\Enums\ActionStatus;
use App\Enums\RequestClassification;
use App\Filament\Actions\Tables\UpdateRequestAction;
use App\Filament\Actions\Tables\ShowRequestAction;
use App\Filament\Filters\OfficeFilter;
use App\Filament\Panels\User\Resources\RequestResource\Pages;
use App\Models\Request;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RequestResource extends Resource
{
    protected static ?string $model = Request::class;

    protected static ?string $navigationIcon = 'heroicon-o-lifebuoy';

    public static function form(Form $form): Form
    {
        return $form
            ->columns(1)
            ->schema(fn (Request $request) => [
                Forms\Components\TextInput::make('subject')
                    ->rule('required')
                    ->markAsRequired()
                    ->extraAttributes([
                        'x-data' => '{}',
                        'x-on:input' => 'event.target.value = event.target.value.charAt(0).toUpperCase() + event.target.value.slice(1)',
                    ])
                    ->helperText(fn () => 'Be clear and concise about '.match ($request->class) {
                        RequestClassification::TICKET => 'the issue you are facing.',
                        RequestClassification::SUGGESTION => 'the idea or suggestion you would like to share.',
                        RequestClassification::INQUIRY => 'the question you have.',
                    }),
                Forms\Components\MarkdownEditor::make('body')
                    ->required()
                    ->helperText(fn () => 'Provide detailed information about '.match ($request->class) {
                        RequestClassification::INQUIRY => 'your question, specifying any necessary context for clarity.',
                        RequestClassification::SUGGESTION => 'your idea, explaining its benefits and potential impact.',
                        RequestClassification::TICKET => 'the issue, including any steps to reproduce it and relevant details.',
                    }),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('subject')
                    ->sortable()
                    ->searchable()
                    ->limit(36)
                    ->tooltip(fn ($column) => strlen($column->getState()) > $column->getCharacterLimit()? $column->getState() : null),
                Tables\Columns\TextColumn::make('office.code')
                    ->sortable()
                    ->searchable()
                    ->limit(36)
                    ->extraCellAttributes(['class' => 'font-mono'])
                    ->tooltip(fn (Request $request) => $request->office->name),
                Tables\Columns\TextColumn::make('class')
                    ->badge()
                    ->alignEnd()
                    ->visible(fn (HasTable $livewire) => $livewire->activeTab === 'requests'),
                Tables\Columns\TextColumn::make('action.status')
                    ->label('Status')
                    ->badge()
                    ->alignEnd(),
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
                ShowRequestAction::make()
                    ->label('Show'),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->label('Update')
                        ->slideOver()
                        ->modalIcon('heroicon-o-pencil-square')
                        ->modalWidth(MaxWidth::ExtraLarge)
                        ->modalHeading(function (Request $request) {
                            $classification = $request->class;

                            $heading = <<<HTML
                                <span class="text-custom-600 dark:text-custom-400" style="--c-400:var(--danger-400);--c-600:var(--danger-600);">
                                    Update $classification->value
                                </span>
                                request for
                                <span class="text-custom-600 dark:text-custom-400" style="--c-400:var(--primary-400);--c-600:var(--primary-600);">
                                    {$request->office->code}
                                </span>
                            HTML;

                            return str($heading)->toHtmlString();
                        }),
                    Tables\Actions\DeleteAction::make()
                        ->disabled(function (Request $request) {
                            if (is_null($request->action)) {
                                return false;
                            }

                            return $request->action->status !== ActionStatus::RETRACTED;
                        }),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRequests::route('/'),
        ];
    }
}
