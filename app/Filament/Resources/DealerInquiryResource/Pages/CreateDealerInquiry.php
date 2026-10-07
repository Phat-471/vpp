<?php

namespace App\Filament\Resources\DealerInquiryResource\Pages;

use App\Filament\Resources\DealerInquiryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDealerInquiry extends CreateRecord
{
    protected static string $resource = DealerInquiryResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
