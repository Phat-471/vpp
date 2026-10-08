<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Chăm sóc khách hàng';

    protected static ?string $navigationLabel = 'Khách hàng';

    protected static ?string $modelLabel = 'Khách hàng';

    protected static ?string $pluralModelLabel = 'Danh sách khách hàng';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Tên khách hàng hoặc đơn vị')
                    ->required()
                    ->maxLength(100),

                Forms\Components\TextInput::make('phone')
                    ->label('Số điện thoại')
                    ->tel()
                    ->required()
                    ->maxLength(20),

                Forms\Components\TextInput::make('address')
                    ->label('Địa chỉ')
                    ->maxLength(255)
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('debt_balance')
                    ->label('Công nợ hiện tại')
                    ->numeric()
                    ->prefix('₫')
                    ->default(0),

                Forms\Components\Textarea::make('notes')
                    ->label('Ghi chú')
                    ->rows(2)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Họ tên')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Số điện thoại')
                    ->searchable()
                    ->description(fn (Customer $c) => $c->zalo_name ? "Zalo: {$c->zalo_name}" : null),

                Tables\Columns\TextColumn::make('activation_status')
                    ->label('Trạng thái tài khoản')
                    ->badge()
                    ->state(fn (Customer $c) => $c->isActivated() ? 'Đã kích hoạt' : 'Chưa kích hoạt')
                    ->color(fn (Customer $c) => $c->isActivated() ? 'success' : 'warning')
                    ->icon(fn (Customer $c) => $c->isActivated() ? 'heroicon-m-check-badge' : 'heroicon-m-clock'),

                Tables\Columns\TextColumn::make('address')
                    ->label('Địa chỉ')
                    ->limit(30),

                Tables\Columns\TextColumn::make('repair_tickets_count')
                    ->label('Lượt sửa chữa')
                    ->counts('repairTickets')
                    ->badge(),

                Tables\Columns\TextColumn::make('orders_count')
                    ->label('Đơn hàng')
                    ->counts('orders')
                    ->badge(),

                Tables\Columns\TextColumn::make('debt_balance')
                    ->label('Công nợ')
                    ->money('VND')
                    ->color(fn ($state) => $state > 0 ? 'danger' : 'gray'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
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
