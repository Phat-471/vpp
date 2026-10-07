<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentTransaction;
use App\Models\PrinterModel;
use App\Models\Product;
use App\Models\ProductUnit;
use App\Models\RepairItem;
use App\Models\RepairTicket;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Users (Roles: Admin, Cashier, Technician)
        $admin = User::firstOrCreate(
            ['email' => 'admin@vpp.local'],
            [
                'name' => 'Nguyễn Quản Trị (Admin)',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'phone' => '0901234567',
            ]
        );

        $cashier = User::firstOrCreate(
            ['email' => 'thungan@vpp.local'],
            [
                'name' => 'Trần Thu Ngân',
                'password' => Hash::make('password'),
                'role' => 'cashier',
                'phone' => '0902345678',
            ]
        );

        $technician = User::firstOrCreate(
            ['email' => 'kythuat@vpp.local'],
            [
                'name' => 'Lê Kỹ Thuật (Thợ máy in)',
                'password' => Hash::make('password'),
                'role' => 'technician',
                'phone' => '0903456789',
            ]
        );

        // 2. Seed Categories with placeholder icons
        $categoriesData = [
            ['name' => 'Giấy in & Photo', 'slug' => 'giay-in-photo', 'icon' => 'heroicon-o-document-text', 'placeholder_image' => 'images/placeholders/paper.svg', 'sort_order' => 1],
            ['name' => 'Bút viết & Mực viết', 'slug' => 'but-viet-muc-viet', 'icon' => 'heroicon-o-pencil', 'placeholder_image' => 'images/placeholders/pen.svg', 'sort_order' => 2],
            ['name' => 'Bìa & File hồ sơ', 'slug' => 'bia-file-ho-so', 'icon' => 'heroicon-o-folder', 'placeholder_image' => 'images/placeholders/folder.svg', 'sort_order' => 3],
            ['name' => 'Sổ tay, Tập & Giấy note', 'slug' => 'so-tay-tap-note', 'icon' => 'heroicon-o-book-open', 'placeholder_image' => 'images/placeholders/notebook.svg', 'sort_order' => 4],
            ['name' => 'Dụng cụ văn phòng', 'slug' => 'dung-cu-van-phong', 'icon' => 'heroicon-o-wrench', 'placeholder_image' => 'images/placeholders/stationery.svg', 'sort_order' => 5],
            ['name' => 'Hộp mực máy in', 'slug' => 'hop-muc-may-in', 'icon' => 'heroicon-o-circle-stack', 'placeholder_image' => 'images/placeholders/toner.svg', 'sort_order' => 6],
            ['name' => 'Linh kiện & Dịch vụ máy in', 'slug' => 'linh-kien-may-in', 'icon' => 'heroicon-o-cog', 'placeholder_image' => 'images/placeholders/parts.svg', 'sort_order' => 7],
        ];

        $categories = [];
        foreach ($categoriesData as $cData) {
            $categories[$cData['slug']] = Category::firstOrCreate(['slug' => $cData['slug']], $cData);
        }

        // 3. Seed Printer Models
        $printerModelsData = [
            ['brand' => 'Canon', 'model_name' => 'LBP 2900', 'printer_type' => 'Laser đen trắng đơn năng', 'compatible_cartridges' => 'Cartridge 303, Cartridge 12A', 'notes' => 'Máy quốc dân, bền bỉ, dễ nạp mực'],
            ['brand' => 'Canon', 'model_name' => 'LBP 3300', 'printer_type' => 'Laser đen trắng in 2 mặt', 'compatible_cartridges' => 'Cartridge 308, Cartridge 49A', 'notes' => 'Tự động đảo mặt duplex'],
            ['brand' => 'Canon', 'model_name' => 'LBP 6230dn', 'printer_type' => 'Laser đen trắng mạng LAN', 'compatible_cartridges' => 'Cartridge 326', 'notes' => 'In qua mạng LAN, hộp mực nhỏ'],
            ['brand' => 'HP', 'model_name' => 'LaserJet 107a', 'printer_type' => 'Laser đen trắng nhỏ gọn', 'compatible_cartridges' => 'HP 107A (W1107A)', 'notes' => 'Có chip mực, cần reset hoặc thay chip'],
            ['brand' => 'HP', 'model_name' => 'LaserJet Pro M404dn', 'printer_type' => 'Laser đen trắng tốc độ cao', 'compatible_cartridges' => 'HP 76A (CF276A)', 'notes' => 'Khổ A4, chip bảo mật cao'],
            ['brand' => 'Brother', 'model_name' => 'HL-L2321D', 'printer_type' => 'Laser đen trắng đảo mặt', 'compatible_cartridges' => 'TN-2385 (Hộp mực) / DR-2385 (Cụm Drum)', 'notes' => 'Cần reset nhông hộp mực sau khi nạp'],
            ['brand' => 'Brother', 'model_name' => 'DCP-B7535DW', 'printer_type' => 'Laser đa năng Wifi', 'compatible_cartridges' => 'TN-B022 / DR-B022', 'notes' => 'Mực siêu rẻ Toner Box'],
            ['brand' => 'Epson', 'model_name' => 'EcoTank L3210', 'printer_type' => 'Phun màu đa năng hệ thống bình mực liên tục', 'compatible_cartridges' => 'Mực nước Epson 003 (C/M/Y/BK)', 'notes' => 'Đổ mực chai chính hãng'],
        ];

        $printerModels = [];
        foreach ($printerModelsData as $pmData) {
            $printerModels[$pmData['model_name']] = PrinterModel::firstOrCreate(
                ['brand' => $pmData['brand'], 'model_name' => $pmData['model_name']],
                $pmData
            );
        }

        // 4. Seed Products with Dual-Unit conversions and Printer compatibility
        $productsData = [
            // Giấy in
            [
                'category_slug' => 'giay-in-photo',
                'sku' => 'GIAY-DBA-A4-70',
                'barcode' => '8936012340011',
                'name' => 'Giấy in Double A A4 định lượng 70gsm (Ram 500 tờ)',
                'slug' => 'giay-in-double-a-a4-70gsm',
                'cost_price' => 58000,
                'retail_price' => 69000,
                'stock_quantity' => 120, // 120 Ram
                'base_unit' => 'Ram',
                'is_service_part' => false,
                'units' => [
                    ['unit_name' => 'Thùng (5 Ram)', 'conversion_rate' => 5, 'price' => 335000, 'barcode' => '8936012340012'],
                ]
            ],
            [
                'category_slug' => 'giay-in-photo',
                'sku' => 'GIAY-PPO-A4-80',
                'barcode' => '8936012340028',
                'name' => 'Giấy in PaperOne A4 định lượng 80gsm (Ram 500 tờ)',
                'slug' => 'giay-in-paperone-a4-80gsm',
                'cost_price' => 70000,
                'retail_price' => 84000,
                'stock_quantity' => 80,
                'base_unit' => 'Ram',
                'is_service_part' => false,
                'units' => [
                    ['unit_name' => 'Thùng (5 Ram)', 'conversion_rate' => 5, 'price' => 410000, 'barcode' => '8936012340029'],
                ]
            ],
            [
                'category_slug' => 'giay-in-photo',
                'sku' => 'GIAY-IKP-A4-70',
                'barcode' => '8936012340035',
                'name' => 'Giấy in IK Plus A4 định lượng 70gsm',
                'slug' => 'giay-in-ik-plus-a4-70gsm',
                'cost_price' => 56000,
                'retail_price' => 66000,
                'stock_quantity' => 65,
                'base_unit' => 'Ram',
                'is_service_part' => false,
                'units' => [
                    ['unit_name' => 'Thùng (5 Ram)', 'conversion_rate' => 5, 'price' => 320000, 'barcode' => '8936012340036'],
                ]
            ],

            // Bút viết
            [
                'category_slug' => 'but-viet-muc-viet',
                'sku' => 'BUT-TL027-XANH',
                'barcode' => '8935001802705',
                'name' => 'Bút bi Thiên Long TL-027 nét 0.5mm màu Xanh',
                'slug' => 'but-bi-thien-long-tl-027-xanh',
                'cost_price' => 3500,
                'retail_price' => 5000,
                'stock_quantity' => 350,
                'base_unit' => 'Cây',
                'is_service_part' => false,
                'units' => [
                    ['unit_name' => 'Hộp (20 cây)', 'conversion_rate' => 20, 'price' => 95000, 'barcode' => '8935001802712'],
                ]
            ],
            [
                'category_slug' => 'but-viet-muc-viet',
                'sku' => 'BUT-GEL08-DEN',
                'barcode' => '8935001800817',
                'name' => 'Bút gel xóa được Thiên Long Masterart màu Đen',
                'slug' => 'but-gel-xoa-duoc-thien-long-den',
                'cost_price' => 9000,
                'retail_price' => 14000,
                'stock_quantity' => 120,
                'base_unit' => 'Cây',
                'is_service_part' => false,
                'units' => [
                    ['unit_name' => 'Hộp (12 cây)', 'conversion_rate' => 12, 'price' => 160000, 'barcode' => '8935001800824'],
                ]
            ],
            [
                'category_slug' => 'but-viet-muc-viet',
                'sku' => 'BUT-WB03-XANH',
                'barcode' => '8935001803030',
                'name' => 'Bút lông bảng Thiên Long WB-03 màu Xanh',
                'slug' => 'but-long-bang-thien-long-wb03-xanh',
                'cost_price' => 6000,
                'retail_price' => 9000,
                'stock_quantity' => 90,
                'base_unit' => 'Cây',
                'is_service_part' => false,
                'units' => [
                    ['unit_name' => 'Hộp (10 cây)', 'conversion_rate' => 10, 'price' => 85000, 'barcode' => '8935001803031'],
                ]
            ],

            // Dụng cụ văn phòng
            [
                'category_slug' => 'dung-cu-van-phong',
                'sku' => 'DC-BAMKIM-PLUS10',
                'barcode' => '4977564000105',
                'name' => 'Bấm kim số 10 Plus PS-10E trợ lực',
                'slug' => 'bam-kim-so-10-plus-ps-10e',
                'cost_price' => 32000,
                'retail_price' => 45000,
                'stock_quantity' => 45,
                'base_unit' => 'Cái',
                'is_service_part' => false,
            ],
            [
                'category_slug' => 'dung-cu-van-phong',
                'sku' => 'DC-KIMBAM-PLUS10',
                'barcode' => '4977564000112',
                'name' => 'Hộp kim bấm số 10 Plus No.10',
                'slug' => 'hop-kim-bam-so-10-plus',
                'cost_price' => 3000,
                'retail_price' => 5000,
                'stock_quantity' => 200,
                'base_unit' => 'Hộp',
                'is_service_part' => false,
                'units' => [
                    ['unit_name' => 'Lốc (10 hộp)', 'conversion_rate' => 10, 'price' => 48000, 'barcode' => '4977564000113'],
                ]
            ],
            [
                'category_slug' => 'dung-cu-van-phong',
                'sku' => 'DC-BANGKEO-5CM',
                'barcode' => '8936012340059',
                'name' => 'Băng keo trong dán thùng 5cm x 100 yard',
                'slug' => 'bang-keo-trong-5cm-100y',
                'cost_price' => 11000,
                'retail_price' => 16000,
                'stock_quantity' => 180,
                'base_unit' => 'Cuộn',
                'is_service_part' => false,
                'units' => [
                    ['unit_name' => 'Cây (6 cuộn)', 'conversion_rate' => 6, 'price' => 90000, 'barcode' => '8936012340050'],
                ]
            ],

            // Bìa & File hồ sơ
            [
                'category_slug' => 'bia-file-ho-so',
                'sku' => 'BIA-CONG-7CM-KOKUYO',
                'barcode' => '8936012340066',
                'name' => 'Bìa còng bật Kokuyo 7cm khổ A4 (lưu ~500 tờ)',
                'slug' => 'bia-cong-bat-kokuyo-7cm-a4',
                'cost_price' => 42000,
                'retail_price' => 58000,
                'stock_quantity' => 60,
                'base_unit' => 'Cái',
                'is_service_part' => false,
            ],
            [
                'category_slug' => 'bia-file-ho-so',
                'sku' => 'BIA-NUT-MYCLEAR-A4',
                'barcode' => '8936012340073',
                'name' => 'Bìa nút My Clear Bag A4 dày dặn',
                'slug' => 'bia-nut-my-clear-bag-a4',
                'cost_price' => 2500,
                'retail_price' => 4500,
                'stock_quantity' => 300,
                'base_unit' => 'Cái',
                'is_service_part' => false,
                'units' => [
                    ['unit_name' => 'Xấp (10 cái)', 'conversion_rate' => 10, 'price' => 40000, 'barcode' => '8936012340074'],
                ]
            ],

            // Hộp mực máy in & Tương thích
            [
                'category_slug' => 'hop-muc-may-in',
                'sku' => 'MUC-CARTRIDGE-303-12A',
                'barcode' => '8936012340080',
                'name' => 'Hộp mực Cartridge 303/12A (Dùng cho Canon 2900 / HP 1020)',
                'slug' => 'hop-muc-cartridge-303-12a',
                'cost_price' => 135000,
                'retail_price' => 240000,
                'stock_quantity' => 25,
                'base_unit' => 'Hộp',
                'is_service_part' => true,
                'compatible_printers' => ['LBP 2900'],
            ],
            [
                'category_slug' => 'hop-muc-may-in',
                'sku' => 'MUC-CARTRIDGE-49A-53A',
                'barcode' => '8936012340097',
                'name' => 'Hộp mực Cartridge 308/49A (Dùng cho Canon 3300 / HP 1320)',
                'slug' => 'hop-muc-cartridge-308-49a',
                'cost_price' => 165000,
                'retail_price' => 290000,
                'stock_quantity' => 18,
                'base_unit' => 'Hộp',
                'is_service_part' => true,
                'compatible_printers' => ['LBP 3300'],
            ],
            [
                'category_slug' => 'hop-muc-may-in',
                'sku' => 'MUC-BROTHER-TN2385',
                'barcode' => '8936012340103',
                'name' => 'Hộp mực Brother TN-2385 (Dùng cho HL-L2321D, L2361DN, L2366DW)',
                'slug' => 'hop-muc-brother-tn-2385',
                'cost_price' => 150000,
                'retail_price' => 260000,
                'stock_quantity' => 20,
                'base_unit' => 'Hộp',
                'is_service_part' => true,
                'compatible_printers' => ['HL-L2321D'],
            ],
            [
                'category_slug' => 'hop-muc-may-in',
                'sku' => 'MUC-HP-107A',
                'barcode' => '8936012340110',
                'name' => 'Hộp mực HP 107A (W1107A không kèm chip)',
                'slug' => 'hop-muc-hp-107a-w1107a',
                'cost_price' => 180000,
                'retail_price' => 310000,
                'stock_quantity' => 14,
                'base_unit' => 'Hộp',
                'is_service_part' => true,
                'compatible_printers' => ['LaserJet 107a'],
            ],
            [
                'category_slug' => 'hop-muc-may-in',
                'sku' => 'MUC-EPSON-003-BK',
                'barcode' => '8936012340127',
                'name' => 'Mực nước Epson 003 màu Đen Black (L3110, L3210, L3250)',
                'slug' => 'muc-nuoc-epson-003-mau-den',
                'cost_price' => 85000,
                'retail_price' => 140000,
                'stock_quantity' => 30,
                'base_unit' => 'Chai',
                'is_service_part' => true,
                'compatible_printers' => ['EcoTank L3210'],
            ],

            // Linh kiện sửa chữa máy in (Dùng chung kho VPP)
            [
                'category_slug' => 'linh-kien-may-in',
                'sku' => 'LK-DRUM-12A',
                'barcode' => '8936012340134',
                'name' => 'Trống in Drum 12A/303 Mitsu (Canon 2900 / HP 1020)',
                'slug' => 'trong-in-drum-12a-mitsu-canon-2900',
                'cost_price' => 38000,
                'retail_price' => 80000,
                'stock_quantity' => 40,
                'base_unit' => 'Cái',
                'is_service_part' => true,
                'compatible_printers' => ['LBP 2900'],
            ],
            [
                'category_slug' => 'linh-kien-may-in',
                'sku' => 'LK-GAT-12A',
                'barcode' => '8936012340141',
                'name' => 'Gạt lớn/Gạt nhỏ hộp mực 12A/303',
                'slug' => 'gat-hop-muc-12a-canon-2900',
                'cost_price' => 12000,
                'retail_price' => 35000,
                'stock_quantity' => 50,
                'base_unit' => 'Cái',
                'is_service_part' => true,
                'compatible_printers' => ['LBP 2900'],
            ],
            [
                'category_slug' => 'linh-kien-may-in',
                'sku' => 'LK-TRUCTU-12A',
                'barcode' => '8936012340158',
                'name' => 'Trục từ nam châm hộp mực 12A/303',
                'slug' => 'truc-tu-hop-muc-12a',
                'cost_price' => 18000,
                'retail_price' => 45000,
                'stock_quantity' => 35,
                'base_unit' => 'Cái',
                'is_service_part' => true,
                'compatible_printers' => ['LBP 2900'],
            ],
            [
                'category_slug' => 'linh-kien-may-in',
                'sku' => 'LK-BAOLUA-2900',
                'barcode' => '8936012340165',
                'name' => 'Bao lụa cụm sấy Canon 2900 / HP 1020 loại tốt',
                'slug' => 'bao-lua-cum-say-canon-2900',
                'cost_price' => 25000,
                'retail_price' => 70000,
                'stock_quantity' => 28,
                'base_unit' => 'Cái',
                'is_service_part' => true,
                'compatible_printers' => ['LBP 2900'],
            ],
            [
                'category_slug' => 'linh-kien-may-in',
                'sku' => 'LK-MUC-NAP-LASER-80G',
                'barcode' => '8936012340172',
                'name' => 'Chai mực nạp Laser đa năng siêu mịn 80g Maxstar',
                'slug' => 'chai-muc-nap-laser-da-nang-80g',
                'cost_price' => 15000,
                'retail_price' => 40000,
                'stock_quantity' => 150,
                'base_unit' => 'Chai',
                'is_service_part' => true,
                'compatible_printers' => ['LBP 2900', 'LBP 3300'],
            ],
        ];

        $products = [];
        foreach ($productsData as $pData) {
            $cat = $categories[$pData['category_slug']] ?? null;
            $product = Product::firstOrCreate(
                ['sku' => $pData['sku']],
                [
                    'category_id' => $cat?->id,
                    'barcode' => $pData['barcode'] ?? null,
                    'name' => $pData['name'],
                    'slug' => $pData['slug'],
                    'cost_price' => $pData['cost_price'],
                    'retail_price' => $pData['retail_price'],
                    'stock_quantity' => $pData['stock_quantity'],
                    'low_stock_threshold' => 5,
                    'base_unit' => $pData['base_unit'],
                    'has_custom_image' => false,
                    'is_service_part' => $pData['is_service_part'] ?? false,
                    'is_active' => true,
                    'description' => 'Mô tả chi tiết sản phẩm chất lượng cao chính hãng.',
                ]
            );

            // Seed dual-unit conversion if any
            if (!empty($pData['units'])) {
                foreach ($pData['units'] as $u) {
                    ProductUnit::firstOrCreate(
                        ['product_id' => $product->id, 'unit_name' => $u['unit_name']],
                        [
                            'conversion_rate' => $u['conversion_rate'],
                            'price' => $u['price'],
                            'barcode' => $u['barcode'] ?? null,
                        ]
                    );
                }
            }

            // Map compatible printers
            if (!empty($pData['compatible_printers'])) {
                foreach ($pData['compatible_printers'] as $pModelName) {
                    if (isset($printerModels[$pModelName])) {
                        $product->compatiblePrinters()->syncWithoutDetaching([$printerModels[$pModelName]->id => ['notes' => 'Tương thích hoàn toàn']]);
                    }
                }
            }

            $products[$pData['sku']] = $product;
        }

        // 5. Seed Customers
        $customersData = [
            ['name' => 'Anh Nguyễn Văn Hùng', 'phone' => '0912345678', 'address' => 'Số 15 Lê Duẩn, P. Bến Nghé, Q.1, TP.HCM'],
            ['name' => 'Công Ty TNHH Thiết Kế Nam Á', 'phone' => '0988765432', 'address' => 'Tòa nhà Landmark 81, P.22, Bình Thạnh, TP.HCM'],
            ['name' => 'Cô Mai (Trường THCS Lê Quý Đôn)', 'phone' => '0903112233', 'address' => '22 Nguyễn Thị Minh Khai, Q.3, TP.HCM'],
        ];

        $customers = [];
        foreach ($customersData as $c) {
            $customer = Customer::firstOrCreate(['phone' => $c['phone']], $c);
            $customers[] = $customer;
        }

        // 6. Seed Sample Repair Tickets (Both Flow 1 and Flow 2)
        // Ticket 1: Flow 1 (Quote immediate, in progress)
        $t1 = RepairTicket::create([
            'uuid' => (string) Str::uuid(),
            'ticket_code' => 'SC260001',
            'customer_id' => $customers[0]->id,
            'customer_name' => $customers[0]->name,
            'customer_phone' => $customers[0]->phone,
            'phone_last4' => '5678',
            'printer_model_id' => $printerModels['LBP 2900']->id,
            'device_name' => 'Canon LBP 2900',
            'serial_number' => 'CN2900-VN-88219',
            'accessories' => 'Dây nguồn, Khay đỡ giấy',
            'issue_description' => 'Khách báo bản in bị vệt đen dọc trang giấy, thỉnh thoảng kẹt giấy ở khay ra.',
            'technician_diagnosis' => 'Hư Drum (trống mòn sọc), gạt mực bị cùn mép, cụm sấy hoạt động tốt.',
            'intake_flow' => 'quote_immediate',
            'status' => 'in_progress',
            'labor_fee' => 60000,
            'parts_total' => 115000,
            'discount_amount' => 0,
            'tax_amount' => 0,
            'grand_total' => 175000,
            'paid_amount' => 0,
            'payment_status' => 'unpaid',
            'payment_method' => 'cash',
            'promised_at' => now()->addDays(1)->setTime(16, 0),
            'technician_id' => $technician->id,
            'created_by' => $cashier->id,
            'internal_notes' => 'Ưu tiên làm sớm, khách cần gấp để in hợp đồng.',
        ]);

        RepairItem::create([
            'repair_ticket_id' => $t1->id,
            'product_id' => $products['LK-DRUM-12A']->id,
            'item_name' => 'Thay Trống in Drum 12A Mitsu mới',
            'unit' => 'Cái',
            'quantity' => 1,
            'cost_price' => 38000,
            'unit_price' => 80000,
            'subtotal' => 80000,
        ]);

        RepairItem::create([
            'repair_ticket_id' => $t1->id,
            'product_id' => $products['LK-GAT-12A']->id,
            'item_name' => 'Thay Gạt lớn 12A',
            'unit' => 'Cái',
            'quantity' => 1,
            'cost_price' => 12000,
            'unit_price' => 35000,
            'subtotal' => 35000,
        ]);

        // Ticket 2: Flow 1 (Completed & Paid via VietQR)
        $t2 = RepairTicket::create([
            'uuid' => (string) Str::uuid(),
            'ticket_code' => 'SC260002',
            'customer_id' => $customers[1]->id,
            'customer_name' => $customers[1]->name,
            'customer_phone' => $customers[1]->phone,
            'phone_last4' => '5432',
            'printer_model_id' => $printerModels['HL-L2321D']->id,
            'device_name' => 'Brother HL-L2321D',
            'serial_number' => 'BR2321D-77312',
            'accessories' => 'Dây nguồn, Cáp USB 1.5m',
            'issue_description' => 'Máy báo đèn Toner vàng chớp nháy, không in được.',
            'technician_diagnosis' => 'Hết mực, nhông reset hộp mực đã nhảy, drum còn tốt 90%. Đã thay hộp mực mới và vệ sinh máy.',
            'intake_flow' => 'quote_immediate',
            'status' => 'completed',
            'labor_fee' => 50000,
            'parts_total' => 260000,
            'discount_amount' => 10000,
            'tax_amount' => 0,
            'grand_total' => 300000,
            'paid_amount' => 300000,
            'payment_status' => 'paid',
            'payment_method' => 'vietqr',
            'promised_at' => now()->setTime(11, 0),
            'completed_at' => now()->subHours(1),
            'technician_id' => $technician->id,
            'created_by' => $cashier->id,
            'internal_notes' => 'Khách quen công ty Nam Á, đã thanh toán qua VietQR SePay.',
        ]);

        RepairItem::create([
            'repair_ticket_id' => $t2->id,
            'product_id' => $products['MUC-BROTHER-TN2385']->id,
            'item_name' => 'Hộp mực Brother TN-2385 mới 100%',
            'unit' => 'Hộp',
            'quantity' => 1,
            'cost_price' => 150000,
            'unit_price' => 260000,
            'subtotal' => 260000,
        ]);

        // Ticket 3: Flow 2 (Quote later, currently diagnosing)
        RepairTicket::create([
            'uuid' => (string) Str::uuid(),
            'ticket_code' => 'SC260003',
            'customer_id' => $customers[2]->id,
            'customer_name' => $customers[2]->name,
            'customer_phone' => $customers[2]->phone,
            'phone_last4' => '2233',
            'printer_model_id' => $printerModels['LaserJet 107a']->id,
            'device_name' => 'HP LaserJet 107a',
            'serial_number' => 'HP107A-99120',
            'accessories' => 'Máy trần (không dây)',
            'issue_description' => 'Bật nguồn máy kêu rè rè lớn ở khay giấy rồi báo đèn đỏ tam giác, không kéo được giấy vào.',
            'technician_diagnosis' => 'Đang tháo vỏ kiểm tra bánh răng nhông truyền động và cảm biến load giấy.',
            'intake_flow' => 'quote_later',
            'status' => 'diagnosing',
            'labor_fee' => 0,
            'parts_total' => 0,
            'discount_amount' => 0,
            'tax_amount' => 0,
            'grand_total' => 0,
            'paid_amount' => 0,
            'payment_status' => 'unpaid',
            'payment_method' => 'cash',
            'promised_at' => now()->addDays(2)->setTime(15, 30),
            'technician_id' => $technician->id,
            'created_by' => $cashier->id,
            'internal_notes' => 'Cần kiểm tra kỹ báo giá trước qua điện thoại cho cô Mai.',
        ]);

        // 7. Seed Sample POS Orders
        // Order 1: POS cash order
        $order1 = Order::create([
            'uuid' => (string) Str::uuid(),
            'order_code' => 'HD260001',
            'customer_id' => $customers[0]->id,
            'customer_name' => $customers[0]->name,
            'customer_phone' => $customers[0]->phone,
            'channel' => 'pos',
            'status' => 'completed',
            'subtotal' => 317000,
            'discount_amount' => 17000,
            'tax_rate' => 0,
            'tax_amount' => 0,
            'grand_total' => 300000,
            'paid_amount' => 300000,
            'payment_status' => 'paid',
            'payment_method' => 'cash',
            'created_by' => $cashier->id,
        ]);

        OrderItem::create([
            'order_id' => $order1->id,
            'product_id' => $products['GIAY-DBA-A4-70']->id,
            'product_name' => $products['GIAY-DBA-A4-70']->name,
            'unit_name' => 'Ram',
            'quantity' => 3,
            'conversion_rate' => 1,
            'cost_price' => 58000,
            'unit_price' => 69000,
            'subtotal' => 207000,
        ]);

        OrderItem::create([
            'order_id' => $order1->id,
            'product_id' => $products['BUT-TL027-XANH']->id,
            'product_name' => $products['BUT-TL027-XANH']->name,
            'unit_name' => 'Cây',
            'quantity' => 10,
            'conversion_rate' => 1,
            'cost_price' => 3500,
            'unit_price' => 5000,
            'subtotal' => 50000,
        ]);

        OrderItem::create([
            'order_id' => $order1->id,
            'product_id' => $products['DC-BANGKEO-5CM']->id,
            'product_name' => $products['DC-BANGKEO-5CM']->name,
            'unit_name' => 'Cuộn',
            'quantity' => 4,
            'conversion_rate' => 1,
            'cost_price' => 11000,
            'unit_price' => 15000,
            'subtotal' => 60000,
        ]);

        // Order 2: POS dual-unit box order (Paid via VietQR)
        $order2 = Order::create([
            'uuid' => (string) Str::uuid(),
            'order_code' => 'HD260002',
            'customer_id' => $customers[1]->id,
            'customer_name' => $customers[1]->name,
            'customer_phone' => $customers[1]->phone,
            'channel' => 'pos',
            'status' => 'completed',
            'subtotal' => 745000,
            'discount_amount' => 0,
            'tax_rate' => 0,
            'tax_amount' => 0,
            'grand_total' => 745000,
            'paid_amount' => 745000,
            'payment_status' => 'paid',
            'payment_method' => 'vietqr',
            'created_by' => $cashier->id,
        ]);

        $doubleAThung = $products['GIAY-DBA-A4-70']->units()->where('unit_name', 'LIKE', '%Thùng%')->first();
        OrderItem::create([
            'order_id' => $order2->id,
            'product_id' => $products['GIAY-DBA-A4-70']->id,
            'product_unit_id' => $doubleAThung?->id,
            'product_name' => $products['GIAY-DBA-A4-70']->name,
            'unit_name' => 'Thùng (5 Ram)',
            'quantity' => 2,
            'conversion_rate' => 5, // Trừ 10 Ram vào kho
            'cost_price' => 290000,
            'unit_price' => 335000,
            'subtotal' => 670000,
        ]);

        OrderItem::create([
            'order_id' => $order2->id,
            'product_id' => $products['DC-KIMBAM-PLUS10']->id,
            'product_name' => $products['DC-KIMBAM-PLUS10']->name,
            'unit_name' => 'Hộp',
            'quantity' => 15,
            'conversion_rate' => 1,
            'cost_price' => 3000,
            'unit_price' => 5000,
            'subtotal' => 75000,
        ]);

        // 8. Seed Payment Transaction for SePay/Casso Log
        PaymentTransaction::create([
            'gateway' => 'sepay',
            'transaction_id' => 'MB_20261007_981245',
            'reference_code' => 'HD260002',
            'order_id' => $order2->id,
            'amount' => 745000,
            'account_number' => '190333888999',
            'bank_brand_name' => 'Techcombank',
            'description' => 'CT DEN NGUYEN VAN A HD260002',
            'transaction_time' => now()->subMinutes(15),
            'raw_payload' => [
                'gateway' => 'sepay',
                'id' => 981245,
                'transactionDate' => now()->toIso8601String(),
                'transferAmount' => 745000,
                'content' => 'CT DEN NGUYEN VAN A HD260002',
                'referenceCode' => 'MB_20261007_981245',
            ],
            'status' => 'processed',
        ]);
    }
}
