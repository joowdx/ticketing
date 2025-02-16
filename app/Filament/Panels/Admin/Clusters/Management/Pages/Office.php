<?php

namespace App\Filament\Panels\Admin\Clusters\Management\Pages;

use App\Filament\Panels\Admin\Clusters\Management;
use Filament\Pages\Page;

class Office extends Page
{
    protected static ?string $navigationIcon = 'gmdi-domain-o';

    protected static string $view = 'filament.panels.admin.clusters.management.pages.office';

    protected static ?string $cluster = Management::class;
}
