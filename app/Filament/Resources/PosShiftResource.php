<?php

namespace App\Filament\Resources;

use App\Enums\PosShiftStatus;
use App\Filament\Resources\PosShiftResource\Pages;
use App\Models\PosShift;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PosShiftResource extends Resource
{
    protected static ?string $model = PosShift::class;

    protected static ?string $navigationIcon = 'heroicon-o-calculator';

    protected static ?string $navigationGroup = 'Bán hàng';

    protected static ?string $navigationLabel = 'Ca thu ngân POS';

    protected static ?string $modelLabel = 'Ca làm việc';

    protected static ?string $pluralModelLabel = 'Quản lý ca làm việc POS';

    protected static ?int $navigationSort = 3;

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::where('status', PosShiftStatus::Open->value)->orWhere('status', 'open')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->isCashier() ?? false;
    }

    public static function canCreate(): bool
    {
        return false;
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

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Thông tin ca làm việc')
                    ->icon('heroicon-o-user')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('cashier_id')
                            ->label('Thu ngân phụ trách')
                            ->relationship('cashier', 'name')
                            ->disabled(),

                        Forms\Components\TextInput::make('status')
                            ->label('Trạng thái')
                            ->formatStateUsing(fn ($state) => ($state === PosShiftStatus::Closed || $state === 'closed') ? 'Đã đóng ca' : 'Đang mở')
                            ->disabled(),

                        Forms\Components\DateTimePicker::make('opened_at')
                            ->label('Thời gian mở ca')
                            ->disabled(),

                        Forms\Components\DateTimePicker::make('closed_at')
                            ->label('Thời gian đóng ca')
                            ->disabled(),
                    ]),

                Forms\Components\Section::make('Đối soát tài chính & Tiền mặt két')
                    ->icon('heroicon-o-banknotes')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('opening_cash')
                            ->label('Tiền mặt đầu ca')
                            ->numeric()
                            ->prefix('₫')
                            ->disabled(),

                        Forms\Components\TextInput::make('cash_sales')
                            ->label('Tiền mặt bán lẻ thu trong ca')
                            ->numeric()
                            ->prefix('₫')
                            ->disabled(),

                        Forms\Components\TextInput::make('transfer_sales')
                            ->label('Doanh thu chuyển khoản VietQR')
                            ->numeric()
                            ->prefix('₫')
                            ->disabled(),

                        Forms\Components\TextInput::make('total_sales')
                            ->label('Tổng doanh thu bán hàng')
                            ->numeric()
                            ->prefix('₫')
                            ->disabled(),

                        Forms\Components\TextInput::make('expected_cash')
                            ->label('Tiền mặt lý thuyết két (= Đầu ca + Tiền mặt thu)')
                            ->numeric()
                            ->prefix('₫')
                            ->disabled(),

                        Forms\Components\TextInput::make('counted_cash')
                            ->label('Tiền mặt kiểm đếm thực tế')
                            ->numeric()
                            ->prefix('₫')
                            ->disabled(),

                        Forms\Components\TextInput::make('difference')
                            ->label('Chênh lệch tiền mặt (= Thực tế − Lý thuyết)')
                            ->numeric()
                            ->prefix('₫')
                            ->disabled(),

                        Forms\Components\TextInput::make('order_count')
                            ->label('Tổng số đơn trong ca')
                            ->disabled(),

                        Forms\Components\Textarea::make('notes')
                            ->label('Ghi chú / Giải trình chênh lệch')
                            ->columnSpanFull()
                            ->disabled(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('Mã ca')
                    ->formatStateUsing(fn ($state) => "#{$state}")
                    ->sortable(),

                Tables\Columns\TextColumn::make('cashier.name')
                    ->label('Thu ngân')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Trạng thái')
                    ->badge()
                    ->colors([
                        'warning' => fn ($state) => $state === PosShiftStatus::Open || $state === 'open',
                        'success' => fn ($state) => $state === PosShiftStatus::Closed || $state === 'closed',
                    ])
                    ->formatStateUsing(fn ($state) => ($state === PosShiftStatus::Closed || $state === 'closed') ? 'Đã đóng' : 'Đang mở'),

                Tables\Columns\TextColumn::make('opened_at')
                    ->label('Mở ca')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('closed_at')
                    ->label('Đóng ca')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('Chưa đóng')
                    ->sortable(),

                Tables\Columns\TextColumn::make('opening_cash')
                    ->label('Đầu ca')
                    ->money('VND')
                    ->sortable(),

                Tables\Columns\TextColumn::make('cash_sales')
                    ->label('Tiền mặt thu')
                    ->money('VND')
                    ->placeholder('0 ₫'),

                Tables\Columns\TextColumn::make('transfer_sales')
                    ->label('CK VietQR')
                    ->money('VND')
                    ->placeholder('0 ₫'),

                Tables\Columns\TextColumn::make('counted_cash')
                    ->label('Thực đếm')
                    ->money('VND')
                    ->placeholder('Chưa chốt'),

                Tables\Columns\TextColumn::make('difference')
                    ->label('Chênh lệch')
                    ->money('VND')
                    ->placeholder('0 ₫')
                    ->badge()
                    ->color(fn ($state) => (float) $state == 0 ? 'success' : ((float) $state > 0 ? 'warning' : 'danger')),

                Tables\Columns\TextColumn::make('order_count')
                    ->label('Số đơn')
                    ->badge()
                    ->color('gray')
                    ->placeholder('0'),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Trạng thái ca')
                    ->options([
                        'open' => 'Đang mở',
                        'closed' => 'Đã đóng',
                    ]),

                Tables\Filters\SelectFilter::make('cashier_id')
                    ->label('Thu ngân')
                    ->relationship('cashier', 'name'),
            ])
            ->actions([
                Tables\Actions\Action::make('print')
                    ->label('In K80')
                    ->icon('heroicon-o-printer')
                    ->color('info')
                    ->url(fn (PosShift $record) => route('print.pos-shift', $record->id))
                    ->openUrlInNewTab(),

                Tables\Actions\ViewAction::make()
                    ->label('Xem chi tiết'),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with('cashier');
        $user = auth()->user();
        if ($user && ! $user->isAdmin()) {
            $query->where('cashier_id', $user->id);
        }
        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosShifts::route('/'),
            'view' => Pages\ViewPosShift::route('/{record}'),
        ];
    }
}
