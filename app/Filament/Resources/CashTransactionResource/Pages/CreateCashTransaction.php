<?php

namespace App\Filament\Resources\CashTransactionResource\Pages;

use App\Filament\Resources\CashTransactionResource;
use App\Models\CashTransaction;
use App\Services\CashbookService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateCashTransaction extends CreateRecord
{
    protected static string $resource = CashTransactionResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $service = app(CashbookService::class);

        if (($data['type'] ?? 'receipt') === 'receipt') {
            return $service->createReceipt($data);
        }

        return $service->createPayment($data);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
