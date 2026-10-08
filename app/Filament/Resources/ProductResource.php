<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Category;
use App\Models\PrinterModel;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationGroup = 'Kho & sản phẩm';

    protected static ?string $navigationLabel = 'Sản phẩm và tồn kho';

    protected static ?string $modelLabel = 'Sản phẩm';

    protected static ?string $pluralModelLabel = 'Danh sách sản phẩm';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Product Details')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Thông tin chung')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Forms\Components\Grid::make(3)
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->label('Tên sản phẩm')
                                            ->required()
                                            ->maxLength(255)
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state)))
                                            ->columnSpan(2),

                                        Forms\Components\TextInput::make('slug')
                                            ->label('Đường dẫn sản phẩm')
                                            ->required()
                                            ->maxLength(255)
                                            ->unique(Product::class, 'slug', ignoreRecord: true),

                                        Forms\Components\TextInput::make('sku')
                                            ->label('Mã SKU')
                                            ->required()
                                            ->unique(Product::class, 'sku', ignoreRecord: true)
                                            ->default(fn () => 'SP-' . strtoupper(Str::random(6))),

                                        Forms\Components\TextInput::make('barcode')
                                            ->label('Mã vạch')
                                            ->maxLength(60),

                                        Forms\Components\Select::make('category_id')
                                            ->label('Danh mục hàng')
                                            ->relationship('category', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->required(),
                                    ]),

                                Forms\Components\Grid::make(3)
                                    ->schema([
                                        Forms\Components\TextInput::make('cost_price')
                                            ->label('Giá vốn (Giá nhập)')
                                            ->numeric()
                                            ->prefix('₫')
                                            ->default(0)
                                            ->required(),

                                        Forms\Components\TextInput::make('retail_price')
                                            ->label('Giá bán lẻ')
                                            ->numeric()
                                            ->prefix('₫')
                                            ->default(0)
                                            ->required(),

                                        Forms\Components\TextInput::make('base_unit')
                                            ->label('Đơn vị tính cơ bản')
                                            ->placeholder('Cái, Ram, Hộp, Chai...')
                                            ->default('Cái')
                                            ->required(),

                                        Forms\Components\TextInput::make('stock_quantity')
                                            ->label('Số lượng tồn kho (theo đơn vị cơ bản)')
                                            ->numeric()
                                            ->default(0)
                                            ->required(),

                                        Forms\Components\TextInput::make('low_stock_threshold')
                                            ->label('Ngưỡng cảnh báo tồn kho')
                                            ->numeric()
                                            ->default(5)
                                            ->helperText('Báo động khi tồn kho xuống dưới mức này'),

                                        Forms\Components\Toggle::make('is_service_part')
                                            ->label('Sử dụng trong dịch vụ sửa chữa hoặc nạp mực')
                                            ->inline(false)
                                            ->helperText('Sản phẩm này là linh kiện hoặc mực nạp dùng khi sửa chữa'),

                                        Forms\Components\Toggle::make('is_active')
                                            ->label('Đang kinh doanh')
                                            ->default(true)
                                            ->inline(false),
                                    ]),

                                Forms\Components\Textarea::make('description')
                                    ->label('Mô tả chi tiết / Ghi chú')
                                    ->rows(3)
                                    ->columnSpanFull(),
                            ]),

                        Forms\Components\Tabs\Tab::make('Đơn vị tính quy đổi')
                            ->icon('heroicon-o-arrows-right-left')
                            ->badge(fn ($record) => $record?->units()->count() ?: null)
                            ->schema([
                                Forms\Components\Section::make('Quy đổi đơn vị (bán sỉ và bán lẻ)')
                                    ->description('Có thể bán theo thùng, lốc hoặc hộp. Hệ thống sẽ quy đổi số lượng và trừ kho theo đơn vị cơ bản. Ví dụ: 1 thùng = 5 ram.')
                                    ->schema([
                                        Forms\Components\Repeater::make('units')
                                            ->relationship('units')
                                            ->schema([
                                                Forms\Components\TextInput::make('unit_name')
                                                    ->label('Tên đơn vị quy đổi')
                                                    ->placeholder('Ví dụ: Thùng (5 ram), lốc (10 cây)')
                                                    ->required(),

                                                Forms\Components\TextInput::make('conversion_rate')
                                                    ->label('Hệ số quy đổi')
                                                    ->helperText('1 đơn vị này = bao nhiêu đơn vị cơ bản')
                                                    ->numeric()
                                                    ->default(1)
                                                    ->required(),

                                                Forms\Components\TextInput::make('price')
                                                    ->label('Giá bán quy đổi')
                                                    ->numeric()
                                                    ->prefix('₫')
                                                    ->required(),

                                                Forms\Components\TextInput::make('barcode')
                                                    ->label('Mã vạch riêng (nếu có)')
                                                    ->maxLength(60),
                                            ])
                                            ->columns(4)
                                            ->defaultItems(0)
                                            ->addActionLabel('Thêm đơn vị quy đổi'),
                                    ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('Tương thích máy in')
                            ->icon('heroicon-o-printer')
                            ->badge(fn ($record) => $record?->compatiblePrinters()->count() ?: null)
                            ->schema([
                                Forms\Components\Section::make('Liên kết dòng máy in tương thích')
                                    ->description('Liên kết sản phẩm hoặc hộp mực với các dòng máy in tương thích để nhân viên tra cứu nhanh.')
                                    ->schema([
                                        Forms\Components\Select::make('compatiblePrinters')
                                            ->label('Các dòng máy in tương thích')
                                            ->relationship('compatiblePrinters', 'model_name')
                                            ->getOptionLabelFromRecordUsing(fn (PrinterModel $record) => "{$record->brand} {$record->model_name} ({$record->printer_type})")
                                            ->multiple()
                                            ->searchable()
                                            ->preload(),
                                    ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('Hình ảnh sản phẩm')
                            ->icon('heroicon-o-photo')
                            ->schema([
                                Forms\Components\FileUpload::make('image_path')
                                    ->label('Ảnh chụp sản phẩm')
                                    ->image()
                                    ->directory('products')
                                    ->imageResizeMode('cover')
                                    ->imageCropAspectRatio('1:1')
                                    ->imageResizeTargetWidth('1200')
                                    ->imageResizeTargetHeight('1200')
                                    ->afterStateUpdated(fn ($state, callable $set) => $set('has_custom_image', !empty($state))),

                                Forms\Components\Toggle::make('has_custom_image')
                                    ->label('Đã có hình ảnh thực tế')
                                    ->helperText('Tự động bật sau khi tải ảnh lên. Dùng bộ lọc để tìm sản phẩm chưa có ảnh.'),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image_path')
                    ->label('Ảnh')
                    ->circular()
                    ->defaultImageUrl(fn ($record) => $record->category?->placeholder_image ? asset($record->category->placeholder_image) : null),

                Tables\Columns\TextColumn::make('name')
                    ->label('Tên sản phẩm')
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->description(fn (Product $record) => "SKU: {$record->sku}" . ($record->barcode ? " | Vạch: {$record->barcode}" : '')),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Danh mục')
                    ->sortable()
                    ->badge(),

                Tables\Columns\TextColumn::make('retail_price')
                    ->label('Giá bán lẻ')
                    ->money('VND')
                    ->sortable(),

                Tables\Columns\TextColumn::make('stock_quantity')
                    ->label('Tồn kho')
                    ->sortable()
                    ->badge()
                    ->color(fn (Product $record) => $record->isLowStock() ? 'danger' : 'success')
                    ->formatStateUsing(fn (Product $record) => "{$record->stock_quantity} {$record->base_unit}"),

                Tables\Columns\IconColumn::make('has_custom_image')
                    ->label('Có ảnh')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->tooltip(fn ($record) => $record->has_custom_image ? 'Đã có ảnh thật' : 'Đang dùng ảnh mẫu mặc định'),

                Tables\Columns\IconColumn::make('is_service_part')
                    ->label('Linh kiện')
                    ->boolean()
                    ->trueIcon('heroicon-o-wrench-screwdriver')
                    ->falseIcon('heroicon-o-minus')
                    ->tooltip('Linh kiện / mực máy in'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Bán')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\Filter::make('missing_image')
                    ->label('📷 Chưa có ảnh sản phẩm')
                    ->query(fn (Builder $query) => $query->where('has_custom_image', false)),

                Tables\Filters\Filter::make('low_stock')
                    ->label('⚠️ Sắp hết hàng')
                    ->query(fn (Builder $query) => $query->whereColumn('stock_quantity', '<=', 'low_stock_threshold')),

                Tables\Filters\Filter::make('is_service_part')
                    ->label('🔧 Linh kiện và mực máy in')
                    ->query(fn (Builder $query) => $query->where('is_service_part', true)),

                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Danh mục')
                    ->relationship('category', 'name'),
            ])
            ->actions([
                Tables\Actions\Action::make('quick_upload_image')
                    ->label('Chụp hoặc đổi ảnh')
                    ->icon('heroicon-o-camera')
                    ->color('info')
                    ->form([
                        Forms\Components\FileUpload::make('image_path')
                            ->label('Chụp ảnh bằng camera hoặc chọn tệp từ thiết bị')
                            ->image()
                            ->disk('public')
                            ->directory('products')
                            ->required(),
                    ])
                    ->action(function (Product $record, array $data) {
                        $record->update([
                            'image_path' => $data['image_path'],
                            'has_custom_image' => true,
                        ]);
                        \Filament\Notifications\Notification::make()
                            ->title('Đã cập nhật ảnh sản phẩm')
                            ->body("Ảnh sản phẩm '{$record->name}' đã được tối ưu hóa sang WebP.")
                            ->success()
                            ->send();
                    }),

                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                Tables\Actions\Action::make('download_template')
                    ->label('Tải tệp mẫu CSV')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    ->action(function () {
                        $fullFile = storage_path('app/public/imports/mau-nhap-1000-sku-vpp.csv');
                        if (file_exists($fullFile)) {
                            return response()->download($fullFile, 'mau-nhap-1000-sku-vpp.csv', [
                                'Content-Type' => 'text/csv; charset=UTF-8',
                            ]);
                        }
                        $csv = (new \App\Services\ProductImportService())->generateSampleCsv();
                        return response()->streamDownload(function () use ($csv) {
                            echo "\xEF\xBB\xBF"; // UTF-8 BOM for Excel compatibility
                            echo $csv;
                        }, 'mau-nhap-1000-sku-vpp.csv', [
                            'Content-Type' => 'text/csv; charset=UTF-8',
                        ]);
                    }),

                Tables\Actions\Action::make('import_excel')
                    ->label('Nhập 1.000 SKU từ CSV')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('success')
                    ->form([
                        Forms\Components\FileUpload::make('file')
                            ->label('Tệp dữ liệu CSV (.csv)')
                            ->disk('public')
                            ->directory('imports')
                            ->acceptedFileTypes(['text/csv', 'text/plain', 'application/csv'])
                            ->required(),
                        Forms\Components\Placeholder::make('note')
                            ->content('Hệ thống hỗ trợ nhập tự động các cột: ma_sku, ten_san_pham, danh_muc, dvt, gia_nhap, gia_ban, ton_kho, ma_vach, linh_kien, quy_doi, hinh_anh (link ảnh trực tuyến hoặc tên tệp). Khi có link ảnh, hệ thống tự động tải và nén sang WebP; nếu bỏ trống ảnh sẽ tự động gán ảnh đại diện theo danh mục.'),
                    ])
                    ->action(function (array $data) {
                        $filePath = storage_path('app/public/' . $data['file']);
                        $service = new \App\Services\ProductImportService();
                        $result = $service->importCsv($filePath);

                        if ($result['success'] > 0) {
                            Notification::make()
                                ->title('Đã nhập dữ liệu sản phẩm')
                                ->body("Đã đồng bộ {$result['success']}/{$result['total']} sản phẩm vào hệ thống kho.")
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Nhập dữ liệu thất bại')
                                ->body(implode("\n", array_slice($result['errors'], 0, 3)))
                                ->danger()
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
