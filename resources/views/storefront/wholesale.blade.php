@extends('layouts.storefront')

@section('title', 'Đăng Ký Đại Lý & Khách Hàng Doanh Nghiệp Mua Sỉ | VPP')
@section('meta_description', 'Chính sách chiết khấu tới 20-30% cho đại lý, doanh nghiệp mua văn phòng phẩm số lượng lớn. Hỗ trợ công nợ 30 ngày, xuất hóa đơn VAT trong ngày.')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-10 space-y-10">

    <!-- Breadcrumb -->
    <nav class="flex items-center space-x-2 text-xs text-slate-500 font-medium">
        <a href="{{ route('storefront.index') }}" class="hover:text-indigo-600">Trang chủ</a>
        <span>/</span>
        <span class="text-slate-900 font-bold">Chính sách & Đăng ký đại lý</span>
    </nav>

    <!-- Hero Banner Đại Lý -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-indigo-950 via-slate-900 to-indigo-900 text-white p-6 sm:p-12 shadow-xl border border-indigo-800">
        <div class="relative z-10 max-w-3xl space-y-4">
            <span class="inline-block px-3 py-1 bg-amber-400 text-slate-950 font-black text-xs rounded-full uppercase tracking-wider shadow-xs">
                ⭐ CHÍNH SÁCH ĐỐI TÁC DOANH NGHIỆP & ĐẠI LÝ 2026
            </span>
            <h1 class="text-2xl sm:text-4xl lg:text-5xl font-black text-white leading-tight">
                Cung Ứng Văn Phòng Phẩm Tận Gốc & Dịch Vụ Máy In Doanh Nghiệp
            </h1>
            <p class="text-xs sm:text-base text-slate-300 leading-relaxed font-normal">
                Chúng tôi chuyên cung cấp trọn gói cho hơn <b>1.000+ công ty, ngân hàng, trường học và đại lý bán lẻ</b> tại TP.HCM và các tỉnh lân cận với bảng giá chiết khấu đặc biệt theo sản lượng.
            </p>
            <div class="pt-2 flex flex-wrap gap-4 text-xs font-bold">
                <span class="flex items-center space-x-1.5 text-amber-300">
                    <span>✔</span> <span>Chiết khấu trực tiếp tới 20–30%</span>
                </span>
                <span class="flex items-center space-x-1.5 text-emerald-400">
                    <span>✔</span> <span>Hạn mức công nợ 15–30 ngày</span>
                </span>
                <span class="flex items-center space-x-1.5 text-sky-300">
                    <span>✔</span> <span>Giao hàng miễn phí tận nơi</span>
                </span>
            </div>
        </div>

        <div class="absolute -right-12 -bottom-12 w-64 h-64 bg-indigo-500/20 rounded-full blur-3xl pointer-events-none"></div>
    </div>

    <!-- 4 Quyền Lợi Cốt Lõi Cho Đại Lý -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-xs space-y-2">
            <span class="text-2xl">🏷️</span>
            <h3 class="font-bold text-slate-900 text-sm">Giá Sỉ Tận Kho</h3>
            <p class="text-xs text-slate-500 leading-relaxed">
                Phân phối trực tiếp từ nhà máy Double A, PaperOne, Thiên Long, Kokuyo, Plus không qua trung gian.
            </p>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-xs space-y-2">
            <span class="text-2xl">💳</span>
            <h3 class="font-bold text-slate-900 text-sm">Hỗ Trợ Công Nợ 30 Ngày</h3>
            <p class="text-xs text-slate-500 leading-relaxed">
                Hỗ trợ công nợ gối đầu linh hoạt theo hợp đồng cung cấp định kỳ hàng tháng cho doanh nghiệp.
            </p>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-xs space-y-2">
            <span class="text-2xl">⚡</span>
            <h3 class="font-bold text-slate-900 text-sm">Ưu Tiên Thợ Máy In 15P</h3>
            <p class="text-xs text-slate-500 leading-relaxed">
                Kỹ thuật viên có mặt xử lý kẹt giấy, nạp mực hoặc sửa chữa trong 15-30 phút không làm gián đoạn công việc.
            </p>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-xs space-y-2">
            <span class="text-2xl">🧾</span>
            <h3 class="font-bold text-slate-900 text-sm">Hóa Đơn VAT Điện Tử</h3>
            <p class="text-xs text-slate-500 leading-relaxed">
                Xuất hóa đơn GTGT điện tử chuẩn xác trong ngày theo quy định của Tổng Cục Thuế.
            </p>
        </div>
    </div>

    <!-- Form Đăng Ký Đại Lý (2 Columns: Form 7 cols + Hotline Box 5 cols) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

        <!-- Form Registration (lg:col-span-7) -->
        <div class="lg:col-span-7 bg-white p-6 sm:p-10 rounded-3xl border border-slate-200 shadow-xs space-y-6">
            <div>
                <span class="text-[11px] font-bold text-indigo-600 uppercase tracking-wider block mb-1">
                    GỬI YÊU CẦU BÁO GIÁ ĐẠI LÝ
                </span>
                <h2 class="text-xl sm:text-2xl font-black text-slate-900">
                    Điền Thông Tin Doanh Nghiệp Của Bạn
                </h2>
                <p class="text-xs text-slate-500 mt-1">
                    Chuyên viên phụ trách khách hàng doanh nghiệp sẽ liên hệ lại gửi bảng báo giá chiết khấu trong vòng 15-30 phút:
                </p>
            </div>

            @if(session('dealer_success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-xs sm:text-sm font-bold flex items-center space-x-2">
                <span class="text-base">✓</span>
                <span>{{ session('dealer_success') }}</span>
            </div>
            @endif

            <form action="{{ route('storefront.post-wholesale') }}" method="POST" class="space-y-4">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Tên đơn vị / Doanh nghiệp *</label>
                        <input type="text" name="company_name" required value="{{ old('company_name') }}" placeholder="VD: Công ty TNHH Phát Triển Á Châu" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-600 bg-slate-50 font-medium" />
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Người đại diện liên hệ *</label>
                        <input type="text" name="contact_name" required value="{{ old('contact_name') }}" placeholder="VD: Anh Minh (Phòng Mua Hàng)" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-600 bg-slate-50 font-medium" />
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Số điện thoại liên hệ (Zalo) *</label>
                        <input type="tel" name="phone" required value="{{ old('phone') }}" placeholder="VD: 0901 234 567" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-600 bg-slate-50 font-medium" />
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Email nhận báo giá</label>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="VD: contact@achau.com" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-600 bg-slate-50 font-medium" />
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Địa chỉ văn phòng / giao hàng</label>
                    <input type="text" name="address" value="{{ old('address') }}" placeholder="VD: Tòa nhà Bitexco, Q.1, TP.HCM" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-600 bg-slate-50 font-medium" />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Loại hình kinh doanh</label>
                        <select name="business_type" class="w-full text-xs px-3 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-600 bg-slate-50 font-medium">
                            <option value="Công ty / Doanh nghiệp">Công ty / Doanh nghiệp</option>
                            <option value="Trường học / Cơ quan nhà nước">Trường học / Cơ quan nhà nước</option>
                            <option value="Đại lý bán lẻ văn phòng phẩm">Đại lý bán lẻ văn phòng phẩm</option>
                            <option value="Tiệm photocopy / In ấn">Tiệm photocopy / In ấn</option>
                            <option value="Cá nhân mua số lượng lớn">Cá nhân mua số lượng lớn</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Ngân sách dự kiến mỗi tháng</label>
                        <select name="estimated_monthly_budget" class="w-full text-xs px-3 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-600 bg-slate-50 font-medium">
                            <option value="Dưới 10 triệu đồng">Dưới 10 triệu đồng</option>
                            <option value="Từ 10 - 30 triệu đồng">Từ 10 - 30 triệu đồng</option>
                            <option value="Từ 30 - 50 triệu đồng">Từ 30 - 50 triệu đồng</option>
                            <option value="Trên 50 triệu đồng">Trên 50 triệu đồng</option>
                        </select>
                    </div>
                </div>

                <!-- Checkbox các ngành hàng quan tâm -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Ngành hàng quý công ty quan tâm chính:</label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-xs">
                        <label class="flex items-center space-x-1.5 p-2 bg-slate-50 rounded-xl border border-slate-200 cursor-pointer">
                            <input type="checkbox" name="interested_categories[]" value="Giấy in A4/A3 theo Thùng" checked class="accent-indigo-600" />
                            <span class="text-[11px] font-semibold">Giấy in theo Thùng</span>
                        </label>
                        <label class="flex items-center space-x-1.5 p-2 bg-slate-50 rounded-xl border border-slate-200 cursor-pointer">
                            <input type="checkbox" name="interested_categories[]" value="Hộp mực & Linh kiện máy in" checked class="accent-indigo-600" />
                            <span class="text-[11px] font-semibold">Hộp mực máy in</span>
                        </label>
                        <label class="flex items-center space-x-1.5 p-2 bg-slate-50 rounded-xl border border-slate-200 cursor-pointer">
                            <input type="checkbox" name="interested_categories[]" value="Bút viết & Bìa hồ sơ" class="accent-indigo-600" />
                            <span class="text-[11px] font-semibold">Bút & Bìa hồ sơ</span>
                        </label>
                        <label class="flex items-center space-x-1.5 p-2 bg-slate-50 rounded-xl border border-slate-200 cursor-pointer">
                            <input type="checkbox" name="interested_categories[]" value="Bảo dưỡng & Sửa máy in" class="accent-indigo-600" />
                            <span class="text-[11px] font-semibold">Sửa máy in định kỳ</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Ghi chú cụ thể hoặc yêu cầu danh mục hàng</label>
                    <textarea name="notes" rows="3" placeholder="VD: Công ty đang sử dụng máy in Canon 2900 và HP 1020, mỗi tháng cần 30 thùng giấy Double A và 5 hộp mực..." class="w-full text-xs px-3.5 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-600 bg-slate-50"></textarea>
                </div>

                <button type="submit" class="w-full py-3.5 px-6 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-black text-xs sm:text-sm shadow-md transition transform hover:-translate-y-0.5 flex items-center justify-center space-x-2">
                    <span>GỬI ĐĂNG KÝ NHẬN BÁO GIÁ ĐẠI LÝ</span>
                    <span>→</span>
                </button>
            </form>
        </div>

        <!-- Right: Contact Direct & FAQs (lg:col-span-5) -->
        <div class="lg:col-span-5 space-y-6">
            
            <!-- Direct Manager Hotline Box -->
            <div class="bg-gradient-to-br from-slate-900 to-indigo-950 text-white p-6 sm:p-8 rounded-3xl border border-slate-800 shadow-sm space-y-4">
                <span class="px-2.5 py-0.5 bg-amber-400 text-slate-950 font-black text-[10px] rounded-full uppercase tracking-wider">
                    HOTLINE KINH DOANH DOANH NGHIỆP
                </span>
                <h3 class="text-lg sm:text-xl font-black text-white">
                    Cần Báo Giá Gấp Trong 10 Phút?
                </h3>
                <p class="text-xs text-slate-300 leading-relaxed">
                    Quý khách có thể gửi trực tiếp danh mục file Excel qua Zalo của Giám đốc kinh doanh để nhận báo giá chiết khấu kèm mẫu hợp đồng:
                </p>
                <div class="pt-2 space-y-2">
                    <a href="https://zalo.me/0901234567" target="_blank" class="w-full py-3 px-4 bg-sky-600 hover:bg-sky-500 text-white font-bold text-xs rounded-xl flex items-center justify-center space-x-2 shadow-xs transition">
                        <span>💬</span>
                        <span>Nhắn Zalo Gửi Danh Mục Báo Giá</span>
                    </a>
                    <a href="tel:0901234567" class="w-full py-3 px-4 bg-white text-slate-900 hover:bg-slate-100 font-bold text-xs rounded-xl flex items-center justify-center space-x-2 transition">
                        <span>📞 Hotline: 0901.234.567</span>
                    </a>
                </div>
            </div>

            <!-- FAQs Box -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xs space-y-3 text-xs">
                <h4 class="font-bold text-slate-900 text-sm">Câu Hỏi Thường Gặp Về Đại Lý:</h4>
                <div class="space-y-2 text-slate-600">
                    <div>
                        <p class="font-bold text-slate-800">• Mua bao nhiêu thì được tính giá sỉ?</p>
                        <p class="text-[11px] text-slate-500 pl-3">Từ 5 thùng giấy in trở lên hoặc đơn hàng tổng cộng từ 1.000.000₫.</p>
                    </div>
                    <div>
                        <p class="font-bold text-slate-800">• Cửa hàng có gửi mẫu sản phẩm thử không?</p>
                        <p class="text-[11px] text-slate-500 pl-3">Có, chúng tôi gửi mẫu giấy in và bút thử miễn phí cho các công ty đăng ký.</p>
                    </div>
                    <div>
                        <p class="font-bold text-slate-800">• Thủ tục ký hợp đồng công nợ như thế nào?</p>
                        <p class="text-[11px] text-slate-500 pl-3">Chỉ cần giấy phép đăng ký kinh doanh photo, hợp đồng hoàn tất trong 24 giờ.</p>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
