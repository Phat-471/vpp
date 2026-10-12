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
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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

        $isFiltering = !empty($searchKeyword) || !empty($selectedCategory) || !empty($selectedPrinter);

        $targetCategorySlugs = [
            'giay-in-photo',
            'hop-muc-may-in',
            'but-viet-muc-viet',
            'bia-ho-so-luu-tru',
            'dung-cu-van-phong'
        ];

        // 1. FLASH SALE & BEST SELLER (Cached danh sách ID số nguyên siêu nhẹ, tránh lỗi serialization)
        if (!$isFiltering) {
            $flashSaleIds = Cache::remember('storefront_flash_sale_ids', 300, function () {
                $flagged = Product::where('is_active', true)
                    ->where('retail_price', '>', 0)
                    ->where('is_flash_sale', true)
                    ->orderBy('id', 'desc')
                    ->take(16)
                    ->pluck('id')
                    ->all();

                if (count($flagged) < 16) {
                    $fillers = Product::where('is_active', true)
                        ->where('retail_price', '>', 0)
                        ->whereNotIn('id', $flagged)
                        ->orderBy('id', 'asc')
                        ->take(16 - count($flagged))
                        ->pluck('id')
                        ->all();
                    return array_merge($flagged, $fillers);
                }
                return $flagged;
            });

            $flashSaleProducts = !empty($flashSaleIds)
                ? Product::with(['category', 'units'])
                    ->whereIn('id', $flashSaleIds)
                    ->get()
                    ->sortBy(fn ($p) => array_search($p->id, $flashSaleIds))
                    ->values()
                : collect();

            $bestSellerIds = Cache::remember('storefront_best_seller_ids', 300, function () {
                $flagged = Product::where('is_active', true)
                    ->where('retail_price', '>', 0)
                    ->where('is_best_seller', true)
                    ->orderBy('id', 'desc')
                    ->take(8)
                    ->pluck('id')
                    ->all();

                if (count($flagged) < 8) {
                    $fillers = Product::where('is_active', true)
                        ->where('retail_price', '>', 0)
                        ->whereNotIn('id', $flagged)
                        ->orderBy('stock_quantity', 'desc')
                        ->take(8 - count($flagged))
                        ->pluck('id')
                        ->all();
                    return array_merge($flagged, $fillers);
                }
                return $flagged;
            });

            $bestSellerProducts = !empty($bestSellerIds)
                ? Product::with(['category', 'units'])
                    ->whereIn('id', $bestSellerIds)
                    ->get()
                    ->sortBy(fn ($p) => array_search($p->id, $bestSellerIds))
                    ->values()
                : collect();

            // Lấy sản phẩm 5 danh mục trọng điểm (Indexed Eager Loading, tận dụng idx_products_active_cat_id)
            $categorySections = Category::where('is_active', true)
                ->whereIn('slug', $targetCategorySlugs)
                ->with(['products' => function ($q) {
                    $q->where('is_active', true)
                      ->with(['units', 'category'])
                      ->orderBy('id', 'asc')
                      ->take(8);
                }])
                ->get()
                ->sortBy(function ($cat) use ($targetCategorySlugs) {
                    return array_search($cat->slug, $targetCategorySlugs);
                })
                ->values();
        } else {
            $flashSaleProducts = collect();
            $bestSellerProducts = collect();
            $categorySections = collect();
        }

        $products = $productsQuery->orderBy('id', 'desc')->take(40)->get();

        // Quick stats for desktop credibility bar (Cached 10 phút)
        $stats = Cache::remember('storefront_stats', 600, function () {
            return [
                'total_products' => Product::where('is_active', true)->count(),
                'total_printers' => PrinterModel::count(),
                'served_tickets' => RepairTicket::count(),
            ];
        });

        $template = (string) $request->query('template', setting('homepage_template', '1'));

        if ($template === '2' || $template === 'b2b') {
            // Lấy sản phẩm giấy in bán sỉ theo thùng (Double A, PaperOne, IK Plus...)
            $b2bPaperProducts = Product::with(['category', 'units'])
                ->where('is_active', true)
                ->whereHas('category', fn ($q) => $q->where('slug', 'giay-in-photo'))
                ->orderByDesc('is_best_seller')
                ->take(8)
                ->get();

            // Hộp mực & Linh kiện kỹ thuật phổ biến nhất
            $popularCartridges = Product::with(['category', 'units', 'compatiblePrinters'])
                ->where('is_active', true)
                ->whereHas('category', fn ($q) => $q->whereIn('slug', ['hop-muc-may-in', 'linh-kien-may-in']))
                ->orderByDesc('is_best_seller')
                ->take(8)
                ->get();

            return view('storefront.index-b2b', compact(
                'categories',
                'printerModels',
                'products',
                'flashSaleProducts',
                'bestSellerProducts',
                'categorySections',
                'isFiltering',
                'selectedCategory',
                'selectedPrinter',
                'searchKeyword',
                'stats',
                'b2bPaperProducts',
                'popularCartridges',
                'template'
            ));
        }

        return view('storefront.index', compact(
            'categories',
            'printerModels',
            'products',
            'flashSaleProducts',
            'bestSellerProducts',
            'categorySections',
            'isFiltering',
            'selectedCategory',
            'selectedPrinter',
            'searchKeyword',
            'stats',
            'template'
        ));
    }

    public function products(Request $request)
    {
        $categories = Category::where('is_active', true)
            ->withCount(['products' => fn ($q) => $q->where('is_active', true)])
            ->orderBy('sort_order')
            ->get();

        $printerModels = PrinterModel::orderBy('brand')->orderBy('model_name')->get();
        $printerBrands = PrinterModel::select('brand')->distinct()->orderBy('brand')->pluck('brand');

        $selectedCategory = $request->query('category');
        $selectedPrinter = $request->query('printer');
        $selectedBrand = $request->query('brand');
        $searchKeyword = trim($request->query('q', ''));
        $sort = $request->query('sort', 'popular');
        $priceRange = $request->query('price_range');
        $minPrice = $request->query('min_price');
        $maxPrice = $request->query('max_price');
        $onlyStock = $request->boolean('in_stock');
        $onlyFlashSale = $request->boolean('flash_sale');
        $onlyBestSeller = $request->boolean('best_seller');
        $viewMode = $request->query('view', 'grid');

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

        if ($selectedBrand) {
            $query->whereHas('compatiblePrinters', fn ($q) => $q->where('printer_models.brand', $selectedBrand));
        }

        if ($selectedPrinter) {
            $query->whereHas('compatiblePrinters', fn ($q) => $q->where('printer_models.id', $selectedPrinter));
        }

        // Lọc theo khoảng giá phổ biến
        if ($priceRange) {
            match ($priceRange) {
                'under_50k' => $query->where('retail_price', '<', 50000),
                '50k_150k' => $query->whereBetween('retail_price', [50000, 150000]),
                '150k_500k' => $query->whereBetween('retail_price', [150000, 500000]),
                'over_500k' => $query->where('retail_price', '>', 500000),
                default => null,
            };
        }
        if (is_numeric($minPrice) && $minPrice > 0) {
            $query->where('retail_price', '>=', (float)$minPrice);
        }
        if (is_numeric($maxPrice) && $maxPrice > 0) {
            $query->where('retail_price', '<=', (float)$maxPrice);
        }

        // Lọc theo tính năng
        if ($onlyStock) {
            $query->where('stock_quantity', '>', 0);
        }
        if ($onlyFlashSale) {
            $query->where('is_flash_sale', true);
        }
        if ($onlyBestSeller) {
            $query->where('is_best_seller', true);
        }

        // Sắp xếp
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
            case 'discount':
                $query->orderByRaw('(retail_price - COALESCE(flash_sale_price, retail_price)) DESC');
                break;
            case 'newest':
                $query->orderBy('id', 'desc');
                break;
            case 'popular':
            default:
                $query->orderByDesc('is_best_seller')
                      ->orderByDesc('stock_quantity')
                      ->orderByDesc('id');
                break;
        }

        $products = $query->paginate(24);

        return view('storefront.products', compact(
            'categories',
            'printerModels',
            'printerBrands',
            'products',
            'selectedCategory',
            'selectedPrinter',
            'selectedBrand',
            'searchKeyword',
            'currentCategory',
            'sort',
            'priceRange',
            'minPrice',
            'maxPrice',
            'onlyStock',
            'onlyFlashSale',
            'onlyBestSeller',
            'viewMode'
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

        // 1. Sản phẩm cùng ngành hàng gợi ý (tối đa 8 sản phẩm)
        $relatedProducts = Product::with(['category', 'units'])
            ->where('is_active', true)
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->orderBy('id', 'desc')
            ->take(8)
            ->get();

        // 2. Sản phẩm bán chạy & phụ kiện mua kèm gợi ý (tối đa 8 sản phẩm)
        $crossSellProducts = Product::with(['category', 'units'])
            ->where('is_active', true)
            ->where('retail_price', '>', 0)
            ->where('id', '!=', $product->id)
            ->where(function ($q) {
                $q->where('is_best_seller', true)
                  ->orWhere('is_featured', true)
                  ->orWhere('is_flash_sale', true);
            })
            ->orderBy('stock_quantity', 'desc')
            ->take(8)
            ->get();

        return view('storefront.product-detail', compact('product', 'relatedProducts', 'crossSellProducts'));
    }

    public function checkoutPage()
    {
        $shippingFeeDefault = (float) setting('shipping_fee_default', 25000);
        $freeshipThreshold = (float) setting('freeship_threshold', 500000);
        $defaultVatRate = (float) setting('default_vat_rate', 8);
        $bankCode = setting('vietqr_bank_code', 'MB');
        $bankName = setting('vietqr_bank_name', 'MB Bank');
        $accountNumber = setting('vietqr_account_number', '190333888999');
        $accountName = setting('vietqr_account_name', 'CONG TY TNHH VPP');

        return view('storefront.checkout', compact(
            'shippingFeeDefault',
            'freeshipThreshold',
            'defaultVatRate',
            'bankCode',
            'bankName',
            'accountNumber',
            'accountName'
        ));
    }

    public function checkout(\App\Http\Requests\CheckoutRequest $request)
    {
        $invoice = app(\App\Services\InvoiceDetails::class)->validate($request->all());
        $request->merge($invoice);
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

            $clientIp = $request->ip();
            $recentOrdersCount = Order::where(function ($q) use ($cleanedPhone, $clientIp) {
                    $q->where('customer_phone', $cleanedPhone)
                      ->orWhere('ip_address', $clientIp);
                })
                ->where('created_at', '>=', now()->subMinutes(15))
                ->count();

            $isSuspicious = $recentOrdersCount >= 2;
            $suspiciousReason = $isSuspicious
                ? "Phát hiện cùng SĐT hoặc IP vừa tạo {$recentOrdersCount} đơn hàng trong vòng 15 phút qua."
                : null;

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
                'ip_address' => $clientIp,
                'is_suspicious' => $isSuspicious,
                'suspicious_reason' => $suspiciousReason,
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

                if (!$unitId) {
                    if ($product->is_flash_sale && $product->flash_sale_price && $product->flash_sale_price > 0 && $product->flash_sale_price < $product->retail_price) {
                        $unitPrice = (float) $product->flash_sale_price;
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

            $taxRate = 0;
            $taxAmount = 0;
            if ($request->boolean('is_vat_invoice')) {
                $taxRate = (float) setting('default_vat_rate', 8);
                $taxAmount = round($total * ($taxRate / 100));
            }

            $shippingFee = 0;
            $freeshipThreshold = (float) setting('freeship_threshold', 500000);
            $defaultShippingFee = (float) setting('shipping_fee_default', 25000);
            if ($total < $freeshipThreshold && $total > 0) {
                $shippingFee = $defaultShippingFee;
            }

            $grandTotal = $total + $taxAmount + $shippingFee;

            $order->update([
                'is_vat_invoice' => $request->boolean('is_vat_invoice'),
                'company_name' => $request->company_name,
                'company_tax_id' => $request->company_tax_id,
                'company_address' => $request->company_address,
                'invoice_email' => $request->invoice_email,
                'tax_rate' => $taxRate,
                'tax_amount' => $taxAmount,
                'shipping_fee' => $shippingFee,
                'subtotal' => $total,
                'grand_total' => $grandTotal,
            ]);

            session()->put('verified_order_' . $order->uuid, true);

            DB::commit();

            $vietQrUrl = null;
            if ($request->payment_method === 'vietqr') {
                $vietQrUrl = AppHelper::generateVietQrUrl($order->grand_total, $order->order_code);
            }

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Đặt hàng thành công!',
                    'order_id' => $order->id,
                    'order_code' => $order->order_code,
                    'grand_total' => $order->grand_total,
                    'grand_total_formatted' => AppHelper::formatMoney($order->grand_total),
                    'payment_method' => $order->payment_method,
                    'viet_qr_url' => $vietQrUrl,
                    'bank_code' => setting('vietqr_bank_code', 'MB'),
                    'bank_name' => setting('vietqr_bank_name', 'MB Bank'),
                    'account_number' => setting('vietqr_account_number', '190333888999'),
                    'account_name' => setting('vietqr_account_name', 'CONG TY TNHH VPP'),
                    'transfer_content' => strtoupper($order->order_code),
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

    public function checkPaymentStatus($orderCode)
    {
        $order = Order::where('order_code', strtoupper(trim($orderCode)))->first();
        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy đơn hàng',
            ], 404);
        }

        $isOwner = auth('customer')->check() && auth('customer')->id() === $order->customer_id;
        $hasVerifiedSession = session()->get('verified_order_' . $order->uuid) === true;
        if (!$isOwner && !$hasVerifiedSession) {
            return response()->json(['success' => false, 'message' => 'Không có quyền xem trạng thái đơn hàng.'], 403);
        }

        $isPaid = $order->payment_status === 'paid' || ($order->paid_amount >= $order->grand_total && $order->grand_total > 0);

        return response()->json([
            'success' => true,
            'payment_status' => $order->payment_status,
            'is_paid' => $isPaid,
        ]);
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
        // 1. Chống Bot Spam qua Honeypot field (trường ẩn, người thật không bao giờ điền)
        if ($request->filled('website')) {
            return back()->withInput()->with('error', 'Yêu cầu bị từ chối do nghi vấn spam.');
        }

        // 2. Làm sạch số điện thoại và đưa vào request để validate
        $rawPhone = $request->input('phone', '');
        $cleanedPhone = AppHelper::cleanPhone($rawPhone);
        $request->merge(['cleaned_phone' => $cleanedPhone]);

        // 3. Strict Validation 2 lớp (Server-side)
        $request->validate([
            'name' => 'required|string|min:2|max:100',
            'phone' => 'required|string',
            'cleaned_phone' => [
                'required',
                'regex:/^(0[35789])[0-9]{8}$/'
            ],
            'email' => 'nullable|email|max:150',
            'address' => 'nullable|string|max:255',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'name.required' => 'Vui lòng nhập họ và tên của bạn.',
            'name.min' => 'Họ và tên phải có tối thiểu 2 ký tự.',
            'phone.required' => 'Vui lòng nhập số điện thoại.',
            'cleaned_phone.regex' => 'Số điện thoại không hợp lệ! Vui lòng nhập đủ 10 số di động Việt Nam (đầu 03, 05, 07, 08, 09).',
            'cleaned_phone.required' => 'Vui lòng nhập số điện thoại hợp lệ.',
            'email.email' => 'Địa chỉ email không đúng định dạng.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min' => 'Mật khẩu phải có từ 6 ký tự trở lên.',
            'password.confirmed' => 'Mật khẩu xác nhận không khớp! Vui lòng nhập lại chính xác.',
        ]);

        $customer = Customer::where('phone', $cleanedPhone)->first();
        if ($customer && !empty($customer->password)) {
            return back()->withInput()->with('error', 'Số điện thoại này đã có tài khoản. Vui lòng đăng nhập hoặc khôi phục mật khẩu.');
        }

        $request->session()->put('pending_customer_registration', [
            'name' => trim($request->name),
            'phone' => $cleanedPhone,
            'email' => $request->email ? trim($request->email) : null,
            'address' => $request->address ? trim($request->address) : null,
            'password_hash' => Hash::make($request->password),
        ]);
        $request->session()->put('otp_phone', $cleanedPhone);

        return redirect()->route('otp.verify.page')
            ->with('success', 'Thông tin đã lưu tạm. Xác minh số điện thoại để hoàn tất đăng ký.');
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
            $customer = auth('customer')->user();
            $request->session()->regenerate();
            return redirect()->intended(route('customer.profile'))->with('success', 'Đăng nhập thành công! Chào mừng ' . $customer->name);
        }

        return back()->withInput()->with('error', 'Số điện thoại hoặc mật khẩu không chính xác.');
    }

    public function forgotPasswordForm()
    {
        if (auth('customer')->check()) {
            return redirect()->route('customer.profile');
        }
        return view('storefront.auth.forgot-password');
    }

    public function postForgotPassword(Request $request)
    {
        if ($request->filled('website_url')) {
            return back()->with('error', 'Yêu cầu không hợp lệ.');
        }

        $request->validate([
            'phone' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
        ], [
            'phone.required' => 'Vui lòng nhập số điện thoại đã đăng ký.',
            'new_password.required' => 'Vui lòng nhập mật khẩu mới.',
            'new_password.min' => 'Mật khẩu mới phải có tối thiểu 6 ký tự.',
            'new_password.confirmed' => 'Xác nhận mật khẩu mới không trùng khớp.',
        ]);

        $cleanedPhone = AppHelper::cleanPhone($request->phone);
        $customer = Customer::where('phone', $cleanedPhone)->first();

        if (!$customer) {
            return back()->withInput()->with('error', 'Số điện thoại này chưa được đăng ký trong hệ thống. Quý khách vui lòng kiểm tra lại hoặc tạo tài khoản mới.');
        }

        $customer->update([
            'password' => $request->new_password,
            'phone_verified_at' => $customer->phone_verified_at ?: now(),
        ]);

        auth('customer')->login($customer);
        $request->session()->regenerate();

        return redirect()->route('customer.profile')->with('success', 'Đặt lại mật khẩu thành công! Chào mừng ' . $customer->name);
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
        $orders = Order::with('items.product')->where('customer_id', $customer->id)->orderBy('id', 'desc')->get();
        $repairTickets = RepairTicket::with('repairItems')->where('customer_id', $customer->id)->orderBy('id', 'desc')->get();

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
            'email' => 'nullable|email|max:150',
            'address' => 'nullable|string|max:255',
        ], [
            'name.required' => 'Vui lòng nhập họ và tên.',
            'email.email' => 'Địa chỉ email không đúng định dạng.',
        ]);

        $customer->update([
            'name' => $request->name,
            'email' => $request->email,
            'address' => $request->address,
        ]);

        return back()->with('success', 'Cập nhật thông tin hồ sơ thành công!')->with('active_tab', 'profile');
    }

    public function changePassword(Request $request)
    {
        if (!auth('customer')->check()) {
            return redirect()->route('customer.login');
        }

        $customer = auth('customer')->user();

        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
        ], [
            'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại.',
            'new_password.required' => 'Vui lòng nhập mật khẩu mới.',
            'new_password.min' => 'Mật khẩu mới phải có tối thiểu 6 ký tự.',
            'new_password.confirmed' => 'Xác nhận mật khẩu mới không trùng khớp.',
        ]);

        if (!Hash::check($request->current_password, $customer->password)) {
            return back()->with('error', 'Mật khẩu hiện tại không chính xác.')->with('active_tab', 'password');
        }

        $customer->update([
            'password' => $request->new_password,
        ]);

        return back()->with('success', 'Đổi mật khẩu thành công!')->with('active_tab', 'password');
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

    public function dynamicPage(string $slug)
    {
        $page = \App\Models\Page::where('slug', $slug)->where('is_active', true)->firstOrFail();
        return view('storefront.page', compact('page'));
    }

    public function about()
    {
        $page = \App\Models\Page::where('slug', 'gioi-thieu')->where('is_active', true)->first();
        if ($page) {
            return view('storefront.page', compact('page'));
        }
        return view('storefront.about');
    }

    public function privacy()
    {
        $page = \App\Models\Page::where('slug', 'chinh-sach-bao-mat')->where('is_active', true)->first();
        if ($page) {
            return view('storefront.page', compact('page'));
        }
        return view('storefront.privacy');
    }

    public function terms()
    {
        $page = \App\Models\Page::where('slug', 'chinh-sach-mua-hang')->where('is_active', true)->first();
        if ($page) {
            return view('storefront.page', compact('page'));
        }
        return view('storefront.terms');
    }
}
