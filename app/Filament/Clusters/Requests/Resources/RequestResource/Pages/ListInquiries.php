<?php

namespace App\Filament\Clusters\Requests\Resources\RequestResource\Pages;

use App\Filament\Clusters\Requests\Resources\InquiryResource;
use Filament\Resources\Pages\ListRecords;

class ListInquiries extends ListRecords
{
    protected static string $resource = InquiryResource::class;
}
