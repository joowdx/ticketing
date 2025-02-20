<?php

namespace App\Filament\Panels\Admin\Clusters\Management\Pages;

use App\Filament\Panels\Admin\Clusters\Management;
use App\Models\Office;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Settings extends Page
{
    use InteractsWithFormActions;

    public array $data = [];

    protected static ?string $navigationIcon = 'gmdi-settings-o';

    protected static string $view = 'filament.panels.admin.clusters.management.pages.settings';

    protected static ?string $cluster = Management::class;

    public function mount(): void
    {
        $this->fillForm();
    }

    public function getBreadcrumbs(): array
    {
        if (filled($cluster = static::getCluster())) {
            return $cluster::unshiftClusterBreadcrumbs([
                Settings::getUrl() => static::getTitle(),
                null => 'Edit',
            ]);
        }

        return [];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Office Information')
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
                                    ->rule('required'),
                                Forms\Components\TextInput::make('code')
                                    ->markAsRequired()
                                    ->rule('required')
                                    ->unique(ignoreRecord: true),
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
                            ->columnSpan(1),
                    ]),
                Forms\Components\Section::make('Request Management')
                    ->columns([
                        'sm' => 1,
                        'md' => 3,
                    ])
                    ->schema([
                        Forms\Components\TextInput::make('settings.auto_queue')
                            ->placeholder('Number of minutes')
                            ->rules(['numeric']),
                        Forms\Components\Toggle::make('settings.support_reassignment')
                            ->inline(false)
                            ->disabled(),
                    ]),
            ])
            ->statePath('data');
    }

    public function update(): void
    {
        $data = $this->form->getState();

        $office = Office::find(Auth::user()->office_id);

        DB::transaction(function () use ($data, $office) {
            $office->update($data);

            Notification::make()
                ->success()
                ->title('Settings updated')
                ->send();
        });
    }

    protected function fillForm(): void
    {
        $this->form->fill(Office::find(Auth::user()->office_id)->toArray());
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('Update')
                ->submit('update')
                ->keyBindings(['mod+s']),
        ];
    }
}
