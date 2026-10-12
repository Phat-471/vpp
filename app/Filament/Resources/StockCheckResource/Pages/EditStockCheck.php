<?php

namespace App\Filament\Resources\StockCheckResource\Pages;

use App\Filament\Resources\StockCheckResource;
use App\Models\StockCheck;
use App\Services\InventoryService;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditStockCheck extends EditRecord
{
    protected static string $resource = StockCheckResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('complete')
                ->label('Cân bằng kho')
                ->icon('heroicon-m-arrow-path-rounded-square')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Xác nhận cân bằng tồn kho')
                ->modalDescription('Tồn kho thực tế kiểm đếm sẽ được ghi đè trực tiếp vào tồn kho hệ thống.')
                ->visible(fn (StockCheck $record) => $record->isDraft() && (auth()->user()?->isAdmin() ?? false))
                ->action(function (StockCheck $record) {
                    try {
                        app(InventoryService::class)->completeStockCheck($record);
                        Notification::make()
                            ->title('Cân bằng kho thành công!')
                            ->body("Phiếu {$record->code} đã được áp dụng cân bằng kho.")
                            ->success()
                            ->send();
                        $this->fillForm();
                    } catch (\Throwable $e) {
                        Notification::make()
                            ->title('Lỗi khi cân bằng kho')
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
                ->modalHeading('Hủy phiếu kiểm kê')
                ->form([
                    Forms\Components\Textarea::make('reason')
                        ->label('Lý do hủy phiếu *')
                        ->required(),
                ])
                ->visible(fn (StockCheck $record) => ! $record->isCancelled() && (auth()->user()?->isAdmin() ?? false))
                ->action(function (StockCheck $record, array $data) {
                    try {
                        app(InventoryService::class)->cancelStockCheck($record, $data['reason']);
                        Notification::make()
                            ->title('Đã hủy phiếu kiểm kê')
                            ->body("Phiếu {$record->code} đã được hủy.")
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
                ->visible(fn (StockCheck $record) => $record->isDraft()),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
