<?php

namespace App\Filament\Resources\PrinterModelResource\RelationManagers;

use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ProductsRelationManager extends RelationManager
{
    protected static string $relationship = 'products';

    protected static ?string $title = 'Linh kiện & Hộp mực tương thích trong kho';

    protected static ?string $modelLabel = 'Sản phẩm tương thích';

    protected static ?string $pluralModelLabel = 'Danh sách linh kiện / Hộp mực trong kho';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('notes')
                    ->label('Ghi chú kỹ thuật khi lắp đặt')
                    ->placeholder('VD: Cần đổi bánh răng reset, thay kèm bao lụa...')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\ImageColumn::make('image_url')
                    ->label('Ảnh')
                    ->circular()
                    ->defaultImageUrl(asset('images/default-product.png')),

                Tables\Columns\TextColumn::make('sku')
                    ->label('Mã SKU')
                    ->fontFamily('mono')
                    ->weight('bold')
                    ->searchable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Tên linh kiện / Hộp mực')
                    ->weight('bold')
                    ->wrap()
                    ->searchable(),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Ngành hàng')
                    ->badge(),

                Tables\Columns\TextColumn::make('stock_quantity')
                    ->label('Tồn kho')
                    ->badge()
                    ->color(fn ($record) => $record->stock_quantity <= 0 ? 'danger' : ($record->stock_quantity <= 5 ? 'warning' : 'success'))
                    ->formatStateUsing(fn ($record) => $record->stock_quantity . ' ' . ($record->base_unit ?: 'Cái')),

                Tables\Columns\TextColumn::make('retail_price')
                    ->label('Giá bán lẻ')
                    ->money('VND')
                    ->weight('bold')
                    ->color('primary'),

                Tables\Columns\TextColumn::make('pivot.notes')
                    ->label('Lưu ý')
                    ->placeholder('—')
                    ->wrap(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->label('➕ Gắn thêm linh kiện / Hộp mực từ kho')
                    ->preloadRecordSelect()
                    ->form(fn (Tables\Actions\AttachAction $action): array => [
                        $action->getRecordSelect()
                            ->label('Chọn sản phẩm trong kho')
                            ->required(),
                        Forms\Components\TextInput::make('notes')
                            ->label('Ghi chú kỹ thuật khi dùng cho máy này')
                            ->placeholder('VD: Hộp mực nguyên bản, tương thích 100%'),
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Sửa ghi chú'),
                Tables\Actions\DetachAction::make()
                    ->label('Gỡ liên kết')
                    ->modalHeading('Gỡ bỏ liên kết tương thích')
                    ->modalDescription('Bạn có chắc muốn bỏ sản phẩm này khỏi danh sách linh kiện tương thích của dòng máy in?'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make()->label('Gỡ các liên kết đã chọn'),
                ]),
            ])
            ->emptyStateHeading('Chưa có linh kiện hoặc hộp mực nào được gắn với máy in này')
            ->emptyStateDescription('Bấm "Gắn thêm linh kiện / Hộp mực từ kho" để nhân viên kỹ thuật và thu ngân tra cứu nhanh.');
    }
}
