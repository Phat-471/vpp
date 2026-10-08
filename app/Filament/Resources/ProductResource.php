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
                Forms\Components\Grid::make(3)
                    ->schema([
                        // CỘT CHÍNH (2/3 chiều rộng): Thông tin sản phẩm, Giá & Tồn kho, Đơn vị quy đổi
                        Forms\Components\Group::make([
                            Forms\Components\Section::make('Thông tin sản phẩm')
                                ->icon('heroicon-o-cube')
                                ->schema([
                                    Forms\Components\TextInput::make('name')
                                        ->label('Tên sản phẩm *')
                                        ->placeholder('VD: Giấy in A4 Double A 70gsm, Hộp mực Canon 2900...')
                                        ->required()
                                        ->maxLength(255)
                                        ->live(onBlur: true)
                                        ->afterStateUpdated(fn ($state, callable $set, $operation) => $operation === 'create' ? $set('slug', Str::slug($state)) : null)
                                        ->columnSpanFull(),

                                    Forms\Components\Grid::make(2)
                                        ->schema([
                                            Forms\Components\Select::make('category_id')
                                                ->label('Danh mục hàng *')
                                                ->relationship('category', 'name')
                                                ->searchable()
                                                ->preload()
                                                ->createOptionForm([
                                                    Forms\Components\TextInput::make('name')
                                                        ->label('Tên danh mục mới')
                                                        ->required()
                                                        ->live(onBlur: true)
                                                        ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))),
                                                    Forms\Components\TextInput::make('slug')
                                                        ->label('Đường dẫn danh mục')
                                                        ->required(),
                                                ])
                                                ->required(),

                                            Forms\Components\TextInput::make('sku')
                                                ->label('Mã SKU *')
                                                ->required()
                                                ->unique(Product::class, 'sku', ignoreRecord: true)
                                                ->default(fn () => 'SP-' . strtoupper(Str::random(6)))
                                                ->suffixAction(
                                                    Forms\Components\Actions\Action::make('generate_sku')
                                                        ->icon('heroicon-m-arrow-path')
                                                        ->tooltip('Tạo mã SKU ngẫu nhiên mới')
                                                        ->action(fn (callable $set) => $set('sku', 'SP-' . strtoupper(Str::random(6))))
                                                ),
                                        ]),

                                    Forms\Components\Grid::make(2)
                                        ->schema([
                                            Forms\Components\TextInput::make('barcode')
                                                ->label('Mã vạch (Barcode)')
                                                ->placeholder('Quét máy quét hoặc nhập mã vạch...')
                                                ->maxLength(60),

                                            Forms\Components\TextInput::make('slug')
                                                ->label('Đường dẫn SEO (Slug) *')
                                                ->required()
                                                ->maxLength(255)
                                                ->unique(Product::class, 'slug', ignoreRecord: true),
                                        ]),

                                    Forms\Components\Textarea::make('description')
                                        ->label('Mô tả chi tiết & Thông số')
                                        ->placeholder('Quy cách đóng gói, định lượng, xuất xứ hoặc lưu ý sử dụng...')
                                        ->rows(3)
                                        ->columnSpanFull(),
                                ]),

                            Forms\Components\Section::make('Giá bán & Quản lý tồn kho')
                                ->icon('heroicon-o-banknotes')
                                ->schema([
                                    Forms\Components\Grid::make(3)
                                        ->schema([
                                            Forms\Components\TextInput::make('cost_price')
                                                ->label('Giá vốn (nhập)')
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
                                                ->label('Đơn vị cơ bản')
                                                ->placeholder('Cái, Ram, Hộp, Cuộn...')
                                                ->default('Cái')
                                                ->required(),
                                        ]),

                                    Forms\Components\Grid::make(2)
                                        ->schema([
                                            Forms\Components\TextInput::make('stock_quantity')
                                                ->label('Số lượng tồn kho')
                                                ->numeric()
                                                ->default(0)
                                                ->suffix(fn ($get) => $get('base_unit') ?: 'đơn vị')
                                                ->required(),

                                            Forms\Components\TextInput::make('low_stock_threshold')
                                                ->label('Ngưỡng cảnh báo sắp hết')
                                                ->numeric()
                                                ->default(5)
                                                ->helperText('Báo động đỏ khi số lượng tồn xuống dưới mức này'),
                                        ]),
                                ]),

                            Forms\Components\Section::make('Đơn vị tính quy đổi & Bán sỉ (Thùng / Lốc / Hộp)')
                                ->icon('heroicon-o-arrows-right-left')
                                ->description('Thiết lập bán theo thùng hoặc lốc. Hệ thống sẽ tự động quy đổi và trừ kho theo đơn vị cơ bản.')
                                ->collapsible()
                                ->collapsed(fn ($record) => empty($record) || $record->units()->count() === 0)
                                ->schema([
                                    Forms\Components\Repeater::make('units')
                                        ->relationship('units')
                                        ->schema([
                                            Forms\Components\TextInput::make('unit_name')
                                                ->label('Tên đơn vị')
                                                ->placeholder('VD: Thùng (5 ram), Lốc (10 cây)')
                                                ->required(),

                                            Forms\Components\TextInput::make('conversion_rate')
                                                ->label('Hệ số quy đổi')
                                                ->helperText('1 đơn vị này = bao nhiêu đơn vị cơ bản')
                                                ->numeric()
                                                ->default(1)
                                                ->required(),

                                            Forms\Components\TextInput::make('price')
                                                ->label('Giá bán sỉ')
                                                ->numeric()
                                                ->prefix('₫')
                                                ->required(),

                                            Forms\Components\TextInput::make('barcode')
                                                ->label('Mã vạch riêng')
                                                ->maxLength(60),
                                        ])
                                        ->columns(4)
                                        ->defaultItems(0)
                                        ->addActionLabel('+ Thêm đơn vị quy đổi'),
                                ]),

                            Forms\Components\Section::make('Dòng máy in tương thích')
                                ->icon('heroicon-o-printer')
                                ->description('Liên kết sản phẩm linh kiện / hộp mực với các dòng máy in Canon, HP, Brother, Epson...')
                                ->collapsible()
                                ->collapsed(fn ($record) => empty($record) || $record->compatiblePrinters()->count() === 0)
                                ->schema([
                                    Forms\Components\Select::make('compatiblePrinters')
                                        ->label('Chọn các dòng máy in')
                                        ->relationship('compatiblePrinters', 'model_name')
                                        ->getOptionLabelFromRecordUsing(fn (PrinterModel $record) => "{$record->brand} {$record->model_name} ({$record->printer_type})")
                                        ->multiple()
                                        ->searchable()
                                        ->preload(),
                                ]),
                        ])->columnSpan(2),

                        // CỘT PHỤ (1/3 chiều rộng): Hình ảnh, Trạng thái kinh doanh, Xem nhanh
                        Forms\Components\Group::make([
                            Forms\Components\Section::make('Hình ảnh sản phẩm')
                                ->icon('heroicon-o-photo')
                                ->schema([
                                    Forms\Components\FileUpload::make('image_path')
                                        ->label('Ảnh chụp sản phẩm')
                                        ->image()
                                        ->disk('public')
                                        ->directory('products')
                                        ->imageCropAspectRatio('1:1')
                                        ->imageResizeMode('cover')
                                        ->imageResizeTargetWidth('800')
                                        ->imageResizeTargetHeight('800')
                                        ->afterStateUpdated(fn ($state, callable $set) => $set('has_custom_image', !empty($state))),

                                    Forms\Components\Toggle::make('has_custom_image')
                                        ->label('Đã có ảnh chụp thật')
                                        ->helperText('Bật xanh khi có ảnh thật. Nếu tắt sẽ dùng ảnh minh họa danh mục.')
                                        ->default(false),
                                ]),

                            Forms\Components\Section::make('Trạng thái kinh doanh')
                                ->icon('heroicon-o-check-circle')
                                ->schema([
                                    Forms\Components\Toggle::make('is_active')
                                        ->label('Đang mở bán')
                                        ->helperText('Hiển thị trên website và màn hình POS')
                                        ->default(true),

                                    Forms\Components\Toggle::make('is_service_part')
                                        ->label('Linh kiện / Mực sửa chữa')
                                        ->helperText('Dùng trong phiếu tiếp nhận kỹ thuật máy in')
                                        ->default(false),
                                ]),

                            Forms\Components\Section::make('Xem nhanh ngoài cửa hàng')
                                ->icon('heroicon-o-arrow-top-right-on-square')
                                ->visible(fn ($record) => !empty($record) && !empty($record->slug))
                                ->schema([
                                    Forms\Components\Placeholder::make('storefront_link')
                                        ->label('Liên kết trực tiếp')
                                        ->content(fn ($record) => new \Illuminate\Support\HtmlString(
                                            "<a href='" . route('storefront.product-detail', $record->slug) . "' target='_blank' class='inline-flex items-center gap-1.5 px-3 py-2 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 rounded-xl font-bold text-xs transition'>
                                                <span>Xem sản phẩm trên web</span> &nearr;
                                            </a>"
                                        )),
                                ]),
                        ])->columnSpan(1),
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
                    ->square()
                    ->size(48)
                    ->defaultImageUrl(fn ($record) => $record->image_url),

                Tables\Columns\TextColumn::make('name')
                    ->label('Tên sản phẩm')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->wrap()
                    ->description(fn (Product $record) => "SKU: {$record->sku}" . ($record->barcode ? " • Vạch: {$record->barcode}" : '')),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Danh mục')
                    ->sortable()
                    ->badge(),

                Tables\Columns\TextColumn::make('retail_price')
                    ->label('Giá bán lẻ')
                    ->money('VND')
                    ->weight('bold')
                    ->color('primary')
                    ->sortable()
                    ->description(fn (Product $record) => $record->cost_price > 0 ? 'Vốn: ' . number_format($record->cost_price, 0, ',', '.') . '₫' : null),

                Tables\Columns\TextColumn::make('stock_quantity')
                    ->label('Tồn kho')
                    ->sortable()
                    ->badge()
                    ->color(fn (Product $record) => $record->stock_quantity <= 0 ? 'danger' : ($record->isLowStock() ? 'warning' : 'success'))
                    ->formatStateUsing(fn (Product $record) => "{$record->stock_quantity} {$record->base_unit}")
                    ->description(fn (Product $record) => "Báo động: {$record->low_stock_threshold}"),

                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Đang bán')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_service_part')
                    ->label('Linh kiện')
                    ->boolean()
                    ->trueIcon('heroicon-o-wrench-screwdriver')
                    ->falseIcon('heroicon-o-minus')
                    ->tooltip('Linh kiện / mực máy in')
                    ->toggleable(isToggledHiddenByDefault: true),
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
                Tables\Actions\EditAction::make()
                    ->label('Sửa')
                    ->icon('heroicon-m-pencil-square'),

                Tables\Actions\DeleteAction::make()
                    ->label('Xóa')
                    ->icon('heroicon-m-trash')
                    ->modalHeading(fn (Product $record) => "Xác nhận xóa sản phẩm: {$record->name}")
                    ->modalDescription('Bạn có chắc chắn muốn xóa sản phẩm này khỏi kho? Hành động này không thể hoàn tác.')
                    ->modalSubmitActionLabel('Đồng ý xóa')
                    ->modalCancelActionLabel('Hủy bỏ'),

                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('view_storefront')
                        ->label('Xem trên web')
                        ->icon('heroicon-m-arrow-top-right-on-square')
                        ->url(fn (Product $record) => route('storefront.product-detail', $record->slug))
                        ->openUrlInNewTab()
                        ->color('gray'),

                    Tables\Actions\ReplicateAction::make()
                        ->label('Nhân bản sản phẩm')
                        ->icon('heroicon-m-document-duplicate')
                        ->color('warning')
                        ->beforeReplicaSaved(function (Product $replica) {
                            $replica->name = $replica->name . ' (Bản sao)';
                            $replica->sku = 'SP-' . strtoupper(Str::random(6));
                            $replica->slug = Str::slug($replica->name . '-' . Str::random(4));
                        })
                        ->successNotificationTitle('Đã nhân bản sản phẩm thành công!'),

                    Tables\Actions\Action::make('quick_upload_image')
                        ->label('Đổi ảnh nhanh')
                        ->icon('heroicon-m-camera')
                        ->color('info')
                        ->form([
                            Forms\Components\FileUpload::make('image_path')
                                ->label('Tải ảnh đại diện mới')
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
                                ->body("Ảnh sản phẩm '{$record->name}' đã được cập nhật thành công.")
                                ->success()
                                ->send();
                        }),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->modalHeading('Xóa các sản phẩm đã chọn')
                        ->modalDescription('Bạn có chắc chắn muốn xóa tất cả các sản phẩm đã chọn?')
                        ->modalSubmitActionLabel('Xóa tất cả')
                        ->modalCancelActionLabel('Hủy'),
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
