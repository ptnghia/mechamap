<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Page;
use App\Models\PageCategory;
use App\Models\User;
use Illuminate\Support\Str;

class StaticPagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create page categories
        $categories = [
            [
                'name' => 'Thông tin công ty',
                'slug' => 'thong-tin-cong-ty',
                'description' => 'Thông tin về MechaMap và công ty',
                'category_type' => 'company_info',
                'order' => 1,
                'is_active' => true,
                'show_in_menu' => true,
            ],
            [
                'name' => 'Pháp lý',
                'slug' => 'phap-ly',
                'description' => 'Các tài liệu pháp lý và chính sách',
                'category_type' => 'company_info',
                'order' => 2,
                'is_active' => true,
                'show_in_menu' => true,
            ],
            [
                'name' => 'Hỗ trợ',
                'slug' => 'ho-tro',
                'description' => 'Tài liệu hỗ trợ và hướng dẫn',
                'category_type' => 'engineering_guides',
                'order' => 3,
                'is_active' => true,
                'show_in_menu' => true,
            ],
        ];

        foreach ($categories as $categoryData) {
            PageCategory::updateOrCreate(
                ['slug' => $categoryData['slug']],
                $categoryData
            );
        }

        // Get admin user
        $adminUser = User::where('role', 'admin')->first();
        if (!$adminUser) {
            $adminUser = User::first();
        }

        // Get categories
        $companyCategory = PageCategory::where('slug', 'thong-tin-cong-ty')->first();
        $legalCategory = PageCategory::where('slug', 'phap-ly')->first();
        $supportCategory = PageCategory::where('slug', 'ho-tro')->first();

        // Create pages
        $pages = [
            // Company Info Pages
            [
                'title' => 'Về chúng tôi',
                'slug' => 've-chung-toi',
                'excerpt' => 'Tìm hiểu về MechaMap - cộng đồng kỹ sư cơ khí hàng đầu Việt Nam',
                'content' => $this->getAboutContent(),
                'category_id' => $companyCategory->id,

                'meta_title' => 'Về chúng tôi - MechaMap',
                'meta_description' => 'MechaMap là cộng đồng kỹ sư cơ khí hàng đầu Việt Nam. Chia sẻ kiến thức, kết nối chuyên gia, phát triển sự nghiệp.',
                'order' => 1,
            ],
            [
                'title' => 'Liên hệ',
                'slug' => 'lien-he',
                'excerpt' => 'Thông tin liên hệ và hỗ trợ khách hàng',
                'content' => $this->getContactContent(),
                'category_id' => $companyCategory->id,

                'meta_title' => 'Liên hệ - MechaMap',
                'meta_description' => 'Liên hệ với đội ngũ MechaMap để được hỗ trợ và tư vấn về các dịch vụ kỹ thuật cơ khí.',
                'order' => 2,
            ],

            // Legal Pages
            [
                'title' => 'Điều khoản sử dụng',
                'slug' => 'dieu-khoan-su-dung',
                'excerpt' => 'Quy định và điều khoản sử dụng dịch vụ MechaMap',
                'content' => $this->getTermsContent(),
                'category_id' => $legalCategory->id,

                'meta_title' => 'Điều khoản sử dụng - MechaMap',
                'meta_description' => 'Điều khoản và điều kiện sử dụng dịch vụ của MechaMap. Vui lòng đọc kỹ trước khi sử dụng.',
                'order' => 1,
            ],
            [
                'title' => 'Chính sách bảo mật',
                'slug' => 'chinh-sach-bao-mat',
                'excerpt' => 'Cam kết bảo vệ thông tin cá nhân và quyền riêng tư người dùng',
                'content' => $this->getPrivacyContent(),
                'category_id' => $legalCategory->id,

                'meta_title' => 'Chính sách bảo mật - MechaMap',
                'meta_description' => 'Chính sách bảo mật thông tin cá nhân của MechaMap. Cam kết bảo vệ quyền riêng tư người dùng.',
                'order' => 2,
            ],
            [
                'title' => 'Quy định cộng đồng',
                'slug' => 'quy-dinh-cong-dong',
                'excerpt' => 'Quy tắc ứng xử và nguyên tắc tham gia cộng đồng MechaMap',
                'content' => $this->getRulesContent(),
                'category_id' => $legalCategory->id,

                'meta_title' => 'Quy định cộng đồng - MechaMap',
                'meta_description' => 'Quy tắc ứng xử và nguyên tắc tham gia cộng đồng kỹ sư cơ khí MechaMap.',
                'order' => 3,
            ],

            // Support Pages
            [
                'title' => 'Trợ giúp',
                'slug' => 'tro-giup',
                'excerpt' => 'Hướng dẫn sử dụng và trung tâm hỗ trợ người dùng',
                'content' => $this->getHelpContent(),
                'category_id' => $supportCategory->id,

                'meta_title' => 'Trợ giúp - MechaMap',
                'meta_description' => 'Trung tâm trợ giúp MechaMap. Hướng dẫn sử dụng và câu trả lời cho các thắc mắc thường gặp.',
                'order' => 1,
            ],
            [
                'title' => 'Câu hỏi thường gặp',
                'slug' => 'cau-hoi-thuong-gap',
                'excerpt' => 'Câu hỏi thường gặp và câu trả lời chi tiết',
                'content' => $this->getFAQContent(),
                'category_id' => $supportCategory->id,

                'meta_title' => 'FAQ - MechaMap',
                'meta_description' => 'Câu hỏi thường gặp về MechaMap và các dịch vụ kỹ thuật cơ khí.',
                'order' => 2,
            ],
        ];

        foreach ($pages as $pageData) {
            $pageData['user_id'] = $adminUser->id;
            $pageData['status'] = 'published';
            $pageData['view_count'] = rand(100, 1000);

            Page::updateOrCreate(
                ['slug' => $pageData['slug']],
                $pageData
            );
        }

        $this->command->info('Static pages seeded successfully!');
    }

    private function getAboutContent(): string
    {
        return '<h2>Về MechaMap</h2>
<p>MechaMap là cộng đồng kỹ sư cơ khí hàng đầu Việt Nam, được thành lập với sứ mệnh kết nối các chuyên gia, chia sẻ kiến thức và thúc đẩy sự phát triển của ngành cơ khí.</p>

<h3>Tầm nhìn</h3>
<p>Trở thành nền tảng số 1 Việt Nam cho cộng đồng kỹ sư cơ khí, nơi mọi người có thể học hỏi, chia sẻ và phát triển sự nghiệp.</p>

<h3>Sứ mệnh</h3>
<ul>
<li>Kết nối cộng đồng kỹ sư cơ khí Việt Nam</li>
<li>Chia sẻ kiến thức và kinh nghiệm thực tế</li>
<li>Hỗ trợ phát triển kỹ năng chuyên môn</li>
<li>Tạo cơ hội việc làm và hợp tác</li>
</ul>

<h3>Giá trị cốt lõi</h3>
<ul>
<li><strong>Chuyên nghiệp:</strong> Đảm bảo chất lượng nội dung và dịch vụ</li>
<li><strong>Chia sẻ:</strong> Khuyến khích tinh thần chia sẻ kiến thức</li>
<li><strong>Sáng tạo:</strong> Thúc đẩy đổi mới và sáng tạo</li>
<li><strong>Cộng đồng:</strong> Xây dựng cộng đồng mạnh mẽ và gắn kết</li>
</ul>';
    }

    private function getContactContent(): string
    {
        return '<h2>Thông tin liên hệ</h2>
<div class="row">
<div class="col-md-6">
<h3>Văn phòng chính</h3>
<p><strong>Công ty Cổ phần Công nghệ MechaMap</strong><br>
Địa chỉ: 123 Đường Kỹ Thuật, Quận 1, TP.HCM<br>
Điện thoại: (028) 1234 5678<br>
Email: info@mechamap.vn</p>

<h3>Hỗ trợ khách hàng</h3>
<p>Hotline: 1900 1234<br>
Email: support@mechamap.vn<br>
Thời gian: 8:00 - 17:00 (T2-T6)</p>
</div>
<div class="col-md-6">
<h3>Hợp tác kinh doanh</h3>
<p>Email: business@mechamap.vn<br>
Điện thoại: (028) 1234 5679</p>

<h3>Báo chí & Truyền thông</h3>
<p>Email: media@mechamap.vn<br>
Điện thoại: (028) 1234 5680</p>
</div>
</div>';
    }

    private function getTermsContent(): string
    {
        return '<h2>1. Định nghĩa và Giải thích</h2>
<p>MechaMap là nền tảng cộng đồng kỹ sư cơ khí trực tuyến được vận hành bởi Công ty Cổ phần Công nghệ MechaMap.</p>

<h2>2. Phạm vi Dịch vụ</h2>
<p>MechaMap cung cấp các dịch vụ sau:</p>
<ul>
<li>Diễn đàn thảo luận kỹ thuật cơ khí</li>
<li>Thư viện tài liệu và file CAD</li>
<li>Marketplace giao dịch sản phẩm kỹ thuật</li>
<li>Công cụ tính toán và thiết kế</li>
<li>Mạng lưới kết nối chuyên gia</li>
</ul>

<h2>3. Quyền và Nghĩa vụ</h2>
<p>Người dùng có quyền sử dụng đầy đủ các tính năng đã đăng ký và có nghĩa vụ tuân thủ các quy định của MechaMap.</p>';
    }

    private function getPrivacyContent(): string
    {
        return '<h2>1. Thông tin Thu thập</h2>
<p>Chúng tôi thu thập các thông tin sau:</p>
<ul>
<li>Thông tin cá nhân cơ bản (họ tên, email, số điện thoại)</li>
<li>Thông tin chuyên môn (trình độ, kinh nghiệm)</li>
<li>Thông tin hoạt động trên nền tảng</li>
</ul>

<h2>2. Mục đích Sử dụng</h2>
<p>Thông tin được sử dụng để:</p>
<ul>
<li>Cung cấp và cải thiện dịch vụ</li>
<li>Hỗ trợ khách hàng</li>
<li>Bảo mật và tuân thủ pháp luật</li>
</ul>

<h2>3. Bảo mật Dữ liệu</h2>
<p>Chúng tôi cam kết bảo vệ thông tin cá nhân bằng các biện pháp bảo mật tiên tiến.</p>';
    }

    private function getRulesContent(): string
    {
        return '<h2>Quy tắc chung</h2>
<ul>
<li>Tôn trọng các thành viên khác trong cộng đồng</li>
<li>Chia sẻ nội dung có giá trị và chính xác</li>
<li>Không spam hoặc quảng cáo không phù hợp</li>
<li>Tuân thủ pháp luật Việt Nam</li>
</ul>

<h2>Nội dung bị cấm</h2>
<ul>
<li>Nội dung vi phạm pháp luật</li>
<li>Thông tin sai lệch, gây hiểu nhầm</li>
<li>Nội dung khiêu dâm, bạo lực</li>
<li>Vi phạm bản quyền</li>
</ul>';
    }

    private function getHelpContent(): string
    {
        return '<h2>Hướng dẫn sử dụng</h2>
<p>Chào mừng bạn đến với MechaMap! Dưới đây là hướng dẫn cơ bản để bạn có thể tận dụng tối đa các tính năng của nền tảng.</p>

<h3>Đăng ký tài khoản</h3>
<p>Để tham gia cộng đồng, bạn cần đăng ký tài khoản miễn phí.</p>

<h3>Tham gia diễn đàn</h3>
<p>Chia sẻ kiến thức và thảo luận với các chuyên gia khác.</p>

<h3>Sử dụng Marketplace</h3>
<p>Mua bán sản phẩm và dịch vụ kỹ thuật.</p>';
    }

    private function getFAQContent(): string
    {
        return '<h2>Câu hỏi thường gặp</h2>

<h3>MechaMap là gì?</h3>
<p>MechaMap là cộng đồng kỹ sư cơ khí trực tuyến hàng đầu Việt Nam.</p>

<h3>Làm thế nào để đăng ký?</h3>
<p>Bạn có thể đăng ký miễn phí bằng cách click vào nút "Đăng ký" ở góc phải màn hình.</p>

<h3>Có mất phí không?</h3>
<p>Việc tham gia cộng đồng cơ bản là miễn phí. Một số dịch vụ premium có thể tính phí.</p>';
    }
}
