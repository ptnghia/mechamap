<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\BusinessCategory;

class BusinessCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'key' => 'manufacturing',
                'name_vi' => 'Sản xuất & Chế tạo',
                'name_en' => 'Manufacturing & Production',
                'description_vi' => 'Sản xuất và chế tạo các sản phẩm cơ khí, thiết bị công nghiệp',
                'description_en' => 'Manufacturing and production of mechanical products, industrial equipment',
                'icon' => 'fas fa-industry',
                'color' => '#007bff',
                'sort_order' => 1,
            ],
            [
                'key' => 'automotive',
                'name_vi' => 'Ô tô & Xe máy',
                'name_en' => 'Automotive & Motorcycles',
                'description_vi' => 'Ngành công nghiệp ô tô, xe máy và phụ tùng',
                'description_en' => 'Automotive, motorcycle and parts industry',
                'icon' => 'fas fa-car',
                'color' => '#28a745',
                'sort_order' => 2,
            ],
            [
                'key' => 'aerospace',
                'name_vi' => 'Hàng không & Vũ trụ',
                'name_en' => 'Aerospace & Space',
                'description_vi' => 'Công nghiệp hàng không, vũ trụ và thiết bị bay',
                'description_en' => 'Aerospace, space and aviation equipment industry',
                'icon' => 'fas fa-plane',
                'color' => '#17a2b8',
                'sort_order' => 3,
            ],
            [
                'key' => 'energy',
                'name_vi' => 'Năng lượng & Điện lực',
                'name_en' => 'Energy & Power',
                'description_vi' => 'Năng lượng tái tạo, điện lực và thiết bị năng lượng',
                'description_en' => 'Renewable energy, power and energy equipment',
                'icon' => 'fas fa-bolt',
                'color' => '#ffc107',
                'sort_order' => 4,
            ],
            [
                'key' => 'construction',
                'name_vi' => 'Xây dựng & Hạ tầng',
                'name_en' => 'Construction & Infrastructure',
                'description_vi' => 'Xây dựng, hạ tầng và thiết bị xây dựng',
                'description_en' => 'Construction, infrastructure and building equipment',
                'icon' => 'fas fa-building',
                'color' => '#fd7e14',
                'sort_order' => 5,
            ],
            [
                'key' => 'electronics',
                'name_vi' => 'Điện tử & Viễn thông',
                'name_en' => 'Electronics & Telecommunications',
                'description_vi' => 'Thiết bị điện tử, viễn thông và công nghệ thông tin',
                'description_en' => 'Electronic devices, telecommunications and IT technology',
                'icon' => 'fas fa-microchip',
                'color' => '#6610f2',
                'sort_order' => 6,
            ],
            [
                'key' => 'medical',
                'name_vi' => 'Y tế & Thiết bị y tế',
                'name_en' => 'Medical & Healthcare Equipment',
                'description_vi' => 'Thiết bị y tế, dụng cụ y khoa và công nghệ sức khỏe',
                'description_en' => 'Medical equipment, healthcare devices and health technology',
                'icon' => 'fas fa-heartbeat',
                'color' => '#e83e8c',
                'sort_order' => 7,
            ],
            [
                'key' => 'food_beverage',
                'name_vi' => 'Thực phẩm & Đồ uống',
                'name_en' => 'Food & Beverage',
                'description_vi' => 'Máy móc chế biến thực phẩm và đồ uống',
                'description_en' => 'Food and beverage processing machinery',
                'icon' => 'fas fa-utensils',
                'color' => '#20c997',
                'sort_order' => 8,
            ],
            [
                'key' => 'textile',
                'name_vi' => 'Dệt may & Thời trang',
                'name_en' => 'Textile & Fashion',
                'description_vi' => 'Máy móc dệt may và thiết bị thời trang',
                'description_en' => 'Textile machinery and fashion equipment',
                'icon' => 'fas fa-tshirt',
                'color' => '#6f42c1',
                'sort_order' => 9,
            ],
            [
                'key' => 'chemical',
                'name_vi' => 'Hóa chất & Dược phẩm',
                'name_en' => 'Chemical & Pharmaceutical',
                'description_vi' => 'Thiết bị hóa chất, dược phẩm và xử lý hóa học',
                'description_en' => 'Chemical, pharmaceutical and chemical processing equipment',
                'icon' => 'fas fa-flask',
                'color' => '#dc3545',
                'sort_order' => 10,
            ],
            [
                'key' => 'mining',
                'name_vi' => 'Khai thác & Khoáng sản',
                'name_en' => 'Mining & Minerals',
                'description_vi' => 'Thiết bị khai thác mỏ và chế biến khoáng sản',
                'description_en' => 'Mining equipment and mineral processing',
                'icon' => 'fas fa-mountain',
                'color' => '#795548',
                'sort_order' => 11,
            ],
            [
                'key' => 'marine',
                'name_vi' => 'Hàng hải & Đóng tàu',
                'name_en' => 'Marine & Shipbuilding',
                'description_vi' => 'Công nghiệp hàng hải, đóng tàu và thiết bị biển',
                'description_en' => 'Marine industry, shipbuilding and maritime equipment',
                'icon' => 'fas fa-ship',
                'color' => '#0dcaf0',
                'sort_order' => 12,
            ],
            [
                'key' => 'agriculture',
                'name_vi' => 'Nông nghiệp & Thủy sản',
                'name_en' => 'Agriculture & Aquaculture',
                'description_vi' => 'Máy móc nông nghiệp, thủy sản và chế biến nông sản',
                'description_en' => 'Agricultural machinery, aquaculture and food processing',
                'icon' => 'fas fa-seedling',
                'color' => '#198754',
                'sort_order' => 13,
            ],
            [
                'key' => 'packaging',
                'name_vi' => 'Bao bì & In ấn',
                'name_en' => 'Packaging & Printing',
                'description_vi' => 'Máy móc bao bì, in ấn và đóng gói',
                'description_en' => 'Packaging machinery, printing and wrapping equipment',
                'icon' => 'fas fa-box',
                'color' => '#fd7e14',
                'sort_order' => 14,
            ],
            [
                'key' => 'consulting',
                'name_vi' => 'Tư vấn & Dịch vụ kỹ thuật',
                'name_en' => 'Consulting & Technical Services',
                'description_vi' => 'Tư vấn kỹ thuật, thiết kế và dịch vụ chuyên môn',
                'description_en' => 'Technical consulting, design and professional services',
                'icon' => 'fas fa-handshake',
                'color' => '#6c757d',
                'sort_order' => 15,
            ],
            [
                'key' => 'education',
                'name_vi' => 'Giáo dục & Đào tạo',
                'name_en' => 'Education & Training',
                'description_vi' => 'Giáo dục kỹ thuật, đào tạo và thiết bị giảng dạy',
                'description_en' => 'Technical education, training and teaching equipment',
                'icon' => 'fas fa-graduation-cap',
                'color' => '#0d6efd',
                'sort_order' => 16,
            ],
            [
                'key' => 'research',
                'name_vi' => 'Nghiên cứu & Phát triển',
                'name_en' => 'Research & Development',
                'description_vi' => 'Nghiên cứu khoa học, phát triển công nghệ và đổi mới',
                'description_en' => 'Scientific research, technology development and innovation',
                'icon' => 'fas fa-microscope',
                'color' => '#6610f2',
                'sort_order' => 17,
            ],
            [
                'key' => 'other',
                'name_vi' => 'Khác',
                'name_en' => 'Other',
                'description_vi' => 'Các lĩnh vực khác không thuộc danh mục trên',
                'description_en' => 'Other fields not listed in above categories',
                'icon' => 'fas fa-ellipsis-h',
                'color' => '#adb5bd',
                'sort_order' => 18,
            ],
        ];

        foreach ($categories as $category) {
            BusinessCategory::create($category);
        }
    }
}
