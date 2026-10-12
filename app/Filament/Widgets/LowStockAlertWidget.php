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
                    ->with('category')
                    ->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
                    ->where('is_active', true)
                    ->orderBy('stock_quantity', 'asc')
                    ->limit(6)
            )
            ->columns([
                Tables\Columns\TextColumn::make('sku')
                    ->label('Mã hàng')
                    ->fontFamily('mono')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('name')
                    ->label('Tên mặt hàng')
                    ->limit(35),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Danh mục')
                    ->placeholder('Chưa phân loại'),

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
                Tables\Actions\Action::make('quick_restock')
                    ->label('Nhập nhanh')
                    ->icon('heroicon-m-plus-circle')
                    ->color('success')
                    ->modalHeading(fn (Product $record) => "Nhập kho nhanh: {$record->name}")
                    ->modalSubmitActionLabel('Xác nhận nhập kho')
                    ->form([
                        \Filament\Forms\Components\TextInput::make('quantity')
                            ->label('Số lượng nhập thêm')
                            ->numeric()
                            ->minValue(1)
                            ->required()
                            ->default(10)
                            ->suffix(fn (Product $record) => $record->base_unit ?? 'cái'),
                        \Filament\Forms\Components\TextInput::make('notes')
                            ->label('Ghi chú')
                            ->default('Nhập bổ sung từ Dashboard')
                            ->maxLength(255),
                    ])
                    ->action(function (Product $record, array $data): void {
                        \Illuminate\Support\Facades\DB::transaction(function () use ($record, $data) {
                            $addQty = (int) $data['quantity'];
                            $record->increment('stock_quantity', $addQty);
                        });

                        \Filament\Notifications\Notification::make()
                            ->title('Nhập kho thành công')
                            ->body("Đã bổ sung {$data['quantity']} {$record->base_unit} cho {$record->name}.")
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('edit')
                    ->label('Sửa')
                    ->icon('heroicon-m-pencil-square')
                    ->color('gray')
                    ->url(fn (Product $record): string => route('filament.admin.resources.products.edit', ['record' => $record])),
            ])
            ->emptyStateHeading('Kho hàng an toàn')
            ->emptyStateDescription('Tất cả mặt hàng đều đạt ngưỡng tồn kho tiêu chuẩn.')
            ->emptyStateIcon('heroicon-o-check-badge')
            ->paginated(false);
    }
}
