<?php

namespace App\Filament\Resources\RepairTicketResource\Pages;

use App\Filament\Resources\RepairTicketResource;
use App\Models\RepairTicket;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListRepairTickets extends ListRecords
{
    protected static string $resource = RepairTicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('+ Tiếp nhận sửa máy mới')
                ->icon('heroicon-m-plus'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Tất cả phiếu')
                ->badge(RepairTicket::count()),

            'processing' => Tab::make('🛠️ Đang xử lý')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('status', ['received', 'diagnosing', 'waiting_approval', 'in_progress']))
                ->badge(RepairTicket::whereIn('status', ['received', 'diagnosing', 'waiting_approval', 'in_progress'])->count())
                ->badgeColor('warning'),

            'waiting_approval' => Tab::make('⏳ Chờ duyệt giá')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'waiting_approval'))
                ->badge(RepairTicket::where('status', 'waiting_approval')->count())
                ->badgeColor('info'),

            'completed' => Tab::make('✅ Đã sửa xong')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'completed'))
                ->badge(RepairTicket::where('status', 'completed')->count())
                ->badgeColor('success'),

            'unpaid' => Tab::make('💳 Chưa trả tiền')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('payment_status', ['unpaid', 'partially_paid']))
                ->badge(RepairTicket::whereIn('payment_status', ['unpaid', 'partially_paid'])->count())
                ->badgeColor('danger'),
        ];
    }
}
