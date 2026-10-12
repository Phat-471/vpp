<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PurchaseOrderResource\Pages;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Services\InventoryService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderResource extends Resource
{
    protected static ?string $model = PurchaseOrder::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-down-on-square-stack';

    protected static ?string $navigationGroup = 'Kho hàng';

    protected static ?string $navigationLabel = 'Nhập kho NCC';

    protected static ?string $modelLabel = 'Phiếu nhập kho';

    protected static ?string $pluralModelLabel = 'Danh sách phiếu nhập hàng NCC';

    protected static ?int $navigationSort = 4;

    public static function canViewAny(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function canEdit(Model $record): bool
    {
        /** @var PurchaseOrder $record */
        return (auth()->user()?->isAdmin() ?? false) && $record->isDraft();
    }

    public static function canDelete(Model $record): bool
    {
        /** @var PurchaseOrder $record */
        return (auth()->user()?->isAdmin() ?? false) && $record->isDraft();
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
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
                        // CỘT CHÍNH (2/3 chiều rộng): Thông tin phiếu & Chi tiết hàng nhập
                        Forms\Components\Group::make([
                            Forms\Components\Section::make('Thông tin phiếu nhập hàng')
                                ->icon('heroicon-o-document-text')
                                ->columns(3)
                                ->schema([
                                    Forms\Components\TextInput::make('code')
                                        ->label('Mã phiếu nhập')
                                        ->default(fn () => PurchaseOrder::generateNextCode())
                                        ->disabled()
                                        ->dehydrated()
                                        ->required(),

                                    Forms\Components\Select::make('supplier_id')
                                        ->label('Nhà cung cấp *')
                                        ->relationship('supplier', 'name', fn (Builder $query) => $query->where('is_active', true))
                                        ->searchable()
                                        ->preload()
                                        ->required()
                                        ->createOptionForm([
                                            Forms\Components\TextInput::make('name')
                                                ->label('Tên nhà cung cấp *')
                                                ->required(),
                                            Forms\Components\TextInput::make('phone')
                                                ->label('Số điện thoại')
                                                ->tel(),
                                            Forms\Components\TextInput::make('contact_name')
                                                ->label('Người liên hệ'),
                                        ])
                                        ->columnSpan(2),

                                    Forms\Components\Textarea::make('notes')
                                        ->label('Ghi chú lô hàng')
                                        ->placeholder('Lưu ý vận chuyển, số hóa đơn đỏ NCC, thỏa thuận đổi trả...')
                                        ->columnSpanFull()
                                        ->rows(2),
                                ]),

                            Forms\Components\Section::make('Chi tiết mặt hàng nhập kho (Tự động tăng tồn kho khi duyệt)')
                                ->icon('heroicon-o-cube')
                                ->schema([
                                    Forms\Components\Repeater::make('items')
                                        ->label('Danh sách sản phẩm nhập kho')
                                        ->relationship('items')
                                        ->schema([
                                            Forms\Components\Grid::make(12)
                                                ->schema([
                                                    Forms\Components\Select::make('product_id')
                                                        ->label('Sản phẩm trong kho *')
                                                        ->options(fn () => Product::pluck('name', 'id'))
                                                        ->searchable()
                                                        ->preload()
                                                        ->required()
                                                        ->live()
                                                        ->afterStateUpdated(function ($state, callable $set) {
                                                            if ($state) {
                                                                $p = Product::find($state);
                                                                if ($p) {
                                                                    $set('product_name', $p->name);
                                                                    $set('unit_name', $p->base_unit ?: 'Cái');
                                                                    $set('cost_price', $p->cost_price ?: 0);
                                                                    $set('quantity', 1);
                                                                    $set('subtotal', $p->cost_price ?: 0);
                                                                }
                                                            }
                                                        })
                                                        ->columnSpan(['default' => 12, 'md' => 8]),

                                                    Forms\Components\TextInput::make('unit_name')
                                                        ->label('Đơn vị tính (ĐVT) *')
                                                        ->default('Cái')
                                                        ->required()
                                                        ->columnSpan(['default' => 12, 'md' => 4]),

                                                    Forms\Components\TextInput::make('product_name')
                                                        ->hidden()
                                                        ->dehydrated(),

                                                    Forms\Components\TextInput::make('quantity')
                                                        ->label('Số lượng nhập *')
                                                        ->numeric()
                                                        ->default(1)
                                                        ->minValue(1)
                                                        ->required()
                                                        ->live()
                                                        ->columnSpan(['default' => 12, 'md' => 4])
                                                        ->afterStateUpdated(function (callable $get, callable $set) {
                                                            $qty = (float) ($get('quantity') ?? 0);
                                                            $cost = (float) ($get('cost_price') ?? 0);
                                                            $set('subtotal', $qty * $cost);
                                                        }),

                                                    Forms\Components\TextInput::make('cost_price')
                                                        ->label('Đơn giá nhập kho (₫) *')
                                                        ->numeric()
                                                        ->prefix('₫')
                                                        ->default(0)
                                                        ->required()
                                                        ->live()
                                                        ->columnSpan(['default' => 12, 'md' => 4])
                                                        ->afterStateUpdated(function (callable $get, callable $set) {
                                                            $qty = (float) ($get('quantity') ?? 0);
                                                            $cost = (float) ($get('cost_price') ?? 0);
                                                            $set('subtotal', $qty * $cost);
                                                        }),

                                                    Forms\Components\TextInput::make('subtotal')
                                                        ->label('Thành tiền (₫)')
                                                        ->numeric()
                                                        ->prefix('₫')
                                                        ->disabled()
                                                        ->dehydrated()
                                                        ->extraInputAttributes(['style' => 'font-weight: 700; color: #4f46e5;'])
                                                        ->columnSpan(['default' => 12, 'md' => 4]),
                                                ]),
                                        ])
                                        ->defaultItems(1)
                                        ->addActionLabel('+ Thêm sản phẩm vào phiếu nhập')
                                        ->itemLabel(fn (array $state): ?string => !empty($state['product_name']) ? "{$state['product_name']} (x" . ($state['quantity'] ?? 1) . ' ' . ($state['unit_name'] ?? '') . ')' : null)
                                        ->collapsible()
                                        ->live()
                                        ->afterStateUpdated(function (callable $get, callable $set) {
                                            $items = $get('items') ?? [];
                                            $total = 0;
                                            foreach ($items as $item) {
                                                $qty = (float) ($item['quantity'] ?? 0);
                                                $price = (float) ($item['cost_price'] ?? 0);
                                                $total += ($qty * $price);
                                            }
                                            $set('total_amount', $total);

                                            $discount = (float) ($get('discount_amount') ?? 0);
                                            $grand = max(0, $total - $discount);
                                            $set('grand_total', $grand);

                                            $paid = (float) ($get('paid_amount') ?? 0);
                                            $set('debt_amount', max(0, $grand - $paid));
                                        }),
                                ]),
                        ])->columnSpan(['lg' => 2]),

                        // CỘT PHỤ (1/3 chiều rộng): Trạng thái & Thanh toán NCC
                        Forms\Components\Group::make([
                            Forms\Components\Section::make('Trạng thái & Duyệt kho')
                                ->icon('heroicon-o-check-badge')
                                ->schema([
                                    Forms\Components\Select::make('status')
                                        ->label('Trạng thái phiếu nhập')
                                        ->options([
                                            'draft' => 'Phiếu tạm (Chưa tăng kho)',
                                            'received' => 'Đã nhập kho (Đã cộng kho)',
                                            'cancelled' => 'Đã hủy',
                                        ])
                                        ->default('draft')
                                        ->disabled(fn ($record) => !empty($record) && ! $record->isDraft())
                                        ->dehydrated()
                                        ->required(),

                                    Forms\Components\Placeholder::make('received_at_display')
                                        ->label('Thời gian nhập kho thực tế')
                                        ->content(fn ($record) => $record?->received_at ? $record->received_at->format('d/m/Y H:i') : 'Chưa nhập kho'),
                                ]),

                            Forms\Components\Section::make('Thanh toán & Công nợ NCC')
                                ->icon('heroicon-o-banknotes')
                                ->schema([
                                    Forms\Components\TextInput::make('total_amount')
                                        ->label('Tổng tiền hàng')
                                        ->numeric()
                                        ->prefix('₫')
                                        ->default(0)
                                        ->disabled()
                                        ->dehydrated(),

                                    Forms\Components\TextInput::make('discount_amount')
                                        ->label('Chiết khấu từ NCC')
                                        ->numeric()
                                        ->prefix('₫')
                                        ->default(0)
                                        ->live()
                                        ->afterStateUpdated(function (callable $get, callable $set) {
                                            $total = (float) ($get('total_amount') ?? 0);
                                            $discount = (float) ($get('discount_amount') ?? 0);
                                            $grand = max(0, $total - $discount);
                                            $set('grand_total', $grand);

                                            $paid = (float) ($get('paid_amount') ?? 0);
                                            $set('debt_amount', max(0, $grand - $paid));
                                        }),

                                    Forms\Components\TextInput::make('grand_total')
                                        ->label('Tổng thanh toán NCC')
                                        ->numeric()
                                        ->prefix('₫')
                                        ->disabled()
                                        ->dehydrated()
                                        ->extraInputAttributes(['style' => 'font-weight: 800; font-size: 1.15rem; color: #10b981;']),

                                    Forms\Components\TextInput::make('paid_amount')
                                        ->label('Đã trả nhà cung cấp')
                                        ->numeric()
                                        ->prefix('₫')
                                        ->default(0)
                                        ->live()
                                        ->afterStateUpdated(function (callable $get, callable $set) {
                                            $grand = (float) ($get('grand_total') ?? 0);
                                            $paid = (float) ($get('paid_amount') ?? 0);
                                            $debt = max(0, $grand - $paid);
                                            $set('debt_amount', $debt);

                                            if ($debt <= 0) {
                                                $set('payment_status', 'paid');
                                            } elseif ($paid > 0) {
                                                $set('payment_status', 'partially_paid');
                                            } else {
                                                $set('payment_status', 'unpaid');
                                            }
                                        }),

                                    Forms\Components\TextInput::make('debt_amount')
                                        ->label('Còn nợ nhà cung cấp')
                                        ->numeric()
                                        ->prefix('₫')
                                        ->default(0)
                                        ->disabled()
                                        ->dehydrated()
                                        ->extraInputAttributes(['style' => 'font-weight: 800; color: #e11d48;']),

                                    Forms\Components\Select::make('payment_status')
                                        ->label('Tình trạng thanh toán')
                                        ->options([
                                            'unpaid' => 'Chưa thanh toán',
                                            'partially_paid' => 'Đã trả một phần',
                                            'paid' => 'Đã thanh toán đủ',
                                        ])
                                        ->default('unpaid'),

                                    Forms\Components\Select::make('payment_method')
                                        ->label('Hình thức chi tiền')
                                        ->options([
                                            'cash' => 'Tiền mặt',
                                            'bank_transfer' => 'Chuyển khoản ngân hàng',
                                        ])
                                        ->default('cash'),
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
                    ->label('Ngày lập')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('supplier.name')
                    ->label('Nhà cung cấp')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->wrap(),

                Tables\Columns\TextColumn::make('items_count')
                    ->label('Mặt hàng')
                    ->counts('items')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('grand_total')
                    ->label('Tổng thanh toán')
                    ->money('VND')
                    ->weight('bold')
                    ->sortable(),

                Tables\Columns\TextColumn::make('paid_amount')
                    ->label('Đã trả NCC')
                    ->money('VND')
                    ->sortable(),

                Tables\Columns\TextColumn::make('debt_amount')
                    ->label('Còn nợ NCC')
                    ->money('VND')
                    ->weight('bold')
                    ->color(fn (PurchaseOrder $record) => $record->debt_amount > 0 ? 'danger' : 'success')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Trạng thái')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'draft' => 'Phiếu tạm',
                        'received' => 'Đã nhập kho',
                        'cancelled' => 'Đã hủy',
                        default => $state,
                    })
                    ->icon(fn ($state) => match ($state) {
                        'draft' => 'heroicon-m-clock',
                        'received' => 'heroicon-m-check-circle',
                        'cancelled' => 'heroicon-m-x-circle',
                        default => null,
                    })
                    ->color(fn ($state) => match ($state) {
                        'draft' => 'warning',
                        'received' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('payment_status')
                    ->label('Thanh toán')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'unpaid' => 'Chưa trả',
                        'partially_paid' => 'Trả một phần',
                        'paid' => 'Đã trả đủ',
                        default => $state,
                    })
                    ->color(fn ($state) => match ($state) {
                        'unpaid' => 'danger',
                        'partially_paid' => 'warning',
                        'paid' => 'success',
                        default => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Trạng thái')
                    ->options([
                        'draft' => 'Phiếu tạm (chờ duyệt)',
                        'received' => 'Đã nhập kho',
                        'cancelled' => 'Đã hủy',
                    ]),

                Tables\Filters\SelectFilter::make('supplier_id')
                    ->label('Nhà cung cấp')
                    ->relationship('supplier', 'name'),

                Tables\Filters\Filter::make('has_debt')
                    ->label('Còn nợ tiền NCC')
                    ->query(fn (Builder $query) => $query->where('debt_amount', '>', 0)),
            ])
            ->actions([
                Tables\Actions\Action::make('receive')
                    ->label('Nhập kho')
                    ->icon('heroicon-m-arrow-down-tray')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading(fn (PurchaseOrder $record) => "Duyệt nhập kho phiếu {$record->code}")
                    ->modalDescription('Bạn có chắc chắn muốn xác nhận nhập kho lô hàng này? Tồn kho tất cả sản phẩm trong phiếu sẽ được cộng thêm tự động, giá vốn sản phẩm sẽ được cập nhật và công nợ NCC sẽ được ghi nhận.')
                    ->modalSubmitActionLabel('Xác nhận nhập kho')
                    ->visible(fn (PurchaseOrder $record) => $record->isDraft())
                    ->action(function (PurchaseOrder $record) {
                        try {
                            app(InventoryService::class)->receivePurchaseOrder($record);
                            Notification::make()
                                ->title('Nhập kho thành công!')
                                ->body("Phiếu {$record->code} đã được nhập kho. Tồn kho và giá vốn sản phẩm đã được cập nhật.")
                                ->success()
                                ->send();
                        } catch (\Throwable $e) {
                            Notification::make()
                                ->title('Lỗi khi nhập kho')
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
                    ->modalHeading(fn (PurchaseOrder $record) => "Hủy phiếu nhập kho {$record->code}")
                    ->modalDescription(fn (PurchaseOrder $record) => $record->isReceived() ? 'CẢNH BÁO: Phiếu này đã nhập kho. Khi hủy, hệ thống sẽ tự động TRỪ LẠI số lượng tồn kho đã cộng và giảm trừ công nợ NCC đã ghi nhận.' : 'Bạn có chắc chắn muốn hủy phiếu tạm này?')
                    ->form([
                        Forms\Components\Textarea::make('reason')
                            ->label('Lý do hủy phiếu *')
                            ->required(),
                    ])
                    ->visible(fn (PurchaseOrder $record) => ! $record->isCancelled())
                    ->action(function (PurchaseOrder $record, array $data) {
                        try {
                            app(InventoryService::class)->cancelPurchaseOrder($record, $data['reason']);
                            Notification::make()
                                ->title('Đã hủy phiếu nhập kho')
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
                    ->visible(fn (PurchaseOrder $record) => $record->isDraft()),

                Tables\Actions\DeleteAction::make()
                    ->label('Xóa')
                    ->visible(fn (PurchaseOrder $record) => $record->isDraft()),
            ])
            ->emptyStateHeading('Chưa có phiếu nhập kho nào')
            ->emptyStateDescription('Tạo phiếu nhập hàng đầu tiên từ nhà cung cấp để tăng tồn kho và quản lý giá vốn sản phẩm.')
            ->emptyStateIcon('heroicon-o-arrow-down-on-square-stack');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['supplier', 'items.product']);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPurchaseOrders::route('/'),
            'create' => Pages\CreatePurchaseOrder::route('/create'),
            'edit' => Pages\EditPurchaseOrder::route('/{record}/edit'),
        ];
    }
}
