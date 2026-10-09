<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CustomerResource;
use App\Models\Customer;
use App\Services\CustomerManagement;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditCustomer extends EditRecord
{
    protected static string $resource = CustomerResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return app(CustomerManagement::class)->save($data, $record->id);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('Xóa khách hàng')
                ->icon('heroicon-m-trash')
                ->modalHeading('Xác nhận xóa khách hàng')
                ->modalDescription(fn (Customer $record) => "Bạn có chắc chắn muốn xóa khách hàng '{$record->name}' ({$record->phone})? Hành động này sẽ bị từ chối nếu khách đã từng có đơn hàng hoặc phiếu sửa chữa.")
                ->modalSubmitActionLabel('Xác nhận xóa vĩnh viễn')
                ->modalCancelActionLabel('Hủy bỏ')
                ->using(function (Customer $record): bool {
                    try {
                        app(CustomerManagement::class)->delete($record->id);
                        Notification::make()
                            ->title('Đã xóa khách hàng thành công')
                            ->success()
                            ->send();
                        return true;
                    } catch (\Throwable $e) {
                        Notification::make()
                            ->title('Không thể xóa khách hàng')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                        return false;
                    }
                }),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
