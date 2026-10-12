<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Models\Customer;
use App\Services\CashbookService;
use App\Services\CustomerManagement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Khách hàng';

    protected static ?string $navigationLabel = 'Khách hàng';

    protected static ?string $modelLabel = 'Khách hàng';

    protected static ?string $pluralModelLabel = 'Danh sách khách hàng';

    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count() ?: null;
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->isCashier() ?? false;
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(3)
                    ->schema([
                        // CỘT CHÍNH (2/3 chiều rộng): Thông tin liên hệ & Ghi chú
                        Forms\Components\Group::make([
                            Forms\Components\Section::make('Thông tin liên hệ & Định danh')
                                ->icon('heroicon-o-user')
                                ->schema([
                                    Forms\Components\Grid::make(3)
                                        ->schema([
                                            Forms\Components\TextInput::make('name')
                                                ->label('Họ tên khách hàng hoặc Đơn vị *')
                                                ->placeholder('VD: Anh Nguyễn Văn Hùng, Công ty TNHH Nam Á...')
                                                ->required()
                                                ->maxLength(100)
                                                ->columnSpan(2),

                                            Forms\Components\Select::make('type')
                                                ->label('Phân loại khách *')
                                                ->options([
                                                    'retail' => 'Khách lẻ',
                                                    'wholesale' => 'Khách sỉ / Doanh nghiệp',
                                                    'vip' => 'Khách VIP',
                                                ])
                                                ->default('retail')
                                                ->required(),
                                        ]),

                                    Forms\Components\Grid::make(3)
                                        ->schema([
                                            Forms\Components\TextInput::make('phone')
                                                ->label('Số điện thoại di động *')
                                                ->placeholder('0912345678')
                                                ->tel()
                                                ->required()
                                                ->maxLength(20)
                                                ->helperText('Dùng tra cứu đơn hàng & nhận thông báo Zalo.'),

                                            Forms\Components\TextInput::make('email')
                                                ->label('Địa chỉ Email')
                                                ->placeholder('khachhang@congty.com')
                                                ->email()
                                                ->maxLength(150),

                                            Forms\Components\TextInput::make('tax_id')
                                                ->label('Mã số thuế (nếu có)')
                                                ->placeholder('MST Doanh nghiệp')
                                                ->maxLength(50),
                                        ]),

                                    Forms\Components\Textarea::make('address')
                                        ->label('Địa chỉ giao hàng / Trụ sở')
                                        ->placeholder('Số nhà, ngõ/ngách, tên đường, phường/xã, quận/huyện...')
                                        ->rows(2)
                                        ->maxLength(255)
                                        ->columnSpanFull(),
                                ]),

                            Forms\Components\Section::make('Ghi chú nội bộ & Thói quen mua sắm')
                                ->icon('heroicon-o-chat-bubble-left-ellipsis')
                                ->collapsible()
                                ->schema([
                                    Forms\Components\Textarea::make('notes')
                                        ->label('Ghi chú cho nhân viên thu ngân / kỹ thuật')
                                        ->placeholder('VD: Khách lấy giấy in định kỳ đầu tháng; yêu cầu xuất hóa đơn VAT; hay mang máy Canon 2900...')
                                        ->rows(3)
                                        ->maxLength(1000)
                                        ->columnSpanFull(),
                                ]),
                        ])
                        ->columnSpan(['lg' => 2]),

                        // CỘT PHỤ (1/3 chiều rộng): Tài chính, Công nợ & Tài khoản Zalo
                        Forms\Components\Group::make([
                            Forms\Components\Section::make('Tài chính & Công nợ')
                                ->icon('heroicon-o-banknotes')
                                ->schema([
                                    Forms\Components\TextInput::make('debt_balance')
                                        ->label('Công nợ hiện tại')
                                        ->numeric()
                                        ->prefix('₫')
                                        ->default(0)
                                        ->disabled()
                                        ->dehydrated()
                                        ->extraInputAttributes(['style' => 'font-weight: 800; color: #e11d48;'])
                                        ->helperText('Tự động cập nhật khi mua hàng nợ hoặc thu nợ.'),

                                    Forms\Components\TextInput::make('credit_limit')
                                        ->label('Hạn mức nợ tối đa (₫)')
                                        ->numeric()
                                        ->prefix('₫')
                                        ->default(0)
                                        ->helperText('0 = Không cho phép nợ vượt định mức.'),
                                ]),

                            Forms\Components\Section::make('Trạng thái tài khoản & Zalo')
                                ->icon('heroicon-o-shield-check')
                                ->schema([
                                    Forms\Components\Placeholder::make('activation_display')
                                        ->label('Trạng thái kích hoạt SĐT')
                                        ->content(fn (?Customer $record) => $record && $record->isActivated()
                                            ? 'Đã kích hoạt xác thực (' . $record->phone_verified_at->format('d/m/Y H:i') . ')'
                                            : 'Chưa kích hoạt qua Zalo / SMS'
                                        ),

                                    Forms\Components\TextInput::make('zalo_name')
                                        ->label('Tên tài khoản Zalo')
                                        ->placeholder('Chưa liên kết')
                                        ->disabled()
                                        ->dehydrated(false),

                                    Forms\Components\TextInput::make('zalo_id')
                                        ->label('Zalo UID')
                                        ->placeholder('Chưa có')
                                        ->disabled()
                                        ->dehydrated(false),
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
                Tables\Columns\TextColumn::make('name')
                    ->label('Họ tên / Đơn vị')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable()
                    ->description(fn (Customer $c) => $c->email ?: null),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Số điện thoại')
                    ->searchable()
                    ->fontFamily('mono')
                    ->weight('bold')
                    ->copyable()
                    ->description(fn (Customer $c) => $c->zalo_name ? "Zalo: {$c->zalo_name}" : null),

                Tables\Columns\TextColumn::make('activation_status')
                    ->label('Tài khoản')
                    ->badge()
                    ->state(fn (Customer $c) => $c->isActivated() ? 'Đã kích hoạt' : 'Chưa kích hoạt')
                    ->color(fn (Customer $c) => $c->isActivated() ? 'success' : 'warning')
                    ->icon(fn (Customer $c) => $c->isActivated() ? 'heroicon-m-check-badge' : 'heroicon-m-clock'),

                Tables\Columns\TextColumn::make('address')
                    ->label('Địa chỉ')
                    ->limit(28)
                    ->tooltip(fn (Customer $c) => $c->address),

                Tables\Columns\TextColumn::make('type')
                    ->label('Nhóm khách')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'wholesale' => 'Khách sỉ',
                        'vip' => 'VIP',
                        default => 'Khách lẻ',
                    })
                    ->color(fn ($state) => match ($state) {
                        'wholesale' => 'info',
                        'vip' => 'warning',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('orders_count')
                    ->label('Đơn hàng')
                    ->counts('orders')
                    ->badge()
                    ->color('primary')
                    ->icon('heroicon-m-shopping-bag')
                    ->sortable(),

                Tables\Columns\TextColumn::make('repair_tickets_count')
                    ->label('Sửa máy')
                    ->counts('repairTickets')
                    ->badge()
                    ->color('info')
                    ->icon('heroicon-m-wrench-screwdriver')
                    ->sortable(),

                Tables\Columns\TextColumn::make('debt_balance')
                    ->label('Công nợ')
                    ->money('VND')
                    ->sortable()
                    ->weight('bold')
                    ->color(fn ($state) => (float) $state > 0 ? 'danger' : 'gray'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Phân loại khách')
                    ->options([
                        'retail' => 'Khách lẻ',
                        'wholesale' => 'Khách sỉ / Doanh nghiệp',
                        'vip' => 'Khách VIP',
                    ]),

                Tables\Filters\Filter::make('activated')
                    ->label('Đã kích hoạt tài khoản')
                    ->query(fn (Builder $query) => $query->whereNotNull('phone_verified_at')),

                Tables\Filters\Filter::make('unactivated')
                    ->label('Chưa kích hoạt')
                    ->query(fn (Builder $query) => $query->whereNull('phone_verified_at')),

                Tables\Filters\Filter::make('has_debt')
                    ->label('Đang có công nợ (> 0đ)')
                    ->query(fn (Builder $query) => $query->where('debt_balance', '>', 0)),
            ])
            ->actions([
                Tables\Actions\Action::make('collect_debt')
                    ->label('Thu nợ')
                    ->icon('heroicon-m-banknotes')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading(fn (Customer $record) => "Thu nợ khách hàng: {$record->name}")
                    ->modalDescription(fn (Customer $record) => "Số dư công nợ hiện tại: " . number_format($record->debt_balance, 0, ',', '.') . "₫. Khoản thu sẽ được tự động ghi nhận vào Sổ quỹ.")
                    ->form([
                        Forms\Components\TextInput::make('amount')
                            ->label('Số tiền thu (₫) *')
                            ->numeric()
                            ->prefix('₫')
                            ->default(fn (Customer $record) => $record->debt_balance)
                            ->required(),

                        Forms\Components\Select::make('payment_method')
                            ->label('Hình thức tiền *')
                            ->options([
                                'cash' => 'Tiền mặt',
                                'bank_transfer' => 'Chuyển khoản ngân hàng',
                            ])
                            ->default('cash')
                            ->required(),

                        Forms\Components\Textarea::make('notes')
                            ->label('Ghi chú')
                            ->placeholder('Khách thanh toán công nợ...')
                            ->rows(2),
                    ])
                    ->visible(fn (Customer $record) => $record->debt_balance > 0)
                    ->action(function (Customer $record, array $data) {
                        try {
                            app(CashbookService::class)->createReceipt([
                                'category' => 'customer_debt',
                                'amount' => $data['amount'],
                                'customer_id' => $record->id,
                                'payment_method' => $data['payment_method'],
                                'notes' => $data['notes'] ?? "Thu nợ khách hàng {$record->name}",
                            ]);

                            $record->refresh();

                            Notification::make()
                                ->title('Đã thu nợ thành công!')
                                ->body("Đã thu " . number_format($data['amount'], 0, ',', '.') . "₫ từ {$record->name}. Công nợ còn lại: " . number_format($record->debt_balance, 0, ',', '.') . "₫.")
                                ->success()
                                ->send();
                        } catch (\Throwable $e) {
                            Notification::make()
                                ->title('Lỗi khi thu nợ')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),

                Tables\Actions\EditAction::make()
                    ->label('Sửa')
                    ->icon('heroicon-m-pencil-square'),

                Tables\Actions\DeleteAction::make()
                    ->label('Xóa')
                    ->icon('heroicon-m-trash')
                    ->modalHeading('Xác nhận xóa khách hàng')
                    ->modalDescription(fn (Customer $record) => "Bạn có chắc chắn muốn xóa khách hàng '{$record->name}' ({$record->phone})? Hành động này sẽ bị từ chối nếu khách đã từng có lịch sử mua hàng, sửa chữa hoặc công nợ.")
                    ->modalSubmitActionLabel('Xác nhận xóa vĩnh viễn')
                    ->modalCancelActionLabel('Hủy bỏ')
                    ->using(function (Customer $record): bool {
                        try {
                            app(CustomerManagement::class)->delete($record->id);
                            Notification::make()
                                ->title('Đã xóa khách hàng thành công')
                                ->success()
                                ->send();
                            return true;
                        } catch (\Throwable $e) {
                            Notification::make()
                                ->title('Không thể xóa khách hàng')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                            return false;
                        }
                    }),
            ])
            ->emptyStateHeading('Chưa có khách hàng nào')
            ->emptyStateDescription('Thêm khách hàng đầu tiên để quản lý thông tin mua hàng, tiến độ sửa máy in và công nợ.')
            ->emptyStateIcon('heroicon-o-users');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
}
