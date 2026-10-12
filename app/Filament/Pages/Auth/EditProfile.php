<?php

namespace App\Filament\Pages\Auth;

use Filament\Actions\Action;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Pages\Auth\EditProfile as BaseEditProfile;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class EditProfile extends BaseEditProfile
{
    public static function isSimple(): bool
    {
        return false;
    }

    public static function getLabel(): string
    {
        return 'Hồ sơ quản trị';
    }

    public function getTitle(): string | Htmlable
    {
        return 'Hồ sơ tài khoản Quản trị';
    }

    public function getHeading(): string | Htmlable
    {
        return 'Thông tin tài khoản & Mật khẩu';
    }

    public function getSubheading(): string | Htmlable | null
    {
        return 'Cập nhật họ tên hiển thị, địa chỉ email đăng nhập và mật khẩu tài khoản quản trị của bạn.';
    }

    public function getBreadcrumbs(): array
    {
        return [
            route('filament.admin.pages.dashboard') => 'Bảng điều khiển',
            'Hồ sơ quản trị',
        ];
    }

    public function getMaxWidth(): \Filament\Support\Enums\MaxWidth | string | null
    {
        return \Filament\Support\Enums\MaxWidth::FiveExtraLarge;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Thông tin cơ bản')
                    ->description('Cập nhật họ tên, địa chỉ email và số điện thoại liên hệ.')
                    ->icon('heroicon-m-user-circle')
                    ->schema([
                        $this->getNameFormComponent(),
                        $this->getEmailFormComponent(),
                        TextInput::make('phone')
                            ->label('Số điện thoại')
                            ->tel()
                            ->maxLength(20)
                            ->placeholder('0901234567'),
                        TextInput::make('role_name')
                            ->label('Vai trò hệ thống')
                            ->disabled()
                            ->dehydrated(false)
                            ->formatStateUsing(fn () => match ($this->getUser()->role) {
                                'admin' => 'Quản trị viên tối cao (Admin)',
                                'cashier' => 'Thu ngân (Bán hàng POS)',
                                'technician' => 'Kỹ thuật viên (Sửa máy in)',
                                default => 'Người dùng',
                            }),
                    ])
                    ->columns(2),

                Section::make('Đổi mật khẩu đăng nhập')
                    ->description('Để trống nếu bạn không có nhu cầu thay đổi mật khẩu hiện tại.')
                    ->icon('heroicon-m-lock-closed')
                    ->schema([
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                    ])
                    ->columns(2),
            ]);
    }

    protected function getNameFormComponent(): Component
    {
        return TextInput::make('name')
            ->label('Họ và tên')
            ->required()
            ->maxLength(255)
            ->autofocus();
    }

    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->label('Địa chỉ Email đăng nhập')
            ->email()
            ->required()
            ->maxLength(255)
            ->unique(ignoreRecord: true);
    }

    protected function getPasswordFormComponent(): Component
    {
        return TextInput::make('password')
            ->label('Mật khẩu mới')
            ->password()
            ->revealable(filament()->arePasswordsRevealable())
            ->rule(Password::default())
            ->autocomplete('new-password')
            ->dehydrated(fn ($state): bool => filled($state))
            ->dehydrateStateUsing(fn ($state): string => Hash::make($state))
            ->live(debounce: 500)
            ->same('passwordConfirmation');
    }

    protected function getPasswordConfirmationFormComponent(): Component
    {
        return TextInput::make('passwordConfirmation')
            ->label('Xác nhận mật khẩu mới')
            ->password()
            ->revealable(filament()->arePasswordsRevealable())
            ->required(fn (Get $get): bool => filled($get('password')))
            ->visible(fn (Get $get): bool => filled($get('password')))
            ->dehydrated(false);
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Cập nhật thông tin tài khoản thành công!';
    }

    protected function getSaveFormAction(): Action
    {
        return Action::make('save')
            ->label('Lưu thay đổi')
            ->submit('save')
            ->keyBindings(['mod+s']);
    }

    protected function getCancelFormAction(): Action
    {
        return Action::make('cancel')
            ->label('Hủy bỏ')
            ->alpineClickHandler('window.history.back()')
            ->color('gray');
    }
}
