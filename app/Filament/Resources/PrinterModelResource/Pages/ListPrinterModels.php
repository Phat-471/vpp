<?php

namespace App\Filament\Resources\PrinterModelResource\Pages;

use App\Filament\Resources\PrinterModelResource;
use App\Models\PrinterModel;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListPrinterModels extends ListRecords
{
    protected static string $resource = PrinterModelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tạo mới dòng máy in')
                ->icon('heroicon-o-plus-circle'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Tất cả hãng')
                ->badge(PrinterModel::count()),

            'canon' => Tab::make('Canon')
                ->badge(PrinterModel::where('brand', 'Canon')->count())
                ->badgeColor('danger')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('brand', 'Canon')),

            'hp' => Tab::make('HP')
                ->badge(PrinterModel::where('brand', 'HP')->count())
                ->badgeColor('info')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('brand', 'HP')),

            'brother' => Tab::make('Brother')
                ->badge(PrinterModel::where('brand', 'Brother')->count())
                ->badgeColor('primary')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('brand', 'Brother')),

            'epson' => Tab::make('Epson')
                ->badge(PrinterModel::where('brand', 'Epson')->count())
                ->badgeColor('warning')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('brand', 'Epson')),

            'others' => Tab::make('Hãng khác')
                ->badge(PrinterModel::whereNotIn('brand', ['Canon', 'HP', 'Brother', 'Epson'])->count())
                ->badgeColor('gray')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNotIn('brand', ['Canon', 'HP', 'Brother', 'Epson'])),
        ];
    }
}
