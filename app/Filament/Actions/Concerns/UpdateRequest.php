<?php

namespace App\Filament\Actions\Concerns;

use App\Enums\ActionStatus;
use App\Enums\RequestClass;
use App\Models\Request;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\TextInput;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Arr;

trait UpdateRequest
{
    protected function bootUpdateRequest(): void
    {
        $this->icon('heroicon-o-pencil-square');

        $this->label('Update');

        $this->modal();

        $this->modalHeading('Update request');

        $this->modalFooterActionsAlignment(Alignment::End);

        $this->modalSubmitActionLabel('Save');

        $this->modalCancelActionLabel('Cancel');

        $this->modalWidth(MaxWidth::ExtraLarge);

        $this->fillForm(fn (Request $request) => [
            'subject' => $request->subject,
            'body' => $request->body,
        ]);

        $this->form([
            TextInput::make('subject')
                ->rule('required')
                ->markAsRequired()
                ->extraAttributes([
                    'x-data' => '{}',
                    'x-on:input' => 'event.target.value = event.target.value.charAt(0).toUpperCase() + event.target.value.slice(1)',
                ])
                ->helperText(fn (Request $request) => 'Be clear and concise about '.match ($request->class) {
                    RequestClass::TICKET => 'the issue you are facing.',
                    RequestClass::SUGGESTION => 'the idea or suggestion you would like to share.',
                    RequestClass::INQUIRY => 'the question you have.',
                }),
            MarkdownEditor::make('body')
                ->required()
                ->helperText(fn (Request $request) => 'Provide detailed information about '.match ($request->class) {
                    RequestClass::INQUIRY => 'your question, specifying any necessary context for clarity.',
                    RequestClass::SUGGESTION => 'your idea, explaining its benefits and potential impact.',
                    RequestClass::TICKET => 'the issue, including any steps to reproduce it and relevant details.',
                }),
        ]);

        $this->action(function (): void {
            $this->process(function (array $data, Model $record, Table $table) {
                $relationship = $table->getRelationship();

                $translatableContentDriver = $table->makeTranslatableContentDriver();

                if ($relationship instanceof BelongsToMany) {
                    $pivot = $record->{$relationship->getPivotAccessor()};

                    $pivotColumns = $relationship->getPivotColumns();
                    $pivotData = Arr::only($data, $pivotColumns);

                    if (count($pivotColumns)) {
                        if ($translatableContentDriver) {
                            $translatableContentDriver->updateRecord($pivot, $pivotData);
                        } else {
                            $pivot->update($pivotData);
                        }
                    }

                    $data = Arr::except($data, $pivotColumns);
                }

                if ($translatableContentDriver) {
                    $translatableContentDriver->updateRecord($record, $data);
                } else {
                    $record->update($data);
                }
            });

            $this->success();
        });

        $this->visible(fn (Request $request) => is_null($request->action) ?: $request->action?->status === ActionStatus::RETRACTED);
    }
}
