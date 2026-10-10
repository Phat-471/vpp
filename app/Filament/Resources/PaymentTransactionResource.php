<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentTransactionResource\Pages;
use App\Models\PaymentTransaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PaymentTransactionResource extends Resource
{
    protected static ?string $model = PaymentTransaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationGroup = 'Tài chính';

    protected static ?string $navigationLabel = 'Giao dịch';

    protected static ?string $modelLabel = 'Giao dịch ngân hàng';

    protected static ?string $pluralModelLabel = 'Nhật ký giao dịch chuyển khoản';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('gateway')
                    ->label('Cổng thanh toán')
                    ->required(),
                Forms\Components\TextInput::make('reference_code')
                    ->label('Mã tham chiếu đơn / phiếu')
                    ->required(),
                Forms\Components\TextInput::make('amount')
                    ->label('Số tiền')
                    ->numeric()
                    ->prefix('₫')
                    ->required(),
                Forms\Components\TextInput::make('account_number')
                    ->label('Số tài khoản nhận'),
                Forms\Components\TextInput::make('bank_brand_name')
                    ->label('Ngân hàng'),
                Forms\Components\Textarea::make('description')
                    ->label('Nội dung chuyển khoản'),
                Forms\Components\DateTimePicker::make('transaction_time')
                    ->label('Thời gian giao dịch'),
                Forms\Components\Select::make('status')
                    ->label('Kết quả đối soát')
                    ->options([
                        'processed' => 'Đã khớp & ghi nhận thanh toán',
                        'unmatched' => 'Không tìm thấy đơn/phiếu khớp',
                        'ignored' => 'Bỏ qua',
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('gateway')
                    ->label('Cổng')
                    ->badge()
                    ->colors([
                        'primary' => 'sepay',
                        'warning' => 'casso',
                    ])
                    ->formatStateUsing(fn ($state) => strtoupper($state)),

                Tables\Columns\TextColumn::make('reference_code')
                    ->label('Mã đơn / Phiếu')
                    ->weight('bold')
                    ->searchable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Số tiền nhận')
                    ->money('VND')
                    ->sortable()
                    ->color('success')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('bank_brand_name')
                    ->label('Ngân hàng')
                    ->description(fn (PaymentTransaction $r) => $r->account_number),

                Tables\Columns\TextColumn::make('description')
                    ->label('Nội dung chuyển khoản')
                    ->searchable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Trạng thái')
                    ->badge()
                    ->colors([
                        'success' => 'processed',
                        'danger' => 'unmatched',
                        'gray' => 'ignored',
                    ])
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'processed' => 'Khớp thành công',
                        'unmatched' => 'Chưa khớp',
                        default => 'Bỏ qua',
                    }),

                Tables\Columns\TextColumn::make('transaction_time')
                    ->label('Thời gian nhận')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('gateway')
                    ->options([
                        'sepay' => 'SePay',
                        'casso' => 'Casso',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'processed' => 'Đã khớp thành công',
                        'unmatched' => 'Chưa khớp đơn',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\Action::make('simulate_webhook')
                    ->label('⚡ Thử nghiệm tiền vào')
                    ->icon('heroicon-o-bolt')
                    ->color('warning')
                    ->form([
                        Forms\Components\Select::make('gateway')
                            ->label('Cổng thanh toán')
                            ->options([
                                'sepay' => 'Cổng SePay',
                                'casso' => 'Cổng Casso',
                            ])
                            ->default('sepay')
                            ->required(),

                        Forms\Components\TextInput::make('reference_code')
                            ->label('Mã đơn hoặc mã phiếu sửa chữa')
                            ->placeholder('VD: SC260001 hoặc HD260001')
                            ->required(),

                        Forms\Components\TextInput::make('amount')
                            ->label('Số tiền chuyển khoản (VNĐ)')
                            ->numeric()
                            ->prefix('₫')
                            ->default(175000)
                            ->required(),

                        Forms\Components\TextInput::make('content')
                            ->label('Nội dung chuyển tiền từ ngân hàng')
                            ->default(fn (callable $get) => 'CHUYEN TIEN THANH TOAN ' . ($get('reference_code') ?: 'SC260001')),
                    ])
                    ->action(function (array $data) {
                        $tx = PaymentTransaction::create([
                            'gateway' => $data['gateway'],
                            'transaction_id' => 'TEST_' . strtoupper(uniqid()),
                            'reference_code' => $data['reference_code'],
                            'amount' => $data['amount'],
                            'account_number' => '190333888999',
                            'bank_brand_name' => 'Techcombank (Simulated)',
                            'description' => $data['content'],
                            'transaction_time' => now(),
                            'raw_payload' => $data,
                            'status' => 'processed',
                        ]);

                        $matched = $tx->matchAndApply();

                        if ($matched) {
                            Notification::make()
                                ->title('Xác nhận thanh toán thành công')
                                ->body("Đơn/Phiếu {$data['reference_code']} đã được ghi nhận thanh toán +{$data['amount']} đ.")
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Đã ghi nhận giao dịch nhưng chưa tìm thấy đơn hàng hoặc phiếu sửa chữa tương ứng')
                                ->body("Không tìm thấy đơn hàng hoặc phiếu sửa nào có mã {$data['reference_code']}.")
                                ->warning()
                                ->send();
                        }
                    }),
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
            'index' => Pages\ListPaymentTransactions::route('/'),
        ];
    }
}
