<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;
use App\Models\Region;

class CountriesRegionsSeeder extends Seeder
{
    /**
     * Seed countries và regions cho forum mechanical engineering
     */
    public function run(): void
    {
        // ====================================================================
        // VIỆT NAM - Trọng tâm chính
        // ====================================================================
        $vietnam = Country::create([
            'name' => 'Việt Nam',
            'name_local' => 'Việt Nam',
            'code' => 'VN',
            'code_alpha3' => 'VNM',
            'phone_code' => '+84',
            'currency_code' => 'VND',
            'currency_symbol' => '₫',
            'continent' => 'Asia',
            'timezone' => 'Asia/Ho_Chi_Minh',
            'timezones' => ['Asia/Ho_Chi_Minh'],
            'language_code' => 'vi',
            'languages' => ['vi', 'en'],
            'measurement_system' => 'metric',
            'standard_organizations' => ['TCVN', 'ISO', 'JIS'],
            'common_cad_software' => ['AutoCAD', 'SolidWorks', 'Inventor', 'CATIA'],
            'flag_emoji' => '🇻🇳',
            'sort_order' => 1,
            'is_active' => true,
            'allow_user_registration' => true,
            'mechanical_specialties' => [
                'Automotive Engineering',
                'Manufacturing Technology',
                'Industrial Automation',
                'Precision Machining',
                'Mold & Die Design'
            ],
            'industrial_sectors' => [
                'Automotive',
                'Electronics Manufacturing',
                'Textile & Garment',
                'Steel & Metallurgy',
                'Shipbuilding',
                'Food Processing'
            ]
        ]);

        // Regions for Vietnam
        $vietnamRegions = [
            [
                'name' => 'TP. Hồ Chí Minh',
                'name_local' => 'TP. Hồ Chí Minh',
                'code' => 'HCM',
                'type' => 'city',
                'latitude' => 10.8231,
                'longitude' => 106.6297,
                'industrial_zones' => [
                    'Khu công nghiệp Tân Bình',
                    'Khu công nghiệp Vĩnh Lộc',
                    'Khu công nghiệp Lê Minh Xuân',
                    'Khu công nghiệp Hiệp Phước'
                ],
                'universities' => [
                    'Đại học Bách khoa TP.HCM',
                    'Đại học Công nghiệp TP.HCM',
                    'Đại học Sư phạm Kỹ thuật TP.HCM'
                ],
                'major_companies' => [
                    'Thaco Auto',
                    'Vinfast',
                    'Saigon Precision',
                    'Nam Long Group'
                ],
                'specialization_areas' => [
                    'Automotive Manufacturing',
                    'Electronics Assembly',
                    'Precision Machining',
                    'Mold Design'
                ],
                'is_featured' => true,
                'sort_order' => 1
            ],
            [
                'name' => 'Hà Nội',
                'name_local' => 'Hà Nội',
                'code' => 'HN',
                'type' => 'city',
                'latitude' => 21.0285,
                'longitude' => 105.8542,
                'industrial_zones' => [
                    'Khu công nghiệp Thăng Long',
                    'Khu công nghiệp Đại An',
                    'Khu công nghiệp Quang Minh'
                ],
                'universities' => [
                    'Đại học Bách khoa Hà Nội',
                    'Đại học Công nghiệp Hà Nội',
                    'Học viện Kỹ thuật Quân sự'
                ],
                'major_companies' => [
                    'Toyota Motor Vietnam',
                    'Honda Vietnam',
                    'Samsung Electronics Vietnam'
                ],
                'specialization_areas' => [
                    'Government Standards',
                    'Research & Development',
                    'Heavy Machinery',
                    'Aerospace Components'
                ],
                'is_featured' => true,
                'sort_order' => 2
            ],
            [
                'name' => 'Đà Nẵng',
                'name_local' => 'Đà Nẵng',
                'code' => 'DN',
                'type' => 'city',
                'latitude' => 16.0544,
                'longitude' => 108.2022,
                'industrial_zones' => [
                    'Khu công nghiệp Đà Nẵng',
                    'Khu công nghiệp Liên Chiểu'
                ],
                'universities' => [
                    'Đại học Bách khoa Đà Nẵng',
                    'Đại học Sư phạm Kỹ thuật Đà Nẵng'
                ],
                'specialization_areas' => [
                    'Marine Engineering',
                    'Software for Manufacturing',
                    'Tourism Machinery'
                ],
                'sort_order' => 3
            ]
        ];

        foreach ($vietnamRegions as $regionData) {
            Region::create(array_merge($regionData, ['country_id' => $vietnam->id]));
        }

        // ====================================================================
        // HOA KỲ - Thị trường lớn
        // ====================================================================
        $usa = Country::create([
            'name' => 'United States',
            'name_local' => 'United States of America',
            'code' => 'US',
            'code_alpha3' => 'USA',
            'phone_code' => '+1',
            'currency_code' => 'USD',
            'currency_symbol' => '$',
            'continent' => 'North America',
            'timezone' => 'America/New_York',
            'timezones' => [
                'America/New_York',
                'America/Chicago',
                'America/Denver',
                'America/Los_Angeles'
            ],
            'language_code' => 'en',
            'languages' => ['en', 'es'],
            'measurement_system' => 'imperial',
            'standard_organizations' => ['ANSI', 'ASME', 'SAE', 'AWS'],
            'common_cad_software' => ['SolidWorks', 'Inventor', 'CATIA', 'NX', 'Fusion 360'],
            'flag_emoji' => '🇺🇸',
            'sort_order' => 2,
            'is_active' => true,
            'allow_user_registration' => true,
            'mechanical_specialties' => [
                'Aerospace Engineering',
                'Automotive Innovation',
                'Advanced Manufacturing',
                'Robotics & Automation',
                'Energy Systems'
            ],
            'industrial_sectors' => [
                'Aerospace & Defense',
                'Automotive',
                'Energy & Utilities',
                'Medical Devices',
                'Consumer Electronics'
            ]
        ]);

        // US Regions
        $usRegions = [
            [
                'name' => 'California',
                'code' => 'CA',
                'type' => 'state',
                'latitude' => 36.7783,
                'longitude' => -119.4179,
                'specialization_areas' => [
                    'Aerospace Technology',
                    'Electric Vehicles',
                    'Semiconductor Manufacturing'
                ],
                'is_featured' => true,
                'sort_order' => 1
            ],
            [
                'name' => 'Michigan',
                'code' => 'MI',
                'type' => 'state',
                'latitude' => 44.3148,
                'longitude' => -85.6024,
                'specialization_areas' => [
                    'Automotive Engineering',
                    'Manufacturing Automation',
                    'Tool & Die Making'
                ],
                'is_featured' => true,
                'sort_order' => 2
            ]
        ];

        foreach ($usRegions as $regionData) {
            Region::create(array_merge($regionData, ['country_id' => $usa->id]));
        }

        // ====================================================================
        // NHẬT BẢN - Công nghệ cao
        // ====================================================================
        $japan = Country::create([
            'name' => 'Japan',
            'name_local' => '日本国',
            'code' => 'JP',
            'code_alpha3' => 'JPN',
            'phone_code' => '+81',
            'currency_code' => 'JPY',
            'currency_symbol' => '¥',
            'continent' => 'Asia',
            'timezone' => 'Asia/Tokyo',
            'timezones' => ['Asia/Tokyo'],
            'language_code' => 'ja',
            'languages' => ['ja', 'en'],
            'measurement_system' => 'metric',
            'standard_organizations' => ['JIS', 'JSA', 'ISO'],
            'common_cad_software' => ['SolidWorks', 'CATIA', 'NX', 'Inventor'],
            'flag_emoji' => '🇯🇵',
            'sort_order' => 3,
            'is_active' => true,
            'allow_user_registration' => true,
            'mechanical_specialties' => [
                'Precision Manufacturing',
                'Robotics & Automation',
                'Automotive Technology',
                'Machine Tools',
                'Electronics Manufacturing'
            ],
            'industrial_sectors' => [
                'Automotive',
                'Electronics',
                'Machinery',
                'Shipbuilding',
                'Precision Instruments'
            ]
        ]);

        // Japan Regions
        Region::create([
            'country_id' => $japan->id,
            'name' => 'Tokyo',
            'name_local' => '東京都',
            'code' => 'TK',
            'type' => 'prefecture',
            'latitude' => 35.6762,
            'longitude' => 139.6503,
            'specialization_areas' => [
                'Advanced Robotics',
                'Precision Instruments',
                'R&D Centers'
            ],
            'is_featured' => true,
            'sort_order' => 1
        ]);

        // ====================================================================
        // ĐỨC - Kỹ thuật cơ khí hàng đầu
        // ====================================================================
        $germany = Country::create([
            'name' => 'Germany',
            'name_local' => 'Deutschland',
            'code' => 'DE',
            'code_alpha3' => 'DEU',
            'phone_code' => '+49',
            'currency_code' => 'EUR',
            'currency_symbol' => '€',
            'continent' => 'Europe',
            'timezone' => 'Europe/Berlin',
            'timezones' => ['Europe/Berlin'],
            'language_code' => 'de',
            'languages' => ['de', 'en'],
            'measurement_system' => 'metric',
            'standard_organizations' => ['DIN', 'VDI', 'ISO'],
            'common_cad_software' => ['CATIA', 'NX', 'SolidWorks', 'Inventor'],
            'flag_emoji' => '🇩🇪',
            'sort_order' => 4,
            'is_active' => true,
            'allow_user_registration' => true,
            'mechanical_specialties' => [
                'Machine Tool Manufacturing',
                'Automotive Engineering',
                'Industrial Automation',
                'Precision Engineering',
                'Renewable Energy'
            ],
            'industrial_sectors' => [
                'Automotive',
                'Machine Tools',
                'Chemical Engineering',
                'Renewable Energy',
                'Aerospace'
            ]
        ]);

        // ====================================================================
        // TRUNG QUỐC - Sản xuất lớn
        // ====================================================================
        $china = Country::create([
            'name' => 'China',
            'name_local' => '中华人民共和国',
            'code' => 'CN',
            'code_alpha3' => 'CHN',
            'phone_code' => '+86',
            'currency_code' => 'CNY',
            'currency_symbol' => '¥',
            'continent' => 'Asia',
            'timezone' => 'Asia/Shanghai',
            'timezones' => ['Asia/Shanghai'],
            'language_code' => 'zh',
            'languages' => ['zh', 'en'],
            'measurement_system' => 'metric',
            'standard_organizations' => ['GB', 'ISO', 'JIS'],
            'common_cad_software' => ['SolidWorks', 'AutoCAD', 'CATIA', 'ZWCAD'],
            'flag_emoji' => '🇨🇳',
            'sort_order' => 5,
            'is_active' => true,
            'allow_user_registration' => true,
            'mechanical_specialties' => [
                'Mass Manufacturing',
                'Electronics Assembly',
                'Heavy Machinery',
                'Infrastructure Engineering'
            ],
            'industrial_sectors' => [
                'Electronics Manufacturing',
                'Automotive',
                'Heavy Industry',
                'Infrastructure',
                'Renewable Energy'
            ]
        ]);

        // ====================================================================
        // HÀN QUỐC - Công nghệ điện tử
        // ====================================================================
        $korea = Country::create([
            'name' => 'South Korea',
            'name_local' => '대한민국',
            'code' => 'KR',
            'code_alpha3' => 'KOR',
            'phone_code' => '+82',
            'currency_code' => 'KRW',
            'currency_symbol' => '₩',
            'continent' => 'Asia',
            'timezone' => 'Asia/Seoul',
            'timezones' => ['Asia/Seoul'],
            'language_code' => 'ko',
            'languages' => ['ko', 'en'],
            'measurement_system' => 'metric',
            'standard_organizations' => ['KS', 'ISO', 'JIS'],
            'common_cad_software' => ['SolidWorks', 'CATIA', 'NX'],
            'flag_emoji' => '🇰🇷',
            'sort_order' => 6,
            'is_active' => true,
            'allow_user_registration' => true,
            'mechanical_specialties' => [
                'Electronics Manufacturing',
                'Shipbuilding',
                'Automotive Technology',
                'Semiconductor Equipment'
            ],
            'industrial_sectors' => [
                'Electronics',
                'Automotive',
                'Shipbuilding',
                'Steel',
                'Petrochemicals'
            ]
        ]);

        // ====================================================================
        // CẬP NHẬT STATISTICS
        // ====================================================================
        $this->updateRegionStatistics();
    }

    /**
     * Cập nhật thống kê cho các region
     */
    private function updateRegionStatistics(): void
    {
        // Placeholder - sẽ được cập nhật bởi real data
        Region::query()->update([
            'forum_count' => 0,
            'user_count' => 0,
            'thread_count' => 0
        ]);
    }
}
