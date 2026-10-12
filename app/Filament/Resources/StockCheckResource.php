<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StockCheckResource\Pages;
use App\Models\Product;
use App\Models\StockCheck;
use App\Services\InventoryService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class StockCheckResource extends Resource
{
    protected static ?string $model = StockCheck::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationGroup = 'Kho hàng';

    protected static ?string $navigationLabel = 'Kiểm kê & Cân bằng kho';

    protected static ?string $modelLabel = 'Phiếu kiểm kê';

    protected static ?string $pluralModelLabel = 'Danh sách kiểm kê kho';

    protected static ?int $navigationSort = 5;

    public static function canViewAny(): bool
    {
        return (auth()->user()?->isAdmin() || auth()->user()?->isCashier()) ?? false;
    }

    public static function canCreate(): bool
    {
        return (auth()->user()?->isAdmin() || auth()->user()?->isCashier()) ?? false;
    }

    public static function canEdit(Model $record): bool
    {
        /** @var StockCheck $record */
        return (auth()->user()?->isAdmin() ?? false) && $record->isDraft();
    }

    public static function canDelete(Model $record): bool
    {
        /** @var StockCheck $record */
        return (auth()->user()?->isAdmin() ?? false) && $record->isDraft();
    }

    public static function getNavigationBadge(): ?string
    {
        $draftCount = static::getModel()::where('status', 'draft')->count();
        return $draftCount ? (string) $draftCount : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(3)
                    ->schema([
                        Forms\Components\Group::make([
                            Forms\Components\Section::make('Thông tin đợt kiểm kê')
                                ->icon('heroicon-o-document-text')
                                ->columns(3)
                                ->schema([
                                    Forms\Components\TextInput::make('code')
                                        ->label('Mã phiếu kiểm')
                                        ->default(fn () => StockCheck::generateNextCode())
                                        ->disabled()
                                        ->dehydrated()
                                        ->required(),

                                    Forms\Components\Select::make('status')
                                        ->label('Trạng thái')
                                        ->options([
                                            'draft' => 'Đang kiểm đếm (Chưa cân bằng)',
                                            'completed' => 'Đã cân bằng kho',
                                            'cancelled' => 'Đã hủy',
                                        ])
                                        ->default('draft')
                                        ->disabled(fn ($record) => !empty($record) && ! $record->isDraft())
                                        ->dehydrated()
                                        ->required(),

                                    Forms\Components\DateTimePicker::make('created_at')
                                        ->label('Ngày lập phiếu')
                                        ->default(now())
                                        ->disabled(),

                                    Forms\Components\Textarea::make('notes')
                                        ->label('Ghi chú đợt kiểm kê')
                                        ->placeholder('Kiểm kê định kỳ cuối tháng, kiểm đột xuất kệ bút...')
                                        ->columnSpanFull()
                                        ->rows(2),
                                ]),

                            Forms\Components\Section::make('Chi tiết kiểm đếm hàng hóa (Thực tế vs Tồn máy)')
                                ->icon('heroicon-o-cube')
                                ->schema([
                                    Forms\Components\Repeater::make('items')
                                        ->label('Danh sách mặt hàng kiểm đếm')
                                        ->relationship('items')
                                        ->schema([
                                            Forms\Components\Select::make('product_id')
                                                ->label('Chọn sản phẩm')
                                                ->options(fn () => Product::pluck('name', 'id'))
                                                ->searchable()
                                                ->required()
                                                ->live()
                                                ->afterStateUpdated(function ($state, callable $set) {
                                                    if ($state) {
                                                        $p = Product::find($state);
                                                        if ($p) {
                                                            $set('product_name', $p->name);
                                                            $set('unit_name', $p->base_unit ?: 'Cái');
                                                            $set('system_quantity', (int) $p->stock_quantity);
                                                            $set('actual_quantity', (int) $p->stock_quantity);
                                                            $set('cost_price', (float) $p->cost_price);
                                                            $set('difference', 0);
                                                            $set('difference_value', 0);
                                                        }
                                                    }
                                                })
                                                ->columnSpan(4),

                                            Forms\Components\TextInput::make('product_name')
                                                ->label('Tên sản phẩm')
                                                ->disabled()
                                                ->dehydrated()
                                                ->columnSpan(3),

                                            Forms\Components\TextInput::make('unit_name')
                                                ->label('ĐVT')
                                                ->disabled()
                                                ->dehydrated()
                                                ->columnSpan(1),

                                            Forms\Components\TextInput::make('system_quantity')
                                                ->label('Tồn máy')
                                                ->numeric()
                                                ->disabled()
                                                ->dehydrated()
                                                ->columnSpan(1),

                                            Forms\Components\TextInput::make('actual_quantity')
                                                ->label('Tồn thực tế *')
                                                ->numeric()
                                                ->required()
                                                ->live()
                                                ->columnSpan(2)
                                                ->afterStateUpdated(function (callable $get, callable $set) {
                                                    $sys = (int) ($get('system_quantity') ?? 0);
                                                    $act = (int) ($get('actual_quantity') ?? 0);
                                                    $cost = (float) ($get('cost_price') ?? 0);
                                                    $diff = $act - $sys;
                                                    $set('difference', $diff);
                                                    $set('difference_value', $diff * $cost);
                                                }),

                                            Forms\Components\TextInput::make('difference')
                                                ->label('Lệch')
                                                ->numeric()
                                                ->disabled()
                                                ->dehydrated()
                                                ->extraInputAttributes(fn (callable $get) => [
                                                    'style' => ((int) $get('difference') !== 0) ? 'color: #e11d48; font-weight: 800;' : 'color: #10b981; font-weight: 800;',
                                                ])
                                                ->columnSpan(1),

                                            Forms\Components\TextInput::make('cost_price')
                                                ->hidden()
                                                ->dehydrated(),

                                            Forms\Components\TextInput::make('difference_value')
                                                ->hidden()
                                                ->dehydrated(),

                                            Forms\Components\TextInput::make('reason')
                                                ->label('Lý do chênh lệch (nếu có)')
                                                ->placeholder('Thừa kiểm kê, rách hỏng bao bì...')
                                                ->columnSpanFull(),
                                        ])
                                        ->columns(12)
                                        ->defaultItems(1)
                                        ->addActionLabel('+ Thêm sản phẩm kiểm đếm'),
                                ]),
                        ])->columnSpan(['lg' => 2]),

                        Forms\Components\Group::make([
                            Forms\Components\Section::make('Tổng kết kiểm kê')
                                ->icon('heroicon-o-chart-pie')
                                ->schema([
                                    Forms\Components\Placeholder::make('total_items_display')
                                        ->label('Tổng số mặt hàng kiểm')
                                        ->content(fn (?StockCheck $record) => $record ? "{$record->total_items} mặt hàng" : 'Đang lập'),

                                    Forms\Components\Placeholder::make('diff_display')
                                        ->label('Số mặt hàng chênh lệch')
                                        ->content(fn (?StockCheck $record) => $record ? "{$record->difference_items} mặt hàng lệch" : 'Đang lập'),

                                    Forms\Components\Placeholder::make('total_diff_value_display')
                                        ->label('Tổng giá trị chênh lệch (giá vốn)')
                                        ->content(fn (?StockCheck $record) => $record ? number_format($record->total_difference_value, 0, ',', '.') . '₫' : 'Đang lập'),

                                    Forms\Components\Placeholder::make('completed_at_display')
                                        ->label('Thời gian cân bằng kho')
                                        ->content(fn (?StockCheck $record) => $record?->completed_at ? $record->completed_at->format('d/m/Y H:i') : 'Chưa cân bằng kho'),
                                ]),
                        ])->columnSpan(['lg' => 1]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Mã phiếu')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_items')
                    ->label('Mặt hàng kiểm')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                Tables\Columns\TextColumn::make('difference_items')
                    ->label('Hàng bị lệch')
                    ->badge()
                    ->color(fn ($state) => $state > 0 ? 'danger' : 'success')
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_difference_value')
                    ->label('Giá trị lệch (₫)')
                    ->money('VND')
                    ->weight('bold')
                    ->color(fn ($state) => (float) $state != 0 ? 'danger' : 'gray')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Trạng thái')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'draft' => 'Đang kiểm đếm',
                        'completed' => 'Đã cân bằng kho',
                        'cancelled' => 'Đã hủy',
                        default => $state,
                    })
                    ->icon(fn ($state) => match ($state) {
                        'draft' => 'heroicon-m-clock',
                        'completed' => 'heroicon-m-check-circle',
                        'cancelled' => 'heroicon-m-x-circle',
                        default => null,
                    })
                    ->color(fn ($state) => match ($state) {
                        'draft' => 'warning',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('creator.name')
                    ->label('Người tạo')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Trạng thái')
                    ->options([
                        'draft' => 'Đang kiểm đếm (Chờ duyệt)',
                        'completed' => 'Đã cân bằng kho',
                        'cancelled' => 'Đã hủy',
                    ]),

                Tables\Filters\Filter::make('has_difference')
                    ->label('Có mặt hàng chênh lệch')
                    ->query(fn (Builder $query) => $query->where('difference_items', '>', 0)),
            ])
            ->actions([
                Tables\Actions\Action::make('complete')
                    ->label('Cân bằng kho')
                    ->icon('heroicon-m-arrow-path-rounded-square')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading(fn (StockCheck $record) => "Cân bằng tồn kho theo phiếu {$record->code}")
                    ->modalDescription('CẢNH BÁO: Tồn kho trên hệ thống của toàn bộ mặt hàng trong phiếu sẽ được CẬP NHẬT TRỰC TIẾP theo số lượng thực tế kiểm đếm. Hành động này không thể hoàn tác nếu không có lý do.')
                    ->modalSubmitActionLabel('Xác nhận cân bằng tồn kho')
                    ->visible(fn (StockCheck $record) => $record->isDraft() && (auth()->user()?->isAdmin() ?? false))
                    ->action(function (StockCheck $record) {
                        try {
                            app(InventoryService::class)->completeStockCheck($record);
                            Notification::make()
                                ->title('Cân bằng tồn kho thành công!')
                                ->body("Phiếu {$record->code} đã được áp dụng. Tồn kho sản phẩm đã đồng bộ theo số liệu thực tế.")
                                ->success()
                                ->send();
                        } catch (\Throwable $e) {
                            Notification::make()
                                ->title('Lỗi khi cân bằng kho')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),

                Tables\Actions\Action::make('cancel')
                    ->label('Hủy phiếu')
                    ->icon('heroicon-m-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading(fn (StockCheck $record) => "Hủy phiếu kiểm kê {$record->code}")
                    ->form([
                        Forms\Components\Textarea::make('reason')
                            ->label('Lý do hủy phiếu *')
                            ->required(),
                    ])
                    ->visible(fn (StockCheck $record) => ! $record->isCancelled() && (auth()->user()?->isAdmin() ?? false))
                    ->action(function (StockCheck $record, array $data) {
                        try {
                            app(InventoryService::class)->cancelStockCheck($record, $data['reason']);
                            Notification::make()
                                ->title('Đã hủy phiếu kiểm kê')
                                ->body("Phiếu {$record->code} đã được hủy thành công.")
                                ->info()
                                ->send();
                        } catch (\Throwable $e) {
                            Notification::make()
                                ->title('Lỗi khi hủy phiếu')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),

                Tables\Actions\EditAction::make()
                    ->label('Sửa')
                    ->visible(fn (StockCheck $record) => $record->isDraft()),

                Tables\Actions\DeleteAction::make()
                    ->label('Xóa')
                    ->visible(fn (StockCheck $record) => $record->isDraft()),
            ])
            ->emptyStateHeading('Chưa có phiếu kiểm kê kho nào')
            ->emptyStateDescription('Tạo đợt kiểm kê mới để đối soát số lượng hàng thực tế và cân bằng tồn kho hệ thống.')
            ->emptyStateIcon('heroicon-o-clipboard-document-check');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['creator', 'items.product']);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStockChecks::route('/'),
            'create' => Pages\CreateStockCheck::route('/create'),
            'edit' => Pages\EditStockCheck::route('/{record}/edit'),
        ];
    }
}
