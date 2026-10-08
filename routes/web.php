<?php

use App\Http\Controllers\BusinessTaxLookupController;
use App\Http\Controllers\LiveChatController;
use App\Http\Controllers\LookupController;
use App\Http\Controllers\PosAuthController;
use App\Http\Controllers\PosReceiptController;
use App\Http\Controllers\PrintController;
use App\Http\Controllers\StorefrontController;
use App\Http\Controllers\ZaloAuthController;
use App\Livewire\PosTerminal;
use Illuminate\Support\Facades\Route;

Route::get('/pos/login', [PosAuthController::class, 'create'])->name('pos.login');
Route::post('/pos/login', [PosAuthController::class, 'store'])->middleware(['guest:web', 'throttle:pos-login'])->name('pos.authenticate');
Route::middleware(['auth:web', 'can:use-pos'])->prefix('pos')->name('pos.')->group(function () {
    Route::get('/', PosTerminal::class)->name('index');
    Route::get('/khach-hang', \App\Livewire\PosCustomers::class)->name('customers');
    Route::post('/logout', [PosAuthController::class, 'destroy'])->name('logout');
    Route::get('/hoa-don/{order:uuid}', PosReceiptController::class)->name('receipt');
});

// 1. Storefront & Sản phẩm & Thanh toán
Route::get('/', [StorefrontController::class, 'index'])->name('storefront.index');
Route::get('/san-pham', [StorefrontController::class, 'products'])->name('storefront.products');
Route::get('/san-pham/{slug}', [StorefrontController::class, 'productDetail'])->name('storefront.product-detail');
Route::get('/gio-hang', [StorefrontController::class, 'checkoutPage'])->name('storefront.cart');
Route::get('/thanh-toan', [StorefrontController::class, 'checkoutPage'])->name('storefront.checkout-page');
Route::post('/dat-hang-online', [StorefrontController::class, 'checkout'])->name('storefront.checkout');
Route::post('/tra-cuu-doanh-nghiep', BusinessTaxLookupController::class)->middleware('throttle:30,1')->name('business.lookup');

// 2. Đăng ký đại lý & Mua sỉ số lượng lớn
Route::get('/dang-ky-dai-ly', [StorefrontController::class, 'wholesale'])->name('storefront.wholesale');
Route::post('/dang-ky-dai-ly', [StorefrontController::class, 'postWholesale'])->name('storefront.post-wholesale');

// 3. Tài khoản khách hàng & Đăng ký / Đăng nhập
Route::get('/dang-ky', [StorefrontController::class, 'registerForm'])->name('customer.register');
Route::post('/dang-ky', [StorefrontController::class, 'postRegister'])->middleware('throttle:10,1')->name('customer.post-register');
Route::get('/dang-nhap', [StorefrontController::class, 'loginForm'])->name('customer.login');
Route::post('/dang-nhap', [StorefrontController::class, 'postLogin'])->name('customer.post-login');
Route::match(['get', 'post'], '/dang-xuat', [StorefrontController::class, 'logout'])->name('customer.logout');
Route::get('/tai-khoan', [StorefrontController::class, 'profile'])->name('customer.profile');
Route::post('/tai-khoan', [StorefrontController::class, 'updateProfile'])->name('customer.update-profile');

// 3.1. Xác thực & Đăng ký 1 chạm qua mã QR Zalo (Không cần OTP)
Route::get('/xac-thuc-zalo/{token}', [ZaloAuthController::class, 'showVerificationPage'])->name('zalo.verify.page');
Route::prefix('zalo-auth')->name('zalo.verify.')->group(function () {
    Route::post('/init', [ZaloAuthController::class, 'init'])->name('init');
    Route::get('/check/{token}', [ZaloAuthController::class, 'check'])->name('check');
    Route::get('/prompt/{token}', [ZaloAuthController::class, 'prompt'])->name('prompt');
    Route::post('/confirm', [ZaloAuthController::class, 'confirm'])->name('confirm');
    Route::post('/mock-confirm', [ZaloAuthController::class, 'mockConfirm'])->name('mock-confirm');
});

// 4. Giới thiệu & Chính sách dịch vụ & Trang CMS động
Route::get('/trang/{slug}', [StorefrontController::class, 'dynamicPage'])->name('storefront.page');
Route::get('/gioi-thieu', [StorefrontController::class, 'about'])->name('storefront.about');
Route::get('/chinh-sach-bao-mat', [StorefrontController::class, 'privacy'])->name('storefront.privacy');
Route::get('/chinh-sach-mua-hang', [StorefrontController::class, 'terms'])->name('storefront.terms');
Route::post('/dat-lich-sua-chua', [StorefrontController::class, 'bookRepair'])->name('storefront.book');

// 5. Live Chat Khách hàng 2 chiều
Route::post('/api/chat/init', [LiveChatController::class, 'init'])->name('chat.init');
Route::post('/api/chat/send', [LiveChatController::class, 'send'])->name('chat.send');
Route::get('/api/chat/messages', [LiveChatController::class, 'getMessages'])->name('chat.messages');

// 6. Tra cứu tiến độ sửa máy in (Chống IDOR: Mã phiếu + 4 số cuối SĐT)
Route::get('/tra-cuu', [LookupController::class, 'index'])->name('lookup.index');
Route::post('/tra-cuu', [LookupController::class, 'search'])->name('lookup.search');
Route::get('/tra-cuu/{code}', [LookupController::class, 'view'])->name('lookup.view');

// 7. In ấn chuẩn A4 / A5 máy in văn phòng
Route::get('/print/repair-ticket/{id}', [PrintController::class, 'repairTicket'])->name('print.repair-ticket');
Route::get('/print/order/{id}', [PrintController::class, 'order'])->name('print.order');
Route::get('/print/vat-invoice/{id}', [PrintController::class, 'vatInvoice'])->name('print.vat-invoice');
