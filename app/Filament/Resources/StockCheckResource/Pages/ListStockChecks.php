<?php

namespace App\Filament\Resources\StockCheckResource\Pages;

use App\Filament\Resources\StockCheckResource;
use App\Models\StockCheck;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListStockChecks extends ListRecords
{
    protected static string $resource = StockCheckResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tạo đợt kiểm kê mới')
                ->icon('heroicon-m-plus'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Tất cả đợt kiểm'),

            'draft' => Tab::make('Đang kiểm đếm')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'draft'))
                ->badge(fn () => StockCheck::where('status', 'draft')->count() ?: null)
                ->badgeColor('warning'),

            'completed' => Tab::make('Đã cân bằng kho')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'completed'))
                ->badge(fn () => StockCheck::where('status', 'completed')->count() ?: null)
                ->badgeColor('success'),

            'difference' => Tab::make('Có chênh lệch')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('difference_items', '>', 0))
                ->badge(fn () => StockCheck::where('difference_items', '>', 0)->count() ?: null)
                ->badgeColor('danger'),
        ];
    }
}
