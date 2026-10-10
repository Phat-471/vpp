<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RepairTicketResource\Pages;
use App\Helpers\AppHelper;
use App\Models\Customer;
use App\Models\PrinterModel;
use App\Models\Product;
use App\Models\RepairTicket;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RepairTicketResource extends Resource
{
    protected static ?string $model = RepairTicket::class;

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected static ?string $navigationGroup = 'Kỹ thuật';

    protected static ?string $navigationLabel = 'Phiếu sửa máy';

    protected static ?string $modelLabel = 'Phiếu sửa chữa';

    protected static ?string $pluralModelLabel = 'Danh sách phiếu sửa chữa';

    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::whereIn('status', ['received', 'diagnosing', 'waiting_approval', 'in_progress'])->count() ?: null;
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
                        // CỘT CHÍNH (2/3 chiều rộng): Tiếp nhận & Kỹ thuật
                        Forms\Components\Group::make([
                            Forms\Components\Section::make('1. Tiếp nhận thiết bị & Thông tin khách hàng')
                                ->icon('heroicon-o-inbox-arrow-down')
                                ->schema([
                                    Forms\Components\Grid::make(3)
                                        ->schema([
                                            Forms\Components\Select::make('customer_id')
                                                ->label('Chọn khách hàng')
                                                ->relationship('customer', 'name')
                                                ->searchable()
                                                ->preload()
                                                ->createOptionForm([
                                                    Forms\Components\TextInput::make('name')
                                                        ->label('Họ tên khách *')
                                                        ->required(),
                                                    Forms\Components\TextInput::make('phone')
                                                        ->label('Số điện thoại *')
                                                        ->tel()
                                                        ->required(),
                                                    Forms\Components\TextInput::make('address')
                                                        ->label('Địa chỉ'),
                                                ])
                                                ->live()
                                                ->afterStateUpdated(function ($state, callable $set) {
                                                    if ($state) {
                                                        $c = Customer::find($state);
                                                        if ($c) {
                                                            $set('customer_name', $c->name);
                                                            $set('customer_phone', $c->phone);
                                                        }
                                                    }
                                                }),

                                            Forms\Components\TextInput::make('customer_name')
                                                ->label('Họ tên khách hàng *')
                                                ->placeholder('Khách lẻ hoặc tên cty')
                                                ->required(),

                                            Forms\Components\TextInput::make('customer_phone')
                                                ->label('Số điện thoại liên hệ *')
                                                ->tel()
                                                ->placeholder('0912345678')
                                                ->required(),
                                        ]),

                                    Forms\Components\Grid::make(3)
                                        ->schema([
                                            Forms\Components\Select::make('printer_model_id')
                                                ->label('Dòng máy in mẫu')
                                                ->relationship('printerModel', 'model_name')
                                                ->getOptionLabelFromRecordUsing(fn (PrinterModel $record) => "{$record->brand} {$record->model_name}")
                                                ->searchable()
                                                ->preload()
                                                ->live()
                                                ->afterStateUpdated(function ($state, callable $set) {
                                                    if ($state) {
                                                        $pm = PrinterModel::find($state);
                                                        if ($pm) {
                                                            $set('device_name', "{$pm->brand} {$pm->model_name}");
                                                        }
                                                    }
                                                }),

                                            Forms\Components\TextInput::make('device_name')
                                                ->label('Tên thiết bị thực tế *')
                                                ->placeholder('VD: Canon LBP 2900, HP 107a...')
                                                ->required(),

                                            Forms\Components\TextInput::make('serial_number')
                                                ->label('Số sê-ri / Mã máy')
                                                ->placeholder('Tem phía sau máy (nếu có)'),
                                        ]),

                                    Forms\Components\Actions::make([
                                        Forms\Components\Actions\Action::make('m_canon_2900')
                                            ->label('Canon 2900')
                                            ->icon('heroicon-m-printer')
                                            ->size('xs')
                                            ->color('gray')
                                            ->action(fn (callable $set) => $set('device_name', 'Canon LBP 2900')),

                                        Forms\Components\Actions\Action::make('m_canon_3300')
                                            ->label('Canon 3300')
                                            ->icon('heroicon-m-printer')
                                            ->size('xs')
                                            ->color('gray')
                                            ->action(fn (callable $set) => $set('device_name', 'Canon LBP 3300')),

                                        Forms\Components\Actions\Action::make('m_hp_1102')
                                            ->label('HP 1102/1020')
                                            ->icon('heroicon-m-printer')
                                            ->size('xs')
                                            ->color('gray')
                                            ->action(fn (callable $set) => $set('device_name', 'HP LaserJet P1102')),

                                        Forms\Components\Actions\Action::make('m_brother_2321')
                                            ->label('Brother 2321D')
                                            ->icon('heroicon-m-printer')
                                            ->size('xs')
                                            ->color('gray')
                                            ->action(fn (callable $set) => $set('device_name', 'Brother HL-L2321D')),

                                        Forms\Components\Actions\Action::make('m_brother_7535')
                                            ->label('Brother 7535DW')
                                            ->icon('heroicon-m-printer')
                                            ->size('xs')
                                            ->color('gray')
                                            ->action(fn (callable $set) => $set('device_name', 'Brother DCP-B7535DW')),

                                        Forms\Components\Actions\Action::make('m_epson_3210')
                                            ->label('Epson L3210')
                                            ->icon('heroicon-m-printer')
                                            ->size('xs')
                                            ->color('gray')
                                            ->action(fn (callable $set) => $set('device_name', 'Epson EcoTank L3210')),
                                    ])
                                        ->columnSpanFull(),

                                    Forms\Components\Grid::make(2)
                                        ->schema([
                                            Forms\Components\TextInput::make('accessories')
                                                ->label('Phụ kiện kèm theo máy')
                                                ->placeholder('Dây nguồn, cáp USB, hộp mực cũ, khay giấy...')
                                                ->default('Dây nguồn'),

                                            Forms\Components\DateTimePicker::make('promised_at')
                                                ->label('Thời gian hẹn trả máy *')
                                                ->default(now()->addDay()->setTime(16, 0))
                                                ->required(),
                                        ]),

                                    Forms\Components\Actions::make([
                                        Forms\Components\Actions\Action::make('acc_pwr')
                                            ->label('+ Dây nguồn')
                                            ->size('xs')
                                            ->color('gray')
                                            ->action(function (callable $get, callable $set) {
                                                $c = trim($get('accessories') ?? '');
                                                if (!Str::contains($c, 'Dây nguồn')) {
                                                    $set('accessories', $c ? "{$c}, Dây nguồn" : 'Dây nguồn');
                                                }
                                            }),
                                        Forms\Components\Actions\Action::make('acc_usb')
                                            ->label('+ Cáp USB')
                                            ->size('xs')
                                            ->color('gray')
                                            ->action(function (callable $get, callable $set) {
                                                $c = trim($get('accessories') ?? '');
                                                if (!Str::contains($c, 'Cáp USB')) {
                                                    $set('accessories', $c ? "{$c}, Cáp USB" : 'Cáp USB');
                                                }
                                            }),
                                        Forms\Components\Actions\Action::make('acc_cartridge')
                                            ->label('+ Hộp mực')
                                            ->size('xs')
                                            ->color('gray')
                                            ->action(function (callable $get, callable $set) {
                                                $c = trim($get('accessories') ?? '');
                                                if (!Str::contains($c, 'Hộp mực')) {
                                                    $set('accessories', $c ? "{$c}, Hộp mực" : 'Hộp mực');
                                                }
                                            }),
                                        Forms\Components\Actions\Action::make('acc_tray')
                                            ->label('+ Khay giấy')
                                            ->size('xs')
                                            ->color('gray')
                                            ->action(function (callable $get, callable $set) {
                                                $c = trim($get('accessories') ?? '');
                                                if (!Str::contains($c, 'Khay giấy')) {
                                                    $set('accessories', $c ? "{$c}, Khay giấy" : 'Khay giấy');
                                                }
                                            }),
                                        Forms\Components\Actions\Action::make('acc_adapter')
                                            ->label('+ Adapter nguồn')
                                            ->size('xs')
                                            ->color('gray')
                                            ->action(function (callable $get, callable $set) {
                                                $c = trim($get('accessories') ?? '');
                                                if (!Str::contains($c, 'Adapter')) {
                                                    $set('accessories', $c ? "{$c}, Adapter" : 'Adapter');
                                                }
                                            }),
                                        Forms\Components\Actions\Action::make('acc_none')
                                            ->label('✕ Không phụ kiện')
                                            ->size('xs')
                                            ->color('danger')
                                            ->action(fn (callable $set) => $set('accessories', 'Không kèm phụ kiện')),
                                    ])
                                        ->columnSpanFull(),

                                    Forms\Components\Textarea::make('issue_description')
                                        ->label('Mô tả tình trạng lỗi khi tiếp nhận *')
                                        ->placeholder('VD: Kẹt giấy liên tục khi in từ tờ thứ 2, bản in mờ/sọc đen dọc trang, máy kêu lạch cạch, không nhận lệnh in...')
                                        ->required()
                                        ->rows(3)
                                        ->columnSpanFull(),

                                    Forms\Components\Actions::make([
                                        Forms\Components\Actions\Action::make('sym_jam')
                                            ->label('⚠️ Kẹt giấy')
                                            ->size('xs')
                                            ->color('gray')
                                            ->action(function (callable $get, callable $set) {
                                                $c = trim($get('issue_description') ?? '');
                                                $s = 'Kẹt giấy liên tục khi in';
                                                if (!Str::contains($c, $s)) {
                                                    $set('issue_description', $c ? "{$c}. {$s}" : $s);
                                                }
                                            }),
                                        Forms\Components\Actions\Action::make('sym_fade')
                                            ->label('📄 Bản in mờ / Sọc đen')
                                            ->size('xs')
                                            ->color('gray')
                                            ->action(function (callable $get, callable $set) {
                                                $c = trim($get('issue_description') ?? '');
                                                $s = 'Bản in mờ, có vệt sọc đen dọc trang';
                                                if (!Str::contains($c, $s)) {
                                                    $set('issue_description', $c ? "{$c}. {$s}" : $s);
                                                }
                                            }),
                                        Forms\Components\Actions\Action::make('sym_roller')
                                            ->label('🚫 Không kéo giấy')
                                            ->size('xs')
                                            ->color('gray')
                                            ->action(function (callable $get, callable $set) {
                                                $c = trim($get('issue_description') ?? '');
                                                $s = 'Không kéo giấy, trượt bánh cao su cuốn giấy';
                                                if (!Str::contains($c, $s)) {
                                                    $set('issue_description', $c ? "{$c}. {$s}" : $s);
                                                }
                                            }),
                                        Forms\Components\Actions\Action::make('sym_noise')
                                            ->label('🔊 Kêu lạch cạch')
                                            ->size('xs')
                                            ->color('gray')
                                            ->action(function (callable $get, callable $set) {
                                                $c = trim($get('issue_description') ?? '');
                                                $s = 'Máy phát tiếng kêu cọt kẹt, lạch cạch cơ nhông';
                                                if (!Str::contains($c, $s)) {
                                                    $set('issue_description', $c ? "{$c}. {$s}" : $s);
                                                }
                                            }),
                                        Forms\Components\Actions\Action::make('sym_refill')
                                            ->label('🧪 Nạp mực / Hết mực')
                                            ->size('xs')
                                            ->color('gray')
                                            ->action(function (callable $get, callable $set) {
                                                $c = trim($get('issue_description') ?? '');
                                                $s = 'Nạp mực và vệ sinh bảo dưỡng hộp mực';
                                                if (!Str::contains($c, $s)) {
                                                    $set('issue_description', $c ? "{$c}. {$s}" : $s);
                                                }
                                            }),
                                        Forms\Components\Actions\Action::make('sym_power')
                                            ->label('🔌 Mất nguồn')
                                            ->size('xs')
                                            ->color('gray')
                                            ->action(function (callable $get, callable $set) {
                                                $c = trim($get('issue_description') ?? '');
                                                $s = 'Không lên nguồn / mất điện hoàn toàn';
                                                if (!Str::contains($c, $s)) {
                                                    $set('issue_description', $c ? "{$c}. {$s}" : $s);
                                                }
                                            }),
                                    ])
                                        ->columnSpanFull(),
                                ]),

                            Forms\Components\Section::make('2. Chẩn đoán kỹ thuật & Vật tư linh kiện (Tự động trừ kho)')
                                ->icon('heroicon-o-wrench')
                                ->schema([
                                    Forms\Components\Grid::make(2)
                                        ->schema([
                                            Forms\Components\Select::make('technician_id')
                                                ->label('Kỹ thuật viên phụ trách')
                                                ->relationship('technician', 'name', fn (Builder $query) => $query->whereIn('role', ['technician', 'admin']))
                                                ->searchable()
                                                ->preload(),

                                            Forms\Components\TextInput::make('technician_diagnosis')
                                                ->label('Tóm tắt bệnh lý máy in')
                                                ->placeholder('VD: Rách bao lụa sấy, hỏng trống Drum, mòn bánh cao su cuốn giấy...'),
                                        ]),

                                    Forms\Components\Repeater::make('repairItems')
                                        ->label('Linh kiện & Hộp mực thay thế')
                                        ->relationship('repairItems')
                                        ->schema([
                                            Forms\Components\Select::make('product_id')
                                                ->label('Chọn từ kho linh kiện/VPP')
                                                ->options(fn () => Product::where('is_service_part', true)->orWhere('is_active', true)->pluck('name', 'id'))
                                                ->searchable()
                                                ->live()
                                                ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                                    if ($state) {
                                                        $p = Product::find($state);
                                                        if ($p) {
                                                            $set('item_name', $p->name);
                                                            $set('unit', $p->base_unit);
                                                            $set('unit_price', $p->retail_price);
                                                            $set('cost_price', $p->cost_price);
                                                        }
                                                    }
                                                })
                                                ->columnSpan(3),

                                            Forms\Components\TextInput::make('item_name')
                                                ->label('Tên linh kiện/dịch vụ')
                                                ->required()
                                                ->columnSpan(2),

                                            Forms\Components\TextInput::make('quantity')
                                                ->label('Số lượng')
                                                ->numeric()
                                                ->default(1)
                                                ->live()
                                                ->required()
                                                ->columnSpan(1),

                                            Forms\Components\TextInput::make('unit')
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
                                        ->columns(9)
                                        ->defaultItems(0)
                                        ->addActionLabel('+ Thêm linh kiện / vật tư')
                                        ->live()
                                        ->afterStateUpdated(function (callable $get, callable $set) {
                                            $items = $get('repairItems') ?? [];
                                            $partsTotal = 0;
                                            foreach ($items as $item) {
                                                $qty = (float) ($item['quantity'] ?? 0);
                                                $price = (float) ($item['unit_price'] ?? 0);
                                                $partsTotal += ($qty * $price);
                                            }
                                            $set('parts_total', $partsTotal);

                                            $labor = (float) ($get('labor_fee') ?? 0);
                                            $discount = (float) ($get('discount_amount') ?? 0);
                                            $set('grand_total', max(0, $labor + $partsTotal - $discount));
                                        }),
                                ]),
                        ])
                        ->columnSpan(['lg' => 2]),

                        // CỘT PHỤ (1/3 chiều rộng): Trạng thái & Chi phí
                        Forms\Components\Group::make([
                            Forms\Components\Section::make('3. Tiến độ sửa chữa')
                                ->icon('heroicon-o-clock')
                                ->schema([
                                    Forms\Components\Select::make('status')
                                        ->label('Trạng thái phiếu *')
                                        ->options([
                                            'received' => '1. 📥 Đã tiếp nhận máy',
                                            'diagnosing' => '2. 🔍 Đang kiểm tra / tháo máy',
                                            'waiting_approval' => '3. ⏳ Chờ khách duyệt giá',
                                            'in_progress' => '4. ⚙️ Đang sửa chữa',
                                            'completed' => '5. ✅ Đã sửa xong',
                                            'delivered' => '6. 📦 Đã bàn giao cho khách',
                                            'cancelled' => '7. ❌ Đã hủy',
                                        ])
                                        ->required()
                                        ->default('received'),

                                    Forms\Components\Radio::make('intake_flow')
                                        ->label('Hình thức báo giá')
                                        ->options([
                                            'quote_immediate' => 'Báo giá ngay tại quầy',
                                            'quote_later' => 'Kiểm tra báo giá sau',
                                        ])
                                        ->default('quote_immediate'),
                                ]),

                            Forms\Components\Section::make('4. Chi phí & Thu ngân')
                                ->icon('heroicon-o-banknotes')
                                ->schema([
                                    Forms\Components\TextInput::make('labor_fee')
                                        ->label('Tiền công kỹ thuật')
                                        ->numeric()
                                        ->prefix('₫')
                                        ->default(0)
                                        ->live()
                                        ->afterStateUpdated(function (callable $get, callable $set) {
                                            $labor = (float) ($get('labor_fee') ?? 0);
                                            $parts = (float) ($get('parts_total') ?? 0);
                                            $discount = (float) ($get('discount_amount') ?? 0);
                                            $set('grand_total', max(0, $labor + $parts - $discount));
                                        }),

                                    Forms\Components\TextInput::make('parts_total')
                                        ->label('Tiền linh kiện')
                                        ->numeric()
                                        ->prefix('₫')
                                        ->default(0)
                                        ->disabled()
                                        ->dehydrated(),

                                    Forms\Components\TextInput::make('discount_amount')
                                        ->label('Giảm giá / Ưu đãi')
                                        ->numeric()
                                        ->prefix('₫')
                                        ->default(0)
                                        ->live()
                                        ->afterStateUpdated(function (callable $get, callable $set) {
                                            $labor = (float) ($get('labor_fee') ?? 0);
                                            $parts = (float) ($get('parts_total') ?? 0);
                                            $discount = (float) ($get('discount_amount') ?? 0);
                                            $set('grand_total', max(0, $labor + $parts - $discount));
                                        }),

                                    Forms\Components\TextInput::make('grand_total')
                                        ->label('Tổng thanh toán')
                                        ->numeric()
                                        ->prefix('₫')
                                        ->disabled()
                                        ->dehydrated()
                                        ->extraInputAttributes(['style' => 'font-weight: 800; font-size: 1.15rem; color: #4f46e5;']),

                                    Forms\Components\TextInput::make('paid_amount')
                                        ->label('Tiền khách đã trả / Đặt cọc')
                                        ->numeric()
                                        ->prefix('₫')
                                        ->default(0)
                                        ->live(),

                                    Forms\Components\Placeholder::make('remaining_due')
                                        ->label('Còn lại phải thu')
                                        ->content(function (callable $get) {
                                            $grand = (float) ($get('grand_total') ?? 0);
                                            $paid = (float) ($get('paid_amount') ?? 0);
                                            $rem = max(0, $grand - $paid);
                                            return number_format($rem, 0, ',', '.') . ' ₫';
                                        })
                                        ->extraAttributes(['class' => 'text-rose-600 font-bold text-base']),

                                    Forms\Components\Select::make('payment_status')
                                        ->label('Trạng thái thanh toán')
                                        ->options([
                                            'unpaid' => 'Chưa thanh toán',
                                            'partially_paid' => 'Đã đặt cọc / Trả một phần',
                                            'paid' => 'Đã thanh toán đủ',
                                        ])
                                        ->default('unpaid'),

                                    Forms\Components\Select::make('payment_method')
                                        ->label('Phương thức thanh toán')
                                        ->options([
                                            'cash' => 'Tiền mặt',
                                            'vietqr' => 'Chuyển khoản VietQR',
                                            'transfer' => 'Chuyển khoản thường',
                                        ])
                                        ->default('cash'),

                                    Forms\Components\Textarea::make('internal_notes')
                                        ->label('Ghi chú nội bộ')
                                        ->placeholder('Ghi chú cho ca sau hoặc lưu ý đặc biệt...')
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
                Tables\Columns\TextColumn::make('ticket_code')
                    ->label('Mã phiếu')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable()
                    ->fontFamily('mono')
                    ->description(fn (RepairTicket $r) => $r->created_at->format('d/m/Y H:i')),

                Tables\Columns\TextColumn::make('customer_name')
                    ->label('Khách hàng')
                    ->searchable()
                    ->weight('bold')
                    ->description(fn (RepairTicket $r) => $r->customer_phone),

                Tables\Columns\TextColumn::make('device_name')
                    ->label('Thiết bị')
                    ->searchable()
                    ->wrap()
                    ->weight('semibold')
                    ->description(fn (RepairTicket $r) => Str::limit($r->issue_description, 45)),

                Tables\Columns\TextColumn::make('status')
                    ->label('Trạng thái')
                    ->badge()
                    ->colors([
                        'gray' => 'received',
                        'warning' => 'diagnosing',
                        'info' => 'waiting_approval',
                        'primary' => 'in_progress',
                        'success' => fn ($state) => in_array($state, ['completed', 'delivered']),
                        'danger' => 'cancelled',
                    ])
                    ->formatStateUsing(fn (RepairTicket $r) => match ($r->status) {
                        'received' => 'Đã tiếp nhận',
                        'diagnosing' => 'Đang kiểm tra',
                        'waiting_approval' => 'Chờ duyệt giá',
                        'in_progress' => 'Đang sửa chữa',
                        'completed' => 'Đã sửa xong',
                        'delivered' => 'Đã bàn giao',
                        'cancelled' => 'Đã hủy',
                        default => $r->status,
                    }),

                Tables\Columns\TextColumn::make('promised_at')
                    ->label('Hẹn trả')
                    ->dateTime('d/m H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('grand_total')
                    ->label('Tổng tiền')
                    ->money('VND')
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('payment_status')
                    ->label('Thanh toán')
                    ->badge()
                    ->colors([
                        'danger' => 'unpaid',
                        'warning' => 'partially_paid',
                        'success' => 'paid',
                    ])
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'paid' => 'Đã trả',
                        'partially_paid' => 'Trả một phần',
                        default => 'Chưa trả',
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Trạng thái sửa')
                    ->options([
                        'received' => 'Đã tiếp nhận',
                        'diagnosing' => 'Đang kiểm tra',
                        'waiting_approval' => 'Chờ duyệt giá',
                        'in_progress' => 'Đang sửa chữa',
                        'completed' => 'Đã sửa xong',
                        'delivered' => 'Đã bàn giao',
                        'cancelled' => 'Đã hủy',
                    ]),

                Tables\Filters\SelectFilter::make('payment_status')
                    ->label('Thanh toán')
                    ->options([
                        'unpaid' => 'Chưa thanh toán',
                        'partially_paid' => 'Trả một phần',
                        'paid' => 'Đã thanh toán',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('quick_status')
                    ->label('Đổi trạng thái')
                    ->icon('heroicon-m-arrow-path')
                    ->color('warning')
                    ->form([
                        Forms\Components\Select::make('status')
                            ->label('Trạng thái mới *')
                            ->options([
                                'received' => '1. 📥 Đã tiếp nhận máy',
                                'diagnosing' => '2. 🔍 Đang kiểm tra / tháo máy',
                                'waiting_approval' => '3. ⏳ Chờ khách duyệt giá',
                                'in_progress' => '4. ⚙️ Đang sửa chữa',
                                'completed' => '5. ✅ Đã sửa xong',
                                'delivered' => '6. 📦 Đã bàn giao cho khách',
                                'cancelled' => '7. ❌ Đã hủy',
                            ])
                            ->required(),
                        Forms\Components\Select::make('payment_status')
                            ->label('Trạng thái thanh toán')
                            ->options([
                                'unpaid' => 'Chưa thanh toán',
                                'partially_paid' => 'Đã đặt cọc / Trả một phần',
                                'paid' => 'Đã thanh toán đủ',
                            ]),
                        Forms\Components\Textarea::make('technician_diagnosis')
                            ->label('Ghi chú tình trạng / Bệnh lý')
                            ->rows(2),
                    ])
                    ->fillForm(fn (RepairTicket $record): array => [
                        'status' => $record->status,
                        'payment_status' => $record->payment_status,
                        'technician_diagnosis' => $record->technician_diagnosis,
                    ])
                    ->action(function (RepairTicket $record, array $data): void {
                        DB::transaction(function () use ($record, $data) {
                            $record->update([
                                'status' => $data['status'],
                                'payment_status' => $data['payment_status'] ?? $record->payment_status,
                                'technician_diagnosis' => $data['technician_diagnosis'] ?? $record->technician_diagnosis,
                            ]);
                        });

                        Notification::make()
                            ->title('Cập nhật trạng thái thành công')
                            ->body("Phiếu {$record->ticket_code} đã được cập nhật.")
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('print')
                    ->label('In phiếu')
                    ->icon('heroicon-o-printer')
                    ->color('info')
                    ->url(fn (RepairTicket $record) => url("/print/repair-ticket/{$record->id}"))
                    ->openUrlInNewTab(),

                Tables\Actions\Action::make('print_sticker')
                    ->label('In tem dán')
                    ->icon('heroicon-o-tag')
                    ->color('gray')
                    ->url(fn (RepairTicket $record) => url("/print/repair-ticket-sticker/{$record->id}"))
                    ->openUrlInNewTab(),

                Tables\Actions\Action::make('vietqr')
                    ->label('VietQR')
                    ->icon('heroicon-o-qr-code')
                    ->color('success')
                    ->modalHeading(fn (RepairTicket $record) => "Mã VietQR thanh toán phiếu {$record->ticket_code}")
                    ->modalSubmitAction(false)
                    ->modalContent(fn (RepairTicket $record) => view('filament.modals.vietqr-ticket', [
                        'ticket' => $record,
                        'amount' => $record->grand_total - $record->paid_amount > 0 ? ($record->grand_total - $record->paid_amount) : $record->grand_total,
                        'qrUrl' => AppHelper::generateVietQrUrl(
                            (float) ($record->grand_total - $record->paid_amount > 0 ? ($record->grand_total - $record->paid_amount) : $record->grand_total),
                            $record->ticket_code
                        ),
                    ])),

                Tables\Actions\EditAction::make()
                    ->label('Sửa')
                    ->icon('heroicon-m-pencil-square'),

                Tables\Actions\DeleteAction::make()
                    ->label('Xóa')
                    ->icon('heroicon-m-trash')
                    ->modalHeading('Xác nhận xóa phiếu sửa chữa')
                    ->modalDescription(fn (RepairTicket $record) => "Bạn có chắc chắn muốn xóa phiếu sửa chữa '{$record->ticket_code}' của khách {$record->customer_name}? Hành động này không thể hoàn tác.")
                    ->modalSubmitActionLabel('Xác nhận xóa')
                    ->modalCancelActionLabel('Hủy bỏ'),
            ])
            ->emptyStateHeading('Chưa có phiếu sửa chữa nào')
            ->emptyStateDescription('Bấm Tạo phiếu mới để tiếp nhận máy in khách mang đến bảo hành hoặc sửa chữa.')
            ->emptyStateIcon('heroicon-o-wrench-screwdriver');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRepairTickets::route('/'),
            'create' => Pages\CreateRepairTicket::route('/create'),
            'edit' => Pages\EditRepairTicket::route('/{record}/edit'),
        ];
    }
}
