<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductUnit;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SeedFullCatalogCommand extends Command
{
    protected $signature = 'vpp:seed-1000-skus {--count=1000 : Số lượng SKU cần tạo} {--csv-only : Chỉ xuất tệp CSV không nhập vào DB}';

    protected $description = 'Tạo dữ liệu danh mục 1.000 SKU văn phòng phẩm & vật tư máy in thực tế kèm quy đổi đơn vị';

    public function handle(): int
    {
        $targetCount = (int) $this->option('count');
        $csvOnly = $this->option('csv-only');

        $this->info("Bắt đầu khởi tạo {$targetCount} SKU văn phòng phẩm & máy in thực tế...");

        $catalog = $this->generateSkus($targetCount);

        // 1. Always export CSV to storage for download
        $importsDir = storage_path('app/public/imports');
        if (!is_dir($importsDir)) {
            mkdir($importsDir, 0755, true);
        }

        $csvPath = $importsDir . '/mau-nhap-1000-sku-vpp.csv';
        $handle = fopen($csvPath, 'w');
        // UTF-8 BOM for Excel
        fwrite($handle, "\xEF\xBB\xBF");
        fputcsv($handle, [
            'ma_sku',
            'ten_san_pham',
            'danh_muc',
            'dvt',
            'gia_nhap',
            'gia_ban',
            'ton_kho',
            'ma_vach',
            'linh_kien',
            'quy_doi',
            'hinh_anh',
        ]);

        foreach ($catalog as $item) {
            fputcsv($handle, [
                $item['sku'],
                $item['name'],
                $item['category'],
                $item['base_unit'],
                $item['cost_price'],
                $item['retail_price'],
                $item['stock'],
                $item['barcode'],
                $item['is_service'] ? '1' : '0',
                $item['dual_unit'],
                $item['image'] ?? '',
            ]);
        }
        fclose($handle);
        $this->info("✓ Đã xuất tệp CSV chứa " . count($catalog) . " SKU tại: {$csvPath}");

        if ($csvOnly) {
            $this->info("Chế độ --csv-only hoàn tất. Không ghi đè database.");
            return Command::SUCCESS;
        }

        // 2. Import into Database
        $this->info("Tiến hành nạp trực tiếp vào cơ sở dữ liệu...");
        $bar = $this->output->createProgressBar(count($catalog));
        $bar->start();

        // Cache category IDs by slug and name
        $categoriesMap = [];
        foreach (Category::all() as $cat) {
            $categoriesMap[$cat->slug] = $cat->id;
            $categoriesMap[mb_strtolower($cat->name)] = $cat->id;
        }

        DB::beginTransaction();
        try {
            foreach ($catalog as $item) {
                $slug = Str::slug($item['category']);
                $nameKey = mb_strtolower($item['category']);

                if (isset($categoriesMap[$slug])) {
                    $catId = $categoriesMap[$slug];
                } elseif (isset($categoriesMap[$nameKey])) {
                    $catId = $categoriesMap[$nameKey];
                } else {
                    $newCat = Category::create([
                        'name' => $item['category'],
                        'slug' => $slug,
                        'placeholder_image' => $item['placeholder'],
                        'is_active' => true,
                    ]);
                    $categoriesMap[$slug] = $newCat->id;
                    $categoriesMap[$nameKey] = $newCat->id;
                    $catId = $newCat->id;
                }

                $product = Product::updateOrCreate(
                    ['sku' => $item['sku']],
                    [
                        'name' => $item['name'],
                        'slug' => Str::slug($item['name']) . '-' . strtolower(Str::random(4)),
                        'category_id' => $catId,
                        'barcode' => $item['barcode'],
                        'cost_price' => $item['cost_price'],
                        'retail_price' => $item['retail_price'],
                        'stock_quantity' => $item['stock'],
                        'base_unit' => $item['base_unit'],
                        'has_custom_image' => false,
                        'is_service_part' => $item['is_service'],
                        'is_active' => true,
                    ]
                );

                if (!empty($item['dual_unit'])) {
                    $units = preg_split('/[|;]/', $item['dual_unit']);
                    foreach ($units as $u) {
                        $parts = explode(':', trim($u));
                        if (count($parts) >= 3) {
                            ProductUnit::updateOrCreate(
                                ['product_id' => $product->id, 'unit_name' => trim($parts[0])],
                                [
                                    'conversion_rate' => (int) $parts[1],
                                    'price' => (float) $parts[2],
                                ]
                            );
                        }
                    }
                }

                $bar->advance();
            }

            DB::commit();
            $bar->finish();
            $this->newLine();
            $this->info("✓ Hoàn tất nạp " . count($catalog) . " SKU vào kho hệ thống thành công!");
            return Command::SUCCESS;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->newLine();
            $this->error("Lỗi khi nạp database: " . $e->getMessage());
            return Command::FAILURE;
        }
    }

    protected function generateSkus(int $target): array
    {
        $templates = [
            [
                'cat' => 'Giấy in & Photo',
                'placeholder' => 'images/placeholders/paper.svg',
                'is_service' => false,
                'items' => [
                    ['Giấy in Double A A4', 'Ram', 62000, 75000, 'Thùng:5:360000'],
                    ['Giấy in Double A A3', 'Ram', 125000, 155000, 'Thùng:5:745000'],
                    ['Giấy in Double A A5', 'Ram', 33000, 42000, 'Thùng:10:400000'],
                    ['Giấy in PaperOne A4', 'Ram', 65000, 79000, 'Thùng:5:380000'],
                    ['Giấy in PaperOne A3', 'Ram', 132000, 165000, 'Thùng:5:790000'],
                    ['Giấy in IK Plus A4', 'Ram', 58000, 70000, 'Thùng:5:340000'],
                    ['Giấy in Bãi Bằng A4 Hồng tem vàng', 'Ram', 45000, 56000, 'Thùng:5:270000'],
                    ['Giấy in Supreme A4', 'Ram', 60000, 72000, 'Thùng:5:350000'],
                    ['Giấy decal da bò A4', 'Xấp', 45000, 60000, 'Hộp:10:570000'],
                    ['Giấy decal đế vàng A4', 'Xấp', 40000, 55000, 'Hộp:10:520000'],
                    ['Giấy decal đế xanh A4', 'Xấp', 42000, 58000, 'Hộp:10:550000'],
                    ['Giấy in bill nhiệt K80x45 bọc bạc', 'Cuộn', 5500, 8000, 'Thùng:50:375000 | Thùng:100:720000'],
                    ['Giấy in bill nhiệt K80x80 Oji', 'Cuộn', 12000, 16000, 'Thùng:30:450000'],
                    ['Giấy in bill nhiệt K57x45 bọc bạc', 'Cuộn', 4500, 6500, 'Thùng:50:310000'],
                    ['Giấy in ảnh Epson Glossy A4 230gsm', 'Xấp', 55000, 75000, 'Lốc:5:360000'],
                    ['Giấy than Kokusai A4 Horse', 'Hộp', 75000, 105000, 'Lốc:5:500000'],
                    ['Giấy bìa màu A4 Dạ quang 160gsm', 'Xấp', 32000, 45000, 'Lốc:10:420000'],
                ],
            ],
            [
                'cat' => 'Bút viết & Mực viết',
                'placeholder' => 'images/placeholders/pen.svg',
                'is_service' => false,
                'items' => [
                    ['Bút bi Thiên Long TL-027', 'Cây', 3500, 5000, 'Hộp:20:95000'],
                    ['Bút bi Thiên Long TL-036', 'Cây', 7500, 10000, 'Hộp:20:190000'],
                    ['Bút bi Thiên Long TL-08', 'Cây', 3000, 4500, 'Hộp:20:85000'],
                    ['Bút bi Thiên Long TL-079', 'Cây', 4000, 6000, 'Hộp:20:110000'],
                    ['Bút gel nước Thiên Long Gel-08 Sunbeam', 'Cây', 6000, 8500, 'Hộp:20:160000'],
                    ['Bút gel Bến Nghé L-28', 'Cây', 4500, 6500, 'Hộp:20:125000'],
                    ['Bút gel Pilot G2 0.5mm', 'Cây', 22000, 30000, 'Hộp:12:340000'],
                    ['Bút gel xóa được Pilot Frixion 0.5mm', 'Cây', 45000, 60000, 'Hộp:12:680000'],
                    ['Ruột bút gel xóa được Pilot Frixion', 'Vỉ', 38000, 52000, 'Hộp:10:500000'],
                    ['Bút dạ quang Thiên Long HL-03', 'Cây', 6500, 9500, 'Hộp:10:90000'],
                    ['Bút lông dầu Thiên Long PM-09', 'Cây', 7000, 10000, 'Hộp:10:95000'],
                    ['Bút lông bảng Thiên Long WB-03', 'Cây', 6500, 9500, 'Hộp:10:90000'],
                    ['Bút xóa kéo Plus WH-605 Nhật Bản', 'Cây', 20000, 28000, 'Hộp:10:260000'],
                    ['Ruột xóa kéo Plus WH-605R', 'Cái', 13000, 18000, 'Hộp:10:170000'],
                    ['Bút xóa nước Thiên Long CP-02', 'Cây', 16000, 22000, 'Hộp:12:250000'],
                    ['Bút chì 2B Staedtler Noris 120 Đức', 'Cây', 6000, 9000, 'Hộp:12:100000'],
                    ['Bút chì kim Pentel Caplet 0.5mm', 'Cây', 15000, 22000, 'Hộp:12:250000'],
                    ['Ruột chì 2B Pentel 0.5mm C275', 'Hộp', 11000, 16000, 'Lốc:12:180000'],
                    ['Gôm tẩy Pentel Hi-Polymer Standard ZEH-10', 'Cục', 9000, 14000, 'Hộp:30:390000'],
                ],
            ],
            [
                'cat' => 'Bìa hồ sơ & Lưu trữ',
                'placeholder' => 'images/placeholders/folder.svg',
                'is_service' => false,
                'items' => [
                    ['Bìa còng Kokuyo F4 5F', 'Cái', 32000, 42000, 'Thùng:50:1950000'],
                    ['Bìa còng Kokuyo F4 7F', 'Cái', 34000, 45000, 'Thùng:50:2100000'],
                    ['Bìa còng KingJim F4 7F', 'Cái', 48000, 65000, 'Thùng:30:1850000'],
                    ['Bìa còng KingJim F4 10F', 'Cái', 55000, 75000, 'Thùng:30:2150000'],
                    ['Bìa còng bật Plus 5F F4', 'Cái', 36000, 48000, 'Thùng:40:1800000'],
                    ['Bìa nút My Clear Bag F4 dày', 'Cái', 2800, 4500, 'Xấp:10:42000 | Thùng:200:800000'],
                    ['Bìa lá A4 Plus 0.15mm', 'Xấp', 45000, 62000, 'Thùng:10:600000'],
                    ['Bìa lỗ A4 Trà My 100 tờ dày 0.04mm', 'Xấp', 28000, 40000, 'Lốc:10:380000'],
                    ['Bìa 20 lá A4 KingJim', 'Cuốn', 22000, 32000, 'Thùng:30:920000'],
                    ['Bìa 40 lá A4 KingJim', 'Cuốn', 30000, 42000, 'Thùng:20:800000'],
                    ['Bìa 60 lá A4 KingJim', 'Cuốn', 38000, 52000, 'Thùng:20:990000'],
                    ['Bìa hộp simili 10F Gấp', 'Cái', 32000, 45000, 'Thùng:25:1050000'],
                    ['Bìa hộp simili 15F Gấp', 'Cái', 38000, 52000, 'Thùng:20:980000'],
                    ['Kệ rổ tài liệu 3 ngăn nhựa liên hoàn Deli', 'Bộ', 42000, 60000, 'Thùng:10:570000'],
                    ['Kẹp acco sắt SDI 50 cái', 'Hộp', 18000, 26000, 'Lốc:10:250000'],
                    ['Kẹp acco nhựa SDI 50 cái', 'Hộp', 15000, 22000, 'Lốc:10:210000'],
                ],
            ],
            [
                'cat' => 'Dụng cụ văn phòng',
                'placeholder' => 'images/placeholders/stationery.svg',
                'is_service' => false,
                'items' => [
                    ['Máy bấm kim số 10 Max HD-10 Nhật', 'Cái', 48000, 65000, 'Hộp:10:620000'],
                    ['Máy bấm kim số 10 Plus PS-10E', 'Cái', 32000, 45000, 'Hộp:10:430000'],
                    ['Máy bấm kim số 3 SDI 1142', 'Cái', 55000, 75000, 'Hộp:10:720000'],
                    ['Kim bấm số 10 Plus Nhật Bản', 'Hộp', 3500, 5000, 'Hộp lớn:20:95000 | Thùng:200:900000'],
                    ['Kim bấm số 3 Max No.3-1M', 'Hộp', 7000, 10000, 'Hộp lớn:10:95000'],
                    ['Kìm gỡ kim số 10 SDI 1162', 'Cái', 12000, 18000, 'Hộp:12:200000'],
                    ['Kẹp bướm 15mm Deli 0221', 'Hộp', 7500, 11000, 'Lốc:12:125000'],
                    ['Kẹp bướm 19mm Deli 0222', 'Hộp', 9000, 13000, 'Lốc:12:145000'],
                    ['Kẹp bướm 25mm Deli 0223', 'Hộp', 13000, 19000, 'Lốc:12:215000'],
                    ['Kẹp bướm 32mm Deli 0224', 'Hộp', 18000, 26000, 'Lốc:12:300000'],
                    ['Kẹp bướm 41mm Deli 0225', 'Hộp', 24000, 35000, 'Lốc:12:400000'],
                    ['Kẹp bướm 51mm Deli 0226', 'Hộp', 35000, 48000, 'Lốc:12:550000'],
                    ['Kẹp giấy kim C62 đầu tròn 100 cái', 'Hộp', 4500, 7000, 'Lốc:10:65000'],
                    ['Dao rọc giấy SDI 0423 lớn 18mm', 'Cây', 16000, 24000, 'Hộp:12:270000'],
                    ['Lưỡi dao rọc giấy SDI 1404 lớn', 'Hộp', 14000, 20000, 'Lốc:10:190000'],
                    ['Kéo văn phòng Deli 175mm cán đen', 'Cây', 18000, 26000, 'Hộp:12:290000'],
                    ['Băng keo dán trong 5cm x 100 yard', 'Cuộn', 12000, 18000, 'Cây:6:100000 | Thùng:36:580000'],
                    ['Băng keo 2 mặt 2.4cm x 15 yard', 'Cuộn', 7000, 11000, 'Cây:10:100000'],
                    ['Băng keo mút xốp 2 mặt 2.4cm', 'Cuộn', 11000, 17000, 'Cây:10:160000'],
                    ['Hồ dán nước Thiên Long G-08', 'Chai', 3500, 5000, 'Khay:30:140000'],
                    ['Keo khô dán giấy Deli 9g', 'Cây', 4500, 7000, 'Hộp:30:195000'],
                    ['Bàn cắt giấy khổ A4 gỗ', 'Cái', 145000, 195000, ''],
                ],
            ],
            [
                'cat' => 'Sổ tay, Giấy note & Vở',
                'placeholder' => 'images/placeholders/folder.svg',
                'is_service' => false,
                'items' => [
                    ['Sổ da dán gáy A4 200 trang Tiến Phát', 'Cuốn', 32000, 45000, 'Lốc:5:215000'],
                    ['Sổ da dán gáy A5 200 trang Tiến Phát', 'Cuốn', 22000, 32000, 'Lốc:5:150000'],
                    ['Sổ còng da cao cấp B5 120 tờ', 'Cuốn', 75000, 105000, 'Hộp:5:500000'],
                    ['Sổ lò xo B5 200 trang Hồng Hà', 'Cuốn', 25000, 35000, 'Lốc:5:165000'],
                    ['Tập học sinh 96 trang 4 ô ly Hồng Hà', 'Cuốn', 7500, 11000, 'Lốc:10:105000'],
                    ['Giấy note vàng 3x3 Post-it 654 3M', 'Xấp', 11000, 16000, 'Lốc:12:180000'],
                    ['Giấy note vàng 3x4 Post-it 656 3M', 'Xấp', 14000, 20000, 'Lốc:12:230000'],
                    ['Giấy note vàng 3x5 Post-it 657 3M', 'Xấp', 17000, 25000, 'Lốc:12:285000'],
                    ['Giấy note 5 màu dạ quang Pronoti 21516', 'Xấp', 9000, 14000, 'Lốc:24:310000'],
                    ['Giấy note phân trang Sign Here 3M 680-9', 'Vỉ', 24000, 35000, 'Hộp:12:400000'],
                ],
            ],
            [
                'cat' => 'Hộp mực máy in',
                'placeholder' => 'images/placeholders/toner.svg',
                'is_service' => true,
                'items' => [
                    ['Hộp mực Cartridge 12A/303 Canon 2900 / HP 1020', 'Hộp', 135000, 240000, 'Thùng:10:2250000'],
                    ['Hộp mực Cartridge 35A/85A/78A HP P1102 / Canon 6000', 'Hộp', 140000, 250000, 'Thùng:10:2350000'],
                    ['Hộp mực Cartridge 05A/80A HP P2035 / M401dn', 'Hộp', 175000, 290000, 'Thùng:10:2750000'],
                    ['Hộp mực Cartridge 49A/53A HP 1160 / 1320 / 3390', 'Hộp', 165000, 280000, 'Thùng:10:2650000'],
                    ['Hộp mực Cartridge 83A/337 HP M127 / Canon 221d', 'Hộp', 150000, 260000, 'Thùng:10:2450000'],
                    ['Hộp mực Brother TN-2385 HL-L2321D / 2361DN', 'Hộp', 125000, 220000, 'Thùng:10:2050000'],
                    ['Hộp mực Brother TN-1010 HL-1111 / 1201', 'Hộp', 115000, 200000, 'Thùng:10:1850000'],
                    ['Hộp mực HP 107a W1107A Laser 107w / 135w', 'Hộp', 230000, 380000, 'Thùng:10:3600000'],
                    ['Chai mực nạp Laser đa năng 80g Max', 'Chai', 14000, 35000, 'Thùng:50:1600000'],
                    ['Chai mực nạp Laser đa năng 140g Siêu Mịn', 'Chai', 22000, 50000, 'Thùng:50:2350000'],
                    ['Chai mực nước Epson 003 Đen 65ml Chính Hãng', 'Chai', 110000, 160000, 'Bộ:4:610000'],
                    ['Chai mực nước Epson 003 Xanh 65ml', 'Chai', 110000, 160000, ''],
                    ['Chai mực nước Epson 003 Đỏ 65ml', 'Chai', 110000, 160000, ''],
                    ['Chai mực nước Epson 003 Vàng 65ml', 'Chai', 110000, 160000, ''],
                    ['Chai mực nước Canon GI-790 Đen 135ml', 'Chai', 125000, 180000, ''],
                ],
            ],
            [
                'cat' => 'Linh kiện máy in',
                'placeholder' => 'images/placeholders/parts.svg',
                'is_service' => true,
                'items' => [
                    ['Trống in Drum 12A Mitsu Phấn Canon 2900', 'Cái', 38000, 80000, 'Hộp:10:720000'],
                    ['Trống in Drum 35A/85A Phấn HP 1102', 'Cái', 38000, 80000, 'Hộp:10:720000'],
                    ['Trống in Drum 05A/80A Mitsu HP 2035', 'Cái', 48000, 95000, 'Hộp:10:850000'],
                    ['Cụm Drum Brother DR-2385 HL-L2321D', 'Cụm', 165000, 280000, ''],
                    ['Gạt lớn (Gạt mực) 12A Canon 2900', 'Cái', 12000, 35000, 'Hộp:10:300000'],
                    ['Gạt nhỏ (Gạt từ) 12A Canon 2900', 'Cái', 12000, 35000, 'Hộp:10:300000'],
                    ['Trục từ 12A Canon 2900 kèm lò xo', 'Cái', 22000, 50000, 'Hộp:10:450000'],
                    ['Trục cao su (Trục sạc) 12A Canon 2900', 'Cái', 18000, 45000, 'Hộp:10:400000'],
                    ['Bao lụa sấy máy in Canon 2900 / HP 1020', 'Cái', 22000, 60000, 'Hộp:10:520000'],
                    ['Rulo ép sấy máy in Canon 2900 Nhựa Cam', 'Cái', 45000, 110000, ''],
                    ['Mỡ nhiệt bôi trơn bao lụa sấy máy in 10g', 'Lọ', 15000, 35000, 'Lốc:5:160000'],
                    ['Bánh xe quả đào kéo giấy Canon 2900', 'Cái', 20000, 55000, ''],
                    ['Cao su kéo giấy khay gầm HP 2035 / M401', 'Cái', 25000, 65000, ''],
                    ['Rơ le tách giấy (Solenoid) Canon 2900', 'Cái', 35000, 80000, ''],
                    ['Cảm biến sensor nhận giấy Canon 2900', 'Cái', 40000, 95000, ''],
                ],
            ],
        ];

        $results = [];
        $skuCounter = 1;

        // Loop and generate variations (colors, sizes, weights, multi-packs) to reach $target
        $variants = [
            'Đỏ', 'Xanh dương', 'Đen', 'Tím', 'Xanh lá',
            'Định lượng 70gsm', 'Định lượng 80gsm', 'Định lượng 100gsm',
            'Cỡ nhỏ', 'Cỡ trung', 'Cỡ đại', 'Loại dày', 'Loại tiêu chuẩn',
            'Bộ 3 cái', 'Bộ 5 cái', 'Gói tiết kiệm', 'Hàng nhập khẩu',
            'Chuẩn văn phòng', 'Dùng cho trường học', 'Dùng cho ngân hàng'
        ];

        while (count($results) < $target) {
            foreach ($templates as $group) {
                if (count($results) >= $target) break;

                foreach ($group['items'] as $item) {
                    if (count($results) >= $target) break;

                    $cycle = (int) floor(count($results) / 100);
                    $variant = $cycle > 0 ? ' - ' . $variants[($skuCounter + $cycle) % count($variants)] : '';

                    $name = $item[0] . $variant;
                    $skuPrefix = match ($group['cat']) {
                        'Giấy in & Photo' => 'GIAY',
                        'Bút viết & Mực viết' => 'BUT',
                        'Bìa hồ sơ & Lưu trữ' => 'BIA',
                        'Dụng cụ văn phòng' => 'DCVP',
                        'Sổ tay, Giấy note & Vở' => 'SO',
                        'Hộp mực máy in' => 'MUC',
                        'Linh kiện máy in' => 'LK',
                        default => 'SP',
                    };

                    $sku = $skuPrefix . '-' . str_pad($skuCounter, 4, '0', STR_PAD_LEFT);
                    $barcode = '893' . str_pad(601000000 + $skuCounter, 10, '0', STR_PAD_LEFT);

                    $stock = rand(15, 300);
                    $cost = $item[2];
                    $retail = $item[3];

                    $results[] = [
                        'sku' => $sku,
                        'name' => $name,
                        'category' => $group['cat'],
                        'base_unit' => $item[1],
                        'cost_price' => $cost,
                        'retail_price' => $retail,
                        'stock' => $stock,
                        'barcode' => $barcode,
                        'is_service' => $group['is_service'],
                        'dual_unit' => $item[4],
                        'placeholder' => $group['placeholder'],
                    ];

                    $skuCounter++;
                }
            }
        }

        return $results;
    }
}
