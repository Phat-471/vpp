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

    protected static ?string $navigationGroup = 'Hệ Thống';

    protected static ?string $navigationLabel = 'Cài đặt website & cửa hàng';

    protected static ?string $title = 'Cài Đặt Website & Thông Tin Cửa Hàng';

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
                        Tabs\Tab::make('Thông tin liên hệ & Cửa hàng')
                            ->icon('heroicon-o-building-storefront')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('site_name')
                                            ->label('Tên thương hiệu / Cửa hàng')
                                            ->required()
                                            ->placeholder('VPP & Thiết Bị Máy In Ánh Dương'),

                                        TextInput::make('site_slogan')
                                            ->label('Khẩu hiệu (Slogan)')
                                            ->placeholder('Tổng Kho Văn Phòng Phẩm & Dịch Vụ Máy In Chuyên Nghiệp'),
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
                                            ->label('Địa chỉ Showroom & Kho tổng')
                                            ->required()
                                            ->placeholder('Số 123 Đường Cầu Giấy, Cầu Giấy, Hà Nội'),

                                        TextInput::make('opening_hours')
                                            ->label('Thời gian làm việc')
                                            ->placeholder('08:00 - 18:30 (Thứ 2 - Thứ 7)'),
                                    ]),

                                Textarea::make('notice_bar_text')
                                    ->label('Nội dung thanh thông báo đầu trang (Notice Bar)')
                                    ->rows(2)
                                    ->placeholder('Thông báo khuyến mãi hoặc hỗ trợ kỹ thuật hiển thị trên cùng website'),
                            ]),

                        Tabs\Tab::make('Thanh toán VietQR Napas 247')
                            ->icon('heroicon-o-qr-code')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('vietqr_bank_code')
                                            ->label('Mã Ngân Hàng (Bank Code)')
                                            ->helperText('VD: MB, VCB, TCB, ACB, ICB, BIDV...')
                                            ->required(),

                                        TextInput::make('vietqr_bank_name')
                                            ->label('Tên đầy đủ của ngân hàng')
                                            ->placeholder('Ngân hàng TMCP Quân Đội (MB Bank)'),
                                    ]),

                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('vietqr_account_number')
                                            ->label('Số tài khoản ngân hàng')
                                            ->required()
                                            ->placeholder('190333888999'),

                                        TextInput::make('vietqr_account_name')
                                            ->label('Tên chủ tài khoản (In hoa không dấu)')
                                            ->required()
                                            ->placeholder('CONG TY TNHH VPP ANH DUONG'),
                                    ]),
                            ]),

                        Tabs\Tab::make('Thuế VAT & Vận chuyển')
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
                                            ->helperText('Áp dụng cho các đơn hàng chưa đạt ngưỡng freeship'),

                                        TextInput::make('freeship_threshold')
                                            ->label('Ngưỡng miễn phí vận chuyển (VNĐ)')
                                            ->numeric()
                                            ->suffix('₫')
                                            ->required()
                                            ->helperText('Đơn hàng từ giá trị này trở lên sẽ được miễn phí giao'),
                                    ]),
                            ]),

                        Tabs\Tab::make('Pháp nhân xuất hóa đơn GTGT')
                            ->icon('heroicon-o-document-currency-dollar')
                            ->schema([
                                TextInput::make('company_name')
                                    ->label('Tên công ty / Doanh nghiệp xuất hóa đơn')
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
                ->label('Lưu tất cả cài đặt')
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
            ->title('Đã cập nhật cài đặt website thành công!')
            ->body('Các thay đổi đã được áp dụng tức thì trên toàn bộ hệ thống và Storefront.')
            ->success()
            ->send();
    }
}
