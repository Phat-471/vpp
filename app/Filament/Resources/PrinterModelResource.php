<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PrinterModelResource\Pages;
use App\Filament\Resources\PrinterModelResource\RelationManagers\ProductsRelationManager;
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

    protected static ?string $navigationGroup = 'Kỹ thuật';

    protected static ?string $navigationLabel = 'Dòng máy in';

    protected static ?string $modelLabel = 'Dòng máy in';

    protected static ?string $pluralModelLabel = 'Danh mục dòng máy in & Hộp mực';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Thông tin định danh dòng máy in')
                    ->description('Hãng sản xuất, tên model và công nghệ in ấn')
                    ->icon('heroicon-o-printer')
                    ->columns(3)
                    ->schema([
                        Forms\Components\TextInput::make('brand')
                            ->label('Hãng máy in')
                            ->placeholder('Canon, HP, Brother, Epson...')
                            ->datalist([
                                'Canon',
                                'HP',
                                'Brother',
                                'Epson',
                                'Pantum',
                                'Fuji Xerox',
                                'Ricoh',
                                'Samsung',
                                'Xprinter',
                            ])
                            ->helperText('Chọn gợi ý hoặc tự nhập tên hãng')
                            ->required(),

                        Forms\Components\TextInput::make('model_name')
                            ->label('Tên dòng máy (Model)')
                            ->placeholder('VD: LBP 2900, 107a, HL-L2321D, EcoTank L3210...')
                            ->required(),

                        Forms\Components\Select::make('printer_type')
                            ->label('Công nghệ & Loại máy in')
                            ->options([
                                'Laser đen trắng đơn năng' => 'Laser đen trắng đơn năng',
                                'Laser đen trắng đa năng (In/Scan/Copy)' => 'Laser đen trắng đa năng (In/Scan/Copy)',
                                'Laser màu đơn năng' => 'Laser màu đơn năng',
                                'Laser màu đa năng' => 'Laser màu đa năng',
                                'Phun màu liên tục (EcoTank/Ink Tank)' => 'Phun màu liên tục (EcoTank/Ink Tank)',
                                'Phun màu hộp mực rời' => 'Phun màu hộp mực rời',
                                'Máy in nhiệt Bill / POS (K80 / K58)' => 'Máy in nhiệt Bill / POS (K80 / K58)',
                                'Máy in kim hóa đơn' => 'Máy in kim hóa đơn',
                            ])
                            ->default('Laser đen trắng đơn năng')
                            ->searchable()
                            ->required(),
                    ]),

                Forms\Components\Section::make('Tiêu chuẩn Hộp mực & Lưu ý kỹ thuật')
                    ->description('Mã hộp mực tiêu chuẩn và hướng dẫn thao tác tháo lắp, nạp mực')
                    ->icon('heroicon-o-wrench-screwdriver')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('compatible_cartridges')
                            ->label('Mã hộp mực hoặc cụm trống tương thích chuẩn')
                            ->placeholder('Ví dụ: Cartridge 12A / 303, HP 107A (W1107A), TN-2385 / DR-2385, Mực nước Epson 003...')
                            ->helperText('Nhập mã mực chuẩn của hãng để nhân viên tra cứu nhanh khi khách hỏi')
                            ->columnSpanFull()
                            ->required(),

                        Forms\Components\Textarea::make('notes')
                            ->label('Ghi chú kỹ thuật chuyên sâu')
                            ->placeholder('Lưu ý khi đổ mực, bánh răng reset, loại trống drum sử dụng, chip đếm trang...')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('brand')
                    ->label('Hãng máy')
                    ->badge()
                    ->colors([
                        'danger' => 'Canon',
                        'info' => 'HP',
                        'primary' => 'Brother',
                        'warning' => 'Epson',
                        'success' => 'Pantum',
                        'gray' => fn ($state) => !in_array($state, ['Canon', 'HP', 'Brother', 'Epson', 'Pantum']),
                    ])
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('model_name')
                    ->label('Tên dòng máy')
                    ->weight('bold')
                    ->description(fn (PrinterModel $record) => $record->printer_type)
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('compatible_cartridges')
                    ->label('Hộp mực tương thích')
                    ->weight('semibold')
                    ->wrap()
                    ->searchable(),

                Tables\Columns\TextColumn::make('products_count')
                    ->label('Linh kiện kho')
                    ->counts('products')
                    ->badge()
                    ->color(fn ($state) => $state > 0 ? 'success' : 'gray')
                    ->prefix('📦 ')
                    ->sortable(),

                Tables\Columns\TextColumn::make('repair_tickets_count')
                    ->label('Lượt sửa')
                    ->counts('repairTickets')
                    ->badge()
                    ->color(fn ($state) => $state > 0 ? 'warning' : 'gray')
                    ->prefix('🔧 ')
                    ->sortable(),
            ])
            ->defaultSort('brand', 'asc')
            ->filters([
                Tables\Filters\SelectFilter::make('brand')
                    ->label('Lọc theo Hãng')
                    ->options([
                        'Canon' => 'Canon',
                        'HP' => 'HP',
                        'Brother' => 'Brother',
                        'Epson' => 'Epson',
                        'Pantum' => 'Pantum',
                    ]),

                Tables\Filters\SelectFilter::make('printer_type')
                    ->label('Lọc theo Công nghệ')
                    ->options([
                        'Laser đen trắng đơn năng' => 'Laser đen trắng đơn năng',
                        'Laser đen trắng đa năng (In/Scan/Copy)' => 'Laser đen trắng đa năng',
                        'Laser màu đơn năng' => 'Laser màu đơn năng',
                        'Phun màu liên tục (EcoTank/Ink Tank)' => 'Phun màu liên tục',
                        'Máy in nhiệt Bill / POS (K80 / K58)' => 'Máy in nhiệt Bill POS',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('view_products')
                    ->label('Linh kiện kho')
                    ->icon('heroicon-o-wrench-screwdriver')
                    ->color('info')
                    ->modalHeading(fn (PrinterModel $record) => "Linh kiện & Hộp mực tương thích: {$record->full_name}")
                    ->modalCancelActionLabel('Đóng')
                    ->modalSubmitAction(false)
                    ->modalContent(fn (PrinterModel $record) => view('filament.modals.printer-compatible-products', ['record' => $record])),

                Tables\Actions\EditAction::make()
                    ->label('Chỉnh sửa'),
                Tables\Actions\DeleteAction::make()
                    ->label('Xóa')
                    ->modalHeading('Xác nhận xóa dòng máy in')
                    ->modalDescription('Bạn có chắc chắn muốn xóa dòng máy in này khỏi danh mục hệ thống?'),
            ])
            ->emptyStateHeading('Chưa có dòng máy in nào')
            ->emptyStateDescription('Bấm "Tạo mới dòng máy in" để thêm các dòng máy in phổ biến.');
    }

    public static function getRelations(): array
    {
        return [
            ProductsRelationManager::class,
        ];
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
