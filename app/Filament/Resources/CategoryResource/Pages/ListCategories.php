<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Filament\Resources\CategoryResource;
use App\Models\Category;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListCategories extends ListRecords
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('+ Thêm danh mục mới')
                ->icon('heroicon-m-plus'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Tất cả danh mục')
                ->badge(Category::count()),

            'active' => Tab::make('Đang hiển thị')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_active', true))
                ->badge(Category::where('is_active', true)->count())
                ->badgeColor('success'),

            'inactive' => Tab::make('Đang ẩn')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_active', false))
                ->badge(Category::where('is_active', false)->count())
                ->badgeColor('gray'),
        ];
    }
}
