<?php

namespace App\Filament\Resources\PurchaseOrderResource\Pages;

use App\Filament\Resources\PurchaseOrderResource;
use App\Models\PurchaseOrder;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListPurchaseOrders extends ListRecords
{
    protected static string $resource = PurchaseOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tạo phiếu nhập mới')
                ->icon('heroicon-m-plus'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Tất cả phiếu'),

            'draft' => Tab::make('Chờ nhập kho')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'draft'))
                ->badge(fn () => PurchaseOrder::where('status', 'draft')->count() ?: null)
                ->badgeColor('warning'),

            'received' => Tab::make('Đã nhập kho')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'received'))
                ->badge(fn () => PurchaseOrder::where('status', 'received')->count() ?: null)
                ->badgeColor('success'),

            'has_debt' => Tab::make('Còn nợ NCC')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('debt_amount', '>', 0))
                ->badge(fn () => PurchaseOrder::where('debt_amount', '>', 0)->count() ?: null)
                ->badgeColor('danger'),
        ];
    }
}
