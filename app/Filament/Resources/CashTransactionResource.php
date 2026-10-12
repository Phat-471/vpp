<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CashTransactionResource\Pages;
use App\Models\CashTransaction;
use App\Models\Customer;
use App\Models\Supplier;
use App\Services\CashbookService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class CashTransactionResource extends Resource
{
    protected static ?string $model = CashTransaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationGroup = 'Tài chính';

    protected static ?string $navigationLabel = 'Sổ quỹ Thu - Chi';

    protected static ?string $modelLabel = 'Phiếu thu chi';

    protected static ?string $pluralModelLabel = 'Sổ quỹ & Thu chi tiền mặt';

    protected static ?int $navigationSort = 1;

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
        return false; // Sổ quỹ không sửa trực tiếp để đảm bảo tính minh bạch, chỉ cho phép hủy và hoàn tác
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Thông tin phiếu thu chi')
                    ->icon('heroicon-o-document-currency-dollar')
                    ->columns(3)
                    ->schema([
                        Forms\Components\Select::make('type')
                            ->label('Loại phiếu *')
                            ->options([
                                'receipt' => 'Thu tiền (Tiền vào quỹ)',
                                'payment' => 'Chi tiền (Tiền ra khỏi quỹ)',
                            ])
                            ->default('receipt')
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (callable $set, $state) {
                                $set('code', CashTransaction::generateNextCode($state ?: 'receipt'));
                                $set('category', null);
                            }),

                        Forms\Components\TextInput::make('code')
                            ->label('Mã phiếu')
                            ->default(fn () => CashTransaction::generateNextCode('receipt'))
                            ->disabled()
                            ->dehydrated()
                            ->required(),

                        Forms\Components\DateTimePicker::make('performed_at')
                            ->label('Thời gian thực hiện')
                            ->default(now())
                            ->required(),

                        Forms\Components\Select::make('category')
                            ->label('Hạng mục thu / chi *')
                            ->options(fn (callable $get) => CashTransaction::getCategoryOptions($get('type')))
                            ->required()
                            ->live()
                            ->columnSpan(2),

                        Forms\Components\Select::make('payment_method')
                            ->label('Hình thức tiền *')
                            ->options([
                                'cash' => 'Tiền mặt',
                                'bank_transfer' => 'Chuyển khoản ngân hàng',
                            ])
                            ->default('cash')
                            ->required(),

                        Forms\Components\TextInput::make('amount')
                            ->label('Số tiền (₫) *')
                            ->numeric()
                            ->prefix('₫')
                            ->required()
                            ->extraInputAttributes(['style' => 'font-size: 1.2rem; font-weight: 800; color: #4f46e5;'])
                            ->columnSpan(2),

                        // Nếu thu nợ khách hàng
                        Forms\Components\Select::make('customer_id')
                            ->label('Khách hàng trả nợ')
                            ->relationship('customer', 'name')
                            ->searchable()
                            ->preload()
                            ->visible(fn (callable $get) => $get('category') === 'customer_debt')
                            ->helperText(function (callable $get) {
                                $cid = $get('customer_id');
                                if ($cid) {
                                    $c = Customer::find($cid);
                                    return $c ? "Công nợ hiện tại: " . number_format($c->debt_balance, 0, ',', '.') . "₫" : null;
                                }
                                return 'Chọn khách hàng để tự động trừ công nợ';
                            })
                            ->columnSpanFull(),

                        // Nếu chi trả nợ nhà cung cấp
                        Forms\Components\Select::make('supplier_id')
                            ->label('Nhà cung cấp nhận tiền')
                            ->relationship('supplier', 'name')
                            ->searchable()
                            ->preload()
                            ->visible(fn (callable $get) => $get('category') === 'supplier_debt')
                            ->helperText(function (callable $get) {
                                $sid = $get('supplier_id');
                                if ($sid) {
                                    $s = Supplier::find($sid);
                                    return $s ? "Số nợ NCC hiện tại: " . number_format($s->debt_balance, 0, ',', '.') . "₫" : null;
                                }
                                return 'Chọn nhà cung cấp để tự động giảm công nợ';
                            })
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('notes')
                            ->label('Diễn giải / Ghi chú thu chi')
                            ->placeholder('Lý do chi tiền, số hóa đơn chứng từ kèm theo...')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
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

                Tables\Columns\TextColumn::make('performed_at')
                    ->label('Thời gian')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('type')
                    ->label('Loại phiếu')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state === 'receipt' ? 'Thu tiền (+)' : 'Chi tiền (-)')
                    ->icon(fn ($state) => $state === 'receipt' ? 'heroicon-m-arrow-down-left' : 'heroicon-m-arrow-up-right')
                    ->color(fn ($state) => $state === 'receipt' ? 'success' : 'danger'),

                Tables\Columns\TextColumn::make('category')
                    ->label('Hạng mục')
                    ->formatStateUsing(fn ($state, CashTransaction $record) => CashTransaction::getCategoryOptions()[$state] ?? $state)
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Số tiền')
                    ->money('VND')
                    ->weight('bold')
                    ->color(fn (CashTransaction $record) => $record->isReceipt() ? 'success' : 'danger')
                    ->sortable(),

                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Hình thức')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state === 'cash' ? 'Tiền mặt' : 'Chuyển khoản')
                    ->color(fn ($state) => $state === 'cash' ? 'warning' : 'info'),

                Tables\Columns\TextColumn::make('partner_info')
                    ->label('Đối tác liên quan')
                    ->state(function (CashTransaction $record) {
                        if ($record->customer) {
                            return "Khách: {$record->customer->name}";
                        }
                        if ($record->supplier) {
                            return "NCC: {$record->supplier->name}";
                        }
                        return '—';
                    }),

                Tables\Columns\TextColumn::make('status')
                    ->label('Trạng thái')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state === 'completed' ? 'Hoàn tất' : 'Đã hủy')
                    ->icon(fn ($state) => $state === 'completed' ? 'heroicon-m-check-circle' : 'heroicon-m-x-circle')
                    ->color(fn ($state) => $state === 'completed' ? 'success' : 'gray'),

                Tables\Columns\TextColumn::make('creator.name')
                    ->label('Người lập')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('performed_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Loại phiếu')
                    ->options([
                        'receipt' => 'Phiếu thu (Tiền vào)',
                        'payment' => 'Phiếu chi (Tiền ra)',
                    ]),

                Tables\Filters\SelectFilter::make('payment_method')
                    ->label('Hình thức')
                    ->options([
                        'cash' => 'Tiền mặt',
                        'bank_transfer' => 'Chuyển khoản',
                    ]),

                Tables\Filters\Filter::make('today')
                    ->label('Hôm nay')
                    ->query(fn (Builder $query) => $query->whereDate('performed_at', today())),
            ])
            ->actions([
                Tables\Actions\Action::make('cancel')
                    ->label('Hủy phiếu')
                    ->icon('heroicon-m-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading(fn (CashTransaction $record) => "Xác nhận hủy phiếu {$record->code}")
                    ->modalDescription('CẢNH BÁO: Khi hủy phiếu này, nếu là phiếu thu nợ hoặc chi nợ, công nợ của Khách hàng hoặc NCC sẽ được hoàn tác tự động.')
                    ->form([
                        Forms\Components\Textarea::make('reason')
                            ->label('Lý do hủy phiếu *')
                            ->required(),
                    ])
                    ->visible(fn (CashTransaction $record) => $record->isCompleted() && (auth()->user()?->isAdmin() ?? false))
                    ->action(function (CashTransaction $record, array $data) {
                        try {
                            app(CashbookService::class)->cancelTransaction($record, $data['reason']);
                            Notification::make()
                                ->title('Đã hủy phiếu thành công')
                                ->body("Phiếu {$record->code} đã được hủy và hoàn tác công nợ tương ứng.")
                                ->success()
                                ->send();
                        } catch (\Throwable $e) {
                            Notification::make()
                                ->title('Lỗi khi hủy phiếu')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->emptyStateHeading('Chưa có phiếu thu chi nào trong sổ quỹ')
            ->emptyStateDescription('Tạo phiếu thu hoặc phiếu chi đầu tiên để theo dõi dòng tiền mặt và công nợ.')
            ->emptyStateIcon('heroicon-o-banknotes');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['customer', 'supplier', 'creator']);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCashTransactions::route('/'),
            'create' => Pages\CreateCashTransaction::route('/create'),
        ];
    }
}
