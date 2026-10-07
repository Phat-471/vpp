<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DealerInquiryResource\Pages;
use App\Models\DealerInquiry;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DealerInquiryResource extends Resource
{
    protected static ?string $model = DealerInquiry::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?string $navigationGroup = 'CSKH & Tương Tác';

    protected static ?string $navigationLabel = 'Đại lý & Mua sỉ B2B';

    protected static ?string $modelLabel = 'Yêu cầu mở đại lý';

    protected static ?string $pluralModelLabel = 'Danh sách đối tác đại lý';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Thông tin đối tác / Đại lý')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('full_name')
                                    ->label('Họ và tên người liên hệ')
                                    ->required(),
                                Forms\Components\TextInput::make('phone')
                                    ->label('Số điện thoại')
                                    ->tel()
                                    ->required(),
                                Forms\Components\TextInput::make('email')
                                    ->label('Email')
                                    ->email(),
                            ]),
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('company_name')
                                    ->label('Tên doanh nghiệp / Cửa hàng')
                                    ->nullable(),
                                Forms\Components\TextInput::make('province')
                                    ->label('Khu vực / Tỉnh thành')
                                    ->required(),
                                Forms\Components\TextInput::make('business_type')
                                    ->label('Mô hình kinh doanh')
                                    ->placeholder('Cửa hàng VPP, Doanh nghiệp...'),
                            ]),
                        Forms\Components\TextInput::make('address')
                            ->label('Địa chỉ chi tiết'),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('monthly_budget')
                                    ->label('Hạn mức / Doanh số dự kiến hàng tháng'),
                                Forms\Components\TextInput::make('product_categories')
                                    ->label('Nhóm hàng hóa quan tâm'),
                            ]),
                        Forms\Components\Textarea::make('notes')
                            ->label('Ghi chú của đối tác')
                            ->rows(3),
                    ]),

                Forms\Components\Section::make('Tiến độ xử lý & CSKH')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('status')
                                    ->label('Trạng thái xử lý')
                                    ->options([
                                        'pending' => 'Chờ liên hệ mới',
                                        'contacted' => 'Đã gọi tư vấn',
                                        'cooperating' => 'Đã ký hợp đồng phân phối',
                                        'rejected' => 'Không phù hợp / Từ chối',
                                    ])
                                    ->default('pending')
                                    ->required(),
                                Forms\Components\Textarea::make('admin_notes')
                                    ->label('Ghi chú của nhân viên kinh doanh')
                                    ->placeholder('Nội dung đã trao đổi, chiết khấu thỏa thuận...')
                                    ->rows(3),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ngày đăng ký')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('full_name')
                    ->label('Người liên hệ')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Số điện thoại')
                    ->searchable()
                    ->copyable()
                    ->url(fn ($record) => "tel:{$record->phone}"),

                Tables\Columns\TextColumn::make('company_name')
                    ->label('Đơn vị / Cửa hàng')
                    ->searchable()
                    ->default('Cá nhân'),

                Tables\Columns\TextColumn::make('province')
                    ->label('Khu vực'),

                Tables\Columns\TextColumn::make('monthly_budget')
                    ->label('Dự kiến mua'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Trạng thái')
                    ->badge()
                    ->colors([
                        'warning' => 'pending',
                        'info' => 'contacted',
                        'success' => 'cooperating',
                        'danger' => 'rejected',
                    ])
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'pending' => 'Chờ liên hệ',
                        'contacted' => 'Đã tư vấn',
                        'cooperating' => 'Hợp tác thành công',
                        'rejected' => 'Từ chối',
                        default => $state,
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Trạng thái')
                    ->options([
                        'pending' => 'Chờ liên hệ',
                        'contacted' => 'Đã tư vấn',
                        'cooperating' => 'Hợp tác thành công',
                        'rejected' => 'Từ chối',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Xử lý'),
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
            'index' => Pages\ListDealerInquiries::route('/'),
            'create' => Pages\CreateDealerInquiry::route('/create'),
            'edit' => Pages\EditDealerInquiry::route('/{record}/edit'),
        ];
    }
}
