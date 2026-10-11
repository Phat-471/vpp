<?php

namespace App\Filament\Resources\PosShiftResource\Pages;

use App\Filament\Resources\PosShiftResource;
use App\Models\PosShift;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPosShift extends ViewRecord
{
    protected static string $resource = PosShiftResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('print')
                ->label('In phiếu kết ca K80')
                ->icon('heroicon-o-printer')
                ->color('info')
                ->url(fn (PosShift $record) => route('print.pos-shift', $record->id))
                ->openUrlInNewTab(),
        ];
    }
}
