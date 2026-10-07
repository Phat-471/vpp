<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelper;
use App\Models\Category;
use App\Models\Customer;
use App\Models\DealerInquiry;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PrinterModel;
use App\Models\Product;
use App\Models\ProductUnit;
use App\Models\RepairTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StorefrontController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();
        $printerModels = PrinterModel::orderBy('brand')->orderBy('model_name')->get();

        $selectedCategory = $request->query('category');
        $selectedPrinter = $request->query('printer');
        $searchKeyword = trim($request->query('q', ''));

        $productsQuery = Product::with(['category', 'units', 'compatiblePrinters'])
            ->where('is_active', true);

        if ($searchKeyword) {
            $productsQuery->where(function ($q) use ($searchKeyword) {
                $q->where('name', 'LIKE', "%{$searchKeyword}%")
                  ->orWhere('sku', 'LIKE', "%{$searchKeyword}%")
                  ->orWhere('barcode', 'LIKE', "%{$searchKeyword}%");
            });
        }

        if ($selectedCategory) {
            $productsQuery->whereHas('category', fn ($q) => $q->where('slug', $selectedCategory));
        }

        if ($selectedPrinter) {
            $productsQuery->whereHas('compatiblePrinters', fn ($q) => $q->where('printer_models.id', $selectedPrinter));
        }

        $products = $productsQuery->orderBy('id', 'desc')->take(40)->get();

        // Quick stats for desktop credibility bar
        $stats = [
            'total_products' => Product::where('is_active', true)->count(),
            'total_printers' => PrinterModel::count(),
            'served_tickets' => RepairTicket::count(),
        ];

        return view('storefront.index', compact(
            'categories',
            'printerModels',
            'products',
            'selectedCategory',
            'selectedPrinter',
            'searchKeyword',
            'stats'
        ));
    }

    public function products(Request $request)
    {
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();
        $printerModels = PrinterModel::orderBy('brand')->orderBy('model_name')->get();

        $selectedCategory = $request->query('category');
        $selectedPrinter = $request->query('printer');
        $searchKeyword = trim($request->query('q', ''));
        $sort = $request->query('sort', 'newest');

        $currentCategory = $selectedCategory ? Category::where('slug', $selectedCategory)->first() : null;

        $query = Product::with(['category', 'units', 'compatiblePrinters'])
            ->where('is_active', true);

        if ($searchKeyword) {
            $query->where(function ($q) use ($searchKeyword) {
                $q->where('name', 'LIKE', "%{$searchKeyword}%")
                  ->orWhere('sku', 'LIKE', "%{$searchKeyword}%")
                  ->orWhere('barcode', 'LIKE', "%{$searchKeyword}%");
            });
        }

        if ($selectedCategory) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $selectedCategory));
        }

        if ($selectedPrinter) {
            $query->whereHas('compatiblePrinters', fn ($q) => $q->where('printer_models.id', $selectedPrinter));
        }

        switch ($sort) {
            case 'price_asc':
                $query->orderBy('retail_price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('retail_price', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'newest':
            default:
                $query->orderBy('id', 'desc');
                break;
        }

        $products = $query->paginate(24);

        return view('storefront.products', compact(
            'categories',
            'printerModels',
            'products',
            'selectedCategory',
            'selectedPrinter',
            'searchKeyword',
            'currentCategory',
            'sort'
        ));
    }

    public function productDetail($slug)
    {
        $product = Product::with(['category', 'units', 'compatiblePrinters'])
            ->where('is_active', true)
            ->where(function ($q) use ($slug) {
                $q->where('slug', $slug)
                  ->orWhere('id', is_numeric($slug) ? (int)$slug : 0);
            })
            ->firstOrFail();

        $relatedProducts = Product::with(['category', 'units'])
            ->where('is_active', true)
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(4)
            ->get();

        return view('storefront.product-detail', compact('product', 'relatedProducts'));
    }

    public function checkoutPage()
    {
        return view('storefront.checkout');
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:100',
            'customer_phone' => 'required|string|max:20',
            'customer_address' => 'required|string|max:255',
            'payment_method' => 'required|in:cod,vietqr',
            'notes' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.unit_id' => 'nullable|exists:product_units,id',
            'items.*.quantity' => 'required|integer|min:1',
        ], [
            'customer_name.required' => 'Vui lòng nhập họ và tên của bạn.',
            'customer_phone.required' => 'Vui lòng nhập số điện thoại nhận hàng.',
            'customer_address.required' => 'Vui lòng nhập địa chỉ giao hàng cụ thể.',
            'payment_method.required' => 'Vui lòng chọn phương thức thanh toán.',
            'items.required' => 'Giỏ hàng đang trống, vui lòng chọn ít nhất 1 sản phẩm.',
            'items.min' => 'Giỏ hàng đang trống, vui lòng chọn ít nhất 1 sản phẩm.',
        ]);

        $cleanedPhone = AppHelper::cleanPhone($request->customer_phone);

        DB::beginTransaction();
        try {
            if (auth('customer')->check()) {
                $customer = auth('customer')->user();
                $customer->update([
                    'name' => $request->customer_name,
                    'address' => $request->customer_address ?: $customer->address,
                ]);
            } else {
                $customer = Customer::firstOrCreate(
                    ['phone' => $cleanedPhone],
                    [
                        'name' => $request->customer_name,
                        'phone_last4' => substr($cleanedPhone, -4),
                        'address' => $request->customer_address,
                    ]
                );
            }

            $order = Order::create([
                'customer_id' => $customer->id,
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'customer_address' => $request->customer_address,
                'channel' => 'web',
                'status' => 'pending',
                'subtotal' => 0,
                'discount_amount' => 0,
                'tax_rate' => 0,
                'tax_amount' => 0,
                'grand_total' => 0,
                'paid_amount' => 0,
                'payment_status' => 'pending',
                'payment_method' => $request->payment_method,
                'notes' => $request->notes,
            ]);

            $total = 0;
            foreach ($request->items as $cartItem) {
                $product = Product::find($cartItem['product_id']);
                if (!$product) {
                    continue;
                }

                $unitName = $product->base_unit;
                $unitPrice = (float) $product->retail_price;
                $conversionRate = 1;
                $unitId = $cartItem['unit_id'] ?? null;

                if ($unitId) {
                    $unit = ProductUnit::where('product_id', $product->id)->find($unitId);
                    if ($unit) {
                        $unitName = $unit->unit_name;
                        $unitPrice = (float) $unit->price;
                        $conversionRate = (int) $unit->conversion_rate;
                    } else {
                        $unitId = null;
                    }
                }

                $qty = (int) $cartItem['quantity'];
                $subtotal = $qty * $unitPrice;
                $total += $subtotal;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_unit_id' => $unitId,
                    'product_name' => $product->name,
                    'unit_name' => $unitName,
                    'quantity' => $qty,
                    'conversion_rate' => $conversionRate,
                    'unit_price' => $unitPrice,
                    'subtotal' => $subtotal,
                ]);
            }

            $order->update([
                'subtotal' => $total,
                'grand_total' => $total,
            ]);

            DB::commit();

            $vietQrUrl = null;
            if ($request->payment_method === 'vietqr') {
                $vietQrUrl = AppHelper::generateVietQrUrl($order->grand_total, $order->order_code);
            }

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Đặt hàng thành công!',
                    'order_code' => $order->order_code,
                    'grand_total' => $order->grand_total,
                    'grand_total_formatted' => AppHelper::formatMoney($order->grand_total),
                    'payment_method' => $order->payment_method,
                    'viet_qr_url' => $vietQrUrl,
                ]);
            }

            return redirect()->route('storefront.checkout-page')
                ->with('order_success', [
                    'code' => $order->order_code,
                    'total' => $order->grand_total,
                    'qr' => $vietQrUrl,
                ]);

        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra: ' . $e->getMessage(),
                ], 422);
            }

            return back()->withInput()->withErrors(['checkout' => $e->getMessage()]);
        }
    }

    public function wholesale()
    {
        return view('storefront.wholesale');
    }

    public function postWholesale(Request $request)
    {
        $request->validate([
            'company_name' => 'required|string|max:150',
            'contact_name' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:150',
            'address' => 'nullable|string|max:255',
            'business_type' => 'nullable|string|max:100',
            'estimated_monthly_budget' => 'nullable|string|max:100',
            'interested_categories' => 'nullable|array',
            'notes' => 'nullable|string|max:1000',
        ], [
            'company_name.required' => 'Vui lòng nhập tên công ty hoặc đơn vị.',
            'contact_name.required' => 'Vui lòng nhập tên người đại diện liên hệ.',
            'phone.required' => 'Vui lòng nhập số điện thoại liên hệ.',
        ]);

        DealerInquiry::create([
            'company_name' => $request->company_name,
            'contact_name' => $request->contact_name,
            'phone' => $request->phone,
            'email' => $request->email,
            'address' => $request->address,
            'business_type' => $request->business_type,
            'estimated_monthly_budget' => $request->estimated_monthly_budget,
            'interested_categories' => $request->interested_categories,
            'notes' => $request->notes,
            'status' => 'pending',
        ]);

        return back()->with('dealer_success', 'Cảm ơn Quý khách! Yêu cầu đăng ký đại lý đã được gửi thành công. Bộ phận khách hàng doanh nghiệp sẽ liên hệ lại trong vòng 30 phút.');
    }

    public function registerForm()
    {
        if (auth('customer')->check()) {
            return redirect()->route('customer.profile');
        }
        return view('storefront.auth.register');
    }

    public function postRegister(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:150',
            'address' => 'nullable|string|max:255',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'name.required' => 'Vui lòng nhập họ và tên.',
            'phone.required' => 'Vui lòng nhập số điện thoại.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min' => 'Mật khẩu phải từ 6 ký tự trở lên.',
            'password.confirmed' => 'Mật khẩu xác nhận không khớp.',
        ]);

        $cleanedPhone = AppHelper::cleanPhone($request->phone);

        $customer = Customer::where('phone', $cleanedPhone)->first();
        if ($customer && !empty($customer->password)) {
            return back()->withInput()->with('error', 'Số điện thoại này đã được đăng ký tài khoản. Vui lòng đăng nhập!');
        }

        if ($customer) {
            $customer->update([
                'name' => $request->name,
                'email' => $request->email,
                'address' => $request->address ?: $customer->address,
                'password' => $request->password,
            ]);
        } else {
            $customer = Customer::create([
                'name' => $request->name,
                'phone' => $cleanedPhone,
                'phone_last4' => substr($cleanedPhone, -4),
                'email' => $request->email,
                'address' => $request->address,
                'password' => $request->password,
            ]);
        }

        auth('customer')->login($customer);

        return redirect()->route('customer.profile')->with('success', 'Đăng ký tài khoản thành công! Chào mừng ' . $customer->name);
    }

    public function loginForm()
    {
        if (auth('customer')->check()) {
            return redirect()->route('customer.profile');
        }
        return view('storefront.auth.login');
    }

    public function postLogin(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'password' => 'required|string',
        ], [
            'phone.required' => 'Vui lòng nhập số điện thoại.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
        ]);

        $cleanedPhone = AppHelper::cleanPhone($request->phone);

        $credentials = [
            'phone' => $cleanedPhone,
            'password' => $request->password,
        ];

        if (auth('customer')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('customer.profile'))->with('success', 'Đăng nhập thành công!');
        }

        return back()->withInput()->with('error', 'Số điện thoại hoặc mật khẩu không chính xác.');
    }

    public function logout(Request $request)
    {
        auth('customer')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('storefront.index')->with('success', 'Bạn đã đăng xuất thành công.');
    }

    public function profile()
    {
        if (!auth('customer')->check()) {
            return redirect()->route('customer.login')->with('error', 'Vui lòng đăng nhập để xem thông tin tài khoản.');
        }

        $customer = auth('customer')->user();
        $orders = Order::with('items')->where('customer_id', $customer->id)->orderBy('id', 'desc')->get();
        $repairTickets = RepairTicket::where('customer_id', $customer->id)->orderBy('id', 'desc')->get();

        return view('storefront.auth.profile', compact('customer', 'orders', 'repairTickets'));
    }

    public function updateProfile(Request $request)
    {
        if (!auth('customer')->check()) {
            return redirect()->route('customer.login');
        }

        $customer = auth('customer')->user();

        $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:150',
            'address' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:6',
        ]);

        $updateData = [
            'name' => $request->name,
            'phone' => AppHelper::cleanPhone($request->phone),
            'email' => $request->email,
            'address' => $request->address,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = $request->password;
        }

        $customer->update($updateData);

        return back()->with('success', 'Cập nhật thông tin tài khoản thành công!');
    }

    public function bookRepair(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:100',
            'customer_phone' => 'required|string|max:20',
            'device_name' => 'required|string|max:150',
            'issue_description' => 'required|string',
        ], [
            'customer_name.required' => 'Vui lòng nhập họ tên của bạn.',
            'customer_phone.required' => 'Vui lòng nhập số điện thoại liên hệ.',
            'device_name.required' => 'Vui lòng nhập tên dòng máy in cần sửa.',
            'issue_description.required' => 'Vui lòng mô tả sơ lược hiện trạng lỗi máy in.',
        ]);

        $cleanedPhone = preg_replace('/\D/', '', $request->customer_phone);

        $customer = Customer::firstOrCreate(
            ['phone' => $cleanedPhone],
            [
                'name' => $request->customer_name,
                'phone_last4' => substr($cleanedPhone, -4),
            ]
        );

        $ticket = RepairTicket::create([
            'customer_id' => $customer->id,
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'phone_last4' => substr($cleanedPhone, -4),
            'device_name' => $request->device_name,
            'printer_model_id' => $request->printer_model_id ?: null,
            'issue_description' => $request->issue_description,
            'intake_flow' => 'quote_later',
            'status' => 'received',
            'promised_at' => now()->addDays(1)->setTime(16, 0),
        ]);

        return redirect()->route('lookup.view', ['code' => $ticket->ticket_code, 'phone4' => $ticket->phone_last4])
            ->with('success', 'Đã tiếp nhận yêu cầu sửa máy thành công! Mã phiếu của bạn là: ' . $ticket->ticket_code);
    }

    public function about()
    {
        return view('storefront.about');
    }

    public function privacy()
    {
        return view('storefront.privacy');
    }

    public function terms()
    {
        return view('storefront.terms');
    }
}
