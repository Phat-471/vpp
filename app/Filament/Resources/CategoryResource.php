<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationGroup = 'Kho & sản phẩm';

    protected static ?string $navigationLabel = 'Danh mục ngành hàng';

    protected static ?string $modelLabel = 'Danh mục';

    protected static ?string $pluralModelLabel = 'Danh mục sản phẩm';

    protected static ?int $navigationSort = 2;

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count() ?: null;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(3)
                    ->schema([
                        // CỘT CHÍNH (2/3): Tên, Slug, Mô tả
                        Forms\Components\Group::make([
                            Forms\Components\Section::make('Thông tin danh mục ngành hàng')
                                ->icon('heroicon-o-tag')
                                ->schema([
                                    Forms\Components\TextInput::make('name')
                                        ->label('Tên danh mục hàng *')
                                        ->placeholder('VD: Giấy in & Giấy photo, Bút viết & Mực, Linh kiện sửa máy in...')
                                        ->required()
                                        ->maxLength(100)
                                        ->live(onBlur: true)
                                        ->afterStateUpdated(fn ($state, callable $set, $operation) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),

                                    Forms\Components\TextInput::make('slug')
                                        ->label('Đường dẫn SEO (Slug) *')
                                        ->placeholder('giay-in-giay-photo')
                                        ->required()
                                        ->maxLength(120)
                                        ->unique(Category::class, 'slug', ignoreRecord: true)
                                        ->helperText('Đường dẫn thân thiện hiển thị trên thanh địa chỉ trình duyệt web.'),

                                    Forms\Components\Textarea::make('description')
                                        ->label('Mô tả tóm tắt ngành hàng')
                                        ->placeholder('Giới thiệu chủng loại mặt hàng, tiêu chuẩn kỹ thuật hoặc ưu điểm nổi bật...')
                                        ->rows(4)
                                        ->maxLength(500)
                                        ->columnSpanFull(),
                                ]),
                        ])
                        ->columnSpan(['lg' => 2]),

                        // CỘT PHỤ (1/3): Hiển thị, Icon, Thứ tự, Ảnh
                        Forms\Components\Group::make([
                            Forms\Components\Section::make('Cấu hình hiển thị')
                                ->icon('heroicon-o-adjustments-horizontal')
                                ->schema([
                                    Forms\Components\Toggle::make('is_active')
                                        ->label('Đang mở bán / Hiển thị')
                                        ->helperText('Bật để hiển thị danh mục trên Website và Quầy POS')
                                        ->default(true),

                                    Forms\Components\TextInput::make('sort_order')
                                        ->label('Thứ tự sắp xếp')
                                        ->numeric()
                                        ->default(0)
                                        ->helperText('Số càng nhỏ (0, 1, 2...) sẽ ưu tiên hiển thị trước.'),

                                    Forms\Components\TextInput::make('icon')
                                        ->label('Biểu tượng Heroicon')
                                        ->placeholder('heroicon-o-pencil')
                                        ->helperText('Ví dụ: heroicon-o-document-text, heroicon-o-wrench...'),

                                    Forms\Components\FileUpload::make('placeholder_image')
                                        ->label('Ảnh đại diện danh mục')
                                        ->image()
                                        ->directory('categories')
                                        ->maxSize(2048)
                                        ->helperText('Hiển thị làm ảnh nền danh mục trên trang chủ cửa hàng.'),
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
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('STT')
                    ->sortable()
                    ->width('70px')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Tên danh mục')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn (Category $c) => $c->slug),

                Tables\Columns\TextColumn::make('products_count')
                    ->label('Số lượng SKU')
                    ->counts('products')
                    ->badge()
                    ->color('primary')
                    ->icon('heroicon-m-cube')
                    ->sortable(),

                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Hiển thị')
                    ->onColor('success')
                    ->offColor('gray')
                    ->afterStateUpdated(function ($record, $state) {
                        Notification::make()
                            ->title($state ? 'Đã kích hoạt hiển thị danh mục' : 'Đã ẩn danh mục')
                            ->success()
                            ->send();
                    }),
            ])
            ->defaultSort('sort_order', 'asc')
            ->filters([
                Tables\Filters\Filter::make('active')
                    ->label('Đang hiển thị')
                    ->query(fn (Builder $query) => $query->where('is_active', true)),

                Tables\Filters\Filter::make('inactive')
                    ->label('Đang ẩn')
                    ->query(fn (Builder $query) => $query->where('is_active', false)),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Sửa')
                    ->icon('heroicon-m-pencil-square'),

                Tables\Actions\DeleteAction::make()
                    ->label('Xóa')
                    ->icon('heroicon-m-trash')
                    ->modalHeading('Xác nhận xóa danh mục')
                    ->modalDescription(fn (Category $record) => "Bạn có chắc chắn muốn xóa danh mục '{$record->name}'? Nếu danh mục đang có sản phẩm, bạn phải chuyển sản phẩm sang danh mục khác trước.")
                    ->modalSubmitActionLabel('Xác nhận xóa')
                    ->modalCancelActionLabel('Hủy bỏ')
                    ->before(function (Category $record, Tables\Actions\DeleteAction $action) {
                        if ($record->products()->count() > 0) {
                            Notification::make()
                                ->title('Không thể xóa danh mục')
                                ->body("Danh mục '{$record->name}' đang chứa {$record->products()->count()} sản phẩm. Vui lòng chuyển các sản phẩm này sang danh mục khác trước khi xóa.")
                                ->danger()
                                ->send();
                            $action->halt();
                        }
                    }),
            ])
            ->emptyStateHeading('Chưa có danh mục nào')
            ->emptyStateDescription('Tạo danh mục đầu tiên để bắt đầu phân loại sản phẩm văn phòng phẩm và linh kiện.')
            ->emptyStateIcon('heroicon-o-tag');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
