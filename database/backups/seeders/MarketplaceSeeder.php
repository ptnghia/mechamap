<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductCategory;
use App\Models\TechnicalProduct;
use App\Models\ProtectedFile;
use App\Models\User;
use App\Services\FileEncryptionService;
use Illuminate\Support\Facades\Storage;

class MarketplaceSeeder extends Seeder
{
    private FileEncryptionService $encryptionService;

    public function __construct(FileEncryptionService $encryptionService)
    {
        $this->encryptionService = $encryptionService;
    }

    /**
     * Seed marketplace với dữ liệu thực tế cho kỹ thuật cơ khí
     */
    public function run(): void
    {
        $this->command->info('🛒 Bắt đầu seed marketplace data...');

        // Tạo categories cơ khí
        $this->createProductCategories();

        // Tạo technical products
        $this->createTechnicalProducts();

        $this->command->info('✅ Hoàn thành seed marketplace data!');
    }

    private function createProductCategories(): void
    {
        $this->command->info('📂 Tạo product categories...');

        $categories = [
            [
                'name' => 'Bản vẽ Thiết kế Cơ khí',
                'slug' => 'ban-ve-thiet-ke-co-khi',
                'description' => 'Bản vẽ 2D/3D các chi tiết cơ khí, cụm máy, thiết bị công nghiệp',
                'icon' => 'https://api.iconify.design/material-symbols:engineering.svg',
                'is_active' => true,
                'sort_order' => 1,
                'children' => [
                    [
                        'name' => 'Bản vẽ AutoCAD',
                        'slug' => 'ban-ve-autocad',
                        'description' => 'File .dwg, .dxf cho AutoCAD với dimension và tolerances đầy đủ',
                    ],
                    [
                        'name' => 'Model SolidWorks',
                        'slug' => 'model-solidworks',
                        'description' => 'File .sldprt, .sldasm với feature tree và material properties',
                    ],
                    [
                        'name' => 'Model Inventor',
                        'slug' => 'model-inventor',
                        'description' => 'File .ipt, .iam của Autodesk Inventor với constraints',
                    ],
                    [
                        'name' => 'STEP/IGES Files',
                        'slug' => 'step-iges-files',
                        'description' => 'Universal format .step, .iges cho cross-platform compatibility',
                    ],
                ]
            ],
            [
                'name' => 'Tài liệu Kỹ thuật',
                'slug' => 'tai-lieu-ky-thuat',
                'description' => 'Tài liệu, sách, báo cáo kỹ thuật chuyên ngành cơ khí',
                'icon' => 'https://api.iconify.design/material-symbols:library-books.svg',
                'is_active' => true,
                'sort_order' => 2,
                'children' => [
                    [
                        'name' => 'Handbook & Standards',
                        'slug' => 'handbook-standards',
                        'description' => 'Sổ tay kỹ thuật, tiêu chuẩn JIS, ANSI, ISO',
                    ],
                    [
                        'name' => 'Báo cáo Phân tích',
                        'slug' => 'bao-cao-phan-tich',
                        'description' => 'FEA reports, CFD analysis, stress analysis documents',
                    ],
                    [
                        'name' => 'Quy trình Sản xuất',
                        'slug' => 'quy-trinh-san-xuat',
                        'description' => 'Process sheets, work instructions, quality procedures',
                    ],
                    [
                        'name' => 'Catalogs Thiết bị',
                        'slug' => 'catalogs-thiet-bi',
                        'description' => 'Technical catalogs, equipment specifications',
                    ],
                ]
            ],
            [
                'name' => 'Chương trình CNC',
                'slug' => 'chuong-trinh-cnc',
                'description' => 'G-code, macro programs cho máy CNC và automation',
                'icon' => 'https://api.iconify.design/material-symbols:precision-manufacturing.svg',
                'is_active' => true,
                'sort_order' => 3,
                'children' => [
                    [
                        'name' => 'G-Code Programs',
                        'slug' => 'g-code-programs',
                        'description' => 'Ready-to-use G-code cho milling, turning, drilling',
                    ],
                    [
                        'name' => 'CAM Templates',
                        'slug' => 'cam-templates',
                        'description' => 'MasterCAM, EdgeCAM, PowerMill templates và post-processors',
                    ],
                    [
                        'name' => 'PLC Programs',
                        'slug' => 'plc-programs',
                        'description' => 'Ladder logic, function blocks cho automation systems',
                    ],
                ]
            ],
            [
                'name' => 'Simulink & Matlab',
                'slug' => 'simulink-matlab',
                'description' => 'Models mô phỏng hệ thống cơ khí, control systems',
                'icon' => 'https://api.iconify.design/material-symbols:calculate.svg',
                'is_active' => true,
                'sort_order' => 4,
                'children' => [
                    [
                        'name' => 'Simulink Models',
                        'slug' => 'simulink-models',
                        'description' => 'Dynamic system models, control system simulation',
                    ],
                    [
                        'name' => 'Matlab Scripts',
                        'slug' => 'matlab-scripts',
                        'description' => 'Calculation scripts cho vibration, heat transfer, stress analysis',
                    ],
                ]
            ],
            [
                'name' => 'Templates & Forms',
                'slug' => 'templates-forms',
                'description' => 'Mẫu biểu, forms cho quản lý dự án kỹ thuật',
                'icon' => 'https://api.iconify.design/material-symbols:assignment.svg',
                'is_active' => true,
                'sort_order' => 5,
                'children' => [
                    [
                        'name' => 'Excel Templates',
                        'slug' => 'excel-templates',
                        'description' => 'Calculation sheets, project tracking, cost estimation',
                    ],
                    [
                        'name' => 'Word Templates',
                        'slug' => 'word-templates',
                        'description' => 'Report templates, specification documents',
                    ],
                ]
            ],
        ];

        foreach ($categories as $categoryData) {
            $children = $categoryData['children'] ?? [];
            unset($categoryData['children']);

            $parent = ProductCategory::firstOrCreate(
                ['slug' => $categoryData['slug']], // Check by slug
                $categoryData // Create with all data if not exists
            );

            foreach ($children as $childData) {
                ProductCategory::firstOrCreate(
                    ['slug' => $childData['slug']], // Check by slug
                    [
                        ...$childData,
                        'parent_id' => $parent->id,
                        'icon' => 'https://api.iconify.design/material-symbols:folder.svg',
                        'is_active' => true,
                        'sort_order' => 1,
                    ]
                );
            }
        }
    }

    private function createTechnicalProducts(): void
    {
        $this->command->info('🔧 Tạo technical products...');

        // Lấy users và categories để tạo products
        $sellers = User::where('role', 'seller')->get();
        if ($sellers->isEmpty()) {
            $sellers = User::factory(5)->create(['role' => 'seller']);
        }

        $categories = ProductCategory::whereNotNull('parent_id')->get();

        $products = [
            [
                'title' => 'Bộ bản vẽ Gearbox 6 cấp cho ô tô',
                'description' => 'Bộ bản vẽ hoàn chỉnh hộp số 6 cấp tiến cho xe du lịch. Bao gồm:
- Assembly drawing với exploded view
- Part drawings của tất cả gears, shafts, housing
- Material specifications (9CrSi, 20CrMnTi)
- Tolerance analysis và kiểm tra can thiệp
- File SolidWorks 2022 (.sldprt, .sldasm)
- Drawings PDF với dimensions đầy đủ
- Technical specifications 180 trang

Phù hợp cho: Sinh viên cuối khóa, kỹ sư automotive, nghiên cứu gearbox design.',
                'category_name' => 'Model SolidWorks',
                'sale_price' => 250000,
                'original_price' => 350000,
                'files' => [
                    'gearbox_assembly.sldasm',
                    'gear_calculations.xlsx',
                    'technical_specs.pdf',
                    'material_list.pdf',
                    'tolerance_analysis.pdf'
                ],
                'tags' => ['gearbox', 'automotive', 'solidworks', 'transmission', 'mechanical-design'],
                'is_featured' => true,
                'technical_specs' => [
                    'CAD Software' => 'SolidWorks 2022',
                    'File Formats' => '.sldprt, .sldasm, .pdf, .xlsx',
                    'Number of Parts' => '127 components',
                    'Gear Ratios' => '1st: 3.727, 2nd: 2.048, 3rd: 1.321, 4th: 1.000, 5th: 0.831, 6th: 0.674',
                    'Material Standards' => 'JIS G 4105 (SCM415), JIS G 4106 (SNCM420)',
                    'Torque Capacity' => '280 Nm maximum input torque',
                ]
            ],
            [
                'title' => 'CNC G-Code cho gia công Block Engine',
                'description' => 'Chương trình CNC hoàn chỉnh gia công cylinder block 4 xilanh:
- Rough machining operations (60% MRR)
- Semi-finish và finish programs
- Drilling programs cho oil holes
- Boring programs cho cylinder bores
- Tool path optimization
- Estimated cycle time: 4.2 hours
- Tested trên Mazak VTC-200B

Include: Setup sheets, tool lists, workholding fixtures.',
                'category_name' => 'G-Code Programs',
                'sale_price' => 180000,
                'original_price' => 250000,
                'files' => [
                    'block_rough.nc',
                    'block_finish.nc',
                    'drilling_program.nc',
                    'setup_sheet.pdf',
                    'tool_list.xlsx'
                ],
                'tags' => ['cnc', 'g-code', 'engine-block', 'mazak', 'machining'],
                'technical_specs' => [
                    'Machine Type' => 'Vertical Machining Center',
                    'Control System' => 'Mazak Matrix Nexus',
                    'Material' => 'Cast Iron FC250',
                    'Workpiece Size' => '520 x 380 x 280mm',
                    'Tools Required' => '18 tools (end mills, drills, reamers)',
                    'Cycle Time' => '4 hours 12 minutes',
                ]
            ],
            [
                'title' => 'Finite Element Analysis - Crankshaft Stress',
                'description' => 'Báo cáo phân tích FEA đầy đủ cho trục khuỷu động cơ 4 xilanh:
- ANSYS Workbench project files
- Static structural analysis với tải trọng max torque
- Modal analysis (natural frequencies)
- Fatigue life prediction (EN 1993-1-9)
- Von Mises stress distribution
- Safety factor calculations
- Optimization recommendations

Professional report 85 trang với validation tests.',
                'category_name' => 'Báo cáo Phân tích',
                'sale_price' => 320000,
                'original_price' => 420000,
                'files' => [
                    'crankshaft_analysis.wbpj',
                    'stress_report.pdf',
                    'modal_results.xlsx',
                    'fatigue_calculation.xlsx',
                    'optimization_guide.pdf'
                ],
                'tags' => ['fea', 'ansys', 'crankshaft', 'stress-analysis', 'fatigue'],
                'is_featured' => true,
                'technical_specs' => [
                    'FEA Software' => 'ANSYS Workbench 2023 R1',
                    'Element Type' => 'SOLID186 (3D 20-node)',
                    'Mesh Quality' => '847,392 elements, aspect ratio < 3:1',
                    'Loading Conditions' => 'Max torque 180 Nm @ 3000 RPM',
                    'Material Model' => 'AISI 4140 Steel (σy = 415 MPa)',
                    'Safety Factor' => 'Min 2.1 (infinit life design)',
                ]
            ],
            [
                'title' => 'AutoCAD Drawings - Hydraulic Press 100T',
                'description' => 'Bộ bản vẽ kỹ thuật máy ép thuỷ lực 100 tấn:
- General assembly drawing 1:10
- Detail drawings của tất cả components
- Hydraulic circuit diagram
- Electrical control panel layout
- Foundation plan và installation guide
- Bill of materials với part numbers
- Manufacturing tolerances theo ISO 2768-m

Total 47 sheets, ready for manufacturing.',
                'category_name' => 'Bản vẽ AutoCAD',
                'sale_price' => 280000,
                'original_price' => 380000,
                'files' => [
                    'hydraulic_press_assembly.dwg',
                    'details_sheets_1-20.dwg',
                    'details_sheets_21-40.dwg',
                    'hydraulic_circuit.dwg',
                    'electrical_panel.dwg',
                    'foundation_plan.dwg',
                    'bom_materials.xlsx'
                ],
                'tags' => ['autocad', 'hydraulic-press', 'manufacturing', 'industrial', 'drawings'],
                'technical_specs' => [
                    'CAD Version' => 'AutoCAD 2023',
                    'Drawing Standard' => 'ISO 128 (Technical drawings)',
                    'Tolerance Standard' => 'ISO 2768-m (General tolerances)',
                    'Press Capacity' => '100 tonnes (981 kN)',
                    'Working Stroke' => '300mm maximum',
                    'Daylight Opening' => '800mm',
                    'Bed Size' => '1200 x 800mm',
                ]
            ],
            [
                'title' => 'Simulink Model - DC Motor Speed Control',
                'description' => 'Mô hình Simulink điều khiển tốc độ motor DC:
- PID controller với auto-tuning
- PWM drive circuit simulation
- Load disturbance rejection
- Step response analysis
- Bode plots và stability margins
- Real-time implementation guide
- Hardware-in-the-loop setup

Tested với Arduino và encoder feedback.',
                'category_name' => 'Simulink Models',
                'sale_price' => 120000,
                'original_price' => 160000,
                'files' => [
                    'dc_motor_control.slx',
                    'pid_tuning_script.m',
                    'arduino_code.ino',
                    'hardware_setup.pdf',
                    'test_results.mat'
                ],
                'tags' => ['simulink', 'matlab', 'motor-control', 'pid', 'arduino'],
                'technical_specs' => [
                    'MATLAB Version' => 'R2023a or later',
                    'Required Toolboxes' => 'Control System, Simulink Control Design',
                    'Motor Specifications' => '24V DC, 2000 RPM, 0.5 Nm',
                    'Controller Type' => 'PID with anti-windup',
                    'Sample Time' => '1ms (1000 Hz)',
                    'Hardware Interface' => 'Arduino Uno via Serial',
                ]
            ],
            [
                'title' => 'Excel Calculator - Bearing Life Analysis',
                'description' => 'Excel template tính toán tuổi thọ vòng bi:
- ISO 281:2007 standard calculations
- Dynamic load rating C và C0
- Contamination factor và lubrication factor
- Temperature correction factors
- L10, L50 life calculations
- Weibull reliability analysis
- Multiple bearing types (ball, roller, tapered)

Professional tool với user-friendly interface.',
                'category_name' => 'Excel Templates',
                'sale_price' => 85000,
                'original_price' => 120000,
                'files' => [
                    'bearing_life_calculator.xlsx',
                    'bearing_database.xlsx',
                    'user_manual.pdf',
                    'validation_examples.pdf'
                ],
                'tags' => ['excel', 'bearing', 'life-analysis', 'iso-281', 'reliability'],
                'technical_specs' => [
                    'Excel Version' => '2016 or later',
                    'Calculation Standard' => 'ISO 281:2007',
                    'Bearing Types' => 'Deep groove, angular contact, tapered roller',
                    'Load Cases' => 'Constant, variable, shock loads',
                    'Reliability Levels' => '90%, 95%, 99% survival probability',
                    'Temperature Range' => '-40°C to +200°C',
                ]
            ],
        ];        foreach ($products as $productData) {
            $category = $categories->firstWhere('name', $productData['category_name']);
            if (!$category) continue;

            $seller = $sellers->random();

            // Check if product already exists
            $existingProduct = TechnicalProduct::where('title', $productData['title'])->first();
            if ($existingProduct) {
                $product = $existingProduct;
            } else {
                $product = TechnicalProduct::create([
                    'title' => $productData['title'],
                    'description' => $productData['description'],
                    'category_id' => $category->id,
                    'seller_id' => $seller->id,
                    'price' => $productData['original_price'], // Use price instead of original_price
                    'discount_percentage' => (($productData['original_price'] - $productData['sale_price']) / $productData['original_price']) * 100,
                    'is_featured' => $productData['is_featured'] ?? false,
                    'status' => 'approved', // Use approved instead of published
                    'tags' => $productData['tags'],
                    'software_compatibility' => $productData['technical_specs'],
                    'sales_count' => rand(5, 50),
                    'view_count' => rand(100, 1000),
                    'rating_average' => rand(40, 50) / 10, // 4.0 - 5.0
                    'rating_count' => rand(3, 25),
                ]);
            }

            // Tạo protected files cho product
            $this->createProtectedFiles($product, $productData['files']);
        }
    }    private function createProtectedFiles(TechnicalProduct $product, array $fileNames): void
    {
        foreach ($fileNames as $fileName) {
            // Tạo sample file content
            $sampleContent = $this->generateSampleFileContent($fileName);

            // Tạo file path
            $filePath = "protected/{$product->id}/{$fileName}";

            // Lưu file vào storage
            Storage::put($filePath, $sampleContent);

            // For seeding, just simulate encryption
            $encryptedPath = "encrypted/" . $filePath;
            Storage::put($encryptedPath, "encrypted_" . $sampleContent);

            ProtectedFile::create([
                'product_id' => $product->id,
                'original_filename' => $fileName,
                'encrypted_filename' => basename($encryptedPath),
                'file_path' => $encryptedPath,
                'file_size' => strlen($sampleContent),
                'file_hash' => md5($sampleContent), // Add file hash
                'mime_type' => $this->getMimeType($fileName),
                'encryption_key' => 'sample_key_' . $product->id,
                'access_level' => 'full_access', // Use full_access instead of purchased
                'download_count' => 0,
            ]);
        }
    }

    private function generateSampleFileContent(string $fileName): string
    {
        $extension = pathinfo($fileName, PATHINFO_EXTENSION);

        return match ($extension) {
            'pdf' => "Sample PDF content for {$fileName} - Technical document with engineering data",
            'dwg', 'sldprt', 'sldasm' => "Binary CAD file content for {$fileName} - Engineering drawing data",
            'xlsx' => "Sample Excel calculations for {$fileName} - Engineering calculations and data",
            'nc' => "Sample G-code program for {$fileName}\nG01 X10 Y20 Z5 F500\nM03 S1200\nG00 Z25",
            'slx' => "Simulink model data for {$fileName} - Control system simulation",
            'm' => "% MATLAB script for {$fileName}\nfunction result = calculation(input)\nresult = input * 2;\nend",
            'ino' => "// Arduino code for {$fileName}\nvoid setup() {\n  Serial.begin(9600);\n}\nvoid loop() {\n}",
            default => "Sample technical file content for {$fileName}"
        };
    }

    private function getMimeType(string $fileName): string
    {
        $extension = pathinfo($fileName, PATHINFO_EXTENSION);

        return match ($extension) {
            'pdf' => 'application/pdf',
            'dwg' => 'application/acad',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'sldprt', 'sldasm' => 'application/solidworks',
            'nc' => 'application/x-gcode',
            'slx' => 'application/simulink',
            'm' => 'text/x-matlab',
            'ino' => 'text/x-arduino',
            default => 'application/octet-stream'
        };
    }
}
