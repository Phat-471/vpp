<?php

namespace App\Filament\Resources\PosShiftResource\Pages;

use App\Filament\Resources\PosShiftResource;
use Filament\Resources\Pages\ListRecords;

class ListPosShifts extends ListRecords
{
    protected static string $resource = PosShiftResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
