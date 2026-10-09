<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CustomerResource;
use App\Models\Customer;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListCustomers extends ListRecords
{
    protected static string $resource = CustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('+ Thêm khách hàng mới')
                ->icon('heroicon-m-user-plus'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Tất cả khách hàng')
                ->badge(Customer::count()),

            'activated' => Tab::make('✅ Đã kích hoạt')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNotNull('phone_verified_at'))
                ->badge(Customer::whereNotNull('phone_verified_at')->count())
                ->badgeColor('success'),

            'unactivated' => Tab::make('⏳ Chưa kích hoạt')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNull('phone_verified_at'))
                ->badge(Customer::whereNull('phone_verified_at')->count())
                ->badgeColor('warning'),

            'has_debt' => Tab::make('💳 Đang nợ')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('debt_balance', '>', 0))
                ->badge(Customer::where('debt_balance', '>', 0)->count())
                ->badgeColor('danger'),
        ];
    }
}
