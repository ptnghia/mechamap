<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ComingSoonController extends Controller
{
    /**
     * Hiển thị trang thông báo "Sắp ra mắt" với thông tin tính năng
     */
    public function show(Request $request)
    {
        // Lấy thông tin tính năng từ query parameters
        $feature = $request->get('feature', 'default');
        $featureData = $this->getFeatureData($feature);

        return view('coming-soon', [
            'title' => $featureData['title'],
            'description' => $featureData['description'],
            'features' => $featureData['features'],
            'progress' => $featureData['progress'],
            'timeline' => $featureData['timeline']
        ]);
    }

    /**
     * Lấy dữ liệu chi tiết cho từng tính năng
     */
    private function getFeatureData($feature)
    {
        $features = [
            'calculator' => [
                'title' => 'Máy Tính Kỹ Thuật',
                'message' => 'Công cụ tính toán cơ khí chuyên nghiệp đang được phát triển',
                'description' => 'Máy tính kỹ thuật tích hợp các công thức cơ khí phổ biến, hỗ trợ tính toán nhanh chóng và chính xác cho các kỹ sư.',
                'features' => [
                    'Tính toán độ bền vật liệu',
                    'Phân tích ứng suất',
                    'Tính toán bánh răng',
                    'Công thức nhiệt động lực học',
                    'Tính toán dầm và cột',
                    'Chuyển đổi đơn vị tự động'
                ],
                'progress' => 75,
                'timeline' => 'Q2 2025'
            ],
            'unit_converter' => [
                'title' => 'Chuyển Đổi Đơn Vị',
                'message' => 'Công cụ chuyển đổi đơn vị đo lường toàn diện',
                'description' => 'Hệ thống chuyển đổi đơn vị thông minh với hơn 500 đơn vị đo lường khác nhau, tối ưu cho ngành cơ khí.',
                'features' => [
                    'Chuyển đổi đơn vị chiều dài',
                    'Đơn vị khối lượng và lực',
                    'Áp suất và nhiệt độ',
                    'Tốc độ và gia tốc',
                    'Moment và công suất',
                    'Lưu lịch sử chuyển đổi'
                ],
                'progress' => 60,
                'timeline' => 'Q2 2025'
            ],
            'material_lookup' => [
                'title' => 'Bảng Tra Cứu Vật Liệu',
                'message' => 'Cơ sở dữ liệu vật liệu kỹ thuật toàn diện',
                'description' => 'Thư viện vật liệu với hơn 10,000 loại vật liệu, bao gồm thông số kỹ thuật chi tiết và ứng dụng thực tế.',
                'features' => [
                    'Thép các loại và hợp kim',
                    'Nhôm và kim loại màu',
                    'Vật liệu composite',
                    'Nhựa kỹ thuật',
                    'Ceramic và gốm',
                    'So sánh vật liệu'
                ],
                'progress' => 45,
                'timeline' => 'Q3 2025'
            ],
            'design_tools' => [
                'title' => 'Công Cụ Thiết Kế',
                'message' => 'Bộ công cụ hỗ trợ thiết kế CAD/CAM',
                'description' => 'Tập hợp các công cụ thiết kế thông minh giúp tối ưu hóa quy trình thiết kế và kiểm tra chất lượng.',
                'features' => [
                    'Kiểm tra thiết kế tự động',
                    'Tối ưu hóa topology',
                    'Phân tích DFM/DFA',
                    'Tạo bản vẽ kỹ thuật',
                    'Quản lý phiên bản',
                    'Tích hợp CAD plugins'
                ],
                'progress' => 30,
                'timeline' => 'Q4 2025'
            ],
            'mobile_app' => [
                'title' => 'Ứng Dụng Mobile',
                'message' => 'Ứng dụng MechaMap cho điện thoại và tablet',
                'description' => 'Ứng dụng di động cho phép truy cập MechaMap mọi lúc mọi nơi với đầy đủ tính năng và giao diện tối ưu.',
                'features' => [
                    'Giao diện responsive',
                    'Thông báo push',
                    'Chế độ offline',
                    'Camera scan QR',
                    'Voice search',
                    'Dark mode'
                ],
                'progress' => 85,
                'timeline' => 'Beta Q1 2025'
            ],
            'api_integration' => [
                'title' => 'API & Integrations',
                'message' => 'Hệ thống API và tích hợp với các công cụ khác',
                'description' => 'API mạnh mẽ cho phép tích hợp MechaMap với các hệ thống ERP, CAD software và công cụ quản lý dự án.',
                'features' => [
                    'RESTful API',
                    'Webhook support',
                    'OAuth 2.0',
                    'Rate limiting',
                    'API documentation',
                    'SDK cho các ngôn ngữ'
                ],
                'progress' => 90,
                'timeline' => 'Q1 2025'
            ],
            'gallery' => [
                'title' => 'Thư Viện Ảnh',
                'message' => 'Hệ thống chia sẻ hình ảnh kỹ thuật',
                'description' => 'Nền tảng chia sẻ và quản lý hình ảnh kỹ thuật với tính năng tìm kiếm thông minh và phân loại tự động.',
                'features' => [
                    'Upload hình ảnh HD',
                    'Phân loại tự động',
                    'Tìm kiếm bằng hình ảnh',
                    'Watermark bảo vệ',
                    'Album và collection',
                    'Chia sẻ xã hội'
                ],
                'progress' => 40,
                'timeline' => 'Q3 2025'
            ],
            'marketplace_products' => [
                'title' => 'Marketplace Sản Phẩm',
                'message' => 'Nền tảng mua bán sản phẩm kỹ thuật',
                'description' => 'Thị trường trực tuyến cho các sản phẩm kỹ thuật, phần mềm CAD, và dịch vụ tư vấn chuyên nghiệp.',
                'features' => [
                    'Sản phẩm kỹ thuật số',
                    'Phần mềm và plugin',
                    'Dịch vụ tư vấn',
                    'Thanh toán an toàn',
                    'Đánh giá và review',
                    'Hỗ trợ khách hàng'
                ],
                'progress' => 55,
                'timeline' => 'Q2 2025'
            ],
            'seller_setup' => [
                'title' => 'Đăng Ký Nhà Cung Cấp',
                'message' => 'Hệ thống đăng ký và quản lý nhà cung cấp',
                'description' => 'Quy trình đăng ký trở thành nhà cung cấp trên MechaMap với các công cụ quản lý bán hàng chuyên nghiệp.',
                'features' => [
                    'Xác thực nhà cung cấp',
                    'Dashboard quản lý',
                    'Báo cáo doanh thu',
                    'Quản lý đơn hàng',
                    'Chính sách bán hàng',
                    'Hỗ trợ marketing'
                ],
                'progress' => 50,
                'timeline' => 'Q2 2025'
            ],
            'documents' => [
                'title' => 'Tài Liệu Kỹ Thuật',
                'message' => 'Hệ thống quản lý tài liệu kỹ thuật',
                'description' => 'Nền tảng tạo, chia sẻ và quản lý tài liệu kỹ thuật với editor markdown và hỗ trợ LaTeX.',
                'features' => [
                    'Editor markdown',
                    'Hỗ trợ LaTeX',
                    'Version control',
                    'Collaborative editing',
                    'Export PDF/Word',
                    'Template library'
                ],
                'progress' => 35,
                'timeline' => 'Q3 2025'
            ],
            'mentorship' => [
                'title' => 'Hệ Thống Mentor',
                'message' => 'Kết nối mentor và mentee trong ngành cơ khí',
                'description' => 'Nền tảng kết nối các chuyên gia kinh nghiệm với kỹ sư trẻ, hỗ trợ phát triển nghề nghiệp.',
                'features' => [
                    'Matching algorithm',
                    'Video call tích hợp',
                    'Lịch hẹn thông minh',
                    'Theo dõi tiến độ',
                    'Đánh giá mentor',
                    'Chương trình đào tạo'
                ],
                'progress' => 25,
                'timeline' => 'Q4 2025'
            ],
            'jobs' => [
                'title' => 'Cơ Hội Việc Làm',
                'message' => 'Nền tảng tuyển dụng chuyên ngành cơ khí',
                'description' => 'Hệ thống tuyển dụng chuyên biệt cho ngành cơ khí với AI matching và đánh giá kỹ năng.',
                'features' => [
                    'AI job matching',
                    'Skill assessment',
                    'Video interview',
                    'Portfolio showcase',
                    'Salary insights',
                    'Career path guidance'
                ],
                'progress' => 20,
                'timeline' => 'Q4 2025'
            ],
            'events' => [
                'title' => 'Sự Kiện & Hội Thảo',
                'message' => 'Hệ thống quản lý sự kiện và hội thảo',
                'description' => 'Nền tảng tổ chức và tham gia các sự kiện, hội thảo kỹ thuật trực tuyến và offline.',
                'features' => [
                    'Tổ chức sự kiện',
                    'Live streaming',
                    'Networking tools',
                    'Certificate system',
                    'Event analytics',
                    'Sponsor management'
                ],
                'progress' => 15,
                'timeline' => 'Q4 2025'
            ],
            'business_connect' => [
                'title' => 'Kết Nối Doanh Nghiệp',
                'message' => 'Mạng lưới đối tác kinh doanh',
                'description' => 'Nền tảng kết nối các doanh nghiệp trong ngành cơ khí để hợp tác và phát triển kinh doanh.',
                'features' => [
                    'Company profiles',
                    'Partnership matching',
                    'Deal pipeline',
                    'Contract management',
                    'Communication tools',
                    'Analytics dashboard'
                ],
                'progress' => 30,
                'timeline' => 'Q3 2025'
            ],
            'faq' => [
                'title' => 'FAQ & Hướng Dẫn',
                'message' => 'Hệ thống câu hỏi thường gặp và hướng dẫn',
                'description' => 'Cơ sở tri thức toàn diện với AI chatbot hỗ trợ trả lời câu hỏi tự động.',
                'features' => [
                    'AI chatbot',
                    'Search functionality',
                    'Video tutorials',
                    'Step-by-step guides',
                    'Community Q&A',
                    'Multi-language support'
                ],
                'progress' => 40,
                'timeline' => 'Q2 2025'
            ],
            'contact' => [
                'title' => 'Hỗ Trợ Khách Hàng',
                'message' => 'Hệ thống hỗ trợ khách hàng 24/7',
                'description' => 'Dịch vụ hỗ trợ khách hàng chuyên nghiệp với nhiều kênh liên lạc và thời gian phản hồi nhanh.',
                'features' => [
                    'Live chat 24/7',
                    'Ticket system',
                    'Phone support',
                    'Remote assistance',
                    'Priority support',
                    'Satisfaction tracking'
                ],
                'progress' => 60,
                'timeline' => 'Q2 2025'
            ],
            'about' => [
                'title' => 'Về MechaMap',
                'message' => 'Thông tin chi tiết về nền tảng',
                'description' => 'Trang thông tin toàn diện về MechaMap, tầm nhìn, sứ mệnh và đội ngũ phát triển.',
                'features' => [
                    'Company story',
                    'Team profiles',
                    'Mission & vision',
                    'Technology stack',
                    'Press releases',
                    'Contact information'
                ],
                'progress' => 70,
                'timeline' => 'Q1 2025'
            ]
        ];

        // Trả về dữ liệu mặc định nếu không tìm thấy tính năng
        return $features[$feature] ?? [
            'title' => 'Tính Năng Mới',
            'message' => 'Chúng tôi đang phát triển tính năng mới thú vị',
            'description' => 'Tính năng này đang được phát triển để mang lại trải nghiệm tốt nhất cho cộng đồng kỹ sư cơ khí.',
            'features' => [
                'Giao diện thân thiện',
                'Hiệu suất cao',
                'Bảo mật tốt',
                'Tích hợp dễ dàng'
            ],
            'progress' => 50,
            'timeline' => 'Q2 2025'
        ];
    }
}
