<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RepairTicketResource\Pages;
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

class RepairTicketResource extends Resource
{
    protected static ?string $model = RepairTicket::class;

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected static ?string $navigationGroup = 'Dịch Vụ Kỹ Thuật';

    protected static ?string $navigationLabel = 'Phiếu sửa chữa máy in';

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
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('1. Thông tin tiếp nhận máy')
                            ->description('Thông tin khách hàng và tình trạng thiết bị khi mang đến cửa hàng')
                            ->schema([
                                Forms\Components\Grid::make(3)
                                    ->schema([
                                        Forms\Components\Select::make('customer_id')
                                            ->label('Khách hàng')
                                            ->relationship('customer', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->createOptionForm([
                                                Forms\Components\TextInput::make('name')->label('Tên khách')->required(),
                                                Forms\Components\TextInput::make('phone')->label('Số điện thoại')->required(),
                                                Forms\Components\TextInput::make('address')->label('Địa chỉ'),
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
                                            ->label('Họ tên khách hàng')
                                            ->required(),

                                        Forms\Components\TextInput::make('customer_phone')
                                            ->label('Số điện thoại')
                                            ->tel()
                                            ->required(),
                                    ]),

                                Forms\Components\Grid::make(3)
                                    ->schema([
                                        Forms\Components\Select::make('printer_model_id')
                                            ->label('Dòng máy in')
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
                                            ->label('Tên thiết bị thực tế')
                                            ->placeholder('VD: Canon LBP 2900')
                                            ->required(),

                                        Forms\Components\TextInput::make('serial_number')
                                            ->label('Số Serial / Tem máy')
                                            ->placeholder('Nếu có'),
                                    ]),

                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('accessories')
                                            ->label('Phụ kiện đi kèm')
                                            ->placeholder('VD: Dây nguồn, cáp USB, khay giấy...')
                                            ->default('Dây nguồn'),

                                        Forms\Components\DateTimePicker::make('promised_at')
                                            ->label('Hẹn thời gian trả máy')
                                            ->default(now()->addDay()->setTime(16, 0))
                                            ->required(),
                                    ]),

                                Forms\Components\Textarea::make('issue_description')
                                    ->label('Hiện trạng lỗi khách báo')
                                    ->placeholder('VD: Kẹt giấy liên tục, bản in bị sọc đen, máy kêu rè rè, không nhận lệnh in...')
                                    ->required()
                                    ->rows(3),

                                Forms\Components\Radio::make('intake_flow')
                                    ->label('Nhánh quy trình tiếp nhận')
                                    ->options([
                                        'quote_immediate' => 'Nhánh 1: Thợ có mặt báo giá ngay (chuyển thẳng sang Đang sửa)',
                                        'quote_later' => 'Nhánh 2: Tiếp nhận trước - Tháo máy chẩn đoán báo giá sau qua điện thoại',
                                    ])
                                    ->default('quote_immediate')
                                    ->inline(),
                            ]),

                        Forms\Components\Section::make('2. Chẩn đoán kỹ thuật & Linh kiện thay thế (Trừ kho VPP)')
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\Select::make('technician_id')
                                            ->label('Kỹ thuật viên phụ trách')
                                            ->relationship('technician', 'name', fn (Builder $query) => $query->whereIn('role', ['technician', 'admin']))
                                            ->searchable()
                                            ->preload(),

                                        Forms\Components\Select::make('status')
                                            ->label('Trạng thái phiếu')
                                            ->options([
                                                'received' => '1. Đã tiếp nhận máy',
                                                'diagnosing' => '2. Đang kiểm tra / tháo máy',
                                                'waiting_approval' => '3. Chờ khách duyệt giá',
                                                'in_progress' => '4. Đang sửa chữa',
                                                'completed' => '5. Đã sửa xong',
                                                'delivered' => '6. Đã bàn giao cho khách',
                                                'cancelled' => '7. Đã hủy',
                                            ])
                                            ->required()
                                            ->default('received'),
                                    ]),

                                Forms\Components\Textarea::make('technician_diagnosis')
                                    ->label('Ghi chú chẩn đoán của kỹ thuật viên')
                                    ->placeholder('Nguyên nhân hư hỏng, tình trạng sấy, drum, gạt, nhông kéo...')
                                    ->rows(2),

                                Forms\Components\Repeater::make('repairItems')
                                    ->label('Linh kiện & Hộp mực thay thế (Tự động trừ kho VPP)')
                                    ->relationship('repairItems')
                                    ->schema([
                                        Forms\Components\Select::make('product_id')
                                            ->label('Chọn từ kho VPP')
                                            ->options(fn () => Product::where('is_service_part', true)->orWhere('is_active', true)->pluck('name', 'id'))
                                            ->searchable()
                                            ->live()
                                            ->afterStateUpdated(function ($state, callable $set) {
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
                                            ->columnSpan(2),

                                        Forms\Components\TextInput::make('item_name')
                                            ->label('Tên linh kiện / dịch vụ')
                                            ->required()
                                            ->columnSpan(2),

                                        Forms\Components\TextInput::make('quantity')
                                            ->label('SL')
                                            ->numeric()
                                            ->default(1)
                                            ->live()
                                            ->required(),

                                        Forms\Components\TextInput::make('unit')
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
                                    ->columns(7)
                                    ->defaultItems(0)
                                    ->addActionLabel('Thêm linh kiện / vật tư'),
                            ]),
                    ])
                    ->columnSpan(['lg' => 2]),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('3. Chi phí & Thanh toán')
                            ->schema([
                                Forms\Components\TextInput::make('labor_fee')
                                    ->label('Tiền công thợ')
                                    ->numeric()
                                    ->prefix('₫')
                                    ->default(0)
                                    ->live(),

                                Forms\Components\TextInput::make('parts_total')
                                    ->label('Tiền linh kiện')
                                    ->numeric()
                                    ->prefix('₫')
                                    ->disabled()
                                    ->dehydrated(),

                                Forms\Components\TextInput::make('discount_amount')
                                    ->label('Chiết khấu / Giảm giá')
                                    ->numeric()
                                    ->prefix('₫')
                                    ->default(0)
                                    ->live(),

                                Forms\Components\TextInput::make('grand_total')
                                    ->label('Tổng thanh toán')
                                    ->numeric()
                                    ->prefix('₫')
                                    ->disabled()
                                    ->dehydrated(),

                                Forms\Components\TextInput::make('paid_amount')
                                    ->label('Số tiền khách đã trả')
                                    ->numeric()
                                    ->prefix('₫')
                                    ->default(0)
                                    ->live(),

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
                                    ->rows(3),
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
                Tables\Columns\TextColumn::make('ticket_code')
                    ->label('Mã phiếu')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable()
                    ->description(fn (RepairTicket $r) => $r->created_at->format('d/m/Y H:i')),

                Tables\Columns\TextColumn::make('customer_name')
                    ->label('Khách hàng')
                    ->searchable()
                    ->description(fn (RepairTicket $r) => $r->customer_phone),

                Tables\Columns\TextColumn::make('device_name')
                    ->label('Thiết bị')
                    ->searchable()
                    ->wrap()
                    ->description(fn (RepairTicket $r) => Str::limit($r->issue_description, 40)),

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
                    ->formatStateUsing(fn (RepairTicket $r) => $r->status_label),

                Tables\Columns\TextColumn::make('grand_total')
                    ->label('Tổng tiền')
                    ->money('VND')
                    ->sortable(),

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

                Tables\Columns\TextColumn::make('promised_at')
                    ->label('Hẹn trả')
                    ->dateTime('d/m H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Trạng thái')
                    ->options([
                        'received' => 'Đã tiếp nhận',
                        'diagnosing' => 'Đang kiểm tra',
                        'waiting_approval' => 'Chờ khách duyệt giá',
                        'in_progress' => 'Đang sửa chữa',
                        'completed' => 'Đã xong',
                        'delivered' => 'Đã bàn giao',
                    ]),
                Tables\Filters\SelectFilter::make('payment_status')
                    ->label('Thanh toán')
                    ->options([
                        'unpaid' => 'Chưa thanh toán',
                        'paid' => 'Đã thanh toán',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('print')
                    ->label('In phiếu')
                    ->icon('heroicon-o-printer')
                    ->color('info')
                    ->url(fn (RepairTicket $record) => url("/print/repair-ticket/{$record->id}"))
                    ->openUrlInNewTab(),

                Tables\Actions\Action::make('vietqr')
                    ->label('VietQR')
                    ->icon('heroicon-o-qr-code')
                    ->color('success')
                    ->modalHeading(fn (RepairTicket $record) => "Mã VietQR thanh toán cho {$record->ticket_code}")
                    ->modalSubmitAction(false)
                    ->modalContent(fn (RepairTicket $record) => view('filament.modals.vietqr-ticket', [
                        'ticket' => $record,
                        'amount' => $record->remaining_amount ?: $record->grand_total,
                        'qrUrl' => "https://img.vietqr.io/image/970407-190333888999-compact2.png?amount=" . ($record->remaining_amount ?: $record->grand_total) . "&addInfo=" . $record->ticket_code . "&accountName=NGUYEN%20VAN%20A",
                    ])),

                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListRepairTickets::route('/'),
            'create' => Pages\CreateRepairTicket::route('/create'),
            'edit' => Pages\EditRepairTicket::route('/{record}/edit'),
        ];
    }
}
