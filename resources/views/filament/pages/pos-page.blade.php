<x-filament-panels::page>
    <div class="space-y-4">
        <!-- TOP CONTROLS: BARCODE SEARCH & CATEGORY FILTER -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3">
                <!-- Search Input with Barcode Icon -->
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                        </svg>
                    </div>
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Quét mã vạch (Barcode) hoặc gõ tên sản phẩm, mã SKU..."
                        class="w-full pl-11 pr-10 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 text-sm font-medium focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                        autofocus
                    />
                    @if(!empty($search))
                    <button
                        wire:click="$set('search', '')"
                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-200"
                        title="Xóa tìm kiếm"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    @endif
                </div>

                <!-- Shortcuts & Status -->
                <div class="flex items-center space-x-2 text-xs text-gray-500 dark:text-gray-400">
                    <span class="bg-gray-100 dark:bg-gray-700 px-2.5 py-1.5 rounded-lg font-mono">F9: Thanh toán</span>
                    <span class="bg-gray-100 dark:bg-gray-700 px-2.5 py-1.5 rounded-lg font-mono">F2: Tạo đơn mới</span>
                </div>
            </div>

            <!-- Horizontal Category Filter Pills -->
            <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-700 flex items-center space-x-2 overflow-x-auto pb-1 scrollbar-thin">
                <button
                    type="button"
                    wire:click="$set('selectedCategory', null)"
                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition {{ is_null($selectedCategory) ? 'bg-indigo-600 text-white shadow-sm' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}"
                >
                    Tất cả danh mục
                </button>
                @foreach($categories as $cat)
                <button
                    type="button"
                    wire:click="$set('selectedCategory', {{ $cat->id }})"
                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition {{ $selectedCategory == $cat->id ? 'bg-indigo-600 text-white shadow-sm' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}"
                >
                    {{ $cat->name }}
                </button>
                @endforeach
            </div>
        </div>

        <!-- MAIN POS WORKSPACE: 2 COLUMNS (CATALOG & CART) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
            
            <!-- LEFT COLUMN: PRODUCT CATALOG GRID (7 COLS ON DESKTOP) -->
            <div class="lg:col-span-7 space-y-4">
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    @forelse($products as $product)
                    <div class="bg-white dark:bg-gray-800 rounded-2xl p-3.5 shadow-sm border border-gray-200 dark:border-gray-700 hover:border-indigo-400 dark:hover:border-indigo-500 transition flex flex-col justify-between">
                        <div>
                            <!-- Header: Stock Badge & Category -->
                            <div class="flex items-center justify-between text-[11px] mb-2">
                                <span class="text-gray-400 truncate max-w-[80px]">{{ $product->category?->name }}</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $product->stock_quantity <= 5 ? 'bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-300' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' }}">
                                    Kho: {{ $product->stock_quantity }} {{ $product->base_unit }}
                                </span>
                            </div>

                            <!-- Image & Name -->
                            <div class="h-20 bg-gray-50 dark:bg-gray-900 rounded-xl mb-2 flex items-center justify-center p-1.5 overflow-hidden border border-gray-100 dark:border-gray-800">
                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="max-h-full max-w-full object-contain" />
                            </div>

                            <h4 class="text-xs font-bold text-gray-900 dark:text-gray-100 line-clamp-2 leading-snug mb-1" title="{{ $product->name }}">
                                {{ $product->name }}
                            </h4>
                            <span class="text-[10px] font-mono text-gray-400 block mb-2">SKU: {{ $product->sku }}</span>
                        </div>

                        <!-- Unit Add Buttons Section -->
                        <div class="space-y-1.5 pt-2 border-t border-gray-100 dark:border-gray-700">
                            <!-- Base Unit Button -->
                            <button
                                type="button"
                                wire:click="addToCart({{ $product->id }})"
                                class="w-full flex items-center justify-between px-2.5 py-1.5 bg-indigo-50 dark:bg-indigo-950 hover:bg-indigo-100 dark:hover:bg-indigo-900 text-indigo-700 dark:text-indigo-300 rounded-xl text-xs font-bold transition border border-indigo-200 dark:border-indigo-800"
                            >
                                <span class="truncate">+ 1 {{ $product->base_unit }}</span>
                                <span class="font-mono ml-1">{{ number_format($product->retail_price, 0, ',', '.') }}₫</span>
                            </button>

                            <!-- Dual-Unit Packaging Buttons (Box, Carton...) -->
                            @if($product->units->count() > 0)
                                @foreach($product->units as $unit)
                                <button
                                    type="button"
                                    wire:click="addToCart({{ $product->id }}, {{ $unit->id }})"
                                    class="w-full flex items-center justify-between px-2.5 py-1.5 bg-emerald-50 dark:bg-emerald-950 hover:bg-emerald-100 dark:hover:bg-emerald-900 text-emerald-800 dark:text-emerald-300 rounded-xl text-[11px] font-bold transition border border-emerald-200 dark:border-emerald-800"
                                >
                                    <span class="truncate">+ 1 {{ $unit->unit_name }} (x{{ $unit->conversion_rate }})</span>
                                    <span class="font-mono ml-1">{{ number_format($unit->price, 0, ',', '.') }}₫</span>
                                </button>
                                @endforeach
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="col-span-full py-12 text-center bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700">
                        <span class="text-3xl block mb-2">📦</span>
                        <p class="text-sm font-bold text-gray-600 dark:text-gray-300">Không tìm thấy sản phẩm nào phù hợp</p>
                        <p class="text-xs text-gray-400 mt-1">Vui lòng thử gõ từ khóa khác hoặc quét mã vạch.</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- RIGHT COLUMN: CART & CHECKOUT PANEL (5 COLS ON DESKTOP) -->
            <div class="lg:col-span-5 bg-white dark:bg-gray-800 rounded-2xl p-4 sm:p-5 shadow-sm border border-gray-200 dark:border-gray-700 sticky top-4 space-y-4">
                
                <!-- Cart Header -->
                <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 pb-3">
                    <div class="flex items-center space-x-2">
                        <span class="text-base font-black text-gray-900 dark:text-gray-100">Đơn Hàng Hiện Tại</span>
                        <span class="px-2 py-0.5 bg-indigo-100 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300 text-xs font-bold rounded-full">
                            {{ count($cart) }} món
                        </span>
                    </div>
                    @if(count($cart) > 0)
                    <button
                        type="button"
                        wire:click="clearCart"
                        onclick="return confirm('Bạn có chắc muốn xóa toàn bộ giỏ hàng?') || event.stopImmediatePropagation()"
                        class="text-xs text-rose-600 hover:text-rose-800 font-bold hover:underline"
                    >
                        Xóa tất cả
                    </button>
                    @endif
                </div>

                <!-- Customer Info Input -->
                <div class="bg-gray-50 dark:bg-gray-900 p-3 rounded-xl border border-gray-200 dark:border-gray-700 space-y-2">
                    <div class="text-xs font-bold text-gray-700 dark:text-gray-300 flex items-center justify-between">
                        <span>Thông Tin Khách Hàng (Tùy chọn)</span>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <input
                            type="text"
                            wire:model.live.debounce.500ms="customerPhone"
                            placeholder="Số điện thoại khách..."
                            class="w-full text-xs py-1.5 px-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 font-medium"
                        />
                        <input
                            type="text"
                            wire:model="customerName"
                            placeholder="Tên khách hàng..."
                            class="w-full text-xs py-1.5 px-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 font-medium"
                        />
                    </div>
                </div>

                <!-- Cart Items List -->
                <div class="max-h-72 overflow-y-auto space-y-2.5 pr-1 divide-y divide-gray-100 dark:divide-gray-700 scrollbar-thin">
                    @forelse($cart as $cartKey => $item)
                    <div class="pt-2.5 first:pt-0 flex items-start justify-between gap-2 text-xs">
                        <div class="flex-1">
                            <h5 class="font-bold text-gray-900 dark:text-gray-100 leading-snug">{{ $item['name'] }}</h5>
                            <div class="flex items-center space-x-2 text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">
                                <span class="bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 rounded text-[10px] font-bold text-gray-700 dark:text-gray-300">
                                    {{ $item['unit_name'] }}
                                </span>
                                <span>{{ number_format($item['unit_price'], 0, ',', '.') }}₫</span>
                            </div>
                        </div>

                        <!-- Quantity Modifiers -->
                        <div class="flex items-center space-x-1.5 bg-gray-50 dark:bg-gray-900 rounded-lg p-1 border border-gray-200 dark:border-gray-700">
                            <button
                                type="button"
                                wire:click="updateQuantity('{{ $cartKey }}', -1)"
                                class="w-6 h-6 flex items-center justify-center rounded bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 font-bold hover:bg-gray-200 dark:hover:bg-gray-700 shadow-sm"
                            >
                                -
                            </button>
                            <span class="w-7 text-center font-bold font-mono text-gray-900 dark:text-gray-100">
                                {{ $item['quantity'] }}
                            </span>
                            <button
                                type="button"
                                wire:click="updateQuantity('{{ $cartKey }}', 1)"
                                class="w-6 h-6 flex items-center justify-center rounded bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 font-bold hover:bg-gray-200 dark:hover:bg-gray-700 shadow-sm"
                            >
                                +
                            </button>
                        </div>

                        <!-- Subtotal & Remove -->
                        <div class="text-right flex flex-col items-end">
                            <span class="font-bold font-mono text-gray-900 dark:text-gray-100">
                                {{ number_format($item['subtotal'], 0, ',', '.') }}₫
                            </span>
                            <button
                                type="button"
                                wire:click="removeFromCart('{{ $cartKey }}')"
                                class="text-[10px] text-rose-500 hover:text-rose-700 font-medium mt-1"
                            >
                                Xóa
                            </button>
                        </div>
                    </div>
                    @empty
                    <div class="py-8 text-center text-gray-400 dark:text-gray-500">
                        <svg class="w-10 h-10 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        <p class="text-xs font-semibold">Giỏ hàng đang trống</p>
                        <p class="text-[11px] text-gray-400 mt-0.5">Nhấp vào nút thêm của sản phẩm bên trái để bắt đầu</p>
                    </div>
                    @endforelse
                </div>

                <!-- Financial Calculation Summary -->
                <div class="border-t border-gray-200 dark:border-gray-700 pt-3 space-y-2 text-xs">
                    <div class="flex justify-between text-gray-600 dark:text-gray-400 font-medium">
                        <span>Tạm tính tiền hàng:</span>
                        <span class="font-mono font-bold">{{ number_format($subtotal, 0, ',', '.') }}₫</span>
                    </div>

                    <div class="flex items-center justify-between text-gray-600 dark:text-gray-400 font-medium">
                        <span>Chiết khấu / Giảm giá:</span>
                        <div class="flex items-center space-x-1">
                            <input
                                type="number"
                                wire:model.live.debounce.300ms="discount"
                                min="0"
                                step="1000"
                                class="w-28 text-right text-xs py-1 px-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 font-mono font-bold text-rose-600"
                            />
                            <span>₫</span>
                        </div>
                    </div>

                    <div class="flex justify-between items-baseline pt-2 border-t border-gray-100 dark:border-gray-700 text-sm">
                        <span class="font-black text-gray-900 dark:text-gray-100 text-base">Tổng Thanh Toán:</span>
                        <span class="text-xl font-black font-mono text-indigo-700 dark:text-indigo-400">
                            {{ number_format($grandTotal, 0, ',', '.') }}₫
                        </span>
                    </div>
                </div>

                <!-- Payment Method Toggle -->
                <div class="border-t border-gray-100 dark:border-gray-700 pt-3 space-y-2">
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300">Phương Thức Thanh Toán:</label>
                    <div class="grid grid-cols-2 gap-2">
                        <button
                            type="button"
                            wire:click="$set('paymentMethod', 'cash')"
                            class="py-2 px-3 rounded-xl text-xs font-bold transition flex items-center justify-center space-x-1.5 border {{ $paymentMethod === 'cash' ? 'bg-emerald-600 text-white border-emerald-600 shadow-sm' : 'bg-gray-50 dark:bg-gray-900 text-gray-700 dark:text-gray-300 border-gray-200 dark:border-gray-700' }}"
                        >
                            <span>💵</span>
                            <span>Tiền Mặt</span>
                        </button>
                        <button
                            type="button"
                            wire:click="$set('paymentMethod', 'vietqr')"
                            class="py-2 px-3 rounded-xl text-xs font-bold transition flex items-center justify-center space-x-1.5 border {{ $paymentMethod === 'vietqr' ? 'bg-indigo-600 text-white border-indigo-600 shadow-sm' : 'bg-gray-50 dark:bg-gray-900 text-gray-700 dark:text-gray-300 border-gray-200 dark:border-gray-700' }}"
                        >
                            <span>📱</span>
                            <span>VietQR / CK</span>
                        </button>
                    </div>

                    <!-- Cash Breakdown Section (When Cash is selected) -->
                    @if($paymentMethod === 'cash' && $grandTotal > 0)
                    <div class="bg-gray-50 dark:bg-gray-900 p-2.5 rounded-xl border border-gray-200 dark:border-gray-700 space-y-2 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600 dark:text-gray-400 font-medium">Tiền khách đưa:</span>
                            <input
                                type="number"
                                wire:model.live.debounce.300ms="cashGiven"
                                step="1000"
                                placeholder="{{ $grandTotal }}"
                                class="w-32 text-right text-xs py-1 px-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 font-mono font-bold text-emerald-700 dark:text-emerald-400"
                            />
                        </div>

                        <!-- Quick Cash Shortcuts -->
                        <div class="flex items-center space-x-1 pt-1 overflow-x-auto pb-0.5">
                            <button
                                type="button"
                                wire:click="$set('cashGiven', {{ $grandTotal }})"
                                class="px-2 py-0.5 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 rounded text-[10px] font-bold whitespace-nowrap"
                            >
                                Đủ tiền
                            </button>
                            <button
                                type="button"
                                wire:click="$set('cashGiven', 50000)"
                                class="px-2 py-0.5 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 rounded text-[10px] font-bold whitespace-nowrap"
                            >
                                50k
                            </button>
                            <button
                                type="button"
                                wire:click="$set('cashGiven', 100000)"
                                class="px-2 py-0.5 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 rounded text-[10px] font-bold whitespace-nowrap"
                            >
                                100k
                            </button>
                            <button
                                type="button"
                                wire:click="$set('cashGiven', 200000)"
                                class="px-2 py-0.5 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 rounded text-[10px] font-bold whitespace-nowrap"
                            >
                                200k
                            </button>
                            <button
                                type="button"
                                wire:click="$set('cashGiven', 500000)"
                                class="px-2 py-0.5 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 rounded text-[10px] font-bold whitespace-nowrap"
                            >
                                500k
                            </button>
                        </div>

                        @if($cashGiven > 0)
                        <div class="flex justify-between items-center pt-1 border-t border-gray-200 dark:border-gray-700 font-bold">
                            <span class="text-gray-700 dark:text-gray-300">Tiền thối lại:</span>
                            <span class="font-mono text-sm {{ $changeAmount >= 0 ? 'text-emerald-700 dark:text-emerald-400' : 'text-rose-600' }}">
                                {{ number_format($changeAmount, 0, ',', '.') }}₫
                            </span>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>

                <!-- Big Checkout Action Button -->
                <button
                    type="button"
                    wire:click="checkout"
                    wire:loading.attr="disabled"
                    @disabled(empty($cart))
                    class="w-full py-3 px-4 rounded-xl font-black text-sm tracking-wide text-white transition flex items-center justify-center space-x-2 shadow-lg {{ empty($cart) ? 'bg-gray-300 dark:bg-gray-700 cursor-not-allowed text-gray-500' : 'bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 shadow-indigo-200 dark:shadow-none' }}"
                >
                    <svg wire:loading.remove wire:target="checkout" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <svg wire:loading wire:target="checkout" class="animate-spin w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    <span>THANH TOÁN & HOÀN TẤT (F9)</span>
                </button>
            </div>
        </div>
    </div>

    <!-- SUCCESS POPUP / RECEIPT MODAL -->
    @if($showSuccessModal)
    <div class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-3xl max-w-md w-full p-6 shadow-2xl border border-gray-100 dark:border-gray-700 text-center space-y-4 animate-in fade-in zoom-in duration-200">
            <!-- Success Icon -->
            <div class="w-16 h-16 mx-auto rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-3xl">
                ✓
            </div>

            <h3 class="text-xl font-black text-gray-900 dark:text-gray-100">
                Thanh Toán Đơn Hàng Thành Công!
            </h3>

            <div class="bg-gray-50 dark:bg-gray-900 rounded-2xl p-4 border border-gray-200 dark:border-gray-700 text-left space-y-2 text-xs">
                <div class="flex justify-between">
                    <span class="text-gray-500">Mã hóa đơn:</span>
                    <span class="font-mono font-bold text-gray-900 dark:text-gray-100 text-sm">{{ $completedOrderCode }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Khách hàng:</span>
                    <span class="font-bold text-gray-900 dark:text-gray-100">{{ $customerName ?: 'Khách lẻ tại quầy' }}</span>
                </div>
                <div class="flex justify-between border-t border-gray-200 dark:border-gray-700 pt-2 text-sm">
                    <span class="font-bold text-gray-700 dark:text-gray-300">Tổng thanh toán:</span>
                    <span class="font-black font-mono text-emerald-600 dark:text-emerald-400">{{ number_format($grandTotal, 0, ',', '.') }}₫</span>
                </div>
            </div>

            <!-- VietQR Display (If chosen or transfer needed) -->
            @if($paymentMethod === 'vietqr' && $vietQrUrl)
            <div class="bg-white p-3 rounded-2xl border border-gray-200 dark:border-gray-700 inline-block shadow-sm">
                <img src="{{ $vietQrUrl }}" alt="Mã VietQR" class="w-48 h-48 mx-auto rounded-xl object-contain" />
                <span class="text-[11px] text-gray-500 font-medium block mt-1.5">Quét mã QR để thanh toán ngân hàng</span>
            </div>
            @endif

            <!-- Modal Action Buttons -->
            <div class="grid grid-cols-2 gap-3 pt-2">
                <a
                    href="{{ route('print.order', ['id' => $completedOrderId]) }}"
                    target="_blank"
                    class="py-2.5 px-4 rounded-xl bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 text-xs font-bold transition flex items-center justify-center space-x-1.5"
                >
                    <span>🖨️</span>
                    <span>In Hóa Đơn A5</span>
                </a>

                <button
                    type="button"
                    wire:click="resetAndNewSale"
                    class="py-2.5 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold transition flex items-center justify-center space-x-1.5 shadow-md shadow-indigo-200 dark:shadow-none"
                >
                    <span>➕</span>
                    <span>Bán Đơn Tiếp (F2)</span>
                </button>
            </div>
        </div>
    </div>
    @endif
</x-filament-panels::page>
