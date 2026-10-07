<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductUnit;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductImportService
{
    /**
     * Mapping dictionary for flexible Excel / CSV headers
     */
    protected array $fieldMap = [
        'name' => ['ten_san_pham', 'ten', 'name', 'san_pham', 'ten_hang', 'ten_mat_hang'],
        'sku' => ['ma_sku', 'sku', 'ma_hang', 'ma_sp', 'code', 'product_code'],
        'category' => ['danh_muc', 'category', 'nhom_hang', 'loai_hang', 'phan_loai'],
        'base_unit' => ['dvt', 'don_vi', 'don_vi_tinh', 'unit', 'base_unit'],
        'retail_price' => ['gia_ban', 'gia_ban_le', 'price', 'retail_price', 'don_gia'],
        'cost_price' => ['gia_nhap', 'gia_von', 'cost_price', 'gia_mua'],
        'stock' => ['ton_kho', 'so_luong', 'stock', 'inventory', 'sl_ton'],
        'barcode' => ['ma_vach', 'barcode', 'upc', 'ean'],
        'is_service' => ['linh_kien', 'may_in', 'is_service', 'service_part', 'linh_kien_may_in'],
        'dual_unit' => ['quy_doi', 'quy_doi_don_vi', 'don_vi_quy_doi', 'don_vi_phu', 'unit_conversion', 'thung', 'loc', 'hop', 'dong_goi'],
        'image' => ['anh', 'hinh_anh', 'image', 'image_url', 'link_anh', 'photo', 'hinh'],
    ];

    /**
     * Import products from CSV file path
     * Returns array [ 'success' => int, 'errors' => array, 'total' => int ]
     */
    public function importCsv(string $filePath): array
    {
        if (!file_exists($filePath) || !is_readable($filePath)) {
            return ['success' => 0, 'errors' => ['Tệp tin không tồn tại hoặc không có quyền đọc.'], 'total' => 0];
        }

        $handle = fopen($filePath, 'r');
        if ($handle === false) {
            return ['success' => 0, 'errors' => ['Không thể mở tệp tin CSV.'], 'total' => 0];
        }

        // Detect delimiter: comma, semicolon or tab
        $firstLine = fgets($handle);
        rewind($handle);

        // Strip UTF-8 BOM if present
        if (str_starts_with($firstLine, "\xEF\xBB\xBF")) {
            fseek($handle, 3);
        }

        $delimiter = str_contains($firstLine, ';') ? ';' : (str_contains($firstLine, "\t") ? "\t" : ',');

        // Read header
        $headerRaw = fgetcsv($handle, 0, $delimiter);
        if (!$headerRaw) {
            fclose($handle);
            return ['success' => 0, 'errors' => ['Tệp tin trống hoặc không có dòng tiêu đề.'], 'total' => 0];
        }

        // Normalize header names to slugs (e.g., "Tên sản phẩm" -> "ten_san_pham")
        $header = array_map(function ($col) {
            $cleaned = str_replace("\xEF\xBB\xBF", '', trim($col));
            $slug = Str::slug($cleaned, '_');
            return $slug ?: strtolower($cleaned);
        }, $headerRaw);

        $successCount = 0;
        $errorList = [];
        $rowNumber = 1;

        // Cache existing categories to avoid repeated SQL queries
        $categories = Category::all()->keyBy(fn ($c) => mb_strtolower($c->name))->toArray();

        DB::beginTransaction();
        try {
            while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                $rowNumber++;
                if (empty(array_filter($row))) {
                    continue; // Skip empty rows
                }

                $data = [];
                foreach ($header as $idx => $key) {
                    $data[$key] = isset($row[$idx]) ? trim($row[$idx]) : '';
                }

                // Helper to extract field matching any synonym (exact or partial)
                $extract = function (string $targetField) use ($data): string {
                    foreach ($this->fieldMap[$targetField] ?? [] as $synonym) {
                        if (isset($data[$synonym]) && $data[$synonym] !== '') {
                            return (string) $data[$synonym];
                        }
                    }
                    foreach ($data as $colKey => $val) {
                        if ($val === '') {
                            continue;
                        }
                        foreach ($this->fieldMap[$targetField] ?? [] as $synonym) {
                            if (str_contains($colKey, $synonym)) {
                                return (string) $val;
                            }
                        }
                    }
                    return '';
                };

                // Validate minimum field: Product Name
                $name = $extract('name');
                if (empty($name)) {
                    $errorList[] = "Dòng $rowNumber: Bỏ qua do thiếu tên sản phẩm.";
                    continue;
                }

                $sku = $extract('sku');
                if (empty($sku)) {
                    $sku = 'SP-' . strtoupper(Str::random(6));
                }

                // Category lookup or intelligent creation
                $categoryName = $extract('category') ?: 'Dụng cụ văn phòng';
                $categoryKey = mb_strtolower($categoryName);

                if (!isset($categories[$categoryKey])) {
                    $slugCat = Str::slug($categoryName) ?: 'danh-muc-' . Str::random(5);
                    $placeholder = $this->determinePlaceholder($categoryName);

                    $newCat = Category::create([
                        'name' => $categoryName,
                        'slug' => $slugCat,
                        'placeholder_image' => $placeholder,
                        'is_active' => true,
                    ]);
                    $categories[$categoryKey] = $newCat->toArray();
                    $categoryId = $newCat->id;
                } else {
                    $categoryId = $categories[$categoryKey]['id'];
                }

                $retailPrice = (float) str_replace([',', '.'], '', $extract('retail_price') ?: '0');
                $costPrice = (float) str_replace([',', '.'], '', $extract('cost_price') ?: '0');
                $stockQuantity = (int) ($extract('stock') ?: '0');
                $baseUnit = $extract('base_unit') ?: 'Cái';
                $barcode = $extract('barcode');
                $isService = in_array(strtolower($extract('is_service')), ['1', 'true', 'yes', 'linh kien', 'may in']);

                $imageVal = $extract('image');
                $imagePath = null;
                $hasCustomImage = false;

                if (!empty($imageVal)) {
                    if (str_starts_with($imageVal, 'http://') || str_starts_with($imageVal, 'https://')) {
                        try {
                            $ctx = stream_context_create(['http' => ['timeout' => 5]]);
                            $content = @file_get_contents($imageVal, false, $ctx);
                            if ($content) {
                                $ext = pathinfo(parse_url($imageVal, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
                                $tempDir = storage_path('app/public/products');
                                if (!is_dir($tempDir)) {
                                    mkdir($tempDir, 0755, true);
                                }
                                $tempFile = $tempDir . '/tmp_' . Str::random(8) . '.' . $ext;
                                file_put_contents($tempFile, $content);
                                $optimizer = new ImageOptimizationService();
                                $optimized = $optimizer->optimizeImage($tempFile, 'products');
                                @unlink($tempFile);
                                if ($optimized) {
                                    $imagePath = $optimized;
                                    $hasCustomImage = true;
                                }
                            }
                        } catch (\Throwable $e) {
                            // Continue without failing entire row
                        }
                    } elseif (file_exists(storage_path('app/public/' . $imageVal))) {
                        $imagePath = $imageVal;
                        $hasCustomImage = true;
                    }
                }

                $product = Product::updateOrCreate(
                    ['sku' => $sku],
                    [
                        'name' => $name,
                        'slug' => Str::slug($name) . '-' . strtolower(Str::random(4)),
                        'category_id' => $categoryId,
                        'barcode' => $barcode ?: null,
                        'cost_price' => $costPrice,
                        'retail_price' => $retailPrice,
                        'stock_quantity' => $stockQuantity,
                        'base_unit' => $baseUnit,
                        'image_path' => $imagePath,
                        'has_custom_image' => $hasCustomImage,
                        'is_service_part' => $isService,
                        'is_active' => true,
                    ]
                );

                // Check for dual unit conversions (support multiple units separated by "|" or ";")
                // Format: "Tên_đơn_vị:Hệ_số:Giá" (VD: "Thùng:5:335000 | Lốc:10:650000")
                $dualUnitRaw = $extract('dual_unit');
                if (!empty($dualUnitRaw)) {
                    $unitItems = preg_split('/[|;]/', $dualUnitRaw);
                    foreach ($unitItems as $unitItem) {
                        $unitItem = trim($unitItem);
                        if (str_contains($unitItem, ':')) {
                            $parts = explode(':', $unitItem);
                            if (count($parts) >= 3) {
                                $uName = trim($parts[0]);
                                $convRate = max(1, (int) $parts[1]);
                                $uPrice = (float) str_replace([',', '.'], '', $parts[2]);

                                ProductUnit::updateOrCreate(
                                    ['product_id' => $product->id, 'unit_name' => $uName],
                                    [
                                        'conversion_rate' => $convRate,
                                        'price' => $uPrice,
                                    ]
                                );
                            }
                        }
                    }
                }

                $successCount++;
            }

            DB::commit();
            fclose($handle);

            return [
                'success' => $successCount,
                'errors' => $errorList,
                'total' => $rowNumber - 1,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            fclose($handle);
            return [
                'success' => 0,
                'errors' => ['Lỗi cơ sở dữ liệu khi nhập dữ liệu: ' . $e->getMessage()],
                'total' => 0,
            ];
        }
    }

    /**
     * Intelligent placeholder selection based on category keyword
     */
    protected function determinePlaceholder(string $categoryName): string
    {
        $name = mb_strtolower($categoryName);

        if (str_contains($name, 'giay') || str_contains($name, 'giấy')) {
            return 'images/placeholders/paper.svg';
        }
        if (str_contains($name, 'but') || str_contains($name, 'bút')) {
            return 'images/placeholders/pen.svg';
        }
        if (str_contains($name, 'muc') || str_contains($name, 'mực') || str_contains($name, 'cartridge')) {
            return 'images/placeholders/toner.svg';
        }
        if (str_contains($name, 'linh kien') || str_contains($name, 'linh kiện') || str_contains($name, 'drum')) {
            return 'images/placeholders/parts.svg';
        }
        if (str_contains($name, 'so') || str_contains($name, 'sổ') || str_contains($name, 'file') || str_contains($name, 'bia') || str_contains($name, 'bìa')) {
            return 'images/placeholders/folder.svg';
        }

        return 'images/placeholders/stationery.svg';
    }

    /**
     * Generate standard sample CSV template for downloading
     */
    public function generateSampleCsv(): string
    {
        $headers = [
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
        ];

        $sampleRows = [
            ['GIAY-DBA-A4-70', 'Giấy in Double A A4 70gsm', 'Giấy in & Photo', 'Ram', '58000', '69000', '100', '8936012340011', '0', 'Thùng:5:335000', 'https://example.com/double-a.jpg'],
            ['GIAY-PPO-A4-80', 'Giấy in PaperOne A4 80gsm', 'Giấy in & Photo', 'Ram', '70000', '84000', '80', '8936012340028', '0', 'Thùng:5:410000', ''],
            ['BUT-TL027-XANH', 'Bút bi Thiên Long TL-027 Xanh', 'Bút viết & Mực viết', 'Cây', '3500', '5000', '300', '8935001802705', '0', 'Hộp:20:95000', ''],
            ['MUC-CARTRIDGE-303', 'Hộp mực Cartridge 303/12A Canon 2900', 'Hộp mực máy in', 'Hộp', '135000', '240000', '30', '8936012340080', '1', '', ''],
            ['LK-DRUM-12A', 'Trống in Drum 12A Mitsu', 'Linh kiện máy in', 'Cái', '38000', '80000', '50', '8936012340134', '1', '', ''],
            ['LK-MUC-NAP-80G', 'Chai mực nạp Laser đa năng 80g', 'Linh kiện máy in', 'Chai', '15000', '40000', '120', '8936012340172', '1', 'Thùng:50:1800000', ''],
        ];

        $output = implode(',', $headers) . "\n";
        foreach ($sampleRows as $row) {
            $output .= implode(',', array_map(fn ($v) => '"' . str_replace('"', '""', $v) . '"', $row)) . "\n";
        }

        return $output;
    }
}
