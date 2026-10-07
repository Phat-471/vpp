<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationGroup = 'Bán Hàng & Thu Ngân';

    protected static ?string $navigationLabel = 'Đơn bán lẻ (POS)';

    protected static ?string $modelLabel = 'Đơn bán lẻ';

    protected static ?string $pluralModelLabel = 'Danh sách đơn bán lẻ';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Khách hàng & Kênh bán')
                            ->schema([
                                Forms\Components\Grid::make(3)
                                    ->schema([
                                        Forms\Components\Select::make('customer_id')
                                            ->label('Khách hàng')
                                            ->relationship('customer', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->live()
                                            ->afterStateUpdated(function ($state, callable $set) {
                                                if ($state) {
                                                    $c = Customer::find($state);
                                                    if ($c) {
                                                        $set('customer_name', $c->name);
                                                        $set('customer_phone', $c->phone);
                                                        $set('customer_address', $c->address);
                                                    }
                                                }
                                            }),

                                        Forms\Components\TextInput::make('customer_name')
                                            ->label('Họ tên khách')
                                            ->placeholder('Khách lẻ vãng lai'),

                                        Forms\Components\TextInput::make('customer_phone')
                                            ->label('Số điện thoại')
                                            ->tel(),
                                    ]),

                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\Select::make('channel')
                                            ->label('Kênh bán')
                                            ->options([
                                                'pos' => 'Bán trực tiếp tại quầy',
                                                'online' => 'Đặt qua Website Storefront',
                                            ])
                                            ->default('pos')
                                            ->required(),

                                        Forms\Components\Select::make('status')
                                            ->label('Trạng thái đơn')
                                            ->options([
                                                'completed' => 'Đã hoàn tất',
                                                'pending' => 'Chờ xử lý',
                                                'cancelled' => 'Đã hủy',
                                            ])
                                            ->default('completed')
                                            ->required(),
                                    ]),
                            ]),

                        Forms\Components\Section::make('Chi tiết mặt hàng')
                            ->schema([
                                Forms\Components\Repeater::make('orderItems')
                                    ->label('Danh sách mặt hàng')
                                    ->relationship('orderItems')
                                    ->schema([
                                        Forms\Components\Select::make('product_id')
                                            ->label('Sản phẩm')
                                            ->options(fn () => Product::where('is_active', true)->pluck('name', 'id'))
                                            ->searchable()
                                            ->required()
                                            ->live()
                                            ->afterStateUpdated(function ($state, callable $set) {
                                                if ($state) {
                                                    $p = Product::find($state);
                                                    if ($p) {
                                                        $set('product_name', $p->name);
                                                        $set('unit_name', $p->base_unit);
                                                        $set('unit_price', $p->retail_price);
                                                        $set('cost_price', $p->cost_price);
                                                        $set('conversion_rate', 1);
                                                    }
                                                }
                                            })
                                            ->columnSpan(3),

                                        Forms\Components\TextInput::make('product_name')
                                            ->label('Tên hiển thị trên hóa đơn')
                                            ->required()
                                            ->columnSpan(2),

                                        Forms\Components\TextInput::make('quantity')
                                            ->label('SL')
                                            ->numeric()
                                            ->default(1)
                                            ->required()
                                            ->live(),

                                        Forms\Components\TextInput::make('unit_name')
                                            ->label('ĐVT')
                                            ->default('Cái')
                                            ->required(),

                                        Forms\Components\TextInput::make('unit_price')
                                            ->label('Đơn giá')
                                            ->numeric()
                                            ->prefix('₫')
                                            ->required()
                                            ->live(),
                                    ])
                                    ->columns(8)
                                    ->defaultItems(1)
                                    ->addActionLabel('Thêm mặt hàng'),
                            ]),
                    ])
                    ->columnSpan(['lg' => 2]),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Tổng tiền & Thanh toán')
                            ->schema([
                                Forms\Components\TextInput::make('subtotal')
                                    ->label('Tiền hàng')
                                    ->numeric()
                                    ->prefix('₫')
                                    ->disabled()
                                    ->dehydrated(),

                                Forms\Components\TextInput::make('discount_amount')
                                    ->label('Giảm giá / Chiết khấu')
                                    ->numeric()
                                    ->prefix('₫')
                                    ->default(0)
                                    ->live(),

                                Forms\Components\TextInput::make('tax_rate')
                                    ->label('Thuế suất VAT (%)')
                                    ->numeric()
                                    ->suffix('%')
                                    ->default(0)
                                    ->helperText('Giai đoạn đầu 0%, nâng cấp doanh nghiệp chỉ cần bật cấu hình'),

                                Forms\Components\TextInput::make('grand_total')
                                    ->label('Tổng thanh toán')
                                    ->numeric()
                                    ->prefix('₫')
                                    ->disabled()
                                    ->dehydrated(),

                                Forms\Components\TextInput::make('paid_amount')
                                    ->label('Tiền khách đưa')
                                    ->numeric()
                                    ->prefix('₫')
                                    ->default(0)
                                    ->live(),

                                Forms\Components\Select::make('payment_status')
                                    ->label('Trạng thái thanh toán')
                                    ->options([
                                        'paid' => 'Đã thanh toán đủ',
                                        'partially_paid' => 'Một phần',
                                        'unpaid' => 'Chưa thanh toán',
                                    ])
                                    ->default('paid'),

                                Forms\Components\Select::make('payment_method')
                                    ->label('Phương thức')
                                    ->options([
                                        'cash' => 'Tiền mặt',
                                        'vietqr' => 'Chuyển khoản VietQR',
                                        'transfer' => 'Chuyển khoản thường',
                                    ])
                                    ->default('cash'),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_code')
                    ->label('Mã hóa đơn')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable()
                    ->description(fn (Order $r) => $r->created_at->format('d/m/Y H:i')),

                Tables\Columns\TextColumn::make('customer_name')
                    ->label('Khách hàng')
                    ->searchable()
                    ->default('Khách vãng lai'),

                Tables\Columns\TextColumn::make('channel')
                    ->label('Kênh')
                    ->badge()
                    ->colors([
                        'primary' => 'pos',
                        'info' => 'online',
                    ])
                    ->formatStateUsing(fn ($state) => $state === 'pos' ? 'Tại quầy' : 'Online'),

                Tables\Columns\TextColumn::make('grand_total')
                    ->label('Tổng tiền')
                    ->money('VND')
                    ->sortable(),

                Tables\Columns\TextColumn::make('payment_status')
                    ->label('Thanh toán')
                    ->badge()
                    ->colors([
                        'success' => 'paid',
                        'warning' => 'partially_paid',
                        'danger' => 'unpaid',
                    ])
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'paid' => 'Đã trả',
                        'partially_paid' => 'Một phần',
                        default => 'Chưa trả',
                    }),

                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Hình thức')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'vietqr' => 'VietQR',
                        'transfer' => 'Chuyển khoản',
                        default => 'Tiền mặt',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('channel')
                    ->label('Kênh bán')
                    ->options([
                        'pos' => 'Tại quầy POS',
                        'online' => 'Online',
                    ]),
                Tables\Filters\SelectFilter::make('payment_status')
                    ->label('Thanh toán')
                    ->options([
                        'paid' => 'Đã thanh toán',
                        'unpaid' => 'Chưa thanh toán',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('print')
                    ->label('In bill')
                    ->icon('heroicon-o-printer')
                    ->color('info')
                    ->url(fn (Order $record) => url("/print/order/{$record->id}"))
                    ->openUrlInNewTab(),

                Tables\Actions\Action::make('vietqr')
                    ->label('VietQR')
                    ->icon('heroicon-o-qr-code')
                    ->color('success')
                    ->modalHeading(fn (Order $record) => "Mã VietQR cho đơn {$record->order_code}")
                    ->modalSubmitAction(false)
                    ->modalContent(fn (Order $record) => view('filament.modals.vietqr-order', [
                        'order' => $record,
                        'amount' => $record->grand_total - $record->paid_amount ?: $record->grand_total,
                        'qrUrl' => "https://img.vietqr.io/image/970407-190333888999-compact2.png?amount=" . ($record->grand_total - $record->paid_amount ?: $record->grand_total) . "&addInfo=" . $record->order_code . "&accountName=NGUYEN%20VAN%20A",
                    ])),

                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
