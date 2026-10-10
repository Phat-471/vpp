<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Helpers\AppHelper;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Services\BusinessTaxLookup;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationGroup = 'Bán hàng';

    protected static ?string $navigationLabel = 'Đơn hàng';

    protected static ?string $modelLabel = 'Đơn hàng';

    protected static ?string $pluralModelLabel = 'Danh sách đơn hàng';

    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::where('payment_status', 'unpaid')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(3)
                    ->schema([
                        // CỘT CHÍNH (2/3 chiều rộng): Khách hàng, Sản phẩm & VAT
                        Forms\Components\Group::make([
                            Forms\Components\Section::make('Thông tin khách hàng & Bán hàng')
                                ->icon('heroicon-o-user')
                                ->schema([
                                    Forms\Components\Grid::make(3)
                                        ->schema([
                                            Forms\Components\Select::make('customer_id')
                                                ->label('Chọn khách hàng')
                                                ->relationship('customer', 'name')
                                                ->searchable()
                                                ->preload()
                                                ->createOptionForm([
                                                    Forms\Components\TextInput::make('name')->label('Tên khách *')->required(),
                                                    Forms\Components\TextInput::make('phone')->label('Số điện thoại *')->tel()->required(),
                                                    Forms\Components\TextInput::make('address')->label('Địa chỉ'),
                                                ])
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
                                                ->label('Họ tên khách *')
                                                ->placeholder('Khách lẻ tại quầy')
                                                ->default('Khách lẻ tại quầy')
                                                ->required(),

                                            Forms\Components\TextInput::make('customer_phone')
                                                ->label('Số điện thoại')
                                                ->placeholder('0912345678')
                                                ->tel(),
                                        ]),

                                    Forms\Components\Actions::make([
                                        Forms\Components\Actions\Action::make('set_retail')
                                            ->label('👤 Khách lẻ vãng lai tại quầy')
                                            ->icon('heroicon-m-user')
                                            ->size('xs')
                                            ->color('gray')
                                            ->action(function (callable $set) {
                                                $set('customer_id', null);
                                                $set('customer_name', 'Khách lẻ tại quầy');
                                                $set('customer_phone', '');
                                                $set('customer_address', '');
                                            }),
                                    ])
                                        ->columnSpanFull(),

                                    Forms\Components\Grid::make(3)
                                        ->schema([
                                            Forms\Components\Select::make('channel')
                                                ->label('Kênh bán hàng')
                                                ->options([
                                                    'pos' => '⚡ Bán trực tiếp tại quầy POS',
                                                    'online' => '🌐 Đặt qua Website Storefront',
                                                ])
                                                ->default('pos')
                                                ->required(),

                                            Forms\Components\Select::make('status')
                                                ->label('Trạng thái đơn')
                                                ->options([
                                                    'completed' => '✅ Đã hoàn tất & xuất kho',
                                                    'shipping' => '🚚 Đang giao hàng',
                                                    'pending' => '⏳ Chờ xử lý / Đóng gói',
                                                    'cancelled' => '❌ Đã hủy đơn',
                                                ])
                                                ->default('completed')
                                                ->required(),

                                            Forms\Components\TextInput::make('customer_address')
                                                ->label('Địa chỉ giao hàng')
                                                ->placeholder('Giao tận nơi hoặc nhận tại cửa hàng'),
                                        ]),
                                ]),

                            Forms\Components\Section::make('Danh sách mặt hàng xuất kho')
                                ->icon('heroicon-o-shopping-bag')
                                ->schema([
                                    Forms\Components\Repeater::make('orderItems')
                                        ->label('Mặt hàng trong đơn')
                                        ->relationship('orderItems')
                                        ->schema([
                                            Forms\Components\Select::make('product_id')
                                                ->label('Chọn sản phẩm từ kho')
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
                                                ->columnSpan(4),

                                            Forms\Components\TextInput::make('product_name')
                                                ->label('Tên in hóa đơn')
                                                ->required()
                                                ->columnSpan(3),

                                            Forms\Components\TextInput::make('quantity')
                                                ->label('Số lượng')
                                                ->numeric()
                                                ->default(1)
                                                ->required()
                                                ->live()
                                                ->columnSpan(1),

                                            Forms\Components\TextInput::make('unit_name')
                                                ->label('ĐVT')
                                                ->default('Cái')
                                                ->required()
                                                ->columnSpan(1),

                                            Forms\Components\TextInput::make('unit_price')
                                                ->label('Đơn giá (₫)')
                                                ->numeric()
                                                ->prefix('₫')
                                                ->required()
                                                ->live()
                                                ->columnSpan(2),
                                        ])
                                        ->columns(11)
                                        ->defaultItems(1)
                                        ->addActionLabel('+ Thêm sản phẩm vào đơn')
                                        ->live()
                                        ->afterStateUpdated(function (callable $get, callable $set) {
                                            $items = $get('orderItems') ?? [];
                                            $subtotal = 0;
                                            foreach ($items as $item) {
                                                $qty = (float) ($item['quantity'] ?? 0);
                                                $price = (float) ($item['unit_price'] ?? 0);
                                                $subtotal += ($qty * $price);
                                            }
                                            $set('subtotal', $subtotal);

                                            $discount = (float) ($get('discount_amount') ?? 0);
                                            $taxRate = (float) ($get('tax_rate') ?? 0);
                                            $shipping = (float) ($get('shipping_fee') ?? 0);
                                            $tax = ($subtotal - $discount) * ($taxRate / 100);
                                            $set('tax_amount', max(0, $tax));
                                            $set('grand_total', max(0, $subtotal - $discount + $tax + $shipping));
                                        }),
                                ]),

                            Forms\Components\Section::make('Hóa đơn giá trị gia tăng (VAT Điện tử)')
                                ->icon('heroicon-o-document-currency-dollar')
                                ->collapsible()
                                ->schema([
                                    Forms\Components\Toggle::make('is_vat_invoice')
                                        ->label('Yêu cầu xuất hóa đơn GTGT (VAT)')
                                        ->helperText('Bật tùy chọn này nếu khách hàng là cơ quan, doanh nghiệp cần xuất VAT.')
                                        ->live(),

                                    Forms\Components\Grid::make(2)
                                        ->visible(fn (Forms\Get $get) => (bool) $get('is_vat_invoice'))
                                        ->schema([
                                            Forms\Components\TextInput::make('company_name')
                                                ->label('Tên đơn vị / Doanh nghiệp *')
                                                ->placeholder('CÔNG TY TNHH / TRƯỜNG HỌC...'),

                                            Forms\Components\TextInput::make('company_tax_id')
                                                ->label('Mã số thuế (MST) *')
                                                ->placeholder('VD: 0101234567')
                                                ->suffixAction(
                                                    Forms\Components\Actions\Action::make('lookup_tax')
                                                        ->label('Tra cứu MST')
                                                        ->icon('heroicon-m-magnifying-glass')
                                                        ->color('primary')
                                                        ->action(function ($state, callable $set) {
                                                            if (! $state) {
                                                                Notification::make()->warning()->title('Vui lòng nhập MST trước khi tra cứu')->send();
                                                                return;
                                                            }
                                                            try {
                                                                $lookup = app(BusinessTaxLookup::class);
                                                                $res = $lookup->find($state);
                                                                if ($res) {
                                                                    $set('company_name', $res['name'] ?? '');
                                                                    $set('company_address', $res['address'] ?? '');
                                                                    Notification::make()->success()->title('Đã tìm thấy thông tin DN')->body($res['name'] ?? '')->send();
                                                                } else {
                                                                    Notification::make()->warning()->title('Không tìm thấy doanh nghiệp')->body('Vui lòng nhập thủ công.')->send();
                                                                }
                                                            } catch (\Throwable $e) {
                                                                Notification::make()->danger()->title('Lỗi tra cứu MST')->body($e->getMessage())->send();
                                                            }
                                                        })
                                                ),

                                            Forms\Components\TextInput::make('company_address')
                                                ->label('Địa chỉ đăng ký kinh doanh')
                                                ->placeholder('Địa chỉ trên giấy phép ĐKKD'),

                                            Forms\Components\TextInput::make('invoice_email')
                                                ->label('Email nhận hóa đơn điện tử')
                                                ->email()
                                                ->placeholder('ketoan@doanhnghiep.vn'),
                                        ]),
                                ]),
                        ])
                        ->columnSpan(['lg' => 2]),

                        // CỘT PHỤ (1/3 chiều rộng): Thu ngân & Thanh toán
                        Forms\Components\Group::make([
                            Forms\Components\Section::make('Thu ngân & Thanh toán')
                                ->icon('heroicon-o-banknotes')
                                ->schema([
                                    Forms\Components\TextInput::make('subtotal')
                                        ->label('Tổng tiền hàng')
                                        ->numeric()
                                        ->prefix('₫')
                                        ->disabled()
                                        ->dehydrated(),

                                    Forms\Components\TextInput::make('discount_amount')
                                        ->label('Giảm giá / Chiết khấu')
                                        ->numeric()
                                        ->prefix('₫')
                                        ->default(0)
                                        ->live()
                                        ->afterStateUpdated(function (callable $get, callable $set) {
                                            $subtotal = (float) ($get('subtotal') ?? 0);
                                            $discount = (float) ($get('discount_amount') ?? 0);
                                            $taxRate = (float) ($get('tax_rate') ?? 0);
                                            $shipping = (float) ($get('shipping_fee') ?? 0);
                                            $tax = ($subtotal - $discount) * ($taxRate / 100);
                                            $set('tax_amount', max(0, $tax));
                                            $set('grand_total', max(0, $subtotal - $discount + $tax + $shipping));
                                        }),

                                    Forms\Components\Grid::make(2)
                                        ->schema([
                                            Forms\Components\TextInput::make('tax_rate')
                                                ->label('Thuế VAT')
                                                ->numeric()
                                                ->suffix('%')
                                                ->default(0)
                                                ->live()
                                                ->afterStateUpdated(function (callable $get, callable $set) {
                                                    $subtotal = (float) ($get('subtotal') ?? 0);
                                                    $discount = (float) ($get('discount_amount') ?? 0);
                                                    $taxRate = (float) ($get('tax_rate') ?? 0);
                                                    $shipping = (float) ($get('shipping_fee') ?? 0);
                                                    $tax = ($subtotal - $discount) * ($taxRate / 100);
                                                    $set('tax_amount', max(0, $tax));
                                                    $set('grand_total', max(0, $subtotal - $discount + $tax + $shipping));
                                                }),

                                            Forms\Components\TextInput::make('shipping_fee')
                                                ->label('Phí vận chuyển')
                                                ->numeric()
                                                ->prefix('₫')
                                                ->default(0)
                                                ->live()
                                                ->afterStateUpdated(function (callable $get, callable $set) {
                                                    $subtotal = (float) ($get('subtotal') ?? 0);
                                                    $discount = (float) ($get('discount_amount') ?? 0);
                                                    $tax = (float) ($get('tax_amount') ?? 0);
                                                    $shipping = (float) ($get('shipping_fee') ?? 0);
                                                    $set('grand_total', max(0, $subtotal - $discount + $tax + $shipping));
                                                }),
                                        ]),

                                    Forms\Components\TextInput::make('grand_total')
                                        ->label('Tổng thanh toán')
                                        ->numeric()
                                        ->prefix('₫')
                                        ->disabled()
                                        ->dehydrated()
                                        ->extraInputAttributes(['style' => 'font-weight: 800; font-size: 1.15rem; color: #10b981;']),

                                    Forms\Components\TextInput::make('paid_amount')
                                        ->label('Khách đã thanh toán')
                                        ->numeric()
                                        ->prefix('₫')
                                        ->default(0)
                                        ->live(),

                                    Forms\Components\Actions::make([
                                        Forms\Components\Actions\Action::make('pay_full')
                                            ->label('Trả đủ')
                                            ->size('xs')
                                            ->color('success')
                                            ->action(function (callable $get, callable $set) {
                                                $gt = (float)($get('grand_total') ?? 0);
                                                $set('paid_amount', $gt);
                                                $set('payment_status', 'paid');
                                            }),
                                        Forms\Components\Actions\Action::make('add_100k')
                                            ->label('+100k')
                                            ->size('xs')
                                            ->color('gray')
                                            ->action(function (callable $get, callable $set) {
                                                $cur = (float)($get('paid_amount') ?? 0);
                                                $set('paid_amount', $cur + 100000);
                                            }),
                                        Forms\Components\Actions\Action::make('add_200k')
                                            ->label('+200k')
                                            ->size('xs')
                                            ->color('gray')
                                            ->action(function (callable $get, callable $set) {
                                                $cur = (float)($get('paid_amount') ?? 0);
                                                $set('paid_amount', $cur + 200000);
                                            }),
                                        Forms\Components\Actions\Action::make('add_500k')
                                            ->label('+500k')
                                            ->size('xs')
                                            ->color('gray')
                                            ->action(function (callable $get, callable $set) {
                                                $cur = (float)($get('paid_amount') ?? 0);
                                                $set('paid_amount', $cur + 500000);
                                            }),
                                    ])
                                        ->columnSpanFull(),

                                    Forms\Components\Placeholder::make('remaining_due')
                                        ->label('Còn lại phải thu / Tiền thừa')
                                        ->content(function (callable $get) {
                                            $grand = (float) ($get('grand_total') ?? 0);
                                            $paid = (float) ($get('paid_amount') ?? 0);
                                            if ($paid >= $grand && $grand > 0) {
                                                $excess = $paid - $grand;
                                                return $excess > 0
                                                    ? '💵 Tiền thừa trả khách: ' . number_format($excess, 0, ',', '.') . ' ₫'
                                                    : '✅ Đã thanh toán đủ (0 ₫)';
                                            }
                                            $rem = max(0, $grand - $paid);
                                            return '🔴 Còn nợ cần thu: ' . number_format($rem, 0, ',', '.') . ' ₫';
                                        })
                                        ->extraAttributes(fn (callable $get) => [
                                            'class' => ((float)($get('paid_amount') ?? 0) >= (float)($get('grand_total') ?? 0) && (float)($get('grand_total') ?? 0) > 0)
                                                ? 'text-emerald-700 font-bold text-sm'
                                                : 'text-rose-600 font-bold text-sm'
                                        ]),

                                    Forms\Components\Select::make('payment_status')
                                        ->label('Trạng thái thanh toán')
                                        ->options([
                                            'paid' => '✅ Đã thanh toán đủ',
                                            'partially_paid' => '⏳ Trả một phần / Đặt cọc',
                                            'unpaid' => '❌ Chưa thanh toán / Ghi nợ',
                                        ])
                                        ->default('paid')
                                        ->required(),

                                    Forms\Components\Select::make('payment_method')
                                        ->label('Hình thức thanh toán')
                                        ->options([
                                            'cash' => '💵 Tiền mặt',
                                            'vietqr' => '⚡ Quét mã VietQR',
                                            'transfer' => '🏦 Chuyển khoản ngân hàng',
                                        ])
                                        ->default('cash')
                                        ->required(),

                                    Forms\Components\Textarea::make('notes')
                                        ->label('Ghi chú đơn hàng')
                                        ->placeholder('Giao giờ hành chính, đóng gói cẩn thận...')
                                        ->rows(2),
                                ]),
                        ])
                        ->columnSpan(['lg' => 1]),
                    ]),
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
                    ->fontFamily('mono')
                    ->description(fn (Order $r) => $r->created_at->format('d/m/Y H:i')),

                Tables\Columns\TextColumn::make('customer_name')
                    ->label('Khách hàng')
                    ->searchable()
                    ->weight('bold')
                    ->default('Khách vãng lai')
                    ->description(function (Order $r) {
                        $desc = $r->customer_phone ?: '';
                        if ($r->is_suspicious) {
                            $spamNote = '⚠️ Cảnh báo spam: ' . ($r->suspicious_reason ?? 'Nghi vấn đặt liên tiếp');
                            return $desc ? "{$spamNote} • {$desc}" : $spamNote;
                        }
                        return $desc ?: null;
                    }),

                Tables\Columns\IconColumn::make('is_suspicious')
                    ->label('An ninh')
                    ->boolean()
                    ->trueIcon('heroicon-s-exclamation-triangle')
                    ->falseIcon('heroicon-o-shield-check')
                    ->trueColor('danger')
                    ->falseColor('gray')
                    ->tooltip(fn (Order $r) => $r->is_suspicious ? ('⚠️ CẢNH BÁO SPAM: ' . ($r->suspicious_reason ?? 'Nghi vấn đặt liên tiếp')) : 'An toàn')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('channel')
                    ->label('Kênh bán')
                    ->badge()
                    ->colors([
                        'primary' => 'pos',
                        'info' => 'online',
                    ])
                    ->formatStateUsing(fn ($state) => $state === 'pos' ? 'Tại quầy' : 'Online'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Trạng thái')
                    ->badge()
                    ->colors([
                        'success' => 'completed',
                        'info' => 'shipping',
                        'warning' => 'pending',
                        'danger' => 'cancelled',
                    ])
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'completed' => 'Đã hoàn tất',
                        'shipping' => 'Đang giao',
                        'pending' => 'Chờ xử lý',
                        'cancelled' => 'Đã hủy',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('grand_total')
                    ->label('Tổng tiền')
                    ->money('VND')
                    ->sortable()
                    ->weight('bold'),

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

                Tables\Columns\IconColumn::make('is_vat_invoice')
                    ->label('VAT')
                    ->boolean()
                    ->trueIcon('heroicon-o-document-check')
                    ->falseIcon('heroicon-o-minus')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->tooltip(fn (Order $r) => $r->is_vat_invoice ? "Xuất VAT: {$r->company_name}" : 'Không xuất VAT'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_suspicious')
                    ->label('Cảnh báo spam')
                    ->placeholder('Tất cả đơn hàng')
                    ->trueLabel('⚠️ Chỉ đơn nghi vấn spam')
                    ->falseLabel('✅ Chỉ đơn an toàn'),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Trạng thái đơn')
                    ->options([
                        'completed' => 'Đã hoàn tất',
                        'shipping' => 'Đang giao',
                        'pending' => 'Chờ xử lý',
                        'cancelled' => 'Đã hủy',
                    ]),

                Tables\Filters\SelectFilter::make('channel')
                    ->label('Kênh bán')
                    ->options([
                        'pos' => 'Tại quầy POS',
                        'online' => 'Online Storefront',
                    ]),

                Tables\Filters\SelectFilter::make('payment_status')
                    ->label('Thanh toán')
                    ->options([
                        'paid' => 'Đã thanh toán',
                        'partially_paid' => 'Trả một phần',
                        'unpaid' => 'Chưa thanh toán',
                    ]),

                Tables\Filters\Filter::make('is_vat_invoice')
                    ->label('Đơn yêu cầu xuất VAT')
                    ->query(fn (Builder $query) => $query->where('is_vat_invoice', true)),
            ])
            ->actions([
                Tables\Actions\Action::make('quick_status')
                    ->label('Đổi trạng thái')
                    ->icon('heroicon-m-arrow-path')
                    ->color('warning')
                    ->form([
                        Forms\Components\Select::make('status')
                            ->label('Trạng thái đơn *')
                            ->options([
                                'completed' => '✅ Đã hoàn tất & xuất kho',
                                'shipping' => '🚚 Đang giao hàng',
                                'pending' => '⏳ Chờ xử lý / Đóng gói',
                                'cancelled' => '❌ Đã hủy đơn',
                            ])
                            ->required(),
                        Forms\Components\Select::make('payment_status')
                            ->label('Trạng thái thanh toán *')
                            ->options([
                                'paid' => '✅ Đã thanh toán đủ',
                                'partially_paid' => '⏳ Trả một phần / Đặt cọc',
                                'unpaid' => '❌ Chưa thanh toán / Ghi nợ',
                            ])
                            ->required(),
                        Forms\Components\Select::make('payment_method')
                            ->label('Hình thức thanh toán')
                            ->options([
                                'cash' => '💵 Tiền mặt',
                                'vietqr' => '⚡ Quét mã VietQR',
                                'transfer' => '🏦 Chuyển khoản ngân hàng',
                            ]),
                        Forms\Components\Textarea::make('notes')
                            ->label('Ghi chú')
                            ->rows(2),
                    ])
                    ->fillForm(fn (Order $record): array => [
                        'status' => $record->status,
                        'payment_status' => $record->payment_status,
                        'payment_method' => $record->payment_method,
                        'notes' => $record->notes,
                    ])
                    ->action(function (Order $record, array $data): void {
                        DB::transaction(function () use ($record, $data) {
                            $record->update([
                                'status' => $data['status'],
                                'payment_status' => $data['payment_status'],
                                'payment_method' => $data['payment_method'] ?? $record->payment_method,
                                'notes' => $data['notes'] ?? $record->notes,
                                'paid_amount' => $data['payment_status'] === 'paid' ? $record->grand_total : $record->paid_amount,
                            ]);
                        });

                        Notification::make()
                            ->title('Cập nhật trạng thái thành công')
                            ->body("Đơn hàng {$record->order_code} đã được cập nhật.")
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('mark_safe')
                    ->label('Bỏ cờ spam')
                    ->icon('heroicon-o-shield-check')
                    ->color('success')
                    ->visible(fn (Order $record) => (bool) $record->is_suspicious)
                    ->requiresConfirmation()
                    ->modalHeading('Xác minh khách hàng thật')
                    ->modalDescription('Bạn đã liên hệ xác nhận người mua này là thật và muốn bỏ đánh dấu nghi vấn spam?')
                    ->action(function (Order $record) {
                        $record->update([
                            'is_suspicious' => false,
                            'suspicious_reason' => 'Đã xác minh khách thật lúc ' . now()->format('H:i d/m/Y'),
                        ]);

                        Notification::make()
                            ->title('Đã bỏ cờ spam thành công')
                            ->body("Đơn hàng {$record->order_code} đã được đánh dấu an toàn.")
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('print')
                    ->label('In bill A5')
                    ->icon('heroicon-o-printer')
                    ->color('info')
                    ->url(fn (Order $record) => url("/print/order/{$record->id}"))
                    ->openUrlInNewTab(),

                Tables\Actions\Action::make('print_k80')
                    ->label('In bill K80')
                    ->icon('heroicon-o-receipt-percent')
                    ->color('warning')
                    ->url(fn (Order $record) => url("/print/order/{$record->id}?format=k80"))
                    ->openUrlInNewTab(),

                Tables\Actions\Action::make('print_vat')
                    ->label('In VAT')
                    ->icon('heroicon-o-document-currency-dollar')
                    ->color('primary')
                    ->visible(fn (Order $record) => (bool) $record->is_vat_invoice)
                    ->url(fn (Order $record) => url("/print/vat-invoice/{$record->id}"))
                    ->openUrlInNewTab(),

                Tables\Actions\Action::make('vietqr')
                    ->label('VietQR')
                    ->icon('heroicon-o-qr-code')
                    ->color('success')
                    ->modalHeading(fn (Order $record) => "Mã VietQR cho đơn {$record->order_code}")
                    ->modalSubmitAction(false)
                    ->modalContent(fn (Order $record) => view('filament.modals.vietqr-order', [
                        'order' => $record,
                        'amount' => $record->grand_total - $record->paid_amount > 0 ? ($record->grand_total - $record->paid_amount) : $record->grand_total,
                        'qrUrl' => AppHelper::generateVietQrUrl(
                            (float) ($record->grand_total - $record->paid_amount > 0 ? ($record->grand_total - $record->paid_amount) : $record->grand_total),
                            $record->order_code
                        ),
                    ])),

                Tables\Actions\EditAction::make()
                    ->label('Sửa')
                    ->icon('heroicon-m-pencil-square'),

                Tables\Actions\DeleteAction::make()
                    ->label('Xóa')
                    ->icon('heroicon-m-trash')
                    ->modalHeading('Xác nhận xóa đơn hàng')
                    ->modalDescription(fn (Order $record) => "Bạn có chắc chắn muốn xóa đơn hàng '{$record->order_code}' trị giá " . number_format($record->grand_total, 0, ',', '.') . "đ? Hành động này không thể hoàn tác.")
                    ->modalSubmitActionLabel('Xác nhận xóa')
                    ->modalCancelActionLabel('Hủy bỏ'),
            ])
            ->emptyStateHeading('Chưa có đơn hàng nào')
            ->emptyStateDescription('Bấm Tạo mới đơn hàng hoặc mở Quầy POS để ghi nhận giao dịch bán văn phòng phẩm đầu tiên.')
            ->emptyStateIcon('heroicon-o-shopping-bag');
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
