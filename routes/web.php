<?php

use App\Http\Controllers\LookupController;
use App\Http\Controllers\PrintController;
use App\Http\Controllers\StorefrontController;
use Illuminate\Support\Facades\Route;

// 1. Storefront & Sản phẩm & Thanh toán
Route::get('/', [StorefrontController::class, 'index'])->name('storefront.index');
Route::get('/san-pham', [StorefrontController::class, 'products'])->name('storefront.products');
Route::get('/san-pham/{slug}', [StorefrontController::class, 'productDetail'])->name('storefront.product-detail');
Route::get('/thanh-toan', [StorefrontController::class, 'checkoutPage'])->name('storefront.checkout-page');
Route::post('/dat-hang-online', [StorefrontController::class, 'checkout'])->name('storefront.checkout');

// 2. Đăng ký đại lý & Mua sỉ số lượng lớn
Route::get('/dang-ky-dai-ly', [StorefrontController::class, 'wholesale'])->name('storefront.wholesale');
Route::post('/dang-ky-dai-ly', [StorefrontController::class, 'postWholesale'])->name('storefront.post-wholesale');

// 3. Tài khoản khách hàng & Đăng ký / Đăng nhập
Route::get('/dang-ky', [StorefrontController::class, 'registerForm'])->name('customer.register');
Route::post('/dang-ky', [StorefrontController::class, 'postRegister'])->name('customer.post-register');
Route::get('/dang-nhap', [StorefrontController::class, 'loginForm'])->name('customer.login');
Route::post('/dang-nhap', [StorefrontController::class, 'postLogin'])->name('customer.post-login');
Route::match(['get', 'post'], '/dang-xuat', [StorefrontController::class, 'logout'])->name('customer.logout');
Route::get('/tai-khoan', [StorefrontController::class, 'profile'])->name('customer.profile');
Route::post('/tai-khoan', [StorefrontController::class, 'updateProfile'])->name('customer.update-profile');

// 4. Giới thiệu & Chính sách dịch vụ
Route::get('/gioi-thieu', [StorefrontController::class, 'about'])->name('storefront.about');
Route::get('/chinh-sach-bao-mat', [StorefrontController::class, 'privacy'])->name('storefront.privacy');
Route::get('/chinh-sach-mua-hang', [StorefrontController::class, 'terms'])->name('storefront.terms');
Route::post('/dat-lich-sua-chua', [StorefrontController::class, 'bookRepair'])->name('storefront.book');

// 5. Tra cứu tiến độ sửa máy in (Chống IDOR: Mã phiếu + 4 số cuối SĐT)
Route::get('/tra-cuu', [LookupController::class, 'index'])->name('lookup.index');
Route::post('/tra-cuu', [LookupController::class, 'search'])->name('lookup.search');
Route::get('/tra-cuu/{code}', [LookupController::class, 'view'])->name('lookup.view');

// 6. In ấn chuẩn A4 / A5 máy in văn phòng
Route::get('/print/repair-ticket/{id}', [PrintController::class, 'repairTicket'])->name('print.repair-ticket');
Route::get('/print/order/{id}', [PrintController::class, 'order'])->name('print.order');
