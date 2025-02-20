<?php

namespace App\Filament\Panels\Root\Clusters\Organization\Resources\OfficeResource\Pages;

use App\Filament\Panels\Root\Clusters\Organization\Resources\OfficeResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateOffice extends CreateRecord
{
    protected static string $resource = OfficeResource::class;

    protected static bool $canCreateAnother = false;

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
            Action::make('back')
                ->color('gray')
                ->icon('heroicon-o-arrow-left')
                ->url(static::$resource::getUrl()),
        ];
    }

    protected function getCancelFormAction(): Action
    {
        return Action::make('cancel')
            ->hidden();
    }
}
