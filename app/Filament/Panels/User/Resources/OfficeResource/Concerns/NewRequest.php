<?php

namespace App\Filament\Panels\User\Resources\OfficeResource\Concerns;

use App\Enums\ActionStatus;
use App\Enums\RequestClass;
use App\Filament\Panels\User\Resources\RequestResource;
use App\Models\Request;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Support\Facades\FilamentView;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

use function Filament\Support\is_app_url;

trait NewRequest
{
    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);

        $this->authorizeAccess();

        $this->fillForm();

        $this->previousUrl = RequestResource::getUrl('index');
    }

    public function getHeaderActions(): array
    {
        $classification = str(class_basename(static::class))->replace('New', '')->toString();

        return [
            ActionGroup::make([])
                ->button()
                ->icon('heroicon-o-arrow-path')
                ->label('Switch request')
                ->actions([
                    Action::make('Switch to Inquiry')
                        ->icon(RequestClass::tryFrom('inquiry')->getIcon())
                        ->url(fn () => static::getResource()::getUrl('new.inquiry', [$this->record->id]))
                        ->hidden($classification === 'Inquiry'),
                    Action::make('Switch to Suggestion')
                        ->icon(RequestClass::tryFrom('suggestion')->getIcon())
                        ->url(fn () => static::getResource()::getUrl('new.suggestion', [$this->record->id]))
                        ->hidden($classification === 'Suggestion'),
                    Action::make('Switch to Ticket')
                        ->icon(RequestClass::tryFrom('ticket')->getIcon())
                        ->url(fn () => static::getResource()::getUrl('new.ticket', [$this->record->id]))
                        ->hidden($classification === 'Ticket'),
                ]),
        ];
    }

    public function getHeading(): string|Htmlable
    {
        $classification = static::getClassification();

        $heading = <<<HTML
            <span class="text-custom-600 dark:text-custom-400" style="--c-400:var(--danger-400);--c-600:var(--danger-600);">
                New $classification->value
            </span>
            request for
            <span class="text-custom-600 dark:text-custom-400" style="--c-400:var(--primary-400);--c-600:var(--primary-600);">
                {$this->record->code}
            </span>
        HTML;

        return str($heading)->toHtmlString();
    }

    public function form(Form $form): Form
    {
        $classification = static::getClassification();

        $subcategories = $this->record->subcategories
            ->load('category')
            ->groupBy('category.name')
            ->mapWithKeys(fn ($subs, $cat) => [
                $cat => $subs->pluck('name', 'id')
                    ->map(fn ($sub) => $cat !== $sub ? "$cat — $sub" : $sub)
                    ->toArray(),
            ]);

        return $form->columns(5)
            ->model(Request::class)
            ->schema([
                Group::make()
                    ->columnSpan(4)
                    ->schema([
                        Select::make('category')
                            ->options($subcategories)
                            ->required()
                            ->placeholder(null)
                            ->helperText(fn () => 'Choose the most relevant category for '.match ($classification) {
                                RequestClass::INQUIRY => 'your question or request for information.',
                                RequestClass::SUGGESTION => 'your idea or feedback.',
                                RequestClass::TICKET => 'the issue you are reporting.',
                            }),
                        TextInput::make('subject')
                            ->rule('required')
                            ->markAsRequired()
                            ->extraAttributes([
                                'x-data' => '{}',
                                'x-on:input' => 'event.target.value = event.target.value.charAt(0).toUpperCase() + event.target.value.slice(1)',
                            ])
                            ->helperText(fn () => 'Be clear and concise about '.match ($classification) {
                                RequestClass::TICKET => 'the issue you are facing.',
                                RequestClass::SUGGESTION => 'the idea or suggestion you would like to share.',
                                RequestClass::INQUIRY => 'the question you have.',
                            }),
                        MarkdownEditor::make('body')
                            ->required()
                            ->hintAction(
                                \Filament\Forms\Components\Actions\Action::make('preview')
                                    ->modalSubmitAction(false)
                                    ->modalCancelActionLabel('Close')
                                    ->infolist(fn ($state) => [
                                        TextEntry::make('preview')
                                            ->hiddenLabel()
                                            ->state(fn () => $state)
                                            ->markdown(),
                                    ]),
                            )
                            ->helperText(fn () => 'Provide detailed information about '.match ($classification) {
                                RequestClass::INQUIRY => 'your question, specifying any necessary context for clarity.',
                                RequestClass::SUGGESTION => 'your idea, explaining its benefits and potential impact.',
                                RequestClass::TICKET => 'the issue, including any steps to reproduce it and relevant details.',
                            }),
                    ]),
            ]);
    }

    protected static function getClassification(): RequestClass
    {
        return static::$classification ?? throw new \RuntimeException('Classification not set.');
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return ['class' => static::$classification ?? null, ...$data];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $subcategory = $record->subcategories->find($data['category']);

        $category = $subcategory->category;

        abort_if(! $record->exists || ! $subcategory?->exists || ! $category?->exists, 404);

        $request = Request::make($data);

        $request->user()->associate(Auth::user());

        $request->office()->associate($record);

        $request->category()->associate($category);

        $request->subcategory()->associate($subcategory);

        $request->save();

        $request->actions()->create(['user_id' => Auth::id(), 'status' => ActionStatus::SUBMITTED]);

        return $request;
    }

    protected function getSavedNotificationMessage(): ?string
    {
        return 'Request submitted successfully';
    }

    protected function getRedirectUrl(): ?string
    {
        return RequestResource::getUrl();
    }

    protected function getCancelFormAction(): Action
    {
        return Action::make('cancel')
            ->label(__('filament-panels::resources/pages/edit-record.form.actions.cancel.label'))
            ->action(fn () => $this->redirect($this->getRedirectUrl(), navigate: FilamentView::hasSpaMode() && is_app_url($this->getRedirectUrl())))
            ->color('gray');
    }
}
