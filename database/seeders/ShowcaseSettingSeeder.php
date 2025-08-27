<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ShowcaseSetting;

class ShowcaseSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // Industries (Ngành công nghiệp ứng dụng)
            [
                'key' => 'industries',
                'name' => 'Ngành công nghiệp',
                'description' => 'Lĩnh vực ứng dụng của dự án',
                'options' => [
                    [
                        'value' => 'aerospace',
                        'translations' => [
                            'vi' => 'Hàng không vũ trụ',
                            'en' => 'Aerospace'
                        ],
                        'icon' => 'fas fa-plane'
                    ],
                    [
                        'value' => 'automotive',
                        'translations' => [
                            'vi' => 'Ô tô',
                            'en' => 'Automotive'
                        ],
                        'icon' => 'fas fa-car'
                    ],
                    [
                        'value' => 'construction',
                        'translations' => [
                            'vi' => 'Xây dựng',
                            'en' => 'Construction'
                        ],
                        'icon' => 'fas fa-building'
                    ],
                    [
                        'value' => 'energy',
                        'translations' => [
                            'vi' => 'Năng lượng',
                            'en' => 'Energy'
                        ],
                        'icon' => 'fas fa-bolt'
                    ],
                    [
                        'value' => 'manufacturing',
                        'translations' => [
                            'vi' => 'Sản xuất',
                            'en' => 'Manufacturing'
                        ],
                        'icon' => 'fas fa-industry'
                    ],
                    [
                        'value' => 'medical',
                        'translations' => [
                            'vi' => 'Y tế',
                            'en' => 'Medical'
                        ],
                        'icon' => 'fas fa-heartbeat'
                    ],
                    [
                        'value' => 'electronics',
                        'translations' => [
                            'vi' => 'Điện tử',
                            'en' => 'Electronics'
                        ],
                        'icon' => 'fas fa-microchip'
                    ],
                    [
                        'value' => 'marine',
                        'translations' => [
                            'vi' => 'Hàng hải',
                            'en' => 'Marine'
                        ],
                        'icon' => 'fas fa-ship'
                    ]
                ],
                'input_type' => 'multiselect',
                'is_multiple' => true,
                'is_required' => false,
                'is_searchable' => true,
                'sort_order' => 1,
                'group' => 'classification',
                'icon' => 'fas fa-industry'
            ],

            // Software Used
            [
                'key' => 'software_options',
                'name' => 'Phần mềm sử dụng',
                'description' => 'Các phần mềm CAD/CAE/CAM được sử dụng trong dự án',
                'options' => [
                    [
                        'value' => 'solidworks',
                        'translations' => [
                            'vi' => 'SolidWorks',
                            'en' => 'SolidWorks'
                        ],
                        'icon' => 'fab fa-solidworks'
                    ],
                    [
                        'value' => 'autocad',
                        'translations' => [
                            'vi' => 'AutoCAD',
                            'en' => 'AutoCAD'
                        ],
                        'icon' => 'fas fa-drafting-compass'
                    ],
                    [
                        'value' => 'inventor',
                        'translations' => [
                            'vi' => 'Inventor',
                            'en' => 'Inventor'
                        ],
                        'icon' => 'fas fa-cube'
                    ],
                    [
                        'value' => 'fusion360',
                        'translations' => [
                            'vi' => 'Fusion 360',
                            'en' => 'Fusion 360'
                        ],
                        'icon' => 'fas fa-atom'
                    ],
                    [
                        'value' => 'catia',
                        'translations' => [
                            'vi' => 'CATIA',
                            'en' => 'CATIA'
                        ],
                        'icon' => 'fas fa-cube'
                    ],
                    [
                        'value' => 'nx',
                        'translations' => [
                            'vi' => 'NX',
                            'en' => 'NX'
                        ],
                        'icon' => 'fas fa-cube'
                    ],
                    [
                        'value' => 'creo',
                        'translations' => [
                            'vi' => 'Creo',
                            'en' => 'Creo'
                        ],
                        'icon' => 'fas fa-cube'
                    ],
                    [
                        'value' => 'ansys',
                        'translations' => [
                            'vi' => 'ANSYS',
                            'en' => 'ANSYS'
                        ],
                        'icon' => 'fas fa-calculator'
                    ],
                    [
                        'value' => 'matlab',
                        'translations' => [
                            'vi' => 'MATLAB',
                            'en' => 'MATLAB'
                        ],
                        'icon' => 'fas fa-chart-line'
                    ],
                    [
                        'value' => 'other',
                        'translations' => [
                            'vi' => 'Khác',
                            'en' => 'Other'
                        ],
                        'icon' => 'fas fa-ellipsis-h'
                    ]
                ],
                'input_type' => 'multiselect',
                'is_multiple' => true,
                'is_required' => false,
                'is_searchable' => true,
                'sort_order' => 2,
                'group' => 'technical',
                'icon' => 'fas fa-laptop-code'
            ],

            // Complexity Levels
            [
                'key' => 'complexity_levels',
                'name' => 'Độ phức tạp',
                'description' => 'Mức độ phức tạp của dự án',
                'options' => [
                    [
                        'value' => 'beginner',
                        'translations' => [
                            'vi' => 'Cơ bản',
                            'en' => 'Beginner'
                        ],
                        'description' => 'Phù hợp cho người mới bắt đầu',
                        'icon' => 'fas fa-seedling'
                    ],
                    [
                        'value' => 'intermediate',
                        'translations' => [
                            'vi' => 'Trung bình',
                            'en' => 'Intermediate'
                        ],
                        'description' => 'Yêu cầu kiến thức cơ bản',
                        'icon' => 'fas fa-tree'
                    ],
                    [
                        'value' => 'advanced',
                        'translations' => [
                            'vi' => 'Nâng cao',
                            'en' => 'Advanced'
                        ],
                        'description' => 'Yêu cầu kinh nghiệm chuyên sâu',
                        'icon' => 'fas fa-mountain'
                    ],
                    [
                        'value' => 'expert',
                        'translations' => [
                            'vi' => 'Chuyên gia',
                            'en' => 'Expert'
                        ],
                        'description' => 'Dành cho chuyên gia',
                        'icon' => 'fas fa-crown'
                    ]
                ],
                'input_type' => 'select',
                'is_multiple' => false,
                'is_required' => false,
                'is_searchable' => true,
                'sort_order' => 3,
                'group' => 'classification',
                'icon' => 'fas fa-layer-group'
            ],

            // Materials Used
            [
                'key' => 'materials_used',
                'name' => 'Vật liệu sử dụng',
                'description' => 'Các loại vật liệu được sử dụng trong dự án',
                'options' => [
                    [
                        'value' => 'steel',
                        'translations' => [
                            'vi' => 'Thép',
                            'en' => 'Steel'
                        ],
                        'icon' => 'fas fa-hammer'
                    ],
                    [
                        'value' => 'aluminum',
                        'translations' => [
                            'vi' => 'Nhôm',
                            'en' => 'Aluminum'
                        ],
                        'icon' => 'fas fa-cube'
                    ],
                    [
                        'value' => 'plastic',
                        'translations' => [
                            'vi' => 'Nhựa',
                            'en' => 'Plastic'
                        ],
                        'icon' => 'fas fa-recycle'
                    ],
                    [
                        'value' => 'composite',
                        'translations' => [
                            'vi' => 'Composite',
                            'en' => 'Composite'
                        ],
                        'icon' => 'fas fa-layer-group'
                    ],
                    [
                        'value' => 'titanium',
                        'translations' => [
                            'vi' => 'Titan',
                            'en' => 'Titanium'
                        ],
                        'icon' => 'fas fa-gem'
                    ],
                    [
                        'value' => 'ceramic',
                        'translations' => [
                            'vi' => 'Gốm sứ',
                            'en' => 'Ceramic'
                        ],
                        'icon' => 'fas fa-fire'
                    ]
                ],
                'input_type' => 'multiselect',
                'is_multiple' => true,
                'is_required' => false,
                'is_searchable' => true,
                'sort_order' => 4,
                'group' => 'technical',
                'icon' => 'fas fa-cubes'
            ]
        ];

        foreach ($settings as $setting) {
            ShowcaseSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }

        $this->command->info('Showcase settings seeded successfully!');
    }
}
