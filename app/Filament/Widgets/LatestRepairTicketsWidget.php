<?php

namespace App\Filament\Widgets;

use App\Helpers\AppHelper;
use App\Models\RepairTicket;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestRepairTicketsWidget extends BaseWidget
{
    protected static ?string $heading = 'Tiến Độ Phiếu Sửa Chữa Máy In Mới Nhất';

    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                RepairTicket::query()->latest()->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('ticket_code')
                    ->label('Mã Phiếu')
                    ->fontFamily('mono')
                    ->weight('bold')
                    ->copyable(),

                Tables\Columns\TextColumn::make('device_name')
                    ->label('Tên Máy In')
                    ->limit(25),

                Tables\Columns\TextColumn::make('customer_name')
                    ->label('Khách Hàng')
                    ->description(fn (RepairTicket $record) => $record->customer_phone),

                Tables\Columns\TextColumn::make('status')
                    ->label('Trạng Thái')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'received' => 'Đã tiếp nhận',
                        'diagnosing' => 'Đang kiểm tra',
                        'quoted' => 'Đã báo giá',
                        'in_progress' => 'Đang sửa chữa',
                        'completed' => 'Đã xong',
                        'delivered' => 'Đã trả khách',
                        'cancelled' => 'Đã hủy',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'received' => 'gray',
                        'diagnosing' => 'info',
                        'quoted' => 'warning',
                        'in_progress' => 'primary',
                        'completed' => 'success',
                        'delivered' => 'emerald',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Chi Phí')
                    ->formatStateUsing(fn ($state) => AppHelper::formatMoney((float) $state))
                    ->fontFamily('mono')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('promised_at')
                    ->label('Hẹn Trả')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('Chưa hẹn'),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('Chi tiết')
                    ->icon('heroicon-m-pencil-square')
                    ->url(fn (RepairTicket $record): string => route('filament.admin.resources.repair-tickets.edit', ['record' => $record])),

                Tables\Actions\Action::make('print')
                    ->label('In Phiếu')
                    ->icon('heroicon-m-printer')
                    ->color('gray')
                    ->url(fn (RepairTicket $record): string => route('print.repair-ticket', ['id' => $record->id]))
                    ->openUrlInNewTab(),
            ])
            ->paginated(false);
    }
}
