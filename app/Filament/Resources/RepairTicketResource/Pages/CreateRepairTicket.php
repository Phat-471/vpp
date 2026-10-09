<?php

namespace App\Filament\Resources\RepairTicketResource\Pages;

use App\Filament\Resources\RepairTicketResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRepairTicket extends CreateRecord
{
    protected static string $resource = RepairTicketResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
