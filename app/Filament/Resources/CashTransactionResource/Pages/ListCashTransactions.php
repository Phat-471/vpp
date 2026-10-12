<?php

namespace App\Filament\Resources\CashTransactionResource\Pages;

use App\Filament\Resources\CashTransactionResource;
use App\Models\CashTransaction;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListCashTransactions extends ListRecords
{
    protected static string $resource = CashTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Lập phiếu thu / chi')
                ->icon('heroicon-m-plus'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Tất cả giao dịch'),

            'receipts' => Tab::make('Phiếu thu (Tiền vào)')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', 'receipt'))
                ->badge(fn () => CashTransaction::where('type', 'receipt')->where('status', 'completed')->count() ?: null)
                ->badgeColor('success'),

            'payments' => Tab::make('Phiếu chi (Tiền ra)')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', 'payment'))
                ->badge(fn () => CashTransaction::where('type', 'payment')->where('status', 'completed')->count() ?: null)
                ->badgeColor('danger'),

            'today' => Tab::make('Hôm nay')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereDate('performed_at', today()))
                ->badge(fn () => CashTransaction::whereDate('performed_at', today())->count() ?: null)
                ->badgeColor('info'),
        ];
    }
}
