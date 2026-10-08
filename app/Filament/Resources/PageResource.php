<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageResource\Pages;
use App\Models\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Nội dung & trang web';

    protected static ?string $navigationLabel = 'Trang nội dung và chính sách';

    protected static ?string $modelLabel = 'Trang bài viết';

    protected static ?string $pluralModelLabel = 'Danh sách trang nội dung';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Nội dung trang')
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->label('Tiêu đề trang')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),

                                Forms\Components\TextInput::make('slug')
                                    ->label('Đường dẫn trang')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->prefix(url('/trang') . '/'),

                                Forms\Components\Textarea::make('summary')
                                    ->label('Mô tả ngắn (hiển thị ở đầu trang và trong thẻ meta)')
                                    ->rows(2),

                                Forms\Components\RichEditor::make('content')
                                    ->label('Nội dung chi tiết')
                                    ->required()
                                    ->toolbarButtons([
                                        'blockquote',
                                        'bold',
                                        'bulletList',
                                        'codeBlock',
                                        'h2',
                                        'h3',
                                        'italic',
                                        'link',
                                        'orderedList',
                                        'redo',
                                        'strike',
                                        'table',
                                        'undo',
                                    ])
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpan(['lg' => 2]),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Cài đặt hiển thị')
                            ->schema([
                                Forms\Components\Toggle::make('is_active')
                                    ->label('Kích hoạt hiển thị')
                                    ->default(true)
                                    ->helperText('Tắt để tạm thời ẩn bài viết khỏi website'),

                                Forms\Components\Select::make('category')
                                    ->label('Phân loại')
                                    ->options([
                                        'about' => 'Giới thiệu công ty',
                                        'policy' => 'Chính sách & Quy định',
                                        'guide' => 'Báo giá & Hướng dẫn',
                                        'announcement' => 'Thông báo & Tin tức',
                                    ])
                                    ->default('policy')
                                    ->required(),

                                Forms\Components\Select::make('position')
                                    ->label('Vị trí liên kết')
                                    ->options([
                                        'footer_col1' => 'Cột Chân trang 1 (Về chúng tôi)',
                                        'footer_col2' => 'Cột Chân trang 2 (Chính sách & Hỗ trợ)',
                                        'header' => 'Menu đầu trang',
                                        'hidden' => 'Chỉ truy cập qua link trực tiếp',
                                    ])
                                    ->default('footer_col2')
                                    ->required(),

                                Forms\Components\TextInput::make('sort_order')
                                    ->label('Thứ tự sắp xếp')
                                    ->numeric()
                                    ->default(0),
                            ]),

                        Forms\Components\Section::make('Thiết lập SEO')
                            ->schema([
                                Forms\Components\TextInput::make('meta_title')
                                    ->label('Tiêu đề SEO')
                                    ->placeholder('Tối đa 60 ký tự'),

                                Forms\Components\Textarea::make('meta_description')
                                    ->label('Mô tả SEO')
                                    ->rows(3)
                                    ->placeholder('Nên viết khoảng 150–160 ký tự'),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Tiêu đề trang')
                    ->searchable()
                    ->weight('bold')
                    ->description(fn (Page $r) => url("/trang/{$r->slug}")),

                Tables\Columns\TextColumn::make('category')
                    ->label('Phân loại')
                    ->badge()
                    ->colors([
                        'primary' => 'about',
                        'warning' => 'policy',
                        'success' => 'guide',
                        'info' => 'announcement',
                    ])
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'about' => 'Giới thiệu',
                        'policy' => 'Chính sách',
                        'guide' => 'Báo giá / Hướng dẫn',
                        'announcement' => 'Thông báo',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('position')
                    ->label('Vị trí')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'footer_col1' => 'Footer Cột 1',
                        'footer_col2' => 'Footer Cột 2',
                        'header' => 'Menu Header',
                        default => 'Ẩn menu',
                    }),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Hiển thị')
                    ->boolean(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Cập nhật')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('view_page')
                    ->label('Xem trang')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (Page $record) => url("/trang/{$record->slug}"))
                    ->openUrlInNewTab(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }
}
