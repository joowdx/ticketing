<?php

namespace App\Filament\Panels\Admin\Clusters\Management\Pages;

use App\Filament\Panels\Admin\Clusters\Management;
use Filament\Pages\Page;

class Settings extends Page
{
    protected static ?string $navigationIcon = 'gmdi-settings-o';

    protected static string $view = 'filament.panels.admin.clusters.management.pages.policies';

    protected static ?string $cluster = Management::class;
}
