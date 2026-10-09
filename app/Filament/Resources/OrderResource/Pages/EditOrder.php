<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('print')
                ->label('In hóa đơn')
                ->icon('heroicon-o-printer')
                ->color('info')
                ->url(fn (Order $record) => url("/print/order/{$record->id}"))
                ->openUrlInNewTab(),

            Actions\Action::make('print_vat')
                ->label('In VAT')
                ->icon('heroicon-o-document-currency-dollar')
                ->color('primary')
                ->visible(fn (Order $record) => (bool) $record->is_vat_invoice)
                ->url(fn (Order $record) => url("/print/vat-invoice/{$record->id}"))
                ->openUrlInNewTab(),

            Actions\DeleteAction::make()
                ->label('Xóa đơn hàng')
                ->icon('heroicon-m-trash')
                ->modalHeading('Xác nhận xóa đơn hàng')
                ->modalDescription(fn (Order $record) => "Bạn có chắc chắn muốn xóa đơn hàng '{$record->order_code}'? Hành động này không thể hoàn tác.")
                ->modalSubmitActionLabel('Xác nhận xóa')
                ->modalCancelActionLabel('Hủy bỏ'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
