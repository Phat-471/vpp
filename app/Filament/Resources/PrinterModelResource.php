<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PrinterModelResource\Pages;
use App\Models\PrinterModel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PrinterModelResource extends Resource
{
    protected static ?string $model = PrinterModel::class;

    protected static ?string $navigationIcon = 'heroicon-o-printer';

    protected static ?string $navigationGroup = 'Dịch vụ kỹ thuật';

    protected static ?string $navigationLabel = 'Dòng máy in phổ biến';

    protected static ?string $modelLabel = 'Dòng máy in';

    protected static ?string $pluralModelLabel = 'Danh mục dòng máy in';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('brand')
                    ->label('Hãng máy in')
                    ->placeholder('Canon, HP, Brother, Epson...')
                    ->required(),

                Forms\Components\TextInput::make('model_name')
                    ->label('Tên dòng máy')
                    ->placeholder('LBP 2900, 107a, HL-L2321D...')
                    ->required(),

                Forms\Components\TextInput::make('printer_type')
                    ->label('Loại máy')
                    ->default('Laser đen trắng')
                    ->required(),

                Forms\Components\TextInput::make('compatible_cartridges')
                    ->label('Mã hộp mực hoặc cụm trống tương thích')
                    ->placeholder('Ví dụ: Cartridge 12A/303, TN-2385...')
                    ->columnSpanFull(),

                Forms\Components\Textarea::make('notes')
                    ->label('Ghi chú kỹ thuật')
                    ->placeholder('Lưu ý khi thay linh kiện, đặt lại bánh răng hoặc mã chip...')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('brand')
                    ->label('Hãng')
                    ->badge()
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('model_name')
                    ->label('Tên dòng máy')
                    ->weight('bold')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('printer_type')
                    ->label('Loại máy')
                    ->searchable(),

                Tables\Columns\TextColumn::make('compatible_cartridges')
                    ->label('Hộp mực tương thích')
                    ->wrap(),

                Tables\Columns\TextColumn::make('repair_tickets_count')
                    ->label('Lượt sửa')
                    ->counts('repairTickets')
                    ->badge(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('brand')
                    ->label('Lọc theo hãng')
                    ->options([
                        'Canon' => 'Canon',
                        'HP' => 'HP',
                        'Brother' => 'Brother',
                        'Epson' => 'Epson',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPrinterModels::route('/'),
            'create' => Pages\CreatePrinterModel::route('/create'),
            'edit' => Pages\EditPrinterModel::route('/{record}/edit'),
        ];
    }
}
