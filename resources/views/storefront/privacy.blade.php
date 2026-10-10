@extends('layouts.storefront')

@section('title', 'Chính Sách Bảo Mật Thông Tin & Dữ Liệu Máy In | ' . ($storefrontSettings['site_name'] ?? 'VPP & Dịch Vụ Máy In'))
@section('meta_description', 'Chính sách bảo mật thông tin cá nhân và bảo mật dữ liệu tài liệu trên máy in của khách hàng tại Cửa hàng Văn Phòng Phẩm & Dịch Vụ Máy In.')

@section('schema_extra')
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@type": "BreadcrumbList",
  "itemListElement": [
    {
      "@type": "ListItem",
      "position": 1,
      "name": "Trang chủ",
      "item": "{{ route('storefront.index') }}"
    },
    {
      "@type": "ListItem",
      "position": 2,
      "name": "Chính sách bảo mật",
      "item": "{{ route('storefront.privacy') }}"
    }
  ]
}
</script>
@endsection

@section('content')
<main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12 space-y-8 flex-1">
    
    <!-- Breadcrumbs -->
    <nav class="flex items-center space-x-2 text-xs text-slate-500 font-medium">
        <a href="{{ route('storefront.index') }}" class="hover:text-indigo-600">Trang chủ</a>
        <span>/</span>
        <span class="text-slate-900 font-bold">Chính sách bảo mật thông tin & dữ liệu</span>
    </nav>

    <!-- Title Banner Box -->
    <div class="bg-gradient-to-br from-indigo-950 via-slate-900 to-indigo-900 text-white p-6 sm:p-10 rounded-3xl shadow-xl border border-indigo-800 space-y-3">
        <div class="inline-flex items-center space-x-2 bg-emerald-500/20 text-emerald-300 px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider border border-emerald-400/30">
            <span>🛡️ TIÊU CHUẨN AN TOÀN DỮ LIỆU TỐI CAO</span>
        </div>
        <h1 class="text-2xl sm:text-4xl font-black text-white leading-tight">
            Chính Sách Bảo Mật Thông Tin & Dữ Liệu Máy In Doanh Nghiệp
        </h1>
        <p class="text-xs sm:text-sm text-slate-300 leading-relaxed max-w-2xl">
            Chúng tôi hiểu rằng máy in và văn phòng phẩm là nơi lưu giữ và in ấn các tài liệu hợp đồng, số liệu tài chính quan trọng của quý khách. Cửa hàng cam kết áp dụng các biện pháp an ninh nghiêm ngặt nhất.
        </p>
    </div>

    <!-- Privacy Content Sections -->
    <div class="bg-white rounded-3xl p-6 sm:p-10 shadow-sm border border-slate-200/80 space-y-8 text-sm text-slate-700 leading-relaxed">

        <!-- Section 1: Machine Data Confidentiality -->
        <section class="space-y-3">
            <h2 class="text-lg sm:text-xl font-black text-slate-900 flex items-center space-x-2.5">
                <span class="w-8 h-8 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center text-sm font-black flex-shrink-0">1</span>
                <span>Cam Kết Bảo Mật Dữ Liệu Thiết Bị & Bản In Thử (Rất Quan Trọng)</span>
            </h2>
            <div class="pl-10 space-y-2 text-xs sm:text-sm">
                <p>
                    Khi khách hàng gửi máy in, máy scan hoặc máy photocopy đến cửa hàng để bảo trì, sửa chữa hoặc nạp mực:
                </p>
                <ul class="list-disc pl-5 space-y-1.5 text-slate-600">
                    <li><b>Tuyệt đối không sao chép hoặc trích xuất dữ liệu:</b> Kỹ thuật viên bị nghiêm cấm truy cập vào bộ nhớ đệm, ổ cứng (đối với dòng máy in đa chức năng) hoặc sao lưu các tệp tài liệu của khách hàng dưới bất kỳ hình thức nào.</li>
                    <li><b>Quy trình xử lý giấy test kỹ thuật:</b> Các trang in test trong quá trình kiểm tra màu mực và trống in chỉ sử dụng tài liệu kiểm tra tiêu chuẩn (Test Page của hệ điều hành). Nếu có tài liệu còn sót lại trên khay nạp giấy, nhân viên sẽ gửi trả nguyên vẹn cho khách hoặc tiêu hủy bằng máy cắt giấy tại quầy nếu có yêu cầu.</li>
                    <li><b>Trách nhiệm của thợ kỹ thuật:</b> 100% nhân viên kỹ thuật đều ký cam kết bảo mật thông tin nội bộ (NDA). Mọi hành vi vi phạm sẽ bị xử lý nghiêm minh theo quy định pháp luật.</li>
                </ul>
            </div>
        </section>

        <!-- Section 2: Tra Cứu Bảo Mật -->
        <section class="space-y-3 border-t border-slate-100 pt-6">
            <h2 class="text-lg sm:text-xl font-black text-slate-900 flex items-center space-x-2.5">
                <span class="w-8 h-8 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center text-sm font-black flex-shrink-0">2</span>
                <span>Bảo Mật Thông Tin Tra Cứu Trực Tuyến</span>
            </h2>
            <div class="pl-10 space-y-2 text-xs sm:text-sm">
                <p>
                    Để đảm bảo quyền riêng tư và an toàn thông tin của khách hàng:
                </p>
                <ul class="list-disc pl-5 space-y-1.5 text-slate-600">
                    <li><b>Xác thực người nhận máy:</b> Khi tra cứu tiến độ sửa chữa máy in, khách hàng cần nhập đúng <b>Mã phiếu tiếp nhận và 4 số cuối số điện thoại</b> đăng ký gửi máy. Người khác không thể xem lén chi tiết thiết bị hay chi phí sửa chữa.</li>
                    <li><b>Bảo vệ thông tin cá nhân:</b> Mọi dữ liệu về tình trạng thiết bị, linh kiện thay thế và chi phí chỉ được hiển thị sau khi nhập đúng thông tin xác thực.</li>
                </ul>
            </div>
        </section>

        <!-- Section 3: Customer Personal Data -->
        <section class="space-y-3 border-t border-slate-100 pt-6">
            <h2 class="text-lg sm:text-xl font-black text-slate-900 flex items-center space-x-2.5">
                <span class="w-8 h-8 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center text-sm font-black flex-shrink-0">3</span>
                <span>Mục Đích Thu Thập & Sử Dụng Thông Tin Cá Nhân</span>
            </h2>
            <div class="pl-10 space-y-2 text-xs sm:text-sm">
                <p>
                    Chúng tôi chỉ thu thập các thông tin tối thiểu cần thiết phục vụ cho việc vận hành dịch vụ:
                </p>
                <ul class="list-disc pl-5 space-y-1.5 text-slate-600">
                    <li><b>Họ tên & Số điện thoại:</b> Dùng để liên hệ báo giá sửa chữa, thông báo máy đã sửa xong và gọi điện giao hàng văn phòng phẩm.</li>
                    <li><b>Địa chỉ giao nhận:</b> Dùng để giao hàng văn phòng phẩm và trả máy in tận nơi theo yêu cầu của khách hàng.</li>
                    <li><b>Cam kết không chia sẻ bên thứ ba:</b> Chúng tôi cam kết không bán, không cho thuê hoặc chuyển nhượng thông tin khách hàng cho bất kỳ công ty quảng cáo hoặc bên thứ ba nào.</li>
                </ul>
            </div>
        </section>

        <!-- Section 4: Payment Security -->
        <section class="space-y-3 border-t border-slate-100 pt-6">
            <h2 class="text-lg sm:text-xl font-black text-slate-900 flex items-center space-x-2.5">
                <span class="w-8 h-8 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center text-sm font-black flex-shrink-0">4</span>
                <span>An Toàn Giao Dịch Thanh Toán & VietQR</span>
            </h2>
            <div class="pl-10 space-y-2 text-xs sm:text-sm">
                <p>
                    Khi quý khách thanh toán qua mã VietQR động hoặc tiền mặt:
                </p>
                <ul class="list-disc pl-5 space-y-1.5 text-slate-600">
                    <li>Hệ thống tạo mã VietQR theo chuẩn Napas 24/7 trực tiếp từ ngân hàng, không thu thập hay lưu trữ thông tin thẻ tín dụng/mật khẩu tài khoản ngân hàng của khách.</li>
                    <li>Mọi giao dịch ngân hàng được đồng bộ tự động mã hóa qua cổng an toàn với chứng chỉ SSL 256-bit.</li>
                </ul>
            </div>
        </section>

        <!-- Section 5: Data Rights -->
        <section class="space-y-3 border-t border-slate-100 pt-6">
            <h2 class="text-lg sm:text-xl font-black text-slate-900 flex items-center space-x-2.5">
                <span class="w-8 h-8 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center text-sm font-black flex-shrink-0">5</span>
                <span>Quyền Của Khách Hàng Đối Với Dữ Liệu</span>
            </h2>
            <div class="pl-10 space-y-2 text-xs sm:text-sm">
                <p>
                    Khách hàng có toàn quyền yêu cầu cửa hàng:
                </p>
                <ul class="list-disc pl-5 space-y-1.5 text-slate-600">
                    <li>Kiểm tra, cập nhật hoặc điều chỉnh thông tin cá nhân của mình.</li>
                    <li>Yêu cầu xóa toàn bộ lịch sử đơn hàng và số điện thoại khỏi hệ thống sau khi đã hoàn tất hợp đồng và hết thời hạn bảo hành.</li>
                    <li>Mọi yêu cầu xin liên hệ hotline: <b>{{ $storefrontSettings['hotline'] }}</b>, Zalo: <b>{{ $storefrontSettings['zalo'] }}</b> hoặc email hỗ trợ: <b>{{ $storefrontSettings['email'] }}</b>.</li>
                </ul>
            </div>
        </section>

    </div>

</main>
@endsection
