<?php

namespace App\Filament\Resources\StockCheckResource\Pages;

use App\Filament\Resources\StockCheckResource;
use Filament\Resources\Pages\CreateRecord;

class CreateStockCheck extends CreateRecord
{
    protected static string $resource = StockCheckResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
