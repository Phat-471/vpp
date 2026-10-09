<?php

namespace App\Filament\Resources\RepairTicketResource\Pages;

use App\Filament\Resources\RepairTicketResource;
use App\Models\RepairTicket;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRepairTicket extends EditRecord
{
    protected static string $resource = RepairTicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('print')
                ->label('In phiếu')
                ->icon('heroicon-o-printer')
                ->color('info')
                ->url(fn (RepairTicket $record) => url("/print/repair-ticket/{$record->id}"))
                ->openUrlInNewTab(),

            Actions\DeleteAction::make()
                ->label('Xóa phiếu')
                ->icon('heroicon-m-trash')
                ->modalHeading('Xác nhận xóa phiếu sửa chữa')
                ->modalDescription(fn (RepairTicket $record) => "Bạn có chắc chắn muốn xóa phiếu sửa chữa '{$record->ticket_code}' của khách {$record->customer_name}? Hành động này không thể hoàn tác.")
                ->modalSubmitActionLabel('Xác nhận xóa')
                ->modalCancelActionLabel('Hủy bỏ'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
