<?php

namespace App\Filament\Resources\PurchaseOrderResource\Pages;

use App\Filament\Resources\PurchaseOrderResource;
use App\Models\PurchaseOrder;
use App\Services\InventoryService;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditPurchaseOrder extends EditRecord
{
    protected static string $resource = PurchaseOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('receive')
                ->label('Duyệt nhập kho')
                ->icon('heroicon-m-arrow-down-tray')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Xác nhận nhập kho')
                ->modalDescription('Tồn kho sản phẩm và công nợ nhà cung cấp sẽ được cập nhật tự động ngay lập tức.')
                ->modalSubmitActionLabel('Xác nhận nhập kho')
                ->visible(fn (PurchaseOrder $record) => $record->isDraft())
                ->action(function (PurchaseOrder $record) {
                    try {
                        app(InventoryService::class)->receivePurchaseOrder($record);
                        Notification::make()
                            ->title('Nhập kho thành công!')
                            ->body("Phiếu {$record->code} đã được nhập kho. Tồn kho và giá vốn đã được cập nhật.")
                            ->success()
                            ->send();
                        $this->fillForm();
                    } catch (\Throwable $e) {
                        Notification::make()
                            ->title('Lỗi khi nhập kho')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            Actions\Action::make('cancel')
                ->label('Hủy phiếu')
                ->icon('heroicon-m-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Hủy phiếu nhập kho')
                ->form([
                    Forms\Components\Textarea::make('reason')
                        ->label('Lý do hủy phiếu *')
                        ->required(),
                ])
                ->visible(fn (PurchaseOrder $record) => ! $record->isCancelled())
                ->action(function (PurchaseOrder $record, array $data) {
                    try {
                        app(InventoryService::class)->cancelPurchaseOrder($record, $data['reason']);
                        Notification::make()
                            ->title('Đã hủy phiếu nhập kho')
                            ->body("Phiếu {$record->code} đã được hủy thành công.")
                            ->info()
                            ->send();
                        $this->fillForm();
                    } catch (\Throwable $e) {
                        Notification::make()
                            ->title('Lỗi khi hủy phiếu')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            Actions\DeleteAction::make()
                ->label('Xóa phiếu')
                ->visible(fn (PurchaseOrder $record) => $record->isDraft()),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
