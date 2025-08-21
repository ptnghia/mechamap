<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FAQSeeder extends Seeder
{
    /**
     * Seed FAQs với câu hỏi thường gặp về cơ khí
     * Tạo FAQ system cho website
     */
    public function run(): void
    {
        $this->command->info('❓ Bắt đầu seed FAQs...');

        // Tạo FAQ categories trước
        $this->createFAQCategories();

        // Tạo FAQs
        $this->createFAQs();

        $this->command->info('✅ Hoàn thành seed FAQs!');
    }

    private function createFAQCategories(): void
    {
        // Check if categories already exist
        if (DB::table('faq_categories')->count() > 0) {
            $this->command->line("   📂 FAQ categories đã tồn tại, bỏ qua...");
            return;
        }

        $categories = [
            [
                'name' => 'Hướng dẫn cơ bản',
                'slug' => 'huong-dan-co-ban',
                'description' => 'Hướng dẫn sử dụng cơ bản cho người mới',
                'engineering_domain' => 'general',
                'faq_count' => 0,
                'order' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sử dụng Forum',
                'slug' => 'su-dung-forum',
                'description' => 'Cách sử dụng các tính năng forum hiệu quả',
                'engineering_domain' => 'general',
                'faq_count' => 0,
                'order' => 2,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Showcase & Projects',
                'slug' => 'showcase-projects',
                'description' => 'Hướng dẫn chia sẻ dự án và showcase',
                'engineering_domain' => 'general',
                'faq_count' => 0,
                'order' => 3,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Tính năng nâng cao',
                'slug' => 'tinh-nang-nang-cao',
                'description' => 'Các tính năng nâng cao và tips sử dụng',
                'engineering_domain' => 'general',
                'faq_count' => 0,
                'order' => 4,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        DB::table('faq_categories')->insert($categories);
        $this->command->line("   📂 Tạo " . count($categories) . " FAQ categories");
    }

    private function createFAQs(): void
    {
        // Get first admin user for created_by
        $adminUser = DB::table('users')->where('role', 'admin')->first();
        $createdBy = $adminUser ? $adminUser->id : 1;

        $faqs = [
            // Hướng dẫn cơ bản
            [
                'category_id' => 1,
                'question' => 'Làm thế nào để đăng ký tài khoản MechaMap?',
                'answer' => "**Bước 1:** Click nút \"Đăng ký\" ở góc phải màn hình\n\n**Bước 2:** Điền thông tin cá nhân:\n- Email (sử dụng email thật để nhận thông báo)\n- Mật khẩu (tối thiểu 8 ký tự, có chữ hoa, số)\n- Họ tên đầy đủ\n- Chuyên ngành (Mechanical Engineering, Manufacturing, etc.)\n\n**Bước 3:** Xác nhận email\n- Check hộp thư để nhận email xác nhận\n- Click link trong email để kích hoạt tài khoản\n\n**Bước 4:** Hoàn thiện profile\n- Thêm ảnh đại diện\n- Viết giới thiệu ngắn về bản thân\n- Thêm kinh nghiệm và kỹ năng\n\n**Lưu ý:** Profile hoàn chỉnh sẽ tăng độ tin cậy trong cộng đồng.",
                'faq_type' => 'general_engineering',
                'difficulty_level' => 'beginner',
                'order' => 1,
                'is_active' => true,
                'view_count' => rand(500, 1000),
                'helpful_votes' => rand(80, 150),
                'created_at' => now()->subDays(rand(1, 30)),
                'updated_at' => now(),
                'last_updated' => now()->subDays(rand(0, 5)),
                'created_by' => $createdBy,
            ],
            [
                'category_id' => 1,
                'question' => 'Cách điều hướng và tìm kiếm thông tin trên MechaMap?',
                'answer' => "**Thanh điều hướng chính:**\n- **Home:** Trang chủ với các bài viết nổi bật\n- **Forums:** Danh sách các diễn đàn chuyên ngành\n- **Showcases:** Thư viện dự án của cộng đồng\n- **Members:** Danh sách thành viên\n\n**Tìm kiếm hiệu quả:**\n- Sử dụng thanh tìm kiếm ở đầu trang\n- Tìm theo từ khóa, tên người dùng, hoặc tag\n- Lọc kết quả theo thời gian, độ phổ biến\n- Sử dụng bộ lọc nâng cao cho kết quả chính xác\n\n**Phân loại nội dung:**\n- **Categories:** Chia theo lĩnh vực (Design, Manufacturing, Analysis...)\n- **Tags:** Từ khóa chi tiết (SolidWorks, CNC, FEA...)\n- **Thread Types:** Question, Discussion, Tutorial, Showcase\n\n**Tips:** Bookmark các thread hữu ích để đọc lại sau.",
                'faq_type' => 'general_engineering',
                'difficulty_level' => 'beginner',
                'order' => 2,
                'is_active' => true,
                'view_count' => rand(300, 600),
                'helpful_votes' => rand(50, 120),
                'created_at' => now()->subDays(rand(1, 25)),
                'updated_at' => now(),
                'last_updated' => now()->subDays(rand(0, 5)),
                'created_by' => $createdBy,
            ],

            // Sử dụng Forum
            [
                'category_id' => 2,
                'question' => 'Cách tạo thread mới hiệu quả?',
                'answer' => "**Chọn Forum phù hợp:**\n- **Mechanical Design:** Thiết kế cơ khí, CAD, modeling\n- **Manufacturing:** Gia công, sản xuất, quy trình\n- **Analysis & Simulation:** FEA, CFD, tính toán\n- **Materials:** Vật liệu, tính chất, lựa chọn\n- **Career & Education:** Nghề nghiệp, học tập\n\n**Viết tiêu đề tốt:**\n- Cụ thể, mô tả rõ vấn đề\n- Tránh \"Help me\", \"Urgent\", \"SOS\"\n- VD: \"Cách tính toán độ bền thanh chịu uốn trong SolidWorks Simulation\"\n\n**Nội dung chi tiết:**\n- Mô tả vấn đề cụ thể\n- Cung cấp thông số kỹ thuật\n- Đính kèm hình ảnh, file CAD nếu cần\n- Nêu rõ mục tiêu muốn đạt được\n\n**Chọn Thread Type:**\n- **Question:** Cần giải đáp thắc mắc\n- **Discussion:** Thảo luận chung về chủ đề\n- **Tutorial:** Chia sẻ hướng dẫn\n- **Showcase:** Trình bày dự án\n\n**Thêm Tags:** Sử dụng 3-5 tags liên quan để dễ tìm kiếm.",
                'faq_type' => 'general_engineering',
                'difficulty_level' => 'beginner',
                'order' => 1,
                'is_active' => true,
                'view_count' => rand(800, 1200),
                'helpful_votes' => rand(100, 200),
                'created_at' => now()->subDays(rand(1, 20)),
                'updated_at' => now(),
                'last_updated' => now()->subDays(rand(0, 5)),
                'created_by' => $createdBy,
            ],
            [
                'category_id' => 2,
                'question' => 'Cách viết comment và tương tác hiệu quả?',
                'answer' => "**Viết comment chất lượng:**\n- Đọc kỹ thread trước khi comment\n- Trả lời trực tiếp vào vấn đề được hỏi\n- Cung cấp giải pháp cụ thể, có thể thực hiện\n- Đính kèm hình ảnh, link tham khảo nếu cần\n\n**Sử dụng công thức toán học:**\n- Inline: `F = ma` → F = ma\n- Block: `σ = F/A`\n- Ký hiệu thường dùng: σ (stress), ε (strain), τ (shear)\n\n**Tương tác tích cực:**\n- **Like:** Cho những comment hữu ích\n- **Reply:** Trả lời trực tiếp comment cụ thể\n- **Quote:** Trích dẫn phần cần thảo luận\n- **Follow:** Theo dõi thread để nhận thông báo\n\n**Quy tắc tương tác:**\n- Tôn trọng ý kiến khác biệt\n- Không spam, không quảng cáo\n- Sử dụng ngôn ngữ chuyên nghiệp\n- Cite nguồn khi tham khảo tài liệu\n\n**Reaction:** Sử dụng emoji để thể hiện cảm xúc nhanh.",
                'faq_type' => 'general_engineering',
                'difficulty_level' => 'intermediate',
                'order' => 2,
                'is_active' => true,
                'view_count' => rand(400, 800),
                'helpful_votes' => rand(60, 140),
                'created_at' => now()->subDays(rand(1, 18)),
                'updated_at' => now(),
                'last_updated' => now()->subDays(rand(0, 5)),
                'created_by' => $createdBy,
            ],

            // Showcase & Projects
            [
                'category_id' => 3,
                'question' => 'Hướng dẫn chi tiết upload showcase project?',
                'answer' => "**Chuẩn bị trước khi upload:**\n- Tổ chức file CAD, hình ảnh, tài liệu\n- Chụp ảnh chất lượng cao từ nhiều góc độ\n- Chuẩn bị mô tả chi tiết về dự án\n- Kiểm tra quyền sở hữu trí tuệ\n\n**Quy trình upload:**\n\n**Bước 1:** Vào Profile → My Showcases → Create New\n\n**Bước 2:** Thông tin cơ bản\n- **Title:** Tên dự án rõ ràng, hấp dẫn\n- **Description:** Mô tả chi tiết mục đích, ứng dụng\n- **Category:** Chọn đúng phân loại\n- **Industry:** Lĩnh vực ứng dụng\n\n**Bước 3:** Chi tiết kỹ thuật\n- **Software Used:** SolidWorks, AutoCAD, ANSYS...\n- **Materials:** Vật liệu sử dụng và lý do chọn\n- **Manufacturing Process:** Quy trình gia công\n- **Timeline:** Thời gian thực hiện\n\n**Bước 4:** Upload media\n- **Cover Image:** Ảnh đại diện chất lượng cao\n- **Gallery:** 5-10 ảnh từ các góc độ khác nhau\n- **CAD Files:** File gốc và format trung tính (STEP, IGES)\n- **Documents:** Báo cáo, tính toán, bản vẽ kỹ thuật\n\n**Bước 5:** Cài đặt\n- **Visibility:** Public/Private\n- **Allow Downloads:** Cho phép tải file\n- **License:** Quyền sử dụng\n\n**Tips để được featured:**\n- Documentation đầy đủ, chuyên nghiệp\n- Ảnh chụp đẹp, rõ nét\n- Giải thích rõ quá trình thiết kế\n- Tương tác tích cực với feedback",
                'faq_type' => 'software_usage',
                'difficulty_level' => 'intermediate',
                'order' => 1,
                'is_active' => true,
                'view_count' => rand(600, 1000),
                'helpful_votes' => rand(80, 160),
                'created_at' => now()->subDays(rand(1, 15)),
                'updated_at' => now(),
                'last_updated' => now()->subDays(rand(0, 5)),
                'created_by' => $createdBy,
            ],
            [
                'category_id' => 3,
                'question' => 'Cách nhận feedback và cải thiện showcase?',
                'answer' => "**Nhận feedback hiệu quả:**\n- **Mở lòng:** Sẵn sàng nhận góp ý xây dựng\n- **Trả lời nhanh:** Phản hồi comment trong 24-48h\n- **Hỏi cụ thể:** \"Anh/chị có thể suggest cách optimize design này không?\"\n- **Cảm ơn:** Luôn cảm ơn những người đóng góp ý kiến\n\n**Phân loại feedback:**\n- **Technical:** Góp ý về mặt kỹ thuật, tính toán\n- **Design:** Ý kiến về thẩm mỹ, ergonomics\n- **Manufacturing:** Khả năng gia công, cost optimization\n- **Safety:** Vấn đề an toàn, tiêu chuẩn\n\n**Cải thiện showcase:**\n- **Update thường xuyên:** Thêm thông tin mới, sửa lỗi\n- **Version control:** Ghi chú các thay đổi\n- **Before/After:** So sánh trước và sau khi cải thiện\n- **Lessons learned:** Chia sẻ bài học rút ra\n\n**Tương tác với community:**\n- **Rate & Review:** Đánh giá showcase của người khác\n- **Share knowledge:** Chia sẻ kinh nghiệm từ dự án\n- **Collaborate:** Mở cơ hội hợp tác\n- **Mentor:** Hướng dẫn người mới\n\n**Metrics quan trọng:**\n- View count, like count\n- Download statistics\n- Comment engagement\n- Rating average",
                'faq_type' => 'software_usage',
                'difficulty_level' => 'intermediate',
                'order' => 2,
                'is_active' => true,
                'view_count' => rand(300, 600),
                'helpful_votes' => rand(40, 100),
                'created_at' => now()->subDays(rand(1, 12)),
                'updated_at' => now(),
                'last_updated' => now()->subDays(rand(0, 5)),
                'created_by' => $createdBy,
            ],

            // Tính năng nâng cao
            [
                'category_id' => 4,
                'question' => 'Cách sử dụng hệ thống notification và follow?',
                'answer' => "**Hệ thống Notification:**\n- **Real-time alerts:** Thông báo ngay khi có hoạt động mới\n- **Email digest:** Tóm tắt hoạt động hàng ngày/tuần\n- **Push notifications:** Thông báo trên mobile app\n\n**Các loại thông báo:**\n- **Reply:** Có người trả lời thread/comment của bạn\n- **Like:** Thread/comment được like\n- **Follow:** Có người follow bạn\n- **Mention:** Được tag trong comment (@username)\n- **System:** Thông báo từ hệ thống\n\n**Follow System:**\n- **Follow Users:** Theo dõi thành viên có kiến thức tốt\n- **Follow Threads:** Nhận thông báo khi có reply mới\n- **Follow Tags:** Theo dõi chủ đề quan tâm\n- **Follow Forums:** Cập nhật thread mới trong forum\n\n**Quản lý notification:**\n- **Settings:** Tùy chỉnh loại thông báo muốn nhận\n- **Frequency:** Chọn tần suất nhận email\n- **Priority:** Ưu tiên thông báo quan trọng\n- **Mute:** Tắt thông báo từ thread/user cụ thể\n\n**Tips sử dụng hiệu quả:**\n- Follow các expert trong lĩnh vực của bạn\n- Theo dõi threads có nhiều discussion chất lượng\n- Sử dụng digest email để không bị spam\n- Tắt notification không cần thiết",
                'faq_type' => 'career_advice',
                'difficulty_level' => 'intermediate',
                'order' => 1,
                'is_active' => true,
                'view_count' => rand(200, 500),
                'helpful_votes' => rand(30, 80),
                'created_at' => now()->subDays(rand(1, 10)),
                'updated_at' => now(),
                'last_updated' => now()->subDays(rand(0, 5)),
                'created_by' => $createdBy,
            ],
            [
                'category_id' => 4,
                'question' => 'Hệ thống reputation và ranking hoạt động như thế nào?',
                'answer' => "**Hệ thống Reputation:**\n- **Điểm cơ bản:** +1 cho mỗi like nhận được\n- **Bonus points:** +5 cho thread được pin, +10 cho showcase featured\n- **Quality bonus:** +3 cho comment được mark as \"Best Answer\"\n- **Penalty:** -2 cho content bị report và xác nhận vi phạm\n\n**Ranking Levels:**\n- **Newbie (0-50):** Thành viên mới\n- **Member (51-200):** Thành viên tích cực\n- **Advanced (201-500):** Thành viên có kinh nghiệm\n- **Expert (501-1000):** Chuyên gia trong lĩnh vực\n- **Master (1000+):** Bậc thầy, có ảnh hưởng lớn\n\n**Privileges theo level:**\n- **Member:** Tạo thread, comment, like\n- **Advanced:** Tạo poll, upload file lớn hơn\n- **Expert:** Edit thread của người khác, moderate comment\n- **Master:** Pin thread, feature showcase\n\n**Badges & Achievements:**\n- **First Post:** Thread đầu tiên\n- **Helpful:** 50+ likes trên comment\n- **Popular:** Thread có 100+ views\n- **Mentor:** Giúp đỡ 10+ thành viên mới\n- **Specialist:** Expert trong tag cụ thể\n\n**Cách tăng reputation:**\n- Đóng góp content chất lượng\n- Trả lời câu hỏi hữu ích\n- Chia sẻ kiến thức chuyên môn\n- Tương tác tích cực với community\n- Upload showcase chất lượng cao\n\n**Lưu ý:** Reputation phản ánh đóng góp cho cộng đồng, không phải level kỹ thuật.",
                'faq_type' => 'career_advice',
                'difficulty_level' => 'advanced',
                'order' => 2,
                'is_active' => true,
                'view_count' => rand(150, 400),
                'helpful_votes' => rand(25, 70),
                'created_at' => now()->subDays(rand(1, 8)),
                'updated_at' => now(),
                'last_updated' => now()->subDays(rand(0, 5)),
                'created_by' => $createdBy,
            ]
        ];

        DB::table('faqs')->insert($faqs);
        $this->command->line("   ❓ Tạo " . count($faqs) . " FAQs");
    }
}
