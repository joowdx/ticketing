<?php

namespace App\Filament\Panels\Root\Clusters\Organization\Resources\UserResource\Pages;

use App\Filament\Actions\ApproveAccountAction;
use App\Filament\Actions\ChangePasswordAction;
use App\Filament\Actions\DeactivateAccessAction;
use App\Filament\Panels\Root\Clusters\Organization\Resources\UserResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    public function getSubNavigation(): array
    {
        if (filled($cluster = static::getCluster())) {
            return $this->generateNavigationItems($cluster::getClusteredComponents());
        }

        return [];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back')
                ->color('gray')
                ->icon('heroicon-o-arrow-left')
                ->url(static::$resource::getUrl()),
            ChangePasswordAction::make(),
            ApproveAccountAction::make(),
            DeactivateAccessAction::make(),
            Actions\RestoreAction::make(),
            Actions\ActionGroup::make([
                Actions\DeleteAction::make(),
                Actions\ForceDeleteAction::make()
                    ->label('Delete'),
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

    protected function getCancelFormAction(): Action
    {
        return Action::make('cancel')
            ->hidden();
    }
}
