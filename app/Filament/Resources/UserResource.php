<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Nhân sự';

    protected static ?string $navigationLabel = 'Tài khoản nhân viên';

    protected static ?string $modelLabel = 'Nhân viên';

    protected static ?string $pluralModelLabel = 'Danh sách nhân viên';

    protected static ?int $navigationSort = 1;

    public static function canViewAny(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function canDelete(Model $record): bool
    {
        // Không cho phép tự xóa chính tài khoản đang đăng nhập
        return (auth()->user()?->isAdmin() ?? false) && $record->id !== auth()->id();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Thông tin nhân sự')
                    ->description('Thông tin định danh và vai trò làm việc trong cửa hàng')
                    ->icon('heroicon-m-user-circle')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Họ và tên nhân viên')
                            ->placeholder('VD: Nguyễn Văn An')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('email')
                            ->label('Email đăng nhập')
                            ->email()
                            ->placeholder('nhanvien@vpp.vn')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        Forms\Components\TextInput::make('phone')
                            ->label('Số điện thoại liên hệ')
                            ->tel()
                            ->placeholder('0901234567')
                            ->maxLength(20),

                        Forms\Components\Select::make('role')
                            ->label('Vai trò & Phân quyền')
                            ->options([
                                'admin' => 'Quản trị viên (Toàn quyền quản lý, tài chính, nhân sự & cấu hình)',
                                'cashier' => 'Thu ngân (Bán hàng quầy POS, quản lý đơn hàng, khách hàng, kho hàng)',
                                'technician' => 'Kỹ thuật viên (Tiếp nhận & xử lý phiếu sửa máy in, tra cứu linh kiện)',
                            ])
                            ->required()
                            ->default('cashier')
                            ->native(false),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Mật khẩu đăng nhập')
                    ->description('Thiết lập hoặc đổi mật khẩu tài khoản truy cập hệ thống')
                    ->icon('heroicon-m-key')
                    ->schema([
                        Forms\Components\TextInput::make('password')
                            ->label('Mật khẩu')
                            ->password()
                            ->revealable()
                            ->rule(Password::default())
                            ->dehydrated(fn ($state): bool => filled($state))
                            ->dehydrateStateUsing(fn ($state): string => Hash::make($state))
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->same('passwordConfirmation')
                            ->helperText(fn (string $operation): string => $operation === 'edit' ? 'Để trống nếu không muốn đổi mật khẩu nhân viên.' : 'Tối thiểu 8 ký tự.'),

                        Forms\Components\TextInput::make('passwordConfirmation')
                            ->label('Xác nhận mật khẩu')
                            ->password()
                            ->revealable()
                            ->required(fn (string $operation, Forms\Get $get): bool => $operation === 'create' || filled($get('password')))
                            ->dehydrated(false),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Họ và tên')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn (User $record): string => $record->id === auth()->id() ? '(Tài khoản của bạn)' : ''),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email đăng nhập')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Đã sao chép email')
                    ->icon('heroicon-m-envelope'),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Số điện thoại')
                    ->searchable()
                    ->placeholder('Chưa có')
                    ->icon('heroicon-m-phone'),

                Tables\Columns\TextColumn::make('role')
                    ->label('Vai trò')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'admin' => 'Quản trị viên',
                        'cashier' => 'Thu ngân',
                        'technician' => 'Kỹ thuật viên',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'admin' => 'danger',
                        'cashier' => 'success',
                        'technician' => 'warning',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'admin' => 'heroicon-m-shield-check',
                        'cashier' => 'heroicon-m-calculator',
                        'technician' => 'heroicon-m-wrench-screwdriver',
                        default => 'heroicon-m-user',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->label('Lọc theo vai trò')
                    ->options([
                        'admin' => 'Quản trị viên',
                        'cashier' => 'Thu ngân',
                        'technician' => 'Kỹ thuật viên',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->hidden(fn (User $record): bool => $record->id === auth()->id()),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->action(function (Collection $records) {
                            $records->filter(fn (User $u) => $u->id !== auth()->id())->each->delete();
                        }),
                ]),
            ])
            ->emptyStateHeading('Chưa có nhân viên nào')
            ->emptyStateDescription('Tạo tài khoản nhân viên để phân quyền làm việc.');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
