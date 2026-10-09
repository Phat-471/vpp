<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Filament\Resources\CategoryResource;
use App\Models\Category;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditCategory extends EditRecord
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('Xóa danh mục')
                ->icon('heroicon-m-trash')
                ->modalHeading('Xác nhận xóa danh mục')
                ->modalDescription(fn (Category $record) => "Bạn có chắc muốn xóa danh mục '{$record->name}'? Nếu có sản phẩm thuộc danh mục này, bạn phải chuyển sản phẩm sang danh mục khác trước.")
                ->modalSubmitActionLabel('Xác nhận xóa')
                ->modalCancelActionLabel('Hủy bỏ')
                ->before(function (Category $record, Actions\DeleteAction $action) {
                    if ($record->products()->count() > 0) {
                        Notification::make()
                            ->title('Không thể xóa danh mục')
                            ->body("Danh mục '{$record->name}' đang chứa {$record->products()->count()} sản phẩm. Vui lòng chuyển sản phẩm sang danh mục khác trước.")
                            ->danger()
                            ->send();
                        $action->halt();
                    }
                }),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
