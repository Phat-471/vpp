<?php

namespace App\Filament\Resources\DealerInquiryResource\Pages;

use App\Filament\Resources\DealerInquiryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDealerInquiries extends ListRecords
{
    protected static string $resource = DealerInquiryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Thêm đối tác mới'),
        ];
    }
}
