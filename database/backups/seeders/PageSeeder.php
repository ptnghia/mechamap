<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PageSeeder extends Seeder
{
    /**
     * Seed pages với static pages cho website
     * Tạo các trang thông tin cần thiết
     */
    public function run(): void
    {
        $this->command->info('📄 Bắt đầu seed pages...');

        // Tạo page categories trước
        $this->createPageCategories();

        // Tạo pages
        $this->createPages();

        $this->command->info('✅ Hoàn thành seed pages!');
    }

    private function createPageCategories(): void
    {
        $categories = [
            [
                'name' => 'Điều khoản pháp lý',
                'slug' => 'dieu-khoan-phap-ly',
                'description' => 'Các điều khoản, chính sách và quy định pháp lý',
                'category_type' => 'general',
                'order' => 1,
                'is_active' => true,
                'show_in_menu' => true,
                'page_count' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Về chúng tôi',
                'slug' => 've-chung-toi',
                'description' => 'Thông tin về MechaMap và đội ngũ',
                'category_type' => 'general',
                'order' => 2,
                'is_active' => true,
                'show_in_menu' => true,
                'page_count' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Hỗ trợ',
                'slug' => 'ho-tro',
                'description' => 'Trang hỗ trợ và hướng dẫn sử dụng',
                'category_type' => 'general',
                'order' => 3,
                'is_active' => true,
                'show_in_menu' => true,
                'page_count' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        DB::table('page_categories')->insert($categories);
        $this->command->line("   📂 Tạo " . count($categories) . " page categories");
    }

    private function createPages(): void
    {
        $pages = [
            // Điều khoản pháp lý
            [
                'category_id' => 1,
                'title' => 'Điều khoản sử dụng',
                'slug' => 'dieu-khoan-su-dung',
                'content' => $this->getTermsOfServiceContent(),
                'excerpt' => 'Điều khoản và điều kiện sử dụng dịch vụ MechaMap',
                'page_type' => 'legal',
                'difficulty_level' => 'beginner',
                'estimated_read_time' => 8,
                'user_id' => 1,
                'status' => 'published',
                'order' => 1,
                'is_featured' => false,
                'view_count' => rand(200, 600),
                'rating_average' => 4.0,
                'rating_count' => 15,
                'requires_login' => false,
                'is_premium' => false,
                'meta_title' => 'Điều khoản sử dụng - MechaMap',
                'meta_description' => 'Điều khoản và điều kiện sử dụng dịch vụ MechaMap',
                'meta_keywords' => 'điều khoản, sử dụng, quy định, pháp lý',
                'created_at' => now()->subDays(90),
                'updated_at' => now()->subDays(10),
                'published_at' => now()->subDays(90),
                'author_id' => 1,
            ],
            [
                'category_id' => 1,
                'title' => 'Chính sách bảo mật',
                'slug' => 'chinh-sach-bao-mat',
                'content' => $this->getPrivacyPolicyContent(),
                'excerpt' => 'Chính sách bảo mật thông tin cá nhân của người dùng',
                'page_type' => 'legal',
                'difficulty_level' => 'beginner',
                'estimated_read_time' => 6,
                'user_id' => 1,
                'status' => 'published',
                'order' => 2,
                'is_featured' => false,
                'view_count' => rand(150, 400),
                'rating_average' => 4.2,
                'rating_count' => 12,
                'requires_login' => false,
                'is_premium' => false,
                'meta_title' => 'Chính sách bảo mật - MechaMap',
                'meta_description' => 'Chính sách bảo mật và xử lý dữ liệu cá nhân tại MechaMap',
                'meta_keywords' => 'bảo mật, riêng tư, dữ liệu, thông tin cá nhân',
                'created_at' => now()->subDays(85),
                'updated_at' => now()->subDays(8),
                'published_at' => now()->subDays(85),
                'author_id' => 1,
            ],
            [
                'category_id' => 1,
                'title' => 'Quy tắc cộng đồng',
                'slug' => 'quy-tac-cong-dong',
                'content' => $this->getCommunityGuidelinesContent(),
                'excerpt' => 'Quy tắc và hướng dẫn tham gia cộng đồng MechaMap',
                'page_type' => 'legal',
                'difficulty_level' => 'beginner',
                'estimated_read_time' => 5,
                'user_id' => 1,
                'status' => 'published',
                'order' => 3,
                'is_featured' => true,
                'view_count' => rand(400, 800),
                'rating_average' => 4.6,
                'rating_count' => 25,
                'requires_login' => false,
                'is_premium' => false,
                'meta_title' => 'Quy tắc cộng đồng - MechaMap',
                'meta_description' => 'Quy tắc và nguyên tắc tham gia cộng đồng MechaMap',
                'meta_keywords' => 'quy tắc, cộng đồng, hướng dẫn, nguyên tắc',
                'created_at' => now()->subDays(80),
                'updated_at' => now()->subDays(5),
                'published_at' => now()->subDays(80),
                'author_id' => 1,
            ],
            [
                'category_id' => 1,
                'title' => 'Chính sách sở hữu trí tuệ',
                'slug' => 'chinh-sach-so-huu-tri-tue',
                'content' => $this->getIntellectualPropertyContent(),
                'excerpt' => 'Chính sách về quyền sở hữu trí tuệ và bản quyền',
                'page_type' => 'legal',
                'difficulty_level' => 'intermediate',
                'estimated_read_time' => 7,
                'user_id' => 1,
                'status' => 'published',
                'order' => 4,
                'is_featured' => false,
                'view_count' => rand(100, 300),
                'rating_average' => 4.1,
                'rating_count' => 8,
                'requires_login' => false,
                'is_premium' => false,
                'meta_title' => 'Chính sách sở hữu trí tuệ - MechaMap',
                'meta_description' => 'Chính sách về quyền sở hữu trí tuệ và bản quyền tại MechaMap',
                'meta_keywords' => 'sở hữu trí tuệ, bản quyền, quyền tác giả',
                'created_at' => now()->subDays(75),
                'updated_at' => now()->subDays(12),
                'published_at' => now()->subDays(75),
                'author_id' => 1,
            ],

            // Về chúng tôi
            [
                'category_id' => 2,
                'title' => 'Về MechaMap',
                'slug' => 've-mechamap',
                'content' => $this->getAboutContent(),
                'excerpt' => 'Tìm hiểu về MechaMap - Cộng đồng kỹ sư cơ khí hàng đầu Việt Nam',
                'page_type' => 'company',
                'difficulty_level' => 'beginner',
                'estimated_read_time' => 4,
                'user_id' => 1,
                'status' => 'published',
                'order' => 1,
                'is_featured' => true,
                'view_count' => rand(500, 1200),
                'rating_average' => 4.7,
                'rating_count' => 35,
                'requires_login' => false,
                'is_premium' => false,
                'meta_title' => 'Về MechaMap - Cộng đồng kỹ sư cơ khí Việt Nam',
                'meta_description' => 'Tìm hiểu về MechaMap, cộng đồng kỹ sư cơ khí hàng đầu Việt Nam',
                'meta_keywords' => 'về chúng tôi, mechamap, kỹ sư cơ khí, cộng đồng',
                'created_at' => now()->subDays(100),
                'updated_at' => now()->subDays(15),
                'published_at' => now()->subDays(100),
                'author_id' => 1,
            ],
            [
                'category_id' => 2,
                'title' => 'Liên hệ',
                'slug' => 'lien-he',
                'content' => $this->getContactContent(),
                'excerpt' => 'Thông tin liên hệ và hỗ trợ từ đội ngũ MechaMap',
                'page_type' => 'company',
                'difficulty_level' => 'beginner',
                'estimated_read_time' => 2,
                'user_id' => 1,
                'status' => 'published',
                'order' => 2,
                'is_featured' => false,
                'view_count' => rand(200, 500),
                'rating_average' => 4.3,
                'rating_count' => 18,
                'requires_login' => false,
                'is_premium' => false,
                'meta_title' => 'Liên hệ - MechaMap',
                'meta_description' => 'Thông tin liên hệ và hỗ trợ từ đội ngũ MechaMap',
                'meta_keywords' => 'liên hệ, hỗ trợ, contact, support',
                'created_at' => now()->subDays(95),
                'updated_at' => now()->subDays(20),
                'published_at' => now()->subDays(95),
                'author_id' => 1,
            ]
        ];

        DB::table('pages')->insert($pages);
        $this->command->line("   📄 Tạo " . count($pages) . " pages");
    }

    private function getTermsOfServiceContent(): string
    {
        return "# Điều khoản sử dụng MechaMap

**Có hiệu lực từ:** 01/01/2024
**Cập nhật lần cuối:** 24/06/2025

## 1. Chấp nhận điều khoản

Bằng việc truy cập và sử dụng MechaMap, bạn đồng ý tuân thủ các điều khoản và điều kiện được quy định trong tài liệu này. Nếu bạn không đồng ý với bất kỳ điều khoản nào, vui lòng không sử dụng dịch vụ.

## 2. Định nghĩa dịch vụ

**MechaMap** là nền tảng cộng đồng trực tuyến dành cho các kỹ sư cơ khí, sinh viên và những người quan tâm đến lĩnh vực kỹ thuật cơ khí tại Việt Nam và khu vực.

**Dịch vụ bao gồm:**
- Diễn đàn thảo luận kỹ thuật
- Chia sẻ dự án và showcase
- Hệ thống tin nhắn và kết nối
- Thư viện tài liệu và hướng dẫn
- Các công cụ hỗ trợ học tập và nghiên cứu

## 3. Tài khoản người dùng

### 3.1 Đăng ký tài khoản
- Bạn phải ít nhất 16 tuổi để tạo tài khoản
- Thông tin đăng ký phải chính xác và đầy đủ
- Mỗi người chỉ được tạo một tài khoản duy nhất
- Bạn có trách nhiệm bảo mật thông tin đăng nhập

### 3.2 Trách nhiệm người dùng
- Cập nhật thông tin cá nhân khi có thay đổi
- Không chia sẻ tài khoản cho người khác
- Thông báo ngay khi phát hiện tài khoản bị xâm nhập
- Tuân thủ quy tắc cộng đồng và pháp luật Việt Nam

## 4. Quy định về nội dung

### 4.1 Nội dung được khuyến khích
- Thảo luận kỹ thuật chuyên nghiệp
- Chia sẻ kiến thức và kinh nghiệm
- Dự án sáng tạo và nghiên cứu
- Hướng dẫn và tutorial chất lượng
- Trao đổi học thuật có giá trị

### 4.2 Nội dung bị cấm
- Spam, quảng cáo không liên quan
- Nội dung khiêu dâm, bạo lực
- Thông tin sai lệch, gây hiểu lầm
- Vi phạm bản quyền, sở hữu trí tuệ
- Ngôn ngữ thù địch, phân biệt đối xử
- Chia sẻ phần mềm lậu, crack

## 5. Quyền sở hữu trí tuệ

### 5.1 Nội dung của MechaMap
- Logo, giao diện, mã nguồn thuộc quyền sở hữu của MechaMap
- Người dùng không được sao chép, phân phối mà không có sự cho phép

### 5.2 Nội dung người dùng
- Bạn giữ quyền sở hữu nội dung mình tạo ra
- Bằng việc đăng tải, bạn cấp cho MechaMap quyền sử dụng, hiển thị nội dung
- MechaMap có quyền xóa nội dung vi phạm mà không cần thông báo trước

## 6. Chính sách sử dụng

### 6.1 Sử dụng được phép
- Truy cập và sử dụng cho mục đích cá nhân, học tập
- Tham gia thảo luận và chia sẻ kiến thức
- Tải xuống tài liệu được phép chia sẻ
- Kết nối và networking với cộng đồng

### 6.2 Sử dụng bị cấm
- Sử dụng cho mục đích thương mại mà không có sự cho phép
- Tấn công, làm gián đoạn hệ thống
- Thu thập dữ liệu người dùng trái phép
- Tạo tài khoản ảo, bot spam
- Mạo danh cá nhân, tổ chức khác

## 7. Trách nhiệm và giới hạn

### 7.1 Trách nhiệm của MechaMap
- Cung cấp dịch vụ ổn định, bảo mật
- Bảo vệ thông tin cá nhân người dùng
- Hỗ trợ kỹ thuật khi cần thiết
- Duy trì môi trường cộng đồng tích cực

### 7.2 Giới hạn trách nhiệm
- MechaMap không chịu trách nhiệm về nội dung do người dùng tạo ra
- Không đảm bảo tính chính xác của thông tin kỹ thuật
- Không chịu trách nhiệm về thiệt hại gián tiếp
- Dịch vụ có thể bị gián đoạn do bảo trì hoặc sự cố kỹ thuật

## 8. Chấm dứt dịch vụ

### 8.1 Chấm dứt bởi người dùng
- Bạn có thể xóa tài khoản bất cứ lúc nào
- Liên hệ support@mechamap.vn để được hỗ trợ

### 8.2 Chấm dứt bởi MechaMap
- Vi phạm nghiêm trọng điều khoản sử dụng
- Hoạt động bất hợp pháp
- Gây tổn hại đến cộng đồng
- Không hoạt động trong thời gian dài (2 năm)

## 9. Thay đổi điều khoản

MechaMap có quyền cập nhật điều khoản sử dụng. Thay đổi sẽ được thông báo qua:
- Email đến tài khoản đã đăng ký
- Thông báo trên website
- Popup khi đăng nhập

## 10. Luật áp dụng

Điều khoản này được điều chỉnh bởi pháp luật Việt Nam. Mọi tranh chấp sẽ được giải quyết tại Tòa án có thẩm quyền tại TP. Hồ Chí Minh.

## 11. Liên hệ

Mọi thắc mắc về điều khoản sử dụng, vui lòng liên hệ:
- **Email:** legal@mechamap.vn
- **Hotline:** 1900 1234
- **Địa chỉ:** 123 Đường Kỹ Thuật, Quận 1, TP.HCM";
    }

    private function getPrivacyPolicyContent(): string
    {
        return "# Chính sách bảo mật MechaMap

**Có hiệu lực từ:** 01/01/2024
**Cập nhật lần cuối:** 24/06/2025

## 1. Cam kết bảo mật

MechaMap cam kết bảo vệ quyền riêng tư và thông tin cá nhân của người dùng. Chính sách này giải thích cách chúng tôi thu thập, sử dụng, lưu trữ và bảo vệ thông tin của bạn.

## 2. Thông tin chúng tôi thu thập

### 2.1 Thông tin cá nhân
- **Thông tin đăng ký:** Họ tên, email, mật khẩu, ngày sinh
- **Thông tin hồ sơ:** Ảnh đại diện, giới thiệu, kinh nghiệm, kỹ năng
- **Thông tin liên hệ:** Số điện thoại, địa chỉ (tùy chọn)
- **Thông tin nghề nghiệp:** Công ty, vị trí, chuyên ngành

### 2.2 Thông tin hoạt động
- **Nội dung đăng tải:** Thread, comment, showcase, tin nhắn
- **Tương tác:** Like, follow, bookmark, rating
- **Lịch sử duyệt:** Trang đã xem, thời gian truy cập
- **Tìm kiếm:** Từ khóa và bộ lọc sử dụng

### 2.3 Thông tin kỹ thuật
- **Thiết bị:** Loại thiết bị, hệ điều hành, trình duyệt
- **Mạng:** Địa chỉ IP, nhà cung cấp dịch vụ internet
- **Cookies:** Dữ liệu lưu trữ cục bộ
- **Log files:** Nhật ký truy cập và lỗi hệ thống

## 3. Cách chúng tôi sử dụng thông tin

### 3.1 Cung cấp dịch vụ
- Tạo và quản lý tài khoản người dùng
- Hiển thị nội dung cá nhân hóa
- Kết nối người dùng có cùng sở thích
- Gửi thông báo về hoạt động liên quan

### 3.2 Cải thiện dịch vụ
- Phân tích hành vi người dùng để tối ưu hóa
- Phát triển tính năng mới dựa trên nhu cầu
- Khắc phục lỗi và cải thiện hiệu suất
- Nghiên cứu xu hướng và thống kê

### 3.3 Bảo mật và an toàn
- Phát hiện và ngăn chặn gian lận
- Bảo vệ khỏi spam và lạm dụng
- Xác minh danh tính khi cần thiết
- Tuân thủ yêu cầu pháp lý

### 3.4 Liên lạc
- Gửi email thông báo quan trọng
- Newsletter và cập nhật sản phẩm
- Phản hồi yêu cầu hỗ trợ
- Khảo sát ý kiến người dùng

## 4. Chia sẻ thông tin

### 4.1 Nguyên tắc chung
**MechaMap KHÔNG bán thông tin cá nhân của bạn cho bên thứ ba.**

### 4.2 Trường hợp chia sẻ
- **Với sự đồng ý:** Khi bạn cho phép rõ ràng
- **Dịch vụ đối tác:** Nhà cung cấp hosting, analytics (Google Analytics)
- **Yêu cầu pháp lý:** Theo lệnh tòa án hoặc cơ quan có thẩm quyền
- **Bảo vệ quyền lợi:** Ngăn chặn gian lận, bảo vệ an toàn

### 4.3 Thông tin công khai
- Tên hiển thị, ảnh đại diện
- Nội dung đăng tải (thread, comment, showcase)
- Thông tin hồ sơ công khai
- Hoạt động tương tác (like, follow)

## 5. Bảo mật dữ liệu

### 5.1 Biện pháp kỹ thuật
- **Mã hóa:** SSL/TLS cho truyền tải dữ liệu
- **Mật khẩu:** Hash và salt với bcrypt
- **Cơ sở dữ liệu:** Mã hóa dữ liệu nhạy cảm
- **Backup:** Sao lưu định kỳ và bảo mật

### 5.2 Biện pháp quản lý
- **Kiểm soát truy cập:** Chỉ nhân viên có thẩm quyền
- **Đào tạo:** Nhân viên được đào tạo về bảo mật
- **Kiểm tra:** Audit bảo mật định kỳ
- **Ứng phó sự cố:** Quy trình xử lý vi phạm dữ liệu

## 6. Quyền của người dùng

### 6.1 Quyền truy cập
- Xem thông tin cá nhân chúng tôi lưu trữ
- Tải xuống dữ liệu của bạn (data export)
- Biết cách thông tin được sử dụng

### 6.2 Quyền chỉnh sửa
- Cập nhật thông tin hồ sơ
- Sửa đổi cài đặt riêng tư
- Thay đổi tùy chọn thông báo

### 6.3 Quyền xóa
- Xóa nội dung đã đăng tải
- Vô hiệu hóa tài khoản tạm thời
- Xóa tài khoản vĩnh viễn

### 6.4 Quyền phản đối
- Từ chối nhận email marketing
- Opt-out khỏi việc thu thập dữ liệu phân tích
- Yêu cầu hạn chế xử lý dữ liệu

## 7. Cookies và công nghệ theo dõi

### 7.1 Loại cookies
- **Essential cookies:** Cần thiết cho hoạt động website
- **Analytics cookies:** Google Analytics để phân tích lưu lượng
- **Preference cookies:** Lưu cài đặt người dùng
- **Marketing cookies:** Hiển thị quảng cáo liên quan (nếu có)

### 7.2 Quản lý cookies
- Cài đặt trình duyệt để chặn cookies
- Xóa cookies đã lưu
- Sử dụng chế độ duyệt ẩn danh

## 8. Lưu trữ và xóa dữ liệu

### 8.1 Thời gian lưu trữ
- **Tài khoản hoạt động:** Cho đến khi người dùng xóa
- **Tài khoản không hoạt động:** 2 năm sau lần đăng nhập cuối
- **Log files:** 12 tháng
- **Analytics data:** 26 tháng (theo Google Analytics)

### 8.2 Xóa dữ liệu
- Khi người dùng yêu cầu xóa tài khoản
- Sau thời gian lưu trữ quy định
- Theo yêu cầu pháp lý
- Khi không còn cần thiết cho mục đích ban đầu

## 9. Chuyển giao dữ liệu quốc tế

Dữ liệu của bạn có thể được lưu trữ và xử lý tại:
- **Việt Nam:** Server chính tại TP.HCM
- **Singapore:** Backup server khu vực
- **Mỹ:** Dịch vụ cloud (AWS, Google Cloud)

Chúng tôi đảm bảo mức độ bảo mật tương đương cho mọi địa điểm.

## 10. Quyền riêng tư của trẻ em

MechaMap không dành cho trẻ em dưới 16 tuổi. Chúng tôi không cố ý thu thập thông tin từ trẻ em. Nếu phát hiện, chúng tôi sẽ xóa ngay lập tức.

## 11. Thay đổi chính sách

Khi có thay đổi quan trọng, chúng tôi sẽ thông báo qua:
- Email đến địa chỉ đã đăng ký
- Thông báo nổi bật trên website
- Popup khi đăng nhập lần tiếp theo

## 12. Liên hệ về quyền riêng tư

Mọi thắc mắc về chính sách bảo mật:
- **Email:** privacy@mechamap.vn
- **Hotline:** 1900 1234 (ext. 2)
- **Địa chỉ:** Data Protection Officer, MechaMap, 123 Đường Kỹ Thuật, Q1, TP.HCM
- **Form liên hệ:** mechamap.vn/privacy-contact";
    }

    private function getCommunityGuidelinesContent(): string
    {
        return "# Community Guidelines

## Our Mission
MechaMap is a professional community for mechanical engineers to share knowledge, collaborate, and grow together.

## Core Principles
1. **Respect** - Treat all members with respect
2. **Quality** - Share high-quality, technical content
3. **Collaboration** - Help others learn and grow
4. **Professionalism** - Maintain professional standards

## Content Standards
✅ **Encouraged:**
- Technical discussions and tutorials
- Project showcases with documentation
- Constructive feedback and advice
- Sharing of best practices
- Educational content

❌ **Not Allowed:**
- Spam or self-promotion
- Off-topic discussions
- Harassment or personal attacks
- Sharing pirated software
- Plagiarism or copyright violation

## Consequences
- First violation: Warning
- Repeated violations: Temporary suspension
- Serious violations: Permanent ban

## Reporting
Use the report button to flag inappropriate content.

## Questions?
Contact moderators@mechamap.vn";
    }

    private function getAboutContent(): string
    {
        return "# About MechaMap

## Our Story
MechaMap was founded in 2024 with a vision to create the premier online community for mechanical engineers in Vietnam and Southeast Asia.

## Mission
To empower mechanical engineers through knowledge sharing, collaboration, and professional development.

## What We Offer
- **Technical Forums** - Specialized discussion areas
- **Project Showcase** - Share your engineering projects
- **Learning Resources** - Tutorials and guides
- **Professional Network** - Connect with peers
- **Career Support** - Job opportunities and advice

## Our Community
- 1000+ Active Engineers
- 50+ Companies Represented
- 500+ Projects Showcased
- 24/7 Community Support

## Values
- **Excellence** in engineering practices
- **Innovation** in problem-solving
- **Collaboration** across disciplines
- **Continuous Learning** and improvement

## Team
Our team consists of experienced mechanical engineers, software developers, and community managers dedicated to serving the engineering community.

## Contact
- Email: info@mechamap.vn
- Phone: +84 123 456 789
- Address: Ho Chi Minh City, Vietnam";
    }

    private function getContactContent(): string
    {
        return "# Contact Us

## Get in Touch
We'd love to hear from you! Reach out to us through any of the following channels:

## General Inquiries
- **Email:** info@mechamap.vn
- **Phone:** +84 123 456 789
- **Response Time:** Within 24 hours

## Technical Support
- **Email:** support@mechamap.vn
- **Forum:** Technical Support section
- **Response Time:** Within 12 hours

## Business Partnerships
- **Email:** partnerships@mechamap.vn
- **Phone:** +84 987 654 321

## Media & Press
- **Email:** media@mechamap.vn

## Office Address
MechaMap Headquarters
123 Engineering Street
District 1, Ho Chi Minh City
Vietnam

## Office Hours
- Monday - Friday: 9:00 AM - 6:00 PM (GMT+7)
- Saturday: 9:00 AM - 12:00 PM (GMT+7)
- Sunday: Closed

## Social Media
- LinkedIn: /company/mechamap
- Facebook: /mechamap.vietnam
- YouTube: /mechamap

## Feedback
Your feedback helps us improve. Share your thoughts at feedback@mechamap.vn";
    }

    private function getGettingStartedContent(): string
    {
        return "# Getting Started with MechaMap

Welcome to MechaMap! This guide will help you get the most out of our community.

## Step 1: Complete Your Profile
- Add a professional photo
- Write a brief bio about your experience
- Specify your engineering specializations
- Add your location and company (optional)

## Step 2: Explore Forums
- Browse different forum categories
- Read community guidelines
- Observe discussions before participating

## Step 3: Start Participating
- Ask thoughtful questions
- Share your knowledge and experience
- Provide helpful answers to others
- Use proper technical terminology

## Step 4: Share Projects
- Upload your engineering projects to Showcase
- Include detailed descriptions and documentation
- Add CAD files, calculations, and photos
- Engage with feedback from the community

## Best Practices
- Search before posting to avoid duplicates
- Use clear, descriptive titles
- Include relevant details and context
- Be respectful and professional
- Follow up on your posts

## Getting Help
- Check our FAQ section
- Use the search function
- Ask in the appropriate forum
- Contact support if needed

## Community Features
- Like and bookmark useful content
- Follow interesting users and threads
- Participate in polls and discussions
- Rate and review showcased projects

Ready to start? Jump into the forums and introduce yourself!";
    }

    private function getShowcaseGuideContent(): string
    {
        return "# How to Upload Your Engineering Showcase

Share your engineering projects with the MechaMap community!

## Before You Start
- Ensure you have rights to share the project
- Prepare high-quality images and documentation
- Gather CAD files, calculations, and reports

## Step-by-Step Upload Process

### 1. Access Showcase Section
- Go to your profile
- Click 'My Showcases'
- Select 'Create New Showcase'

### 2. Project Information
- **Title:** Clear, descriptive project name
- **Description:** Detailed project overview
- **Category:** Select appropriate category
- **Industry:** Choose relevant industry

### 3. Technical Details
- **Software Used:** List CAD/analysis software
- **Materials:** Specify materials and grades
- **Manufacturing Process:** Describe processes used
- **Timeline:** Project duration and milestones

### 4. Media Upload
- **Cover Image:** High-quality main image
- **Gallery:** Additional photos and renders
- **CAD Files:** Native and neutral formats
- **Documents:** Reports, calculations, drawings

### 5. Settings
- **Visibility:** Public or private
- **Downloads:** Allow file downloads
- **Comments:** Enable community feedback
- **License:** Specify usage rights

## File Format Guidelines
- **Images:** JPG, PNG (max 10MB each)
- **CAD Files:** STEP, IGES, native formats
- **Documents:** PDF, DOCX, XLSX
- **Total Size:** 100MB per showcase

## Best Practices
- Use professional photography
- Include multiple views and details
- Write comprehensive descriptions
- Add technical specifications
- Respond to community feedback

## Showcase Categories
- Mechanical Design
- Manufacturing
- Analysis & Simulation
- Prototyping
- Research Projects
- Student Projects

## Getting Featured
High-quality showcases may be featured on our homepage. Criteria include:
- Technical excellence
- Clear documentation
- Professional presentation
- Community engagement

Need help? Contact showcase@mechamap.vn";
    }

    private function getIntellectualPropertyContent(): string
    {
        return "# Chính sách sở hữu trí tuệ MechaMap

**Có hiệu lực từ:** 01/01/2024
**Cập nhật lần cuối:** 24/06/2025

## 1. Tổng quan

MechaMap tôn trọng quyền sở hữu trí tuệ và cam kết bảo vệ quyền lợi của cả người sáng tạo và người sử dụng. Chính sách này quy định rõ ràng về quyền và trách nhiệm liên quan đến nội dung trên nền tảng.

## 2. Quyền sở hữu nội dung MechaMap

### 2.1 Tài sản trí tuệ của MechaMap
- **Thương hiệu:** Logo, tên thương hiệu \"MechaMap\"
- **Giao diện:** Thiết kế website, mobile app
- **Mã nguồn:** Code, database structure, algorithms
- **Nội dung gốc:** Hướng dẫn, tutorial do MechaMap tạo ra

### 2.2 Bảo vệ quyền
- Mọi sử dụng trái phép sẽ bị xử lý theo pháp luật
- Người dùng không được sao chép, phân phối mà không có sự cho phép
- Cấm sử dụng logo, thương hiệu cho mục đích thương mại

## 3. Quyền sở hữu nội dung người dùng

### 3.1 Nguyên tắc cơ bản
- **Bạn giữ quyền sở hữu** toàn bộ nội dung mình tạo ra
- **Bạn chịu trách nhiệm** đảm bảo nội dung không vi phạm bản quyền
- **MechaMap không sở hữu** nội dung người dùng đăng tải

### 3.2 Giấy phép sử dụng cho MechaMap
Khi đăng nội dung, bạn cấp cho MechaMap quyền:
- **Hiển thị:** Trên website và mobile app
- **Lưu trữ:** Trong cơ sở dữ liệu
- **Sao lưu:** Để đảm bảo an toàn dữ liệu
- **Chia sẻ:** Theo cài đặt riêng tư của bạn
- **Tối ưu hóa:** Nén ảnh, format text để hiển thị tốt hơn

### 3.3 Giới hạn quyền của MechaMap
MechaMap **KHÔNG** có quyền:
- Bán nội dung của bạn cho bên thứ ba
- Sử dụng cho mục đích thương mại mà không có sự đồng ý
- Chỉnh sửa nội dung mà thay đổi ý nghĩa gốc
- Cấp quyền cho người khác sử dụng nội dung của bạn

## 4. Nội dung được chia sẻ

### 4.1 Showcase Projects
- **Quyền tác giả:** Thuộc về người tạo ra
- **Quyền tải xuống:** Theo cài đặt của người đăng
- **Sử dụng thương mại:** Cần xin phép trực tiếp từ tác giả
- **Modification:** Chỉ với sự cho phép của tác giả

### 4.2 CAD Files và Technical Documents
- **Bản quyền thiết kế:** Thuộc về người thiết kế
- **Sử dụng học tập:** Được khuyến khích
- **Sử dụng thương mại:** Cần license từ tác giả
- **Reverse engineering:** Tuân theo pháp luật về sở hữu trí tuệ

### 4.3 Code và Scripts
- **Open source:** Nếu tác giả chọn license mở
- **Proprietary:** Bảo vệ theo quyền tác giả
- **Attribution:** Luôn ghi nguồn khi sử dụng
- **Modification:** Theo điều khoản license cụ thể

## 5. Bảo vệ bản quyền

### 5.1 DMCA Compliance
MechaMap tuân thủ Digital Millennium Copyright Act:
- **Thông báo vi phạm:** Quy trình báo cáo rõ ràng
- **Xử lý nhanh chóng:** Trong vòng 24-48 giờ
- **Counter-notice:** Quyền phản bác của người bị tố cáo
- **Repeat offender:** Khóa tài khoản vi phạm nhiều lần

### 5.2 Quy trình báo cáo vi phạm bản quyền
**Bước 1:** Gửi thông báo đến copyright@mechamap.vn
**Bước 2:** Cung cấp thông tin:
- Mô tả tác phẩm bị vi phạm
- URL nội dung vi phạm trên MechaMap
- Thông tin liên hệ của bạn
- Tuyên bố về quyền sở hữu
- Chữ ký (điện tử hoặc vật lý)

**Bước 3:** MechaMap sẽ:
- Xem xét thông báo trong 24h
- Gỡ bỏ nội dung vi phạm nếu hợp lệ
- Thông báo cho người đăng
- Ghi nhận vi phạm vào hồ sơ

## 6. Sử dụng hợp lý (Fair Use)

### 6.1 Mục đích giáo dục
- **Trích dẫn:** Cho mục đích học tập, nghiên cứu
- **Phân tích:** Đánh giá, bình luận kỹ thuật
- **Parody:** Sáng tác dựa trên tác phẩm gốc
- **News reporting:** Báo cáo tin tức, sự kiện

### 6.2 Điều kiện sử dụng hợp lý
- **Ghi nguồn:** Luôn cite tác giả và nguồn gốc
- **Không thương mại:** Chỉ cho mục đích cá nhân, học tập
- **Tỷ lệ hợp lý:** Không sử dụng toàn bộ tác phẩm
- **Không ảnh hưởng:** Đến giá trị thương mại của tác phẩm gốc

## 7. Trademark và thương hiệu

### 7.1 Thương hiệu của MechaMap
- **MechaMap®:** Thương hiệu đã đăng ký
- **Logo và biểu tượng:** Được bảo vệ bởi luật thương hiệu
- **Slogan:** \"Connecting Mechanical Engineers\"

### 7.2 Sử dụng thương hiệu
- **Được phép:** Đề cập trong bài viết, nghiên cứu
- **Cần xin phép:** Sử dụng trong tài liệu thương mại
- **Cấm:** Tạo confusion về nguồn gốc sản phẩm/dịch vụ

## 8. Patent và sáng chế

### 8.1 Chia sẻ ý tưởng sáng chế
- **Rủi ro:** Có thể mất quyền đăng ký patent
- **Khuyến nghị:** Đăng ký bảo hộ trước khi chia sẻ
- **NDA:** Sử dụng thỏa thuận bảo mật khi cần

### 8.2 Prior art và công bố
- **Tìm kiếm:** Kiểm tra prior art trước khi claim
- **Công bố:** Nội dung công khai có thể làm prior art
- **Tư vấn:** Liên hệ luật sư sở hữu trí tuệ khi cần

## 9. Tranh chấp và giải quyết

### 9.1 Quy trình nội bộ
- **Thương lượng:** Khuyến khích giải quyết hòa bình
- **Mediation:** Trung gian hòa giải
- **Arbitration:** Trọng tài nếu cần thiết

### 9.2 Pháp luật áp dụng
- **Luật Việt Nam:** Luật Sở hữu trí tuệ 2005 (sửa đổi 2019)
- **Luật quốc tế:** Công ước Berne, TRIPS Agreement
- **Thẩm quyền:** Tòa án TP.HCM

## 10. Hỗ trợ và tư vấn

### 10.1 Dịch vụ hỗ trợ
- **Tư vấn cơ bản:** Miễn phí cho thành viên
- **Hướng dẫn đăng ký:** Bản quyền, thương hiệu
- **Kết nối chuyên gia:** Luật sư sở hữu trí tuệ

### 10.2 Liên hệ
- **Email:** ip@mechamap.vn
- **Hotline:** 1900 1234 (ext. 3)
- **Tư vấn trực tuyến:** Thứ 2-6, 9h-17h
- **Workshop:** Định kỳ hàng tháng về IP

## 11. Cập nhật chính sách

Chính sách này có thể được cập nhật để phù hợp với:
- Thay đổi pháp luật
- Phát triển công nghệ mới
- Feedback từ cộng đồng
- Best practices quốc tế

Mọi thay đổi sẽ được thông báo trước 30 ngày.

---

**Lưu ý quan trọng:** Đây là hướng dẫn chung. Đối với các vấn đề phức tạp, vui lòng tham khảo ý kiến luật sư chuyên ngành sở hữu trí tuệ.";
    }
}
