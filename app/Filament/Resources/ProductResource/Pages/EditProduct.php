<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected static ?string $title = 'Chỉnh sửa sản phẩm';

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('view_web')
                ->label('Xem trên web')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->color('gray')
                ->url(fn () => route('storefront.product-detail', $this->record->slug))
                ->openUrlInNewTab(),

            Actions\DeleteAction::make()
                ->label('Xóa sản phẩm')
                ->modalHeading('Xóa sản phẩm này?')
                ->modalDescription('Bạn có chắc chắn muốn xóa sản phẩm này khỏi cơ sở dữ liệu? Hành động này không thể hoàn tác.')
                ->modalSubmitActionLabel('Đồng ý xóa')
                ->modalCancelActionLabel('Hủy bỏ'),
        ];
    }
}
