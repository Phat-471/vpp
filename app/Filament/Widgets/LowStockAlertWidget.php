<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LowStockAlertWidget extends BaseWidget
{
    protected static ?string $heading = 'Hàng hóa cần nhập thêm';

    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = 1;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Product::query()
                    ->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
                    ->where('is_active', true)
                    ->orderBy('stock_quantity', 'asc')
                    ->limit(6)
            )
            ->columns([
                Tables\Columns\TextColumn::make('sku')
                    ->label('Mã SKU')
                    ->fontFamily('mono')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('name')
                    ->label('Tên mặt hàng')
                    ->limit(35),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Danh mục'),

                Tables\Columns\TextColumn::make('stock_quantity')
                    ->label('Tồn kho hiện tại')
                    ->badge()
                    ->color(fn (int $state): string => $state <= 0 ? 'danger' : 'warning')
                    ->formatStateUsing(fn ($state, Product $record) => "{$state} {$record->base_unit}")
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('low_stock_threshold')
                    ->label('Ngưỡng cảnh báo')
                    ->formatStateUsing(fn ($state, Product $record) => "{$state} {$record->base_unit}"),

                Tables\Columns\TextColumn::make('is_service_part')
                    ->label('Loại')
                    ->badge()
                    ->formatStateUsing(fn (bool $state) => $state ? 'Linh kiện máy in' : 'VPP')
                    ->color(fn (bool $state) => $state ? 'info' : 'gray'),
            ])
            ->actions([
                Tables\Actions\Action::make('edit')
                    ->label('Cập nhật tồn kho')
                    ->icon('heroicon-m-arrow-path')
                    ->url(fn (Product $record): string => route('filament.admin.resources.products.edit', ['record' => $record])),
            ])
            ->emptyStateHeading('Kho hàng an toàn')
            ->emptyStateDescription('Tất cả mặt hàng đều đạt ngưỡng tồn kho tiêu chuẩn.')
            ->emptyStateIcon('heroicon-o-check-badge')
            ->paginated(false);
    }
}
