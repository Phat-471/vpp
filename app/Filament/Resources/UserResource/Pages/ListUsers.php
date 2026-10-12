<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('+ Thêm nhân viên mới')
                ->icon('heroicon-m-user-plus'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Tất cả nhân sự')
                ->badge(User::count()),

            'admin' => Tab::make('Quản trị viên')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('role', 'admin'))
                ->badge(User::where('role', 'admin')->count())
                ->badgeColor('danger'),

            'cashier' => Tab::make('Thu ngân (POS)')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('role', 'cashier'))
                ->badge(User::where('role', 'cashier')->count())
                ->badgeColor('success'),

            'technician' => Tab::make('Kỹ thuật viên')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('role', 'technician'))
                ->badge(User::where('role', 'technician')->count())
                ->badgeColor('warning'),
        ];
    }
}
