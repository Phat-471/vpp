<?php

namespace App\Filament\Resources\PrinterModelResource\Pages;

use App\Filament\Resources\PrinterModelResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePrinterModel extends CreateRecord
{
    protected static string $resource = PrinterModelResource::class;
}
