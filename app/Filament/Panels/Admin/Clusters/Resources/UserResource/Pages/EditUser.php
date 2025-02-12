<?php

namespace App\Filament\Panels\Admin\Clusters\Resources\UserResource\Pages;

use App\Filament\Panels\Admin\Actions\ApproveAccountAction;
use App\Filament\Panels\Admin\Actions\DeactivateAccessAction;
use App\Filament\Panels\Admin\Actions\Tables\ChangePasswordAction;
use App\Filament\Panels\Admin\Clusters\Resources\UserResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ChangePasswordAction::make(),
            ApproveAccountAction::make(),
            DeactivateAccessAction::make(),
            Actions\RestoreAction::make(),
            Actions\ActionGroup::make([
                Actions\DeleteAction::make(),
                Actions\ForceDeleteAction::make(),
            ]),
        ];
    }

    protected function getSaveFormAction(): Action
    {
        return Action::make('save')
            ->label(__('filament-panels::resources/pages/edit-record.form.actions.save.label'))
            ->submit('save')
            ->keyBindings(['mod+s'])
            ->disabled(fn () => ! $this->record->hasActiveAccess() || ! $this->record->hasVerifiedEmail() || $this->record->trashed());
    }
}
