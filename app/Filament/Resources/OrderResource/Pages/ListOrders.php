<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('+ Tạo mới đơn hàng')
                ->icon('heroicon-m-plus'),
        ];
    }

    public function getTabs(): array
    {
        $businessNow = now('Asia/Ho_Chi_Minh');
        $todayStart = $businessNow->copy()->startOfDay()->utc();
        $tomorrowStart = $todayStart->copy()->addDay();
        $monthStartLocal = $businessNow->copy()->startOfMonth();
        $nextMonthStartLocal = $monthStartLocal->copy()->addMonth();
        $monthStart = $monthStartLocal->copy()->utc();
        $nextMonthStart = $nextMonthStartLocal->utc();

        return [
            'all' => Tab::make('Tất cả đơn')
                ->badge(Order::count()),

            'pos' => Tab::make('Bán tại quầy (POS)')
                ->icon('heroicon-m-computer-desktop')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('channel', 'pos'))
                ->badge(Order::where('channel', 'pos')->count())
                ->badgeColor('primary'),

            'online' => Tab::make('Đơn Online')
                ->icon('heroicon-m-globe-alt')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('channel', 'online'))
                ->badge(Order::where('channel', 'online')->count())
                ->badgeColor('info'),

            'unpaid' => Tab::make('Chưa thanh toán')
                ->icon('heroicon-m-banknotes')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('payment_status', 'unpaid'))
                ->badge(Order::where('payment_status', 'unpaid')->count())
                ->badgeColor('danger'),

            'paid_today' => Tab::make('Đã thanh toán hôm nay')
                ->modifyQueryUsing(fn (Builder $query) => $query
                    ->where('payment_status', 'paid')
                    ->where('created_at', '>=', $todayStart)
                    ->where('created_at', '<', $tomorrowStart))
                ->badge(Order::query()
                    ->where('payment_status', 'paid')
                    ->where('created_at', '>=', $todayStart)
                    ->where('created_at', '<', $tomorrowStart)
                    ->count())
                ->badgeColor('success'),

            'paid_this_month' => Tab::make('Đã thanh toán tháng này')
                ->modifyQueryUsing(fn (Builder $query) => $query
                    ->where('payment_status', 'paid')
                    ->where('created_at', '>=', $monthStart)
                    ->where('created_at', '<', $nextMonthStart))
                ->badge(Order::query()
                    ->where('payment_status', 'paid')
                    ->where('created_at', '>=', $monthStart)
                    ->where('created_at', '<', $nextMonthStart)
                    ->count())
                ->badgeColor('primary'),

            'vat' => Tab::make('Hóa đơn VAT')
                ->icon('heroicon-m-document-text')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_vat_invoice', true))
                ->badge(Order::where('is_vat_invoice', true)->count())
                ->badgeColor('success'),
        ];
    }
}
