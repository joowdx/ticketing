<?php

namespace App\Filament\Panels\Admin\Actions\Concerns;

use App\Enums\UserRole;
use App\Models\Office;
use App\Models\User;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

trait ApproveAccount
{
    protected function bootApproveUser(): void
    {
        $this->name($this instanceof BulkAction ? 'approve-accounts' : 'approve-account');

        $this->icon('gmdi-verified-o');

        $this->slideOver();

        $this->color('primary');

        $this->visible(fn (?User $user) => $this instanceof BulkAction ?: $user->hasVerifiedEmail() && is_null($user->approved_at) && ! $user->trashed());

        $this->modalWidth('xl');

        $this->modalIcon('gmdi-verified-o');

        $this->modalHeading($this instanceof BulkAction ? 'Approve accounts' : 'Approve account');

        $this->modalDescription(fn (?Collection $selectedRecords) => 'Approve '.($this instanceof BulkAction ? 'these '.$selectedRecords->count().' users' : 'this user').' to grant them access.');

        $this->successNotificationTitle('User approved');

        $this->form(fn (?User $user) => [
            Select::make('office_id')
                ->label('Office')
                ->options(Office::pluck('code', 'id'))
                ->default($this instanceof Action ? $user->office_id : null)
                ->placeholder('Select an office')
                ->searchable()
                ->required()
                ->noSearchResultsMessage('No offices found.'),
            Select::make('role')
                ->options(UserRole::class)
                ->default($this instanceof Action ? $user->role?->value : null)
                ->required(),
            Placeholder::make('purpose')
                ->content($this instanceof Action ? str($user->purpose)->replace("\n", '<br>')->toHtmlString() : null)
                ->hidden($this instanceof BulkAction),
        ]);

        switch (true) {
            case $this instanceof BulkAction:
                $this->action(function (Collection $selectedRecords, array $data) {
                    $selectedRecords->toQuery()->update([
                        'role' => $data['role'],
                        'office_id' => $data['office_id'],
                        'approved_by' => Auth::id(),
                        'approved_at' => now(),
                    ]);

                    $this->sendSuccessNotification();
                });

                $this->deselectRecordsAfterCompletion();

                break;

            default:
                $this->action(function (User $user, array $data) {
                    $user->forceFill([
                        'role' => $data['role'],
                        'office_id' => $data['office_id'],
                        'approved_by' => Auth::id(),
                        'approved_at' => now(),
                    ]);

                    $user->save();

                    $this->sendSuccessNotification();
                });

                if ($this instanceof Action) {
                    $this->accessSelectedRecords();
                }

        }
    }
}
