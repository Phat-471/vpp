<?php

namespace App\Filament\Resources\DealerInquiryResource\Pages;

use App\Filament\Resources\DealerInquiryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDealerInquiry extends EditRecord
{
    protected static string $resource = DealerInquiryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
