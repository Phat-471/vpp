@php
    $products = $record->products()->with('category')->get();
@endphp

<div class="space-y-4">
    {{-- Banner tóm tắt tiêu chuẩn dòng máy --}}
    <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-xs">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
            <div>
                <span class="text-slate-500">Hãng & Dòng máy:</span>
                <strong class="text-slate-900 dark:text-white ml-1 font-bold">{{ $record->full_name }}</strong>
            </div>
            <div>
                <span class="text-slate-500">Công nghệ in:</span>
                <span class="font-medium text-slate-800 dark:text-slate-200 ml-1">{{ $record->printer_type }}</span>
            </div>
            <div class="sm:col-span-2">
                <span class="text-slate-500">Mã hộp mực / Cụm drum chuẩn:</span>
                <span class="font-bold text-indigo-600 dark:text-indigo-400 ml-1">{{ $record->compatible_cartridges ?: 'Chưa cập nhật mã mực' }}</span>
            </div>
            @if($record->notes)
                <div class="sm:col-span-2 text-slate-600 dark:text-slate-300 italic pt-1 border-t border-slate-200 dark:border-slate-700">
                    💡 Lưu ý kỹ thuật: {{ $record->notes }}
                </div>
            @endif
        </div>
    </div>

    {{-- Danh sách linh kiện / Hộp mực trong kho --}}
    <div>
        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">
            Sản phẩm & Linh kiện trong kho tương thích ({{ $products->count() }} mặt hàng):
        </h4>

        @if($products->isNotEmpty())
            <div class="divide-y divide-slate-100 dark:divide-slate-800 border border-slate-200 dark:border-slate-800 rounded-xl overflow-hidden max-h-80 overflow-y-auto">
                @foreach($products as $prod)
                    <div class="p-3 flex items-center justify-between gap-3 hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition text-xs">
                        <div class="flex items-center space-x-3 min-w-0">
                            <img src="{{ $prod->image_url }}" alt="{{ $prod->name }}" class="w-9 h-9 object-cover rounded-lg border border-slate-200 shrink-0">
                            <div class="min-w-0">
                                <div class="font-bold text-slate-900 dark:text-white truncate">{{ $prod->name }}</div>
                                <div class="text-[11px] text-slate-400 flex items-center space-x-2">
                                    <span class="font-mono text-indigo-600">{{ $prod->sku }}</span>
                                    <span>•</span>
                                    <span>{{ $prod->category?->name ?? 'Linh kiện' }}</span>
                                    @if($prod->pivot?->notes)
                                        <span>•</span>
                                        <span class="text-amber-600 italic">{{ $prod->pivot->notes }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="text-right shrink-0">
                            <div class="font-bold text-slate-900 dark:text-white">{{ number_format($prod->retail_price, 0, ',', '.') }} ₫</div>
                            <div>
                                @if($prod->stock_quantity > 0)
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">
                                        Còn {{ $prod->stock_quantity }} {{ $prod->base_unit_name ?? 'Cái' }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 dark:bg-rose-950 dark:text-rose-300">
                                        Hết hàng
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="p-6 text-center rounded-xl border border-dashed border-slate-300 dark:border-slate-700 text-xs text-slate-500">
                <span class="text-2xl block mb-1">📦</span>
                Chưa có linh kiện hoặc hộp mực nào trong kho được liên kết với máy in này.
                <p class="text-[11px] text-slate-400 mt-1">Vào trang "Chỉnh sửa" dòng máy in để gắn thêm sản phẩm tương thích.</p>
            </div>
        @endif
    </div>
</div>
