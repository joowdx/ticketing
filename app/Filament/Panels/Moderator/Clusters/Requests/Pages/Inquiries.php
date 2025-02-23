<?php

namespace App\Filament\Panels\Moderator\Clusters\Requests\Pages;

use App\Filament\Clusters\Requests\Resources\RequestResource\Pages\ListInquiries;
use App\Filament\Panels\Moderator\Clusters\Requests\Resources\InquiryResource;

class Inquiries extends ListInquiries
{
    protected static string $resource = InquiryResource::class;
}
