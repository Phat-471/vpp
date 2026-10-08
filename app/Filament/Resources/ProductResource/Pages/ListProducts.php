<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Models\Product;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('+ Thêm sản phẩm mới')
                ->icon('heroicon-m-plus'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Tất cả')
                ->badge(Product::count()),

            'active' => Tab::make('Đang mở bán')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_active', true))
                ->badge(Product::where('is_active', true)->count()),

            'low_stock' => Tab::make('⚠️ Sắp hết hàng')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereColumn('stock_quantity', '<=', 'low_stock_threshold'))
                ->badge(Product::whereColumn('stock_quantity', '<=', 'low_stock_threshold')->count())
                ->badgeColor('danger'),

            'missing_image' => Tab::make('📷 Chưa có ảnh')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('has_custom_image', false))
                ->badge(Product::where('has_custom_image', false)->count())
                ->badgeColor('warning'),

            'service' => Tab::make('🔧 Linh kiện máy in')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_service_part', true)),
        ];
    }
}
