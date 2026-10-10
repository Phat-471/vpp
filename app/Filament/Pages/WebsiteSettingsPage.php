<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\HtmlString;

class WebsiteSettingsPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationGroup = 'Cấu hình';

    protected static ?string $navigationLabel = 'Cài đặt';

    protected static ?string $title = 'Cài đặt hệ thống';

    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.website-settings-page';

    public ?array $data = [];

    public const VIETNAMESE_BANKS = [
        'MB' => 'MB Bank - Ngân hàng TMCP Quân Đội',
        'VCB' => 'Vietcombank - Ngân hàng Ngoại Thương Việt Nam',
        'TCB' => 'Techcombank - Ngân hàng Kỹ Thương Việt Nam',
        'ACB' => 'ACB - Ngân hàng TMCP Á Châu',
        'ICB' => 'VietinBank - Ngân hàng Công Thương Việt Nam',
        'BIDV' => 'BIDV - Ngân hàng Đầu Tư và Phát Triển Việt Nam',
        'VPB' => 'VPBank - Ngân hàng Việt Nam Thịnh Vượng',
        'TPB' => 'TPBank - Ngân hàng Tiên Phong',
        'STB' => 'Sacombank - Ngân hàng Sài Gòn Thương Tín',
        'HDB' => 'HDBank - Ngân hàng Phát Triển TP.HCM',
        'VIB' => 'VIB - Ngân hàng Quốc Tế Việt Nam',
        'SHB' => 'SHB - Ngân hàng Sài Gòn - Hà Nội',
        'MSB' => 'MSB - Ngân hàng Hàng Hải Việt Nam',
        'OCB' => 'OCB - Ngân hàng Phương Đông',
        'LPB' => 'LPBank - Ngân hàng Lộc Phát Việt Nam',
        'SEAB' => 'SeABank - Ngân hàng Đông Nam Á',
        'ABB' => 'ABBANK - Ngân hàng An Bình',
        'BVB' => 'BaoViet Bank - Ngân hàng Bảo Việt',
        'NVB' => 'NCB - Ngân hàng Quốc Dân',
        'VAB' => 'VietABank - Ngân hàng Việt Á',
        'EIB' => 'Eximbank - Ngân hàng Xuất Nhập Khẩu',
        'TIMO' => 'Timo - Ngân hàng số Timo by Bản Việt',
        'CAKE' => 'Cake - Ngân hàng số Cake by VPBank',
    ];

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
                        // ==========================================
                        // TAB 1: NHẬN DIỆN THƯƠNG HIỆU & THÔNG TIN CHUNG
                        // ==========================================
                        Tabs\Tab::make('Nhận diện & Cửa hàng')
                            ->icon('heroicon-o-building-storefront')
                            ->schema([
                                Grid::make(3)
                                    ->schema([
                                        Section::make('Thông tin thương hiệu')
                                            ->description('Tên hiển thị trên thanh tiêu đề, chân trang và tab trình duyệt.')
                                            ->schema([
                                                TextInput::make('site_name')
                                                    ->label('Tên thương hiệu / Cửa hàng *')
                                                    ->required()
                                                    ->placeholder('Ánh Dương ERP - VPP & Dịch Vụ Máy In'),

                                                TextInput::make('site_slogan')
                                                    ->label('Khẩu hiệu Slogan')
                                                    ->placeholder('Tổng kho văn phòng phẩm và dịch vụ kỹ thuật máy in'),

                                                TextInput::make('opening_hours')
                                                    ->label('Thời gian làm việc phục vụ khách')
                                                    ->placeholder('07:30 - 20:00 (Thứ 2 - Chủ Nhật)'),
                                            ])
                                            ->columnSpan(2),

                                        Section::make('Logo & Biểu tượng')
                                            ->description('Ảnh hiển thị trên Header, Footer & Tab duyệt web.')
                                            ->schema([
                                                FileUpload::make('site_logo')
                                                    ->label('Logo thương hiệu')
                                                    ->image()
                                                    ->directory('settings')
                                                    ->helperText('Định dạng PNG/SVG nền trong suốt, tối ưu kích thước 250x60px.'),

                                                FileUpload::make('site_favicon')
                                                    ->label('Favicon trình duyệt')
                                                    ->image()
                                                    ->directory('settings')
                                                    ->helperText('Icon hiển thị trên góc tab trình duyệt (khổ vuông 32x32px).'),
                                            ])
                                            ->columnSpan(1),
                                    ]),

                                Section::make('Thanh thông báo đầu trang (Top Announcement Bar)')
                                    ->collapsible()
                                    ->schema([
                                        Toggle::make('notice_bar_enabled')
                                            ->label('Bật hiển thị thanh thông báo trên cùng website')
                                            ->default(true),

                                        Textarea::make('notice_bar_text')
                                            ->label('Nội dung thông báo nổi')
                                            ->rows(2)
                                            ->placeholder('VD: 🎉 Khuyến mãi đầu tháng: Miễn phí vận chuyển cho đơn từ 500.000đ nội thành | Nhận nạp mực máy in tận nơi siêu tốc trong 30 phút!'),
                                    ]),
                            ]),

                        // ==========================================
                        // TAB 2: LIÊN HỆ, HỖ TRỢ & MẠNG XÃ HỘI
                        // ==========================================
                        Tabs\Tab::make('Liên hệ & Mạng xã hội')
                            ->icon('heroicon-o-phone')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        Section::make('Đường dây nóng & Hỗ trợ')
                                            ->icon('heroicon-o-phone-arrow-up-right')
                                            ->schema([
                                                TextInput::make('hotline')
                                                    ->label('Hotline tổng đài bán hàng (Bấm gọi ngay) *')
                                                    ->required()
                                                    ->tel()
                                                    ->placeholder('0901.234.567'),

                                                TextInput::make('technical_hotline')
                                                    ->label('Hotline kỹ thuật máy in (Trực 24/7)')
                                                    ->tel()
                                                    ->placeholder('0912.345.678')
                                                    ->helperText('Hiển thị trên trang tra cứu phiếu sửa chữa và bảo hành.'),

                                                TextInput::make('zalo')
                                                    ->label('Số điện thoại Zalo tư vấn / Báo giá sỉ *')
                                                    ->required()
                                                    ->placeholder('0901.234.567')
                                                    ->helperText('Liên kết với nút Zalo chat nổi trên toàn bộ website.'),

                                                TextInput::make('email')
                                                    ->label('Email hỗ trợ khách hàng')
                                                    ->email()
                                                    ->placeholder('hotro@vppanhduong.vn'),
                                            ]),

                                        Section::make('Địa chỉ & Kết nối mạng xã hội')
                                            ->icon('heroicon-o-map-pin')
                                            ->schema([
                                                Textarea::make('address')
                                                    ->label('Địa chỉ showroom / Tổng kho *')
                                                    ->required()
                                                    ->rows(2)
                                                    ->placeholder('Số 123 Đường Văn Phòng Phẩm, Phường Bến Nghé, Quận 1, TP.HCM'),

                                                TextInput::make('facebook_url')
                                                    ->label('Đường dẫn Fanpage Facebook')
                                                    ->placeholder('https://facebook.com/vppanhduong'),

                                                Textarea::make('google_maps_iframe')
                                                    ->label('Mã nhúng bản đồ Google Maps (iframe src)')
                                                    ->rows(2)
                                                    ->placeholder('https://www.google.com/maps/embed?...')
                                                    ->helperText('Dán đường link src từ mã nhúng Google Maps để hiển thị chỉ đường.'),
                                            ]),
                                    ]),
                            ]),

                        // ==========================================
                        // TAB 3: THANH TOÁN VIETQR NAPAS 24/7
                        // ==========================================
                        Tabs\Tab::make('Thanh toán VietQR')
                            ->icon('heroicon-o-qr-code')
                            ->schema([
                                Grid::make(3)
                                    ->schema([
                                        Section::make('Cấu hình tài khoản ngân hàng thụ hưởng')
                                            ->description('Dùng để tự động tạo mã QR Napas 24/7 khi khách mua hàng hoặc thanh toán phiếu sửa.')
                                            ->schema([
                                                Select::make('vietqr_bank_code')
                                                    ->label('Chọn ngân hàng thụ hưởng *')
                                                    ->options(self::VIETNAMESE_BANKS)
                                                    ->searchable()
                                                    ->required()
                                                    ->live()
                                                    ->afterStateUpdated(function ($state, callable $set) {
                                                        if (isset(self::VIETNAMESE_BANKS[$state])) {
                                                            $set('vietqr_bank_name', self::VIETNAMESE_BANKS[$state]);
                                                        }
                                                    }),

                                                TextInput::make('vietqr_bank_name')
                                                    ->label('Tên đầy đủ của ngân hàng')
                                                    ->disabled()
                                                    ->dehydrated(),

                                                TextInput::make('vietqr_account_number')
                                                    ->label('Số tài khoản ngân hàng *')
                                                    ->required()
                                                    ->live()
                                                    ->placeholder('190333888999'),

                                                TextInput::make('vietqr_account_name')
                                                    ->label('Tên chủ tài khoản (Viết hoa không dấu) *')
                                                    ->required()
                                                    ->live()
                                                    ->placeholder('CONG TY TNHH VPP ANH DUONG')
                                                    ->helperText('Lưu ý: Tên phải viết hoa, không dấu đúng chuẩn đăng ký ngân hàng.'),
                                            ])
                                            ->columnSpan(2),

                                        Section::make('Xem trước mã QR mẫu')
                                            ->description('Kiểm tra mã QR thanh toán hiển thị thực tế.')
                                            ->schema([
                                                Placeholder::make('vietqr_preview')
                                                    ->label('')
                                                    ->content(fn ($get) => new HtmlString(
                                                        '<div class="p-4 bg-slate-50 border border-slate-200 rounded-2xl text-center space-y-2">'
                                                        . '<img src="https://img.vietqr.io/image/' . ($get('vietqr_bank_code') ?: 'MB') . '-' . ($get('vietqr_account_number') ?: '190333888999') . '-compact2.png?accountName=' . urlencode($get('vietqr_account_name') ?: 'NGUYEN VAN A') . '" class="w-48 h-48 mx-auto rounded-xl object-contain bg-white border border-slate-200 p-1 shadow-sm" alt="QR Preview" />'
                                                        . '<div class="text-xs font-mono font-bold text-slate-800">' . ($get('vietqr_account_number') ?: '190333888999') . '</div>'
                                                        . '<div class="text-[11px] font-bold text-emerald-600">' . ($get('vietqr_account_name') ?: 'NGUYEN VAN A') . '</div>'
                                                        . '<p class="text-[10px] text-slate-400">Quét thử bằng app ngân hàng để kiểm tra thông tin người nhận.</p>'
                                                        . '</div>'
                                                    )),
                                            ])
                                            ->columnSpan(1),
                                    ]),
                            ]),

                        // ==========================================
                        // TAB 4: CHÍNH SÁCH BÁN HÀNG, VẬN CHUYỂN & THUẾ
                        // ==========================================
                        Tabs\Tab::make('Bán hàng, Giao hàng & Thuế')
                            ->icon('heroicon-o-truck')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        Section::make('Chính sách giao hàng')
                                            ->icon('heroicon-o-truck')
                                            ->schema([
                                                TextInput::make('shipping_fee_default')
                                                    ->label('Phí vận chuyển tiêu chuẩn *')
                                                    ->numeric()
                                                    ->prefix('₫')
                                                    ->required()
                                                    ->default(25000)
                                                    ->helperText('Áp dụng tự động cho các đơn hàng chưa đạt ngưỡng miễn phí giao hàng.'),

                                                TextInput::make('freeship_threshold')
                                                    ->label('Ngưỡng miễn phí vận chuyển (Freeship) *')
                                                    ->numeric()
                                                    ->prefix('₫')
                                                    ->required()
                                                    ->default(500000)
                                                    ->helperText('Đơn hàng đạt giá trị từ mức này trở lên sẽ được miễn phí vận chuyển 100%.'),
                                            ]),

                                        Section::make('Thuế VAT & Cảnh báo kho')
                                            ->icon('heroicon-o-calculator')
                                            ->schema([
                                                TextInput::make('default_vat_rate')
                                                    ->label('Thuế suất VAT mặc định (%) *')
                                                    ->numeric()
                                                    ->suffix('%')
                                                    ->required()
                                                    ->default(8)
                                                    ->helperText('Thuế suất GTGT tiêu chuẩn (8% hoặc 10% theo quy định pháp luật).'),

                                                TextInput::make('default_low_stock_threshold')
                                                    ->label('Ngưỡng cảnh báo tồn kho sắp hết mặc định *')
                                                    ->numeric()
                                                    ->default(5)
                                                    ->required()
                                                    ->helperText('Áp dụng làm giá trị mặc định khi tạo mới sản phẩm trong kho.'),
                                            ]),
                                    ]),
                            ]),

                        // ==========================================
                        // TAB 5: THÔNG TIN PHÁP LÝ & HÓA ĐƠN VAT
                        // ==========================================
                        Tabs\Tab::make('Pháp lý & Hóa đơn VAT')
                            ->icon('heroicon-o-document-currency-dollar')
                            ->schema([
                                Section::make('Thông tin xuất hóa đơn giá trị gia tăng (VAT Điện tử)')
                                    ->description('Thông tin doanh nghiệp in trên hóa đơn VAT và phiếu xuất kho phục vụ cơ quan thuế.')
                                    ->schema([
                                        TextInput::make('company_name')
                                            ->label('Tên doanh nghiệp / Đơn vị bán lẻ lẻ *')
                                            ->required()
                                            ->placeholder('CÔNG TY TNHH THƯƠNG MẠI VÀ DỊCH VỤ VPP ÁNH DƯƠNG'),

                                        Grid::make(2)
                                            ->schema([
                                                TextInput::make('company_tax_id')
                                                    ->label('Mã số thuế (MST) *')
                                                    ->required()
                                                    ->placeholder('0109887766'),

                                                TextInput::make('invoice_email')
                                                    ->label('Email phòng kế toán xuất hóa đơn')
                                                    ->email()
                                                    ->placeholder('ketoan@vppanhduong.vn'),
                                            ]),

                                        Textarea::make('company_address')
                                            ->label('Địa chỉ trụ sở đăng ký kinh doanh *')
                                            ->required()
                                            ->rows(2)
                                            ->placeholder('Số 123 Đường Văn Phòng Phẩm, Phường Bến Nghé, Quận 1, TP.HCM'),
                                    ]),
                            ]),

                        // ==========================================
                        // TAB 6: VẬN HÀNH KỸ THUẬT & QUẦY THU NGÂN POS
                        // ==========================================
                        Tabs\Tab::make('Vận hành Kỹ thuật & POS')
                            ->icon('heroicon-o-wrench-screwdriver')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        Section::make('Quy trình kỹ thuật máy in')
                                            ->icon('heroicon-o-shield-check')
                                            ->schema([
                                                TextInput::make('warranty_period_days')
                                                    ->label('Thời gian bảo hành sửa chữa mặc định (Ngày)')
                                                    ->numeric()
                                                    ->default(30)
                                                    ->suffix('ngày')
                                                    ->helperText('Thời hạn bảo hành linh kiện và công sửa (in trên phiếu sửa chữa).'),

                                                TextInput::make('repair_turnaround_hours')
                                                    ->label('Cam kết thời gian xử lý sự cố (Giờ)')
                                                    ->numeric()
                                                    ->default(24)
                                                    ->suffix('giờ')
                                                    ->helperText('Thời gian tối đa để kiểm tra và báo giá cho khách mang máy đến.'),
                                            ]),

                                        Section::make('Quy chuẩn quầy thu ngân POS')
                                            ->icon('heroicon-o-calculator')
                                            ->schema([
                                                Toggle::make('pos_shift_required')
                                                    ->label('Bắt buộc mở ca làm việc trước khi bán hàng POS')
                                                    ->helperText('Khi bật, thu ngân phải khai báo số tiền đầu ca trước khi lập đơn tại quầy.')
                                                    ->default(true),
                                            ]),
                                    ]),
                            ]),

                        // ==========================================
                        // TAB 7: XÁC THỰC TÀI KHOẢN OTP & ZALO ZNS / SMS
                        // ==========================================
                        Tabs\Tab::make('Xác thực OTP & Zalo ZNS')
                            ->icon('heroicon-o-chat-bubble-left-right')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        Section::make('Cấu hình Dịch vụ gửi mã OTP')
                                            ->description('Thiết lập kênh phát hành mã xác thực 6 số khi khách hàng đăng ký hoặc kích hoạt tài khoản.')
                                            ->icon('heroicon-o-shield-check')
                                            ->schema([
                                                Select::make('otp_provider')
                                                    ->label('Kênh phát hành mã OTP chính')
                                                    ->options([
                                                        'auto' => '⚡ Tự động (Ưu tiên Zalo ZNS, dự phòng SMS)',
                                                        'zns' => '💬 Zalo Notification Service (ZNS)',
                                                        'sms' => '📩 SMS Brandname (SpeedSMS / eSMS)',
                                                        'mock' => '🧪 Chế độ thử nghiệm (Ghi log & hiện mã test)',
                                                    ])
                                                    ->default('auto')
                                                    ->helperText('Nếu chưa có hợp đồng ZNS/SMS với nhà mạng, chọn Chế độ thử nghiệm để kiểm thử hệ thống.'),

                                                TextInput::make('otp_expire_minutes')
                                                    ->label('Thời hạn hiệu lực của mã OTP (Phút)')
                                                    ->numeric()
                                                    ->default(5)
                                                    ->suffix('phút'),
                                            ]),

                                        Section::make('Kết nối Zalo Notification Service (ZNS)')
                                            ->description('Gửi tin nhắn ZNS chính chủ qua Zalo Official Account (OA).')
                                            ->icon('heroicon-o-chat-bubble-oval-left')
                                            ->schema([
                                                TextInput::make('zalo_zns_template_id')
                                                    ->label('Mã mẫu tin nhắn ZNS (Template ID)')
                                                    ->placeholder('VD: 345678')
                                                    ->helperText('Mã mẫu tin nhắn OTP đã được VNG Zalo phê duyệt.'),

                                                Textarea::make('zalo_zns_access_token')
                                                    ->label('Access Token Zalo OA')
                                                    ->rows(3)
                                                    ->placeholder('Nhập Access Token dài hạn từ developers.zalo.me...')
                                                    ->helperText('Token API của ứng dụng Zalo Official Account có quyền gửi ZNS.'),
                                            ]),

                                        Section::make('Kết nối SMS Brandname Gateway (Dự phòng)')
                                            ->description('Gửi tin nhắn SMS trực tiếp đến số điện thoại khi khách không dùng Zalo.')
                                            ->icon('heroicon-o-device-phone-mobile')
                                            ->schema([
                                                TextInput::make('sms_brandname')
                                                    ->label('Tên Brandname hiển thị')
                                                    ->placeholder('VPP_ANHDUONG')
                                                    ->default('VPP'),

                                                TextInput::make('sms_api_key')
                                                    ->label('Khóa API Gateway (SpeedSMS / eSMS API Key)')
                                                    ->password()
                                                    ->placeholder('Nhập API Access Token...'),
                                            ])
                                            ->columnSpan(2),
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
                ->label('Lưu cài đặt website & cửa hàng')
                ->icon('heroicon-o-check-circle')
                ->size('lg')
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        $state = $this->form->getState();

        foreach ($state as $key => $value) {
            if (is_array($value)) {
                // If FileUpload returns an array of paths, take the first one or encode
                $value = reset($value) ?: null;
            }
            Setting::set($key, $value);
        }

        Notification::make()
            ->title('Đã cập nhật cài đặt thành công')
            ->body('Các thay đổi đã được áp dụng tức thì trên toàn bộ Storefront, Quầy POS, Mẫu in hóa đơn và mã VietQR.')
            ->success()
            ->send();
    }
}
