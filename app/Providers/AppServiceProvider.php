<?php

namespace App\Providers;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Livewire\Notifications;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\VerticalAlignment;
use Filament\Support\Facades\FilamentView;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Filament\View\PanelsRenderHook;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('local') && class_exists(\Laravel\Telescope\TelescopeServiceProvider::class)) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
        }

        FilamentView::registerRenderHook(PanelsRenderHook::HEAD_START, fn () => Blade::render('@vite(\'resources/css/app.css\')'));

        Notifications::verticalAlignment(VerticalAlignment::End);

        Notifications::alignment(Alignment::Start);

        Table::configureUsing(function (Table $table) {
            $table->paginated([5, 10, 15, 20, 25, 50, 100])
                ->defaultPaginationPageOption(20)
                ->recordClasses(function (Model $model) {
                    if (in_array(SoftDeletes::class, class_uses_recursive($model))) {
                        return $model->trashed() ? 'bg-gray-100 dark:bg-gray-800' : null;
                    }
                });
        });

        TextInput::configureUsing(fn (TextInput $input) => $input->maxLength(255));

        Select::configureUsing(fn (Select $select) => $select->native(false));

        SelectFilter::configureUsing(fn (SelectFilter $filter) => $filter->native(false));

        TrashedFilter::configureUsing(fn (TrashedFilter $filter) => $filter->native(false));

        \Filament\Actions\ForceDeleteAction::configureUsing(function (\Filament\Actions\ForceDeleteAction $action) {
            $action->form([
                TextInput::make('password')
                    ->password()
                    ->rule('required')
                    ->markAsRequired()
                    ->currentPassword()
                    ->helperText('Enter your password to confirm.'),
            ]);
        });

        \Filament\Tables\Actions\ForceDeleteAction::configureUsing(function (\Filament\Tables\Actions\ForceDeleteAction $action) {
            $action->form([
                TextInput::make('password')
                    ->password()
                    ->rule('required')
                    ->markAsRequired()
                    ->currentPassword()
                    ->helperText('Enter your password to confirm.'),
            ]);
        });
    }
}
