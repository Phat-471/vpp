<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Actions\Action;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class WebsiteSettingsPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationGroup = 'Hệ thống';

    protected static ?string $navigationLabel = 'Cài đặt website và cửa hàng';

    protected static ?string $title = 'Cài đặt website và cửa hàng';

    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.website-settings-page';

    public ?array $data = [];

    public function mount(): void
    {
        $settings = Setting::all()->pluck('value', 'key')->toArray();
        $this->form->fill($settings);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('SettingsTabs')
                    ->tabs([
                        Tabs\Tab::make('Thông tin cửa hàng')
                            ->icon('heroicon-o-building-storefront')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('site_name')
                                            ->label('Tên thương hiệu hoặc cửa hàng')
                                            ->required()
                                            ->placeholder('VPP và thiết bị máy in Ánh Dương'),

                                        TextInput::make('site_slogan')
                                            ->label('Khẩu hiệu')
                                            ->placeholder('Tổng kho văn phòng phẩm và dịch vụ máy in'),
                                    ]),

                                Grid::make(3)
                                    ->schema([
                                        TextInput::make('hotline')
                                            ->label('Hotline tổng đài (24/7)')
                                            ->required()
                                            ->placeholder('1900 6868'),

                                        TextInput::make('zalo')
                                            ->label('Số Zalo tư vấn / Báo giá sỉ')
                                            ->required()
                                            ->placeholder('0988.123.456'),

                                        TextInput::make('email')
                                            ->label('Email hỗ trợ khách hàng')
                                            ->email()
                                            ->placeholder('hotro@vppanhduong.vn'),
                                    ]),

                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('address')
                                            ->label('Địa chỉ cửa hàng và kho')
                                            ->required()
                                            ->placeholder('Số 123 Đường Cầu Giấy, Cầu Giấy, Hà Nội'),

                                        TextInput::make('opening_hours')
                                            ->label('Thời gian làm việc')
                                            ->placeholder('08:00 - 18:30 (Thứ 2 - Thứ 7)'),
                                    ]),

                                Textarea::make('notice_bar_text')
                                    ->label('Thông báo hiển thị ở đầu trang')
                                    ->rows(2)
                                    ->placeholder('Thông báo khuyến mãi hoặc hỗ trợ kỹ thuật hiển thị trên cùng website'),
                            ]),

                        Tabs\Tab::make('Thanh toán VietQR Napas 247')
                            ->icon('heroicon-o-qr-code')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('vietqr_bank_code')
                                            ->label('Mã ngân hàng')
                                            ->helperText('VD: MB, VCB, TCB, ACB, ICB, BIDV...')
                                            ->required(),

                                        TextInput::make('vietqr_bank_name')
                                            ->label('Tên đầy đủ của ngân hàng')
                                            ->placeholder('Ngân hàng TMCP Quân đội (MB)'),
                                    ]),

                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('vietqr_account_number')
                                            ->label('Số tài khoản ngân hàng')
                                            ->required()
                                            ->placeholder('190333888999'),

                                        TextInput::make('vietqr_account_name')
                                            ->label('Tên chủ tài khoản (viết hoa, không dấu)')
                                            ->required()
                                            ->placeholder('CONG TY TNHH VPP ANH DUONG'),
                                    ]),
                            ]),

                        Tabs\Tab::make('Thuế VAT và vận chuyển')
                            ->icon('heroicon-o-calculator')
                            ->schema([
                                Grid::make(3)
                                    ->schema([
                                        TextInput::make('default_vat_rate')
                                            ->label('Thuế suất VAT mặc định (%)')
                                            ->numeric()
                                            ->suffix('%')
                                            ->required()
                                            ->helperText('Thường là 8% hoặc 10% theo quy định thuế'),

                                        TextInput::make('shipping_fee_default')
                                            ->label('Phí giao hàng mặc định (VNĐ)')
                                            ->numeric()
                                            ->suffix('₫')
                                            ->required()
                                            ->helperText('Áp dụng cho đơn hàng chưa đạt ngưỡng miễn phí vận chuyển.'),

                                        TextInput::make('freeship_threshold')
                                            ->label('Ngưỡng miễn phí vận chuyển (VNĐ)')
                                            ->numeric()
                                            ->suffix('₫')
                                            ->required()
                                            ->helperText('Đơn hàng đạt giá trị này sẽ được miễn phí vận chuyển.'),
                                    ]),
                            ]),

                        Tabs\Tab::make('Thông tin xuất hóa đơn GTGT')
                            ->icon('heroicon-o-document-currency-dollar')
                            ->schema([
                                TextInput::make('company_name')
                                    ->label('Tên đơn vị xuất hóa đơn')
                                    ->required()
                                    ->placeholder('CÔNG TY TNHH THƯƠNG MẠI VÀ DỊCH VỤ VPP ÁNH DƯƠNG'),

                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('company_tax_id')
                                            ->label('Mã số thuế (MST)')
                                            ->required()
                                            ->placeholder('0109887766'),

                                        TextInput::make('company_address')
                                            ->label('Địa chỉ đăng ký kinh doanh')
                                            ->required()
                                            ->placeholder('Số 123 Đường Cầu Giấy, Phường Dịch Vọng, Quận Cầu Giấy, Hà Nội'),
                                    ]),
                            ]),
                    ]),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Lưu cài đặt')
                ->icon('heroicon-o-check-circle')
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        $state = $this->form->getState();

        foreach ($state as $key => $value) {
            Setting::set($key, $value);
        }

        Notification::make()
            ->title('Đã cập nhật cài đặt website')
            ->body('Các thay đổi đã được áp dụng tức thì trên toàn bộ hệ thống và Storefront.')
            ->success()
            ->send();
    }
}
