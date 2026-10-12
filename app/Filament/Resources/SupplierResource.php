<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SupplierResource\Pages;
use App\Models\Supplier;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SupplierResource extends Resource
{
    protected static ?string $model = Supplier::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationGroup = 'Kho hàng';

    protected static ?string $navigationLabel = 'Nhà cung cấp';

    protected static ?string $modelLabel = 'Nhà cung cấp';

    protected static ?string $pluralModelLabel = 'Danh sách nhà cung cấp';

    protected static ?int $navigationSort = 3;

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
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function getNavigationBadge(): ?string
    {
        $debtCount = static::getModel()::where('debt_balance', '>', 0)->count();
        return $debtCount ? (string) $debtCount : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Thông tin định danh nhà cung cấp')
                    ->description('Thông tin công ty phân phối hoặc đầu mối giao hàng')
                    ->icon('heroicon-o-building-office-2')
                    ->columns(3)
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->label('Mã nhà cung cấp *')
                            ->default(fn () => Supplier::generateNextCode())
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50),

                        Forms\Components\TextInput::make('name')
                            ->label('Tên nhà cung cấp / Công ty *')
                            ->placeholder('VD: Công ty TNHH Thiên Long, VPP Bến Nghé...')
                            ->required()
                            ->columnSpan(2)
                            ->maxLength(255),

                        Forms\Components\TextInput::make('contact_name')
                            ->label('Người liên hệ / Sales phụ trách')
                            ->placeholder('Họ tên nhân viên kinh doanh')
                            ->maxLength(100),

                        Forms\Components\TextInput::make('phone')
                            ->label('Số điện thoại liên hệ')
                            ->tel()
                            ->placeholder('0901234567')
                            ->maxLength(20),

                        Forms\Components\TextInput::make('email')
                            ->label('Email đặt hàng')
                            ->email()
                            ->placeholder('sales@nhacungcap.vn')
                            ->maxLength(100),

                        Forms\Components\TextInput::make('tax_id')
                            ->label('Mã số thuế (MST)')
                            ->placeholder('MST công ty')
                            ->maxLength(50),

                        Forms\Components\TextInput::make('address')
                            ->label('Địa chỉ kho / Trụ sở')
                            ->placeholder('Số nhà, tên đường, quận/huyện...')
                            ->columnSpan(2)
                            ->maxLength(255),
                    ]),

                Forms\Components\Section::make('Công nợ & Trạng thái')
                    ->icon('heroicon-o-banknotes')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('debt_balance')
                            ->label('Công nợ hiện tại phải trả NCC')
                            ->numeric()
                            ->prefix('₫')
                            ->default(0)
                            ->helperText('Số tiền cửa hàng đang còn nợ nhà cung cấp này')
                            ->required(),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Đang giao dịch / Hợp tác')
                            ->default(true)
                            ->inline(false),

                        Forms\Components\Textarea::make('notes')
                            ->label('Ghi chú chính sách giao hàng / Chiết khấu')
                            ->placeholder('Chính sách công nợ 30 ngày, đổi trả hàng lỗi...')
                            ->columnSpanFull()
                            ->rows(3),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Mã NCC')
                    ->sortable()
                    ->searchable()
                    ->weight('bold')
                    ->copyable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Tên nhà cung cấp')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->wrap()
                    ->description(fn (Supplier $record) => $record->contact_name ? "LH: {$record->contact_name}" : null),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Số điện thoại')
                    ->searchable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('debt_balance')
                    ->label('Công nợ phải trả')
                    ->money('VND')
                    ->sortable()
                    ->weight('bold')
                    ->color(fn (Supplier $record) => $record->debt_balance > 0 ? 'danger' : 'success')
                    ->description(fn (Supplier $record) => $record->debt_balance > 0 ? 'Đang nợ NCC' : 'Đã thanh toán hết'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Hợp tác')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('purchase_orders_count')
                    ->label('Số đơn nhập')
                    ->counts('purchaseOrders')
                    ->badge()
                    ->color('info'),
            ])
            ->filters([
                Tables\Filters\Filter::make('has_debt')
                    ->label('Đang còn nợ NCC')
                    ->query(fn (Builder $query) => $query->where('debt_balance', '>', 0)),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Trạng thái hợp tác')
                    ->placeholder('Tất cả')
                    ->trueLabel('Đang hợp tác')
                    ->falseLabel('Ngừng hợp tác'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Sửa'),
                Tables\Actions\DeleteAction::make()
                    ->label('Xóa')
                    ->before(function (Supplier $record, Tables\Actions\DeleteAction $action) {
                        if ($record->debt_balance > 0) {
                            Notification::make()
                                ->title('Không thể xóa nhà cung cấp')
                                ->body("Nhà cung cấp '{$record->name}' đang còn công nợ " . number_format($record->debt_balance, 0, ',', '.') . '₫. Vui lòng tất toán nợ trước khi xóa.')
                                ->danger()
                                ->send();
                            $action->halt();
                        }
                    }),
            ])
            ->emptyStateHeading('Chưa có nhà cung cấp nào')
            ->emptyStateDescription('Thêm nhà cung cấp đầu tiên để quản lý nguồn hàng và theo dõi công nợ nhập kho.')
            ->emptyStateIcon('heroicon-o-building-office-2');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSuppliers::route('/'),
            'create' => Pages\CreateSupplier::route('/create'),
            'edit' => Pages\EditSupplier::route('/{record}/edit'),
        ];
    }
}
