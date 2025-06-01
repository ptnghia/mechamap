<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Comment;
use App\Models\CommentLike;
use App\Models\Forum;
use App\Models\Media;
use App\Models\Poll;
use App\Models\PollOption;
use App\Models\PollVote;
use App\Models\Thread;
use App\Models\ThreadFollow;
use App\Models\ThreadLike;
use App\Models\ThreadSave;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class ThreadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        $users = User::all();
        $categories = Category::all();
        $forums = Forum::all();

        // Tạo dữ liệu demo cho các loại threads khác nhau
        $this->createNewsThreads($faker, $users, $categories, $forums);
        $this->createProjectThreads($faker, $users, $categories, $forums);
        $this->createArchitectureThreads($faker, $users, $categories, $forums);
        $this->createUrbanPlanningThreads($faker, $users, $categories, $forums);
        $this->createQAThreads($faker, $users, $categories, $forums);
        $this->createShowcaseThreads($faker, $users, $categories, $forums);
    }

    /**
     * Tạo threads tin tức
     */
    private function createNewsThreads($faker, $users, $categories, $forums): void
    {
        $newsCategory = Category::where('slug', 'tin-tuc')->first();
        if (!$newsCategory) {
            $newsCategory = $categories->first();
        }

        $newsForum = Forum::where('name', 'News & Announcements')->first();
        if (!$newsForum) {
            $newsForum = $forums->first();
        }

        $newsTitles = [
            "Tin tức: Robot ABB mới ra mắt tại triển lãm Công nghiệp 2024",
            "Tin tức: Công nghệ AI được tích hợp vào hệ thống sản xuất thông minh",
            "Tin tức: Siemens ra mắt PLC S7-1500 thế hệ mới với tính năng nâng cao",
            "Tin tức: Fanuc phát triển robot tốc độ cao cho ngành điện tử",
            "Tin tức: Industry 4.0 đang thay đổi cách thức sản xuất toàn cầu"
        ];

        $newsContents = [
            "Robot ABB mới ra mắt tại triển lãm Công nghiệp 2024 với nhiều tính năng vượt trội. Robot IRB 14000 YuMi được trang bị AI và vision system tiên tiến, có thể làm việc an toàn cùng con người. Với tốc độ và độ chính xác cao, robot này phù hợp cho ứng dụng lắp ráp linh kiện điện tử và các sản phẩm nhỏ gọn. Giá thành cạnh tranh hơn so với thế hệ trước 20%, dự kiến sẽ có mặt tại thị trường Việt Nam trong quý 3/2024.",

            "Công nghệ trí tuệ nhân tạo (AI) đang được tích hợp ngày càng nhiều vào các hệ thống sản xuất thông minh. Predictive maintenance giúp giảm 35% thời gian downtime, machine learning tối ưu hóa quy trình sản xuất và computer vision kiểm tra chất lượng tự động. Nhiều nhà máy tại Việt Nam đã bắt đầu triển khai AI với kết quả khả quan.",

            "Siemens vừa chính thức ra mắt dòng PLC S7-1500 thế hệ mới với nhiều cải tiến đáng chú ý. CPU mới có tốc độ xử lý nhanh hơn 40%, bộ nhớ lớn hơn và hỗ trợ OPC UA native. TIA Portal v18 đi kèm có giao diện thân thiện hơn và nhiều function block mới. Đặc biệt, tính năng cybersecurity được tăng cường mạnh mẽ.",

            "Fanuc đã phát triển thành công robot tốc độ cao chuyên dụng cho ngành điện tử. Robot M-1iD/0.5S có thể thực hiện 200 chu kỳ pick & place mỗi phút với độ chính xác ±0.01mm. Thiết kế compact và clean room ready, phù hợp cho sản xuất smartphone và linh kiện điện tử. Dự kiến sẽ thương mại hóa vào đầu năm 2025.",

            "Industry 4.0 đang từng bước thay đổi toàn bộ cách thức sản xuất trên toàn cầu. IoT sensors, big data analytics, cloud computing và autonomous systems tạo nên cách mạng công nghiệp lần thứ 4. Tại Việt Nam, nhiều doanh nghiệp FDI đã đầu tư mạnh vào smart factory. Chính phủ cũng khuyến khích chuyển đổi số trong sản xuất qua các chính sách ưu đãi."
        ];

        for ($i = 0; $i < 5; $i++) {
            $title = $newsTitles[$i];
            $content = $newsContents[$i];
            $user = $users->random();

            $thread = Thread::create([
                'title' => $title,
                'slug' => Str::slug($title) . '-' . Str::random(5),
                'content' => $content,
                'user_id' => $user->id,
                'category_id' => $newsCategory->id,
                'forum_id' => $newsForum->id,
                'is_sticky' => $faker->boolean(20),
                'is_locked' => $faker->boolean(10),
                'is_featured' => $faker->boolean(30),
                'view_count' => $faker->numberBetween(50, 2000),
            ]);

            // Tạo comments
            $this->createComments($faker, $thread, $users);

            // Tạo likes, saves, follows
            $this->createInteractions($faker, $thread, $users);
        }
    }

    /**
     * Tạo threads dự án
     */
    private function createProjectThreads($faker, $users, $categories, $forums): void
    {
        $projectCategory = Category::where('slug', 'du-an')->first();
        if (!$projectCategory) {
            $projectCategory = $categories->first();
        }

        $projectForums = [];
        $hardware = Forum::where('name', 'Hardware')->first();
        $software = Forum::where('name', 'Software')->first();

        if ($hardware) {
            $projectForums[] = $hardware;
        }

        if ($software) {
            $projectForums[] = $software;
        }

        if (empty($projectForums)) {
            $projectForums = [$forums->first()];
        }

        $statuses = ['Đề xuất', 'Đã phê duyệt', 'Đang xây dựng', 'Hoàn thành', 'Tạm dừng', 'Đã hủy'];

        // Nội dung dự án mẫu về cơ khí và tự động hóa
        $projectTitles = [
            "Dự án: Hệ thống băng tải tự động cho nhà máy sản xuất",
            "Dự án: Robot lắp ráp linh kiện điện tử tự động",
            "Dự án: Hệ thống kiểm soát chất lượng bằng vision system",
            "Dự án: Máy CNC 5 trục gia công chi tiết phức tạp",
            "Dự án: Dây chuyền sơn tự động cho ngành ô tô",
            "Dự án: Hệ thống SCADA giám sát nhà máy",
            "Dự án: Robot hàn TIG tự động cho kết cấu thép",
            "Dự án: Máy đóng gói tự động đa sản phẩm",
            "Dự án: Hệ thống cấp liệu tự động cho máy ép nhựa",
            "Dự án: AGV vận chuyển hàng hóa trong kho"
        ];

        $projectContents = [
            "Thiết kế và triển khai hệ thống băng tải tự động với PLC Siemens S7-1200. Hệ thống bao gồm 3 tầng băng tải, sensor cảm biến vị trí, động cơ servo và HMI điều khiển. Tốc độ vận chuyển có thể điều chỉnh từ 0.5-2m/s. Được tích hợp safety circuit đảm bảo an toàn vận hành.",

            "Phát triển robot 6 DOF sử dụng servo motor Mitsubishi để lắp ráp linh kiện điện tử. Robot có thể thực hiện 15 chu kỳ lắp ráp/phút với độ chính xác ±0.05mm. Tích hợp camera vision để nhận diện và định vị linh kiện. Giao diện lập trình đơn giản cho operator.",

            "Xây dựng hệ thống kiểm tra chất lượng tự động sử dụng camera Cognex và phần mềm VisionPro. Hệ thống có thể phát hiện lỗi kích thước, màu sắc, vết xước với độ chính xác 99.5%. Tốc độ kiểm tra 200 sản phẩm/phút. Tích hợp với database để lưu trữ kết quả kiểm tra.",

            "Cài đặt và vận hành máy CNC DMG MORI 5 trục gia công các chi tiết hàng không vũ trụ. Sử dụng CAM PowerMill để tạo chương trình gia công. Độ chính xác đạt IT7, bề mặt Ra 0.8μm. Trang bị hệ thống làm mát qua tâm và tự động thay dao.",

            "Thiết kế dây chuyền sơn tự động cho ngành ô tô gồm cabin sơn, robot sơn ABB, hệ thống thu hồi sơn và lò sấy. Công suất 50 xe/ngày. Tuân thủ tiêu chuẩn môi trường và an toàn lao động. Tiết kiệm 30% sơn so với sơn thủ công.",

            "Triển khai hệ thống SCADA WinCC để giám sát và điều khiển toàn bộ nhà máy sản xuất. Tích hợp 50 PLC, 200 I/O point và 15 màn hình HMI. Có chức năng báo động, trending, báo cáo và backup dữ liệu tự động. Giao diện thân thiện với người dùng.",

            "Phát triển robot hàn TIG tự động cho kết cấu thép sử dụng robot Fanuc R-2000iC. Tích hợp sensor tracking để bám theo đường hàn. Chất lượng mối hàn đạt chuẩn AWS D1.1. Tốc độ hàn 25cm/phút, giảm 60% thời gian so với hàn thủ công.",

            "Thiết kế máy đóng gói tự động có thể xử lý nhiều loại sản phẩm khác nhau. Sử dụng PLC Allen-Bradley và servo motor. Tốc độ đóng gói 120 hộp/phút. Tự động thay kích thước hộp, dán nhãn và in date code. Tích hợp cân điện tử kiểm tra trọng lượng.",

            "Xây dựng hệ thống cấp liệu tự động cho máy ép nhựa injection molding. Bao gồm silo chứa nhựa, máy sấy, máy trộn màu và robot cấp liệu. Đảm bảo chất lượng nhựa ổn định và giảm thất thoát. Tự động báo cáo lượng nguyên liệu tiêu thụ.",

            "Triển khai Automated Guided Vehicle (AGV) cho kho hàng với khả năng tự động điều hướng bằng laser navigation. Fleet 10 AGV có thể vận chuyển 500kg/xe. Tích hợp với WMS để tối ưu hóa tuyến đường. Sạc pin tự động khi không sử dụng."
        ];

        for ($i = 0; $i < 10; $i++) {
            $title = $projectTitles[$i];
            $content = $projectContents[$i];
            $user = $users->random();
            $forum = $faker->randomElement($projectForums);

            $thread = Thread::create([
                'title' => $title,
                'slug' => Str::slug($title) . '-' . Str::random(5),
                'content' => $content,
                'user_id' => $user->id,
                'category_id' => $projectCategory->id,
                'forum_id' => $forum->id,
                'status' => $faker->randomElement($statuses),
                'is_sticky' => $faker->boolean(20),
                'is_locked' => $faker->boolean(10),
                'is_featured' => $faker->boolean(30),
                'view_count' => $faker->numberBetween(100, 5000),
            ]);

            // Tạo comments
            $this->createComments($faker, $thread, $users);

            // Tạo likes, saves, follows
            $this->createInteractions($faker, $thread, $users);

            // Tạo media
            $this->createMedia($faker, $thread, $user);

            // Tạo poll
            if ($faker->boolean(30)) {
                $this->createPoll($faker, $thread, $users);
            }
        }
    }

    /**
     * Tạo threads kiến trúc
     */
    private function createArchitectureThreads($faker, $users, $categories, $forums): void
    {
        $architectureCategory = Category::where('slug', 'kien-truc')->first();
        if (!$architectureCategory) {
            $architectureCategory = $categories->first();
        }

        $architectureForums = [];
        $events = Forum::where('name', 'Events')->first();
        $feedback = Forum::where('name', 'Feedback')->first();

        if ($events) {
            $architectureForums[] = $events;
        }

        if ($feedback) {
            $architectureForums[] = $feedback;
        }

        if (empty($architectureForums)) {
            $architectureForums = [$forums->first()];
        }

        // Nội dung kiến trúc mẫu về nhà máy và cơ sở hạ tầng công nghiệp
        $architectureTitles = [
            "Kiến trúc: Thiết kế nhà máy sản xuất linh kiện điện tử hiện đại",
            "Kiến trúc: Kho hàng tự động với hệ thống AS/RS",
            "Kiến trúc: Trung tâm R&D robot và AI",
            "Kiến trúc: Nhà máy sản xuất pin lithium thông minh",
            "Kiến trúc: Cơ sở đào tạo kỹ thuật công nghiệp 4.0"
        ];

        $architectureContents = [
            "Thiết kế nhà máy sản xuất linh kiện điện tử với diện tích 50,000m2, đáp ứng tiêu chuẩn ISO 14644 Clean Room Class 1000. Bao gồm khu vực SMT, thử nghiệm, kho nguyên liệu và thành phẩm. Hệ thống HVAC đặc biệt với filtration 99.99% và kiểm soát nhiệt độ ±1°C. Thiết kế modular cho phép mở rộng dễ dàng.",

            "Kho hàng tự động 15 tầng với hệ thống AS/RS (Automated Storage and Retrieval System) của Dematic. Sức chứa 50,000 pallet, thông lượng 1,200 pallet/giờ. Tích hợp AGV, robot picking và WMS. Giảm 80% nhân lực, tăng 300% hiệu quả so với kho truyền thống. Hệ thống chống cháy nổ FM 200.",

            "Trung tâm R&D robot và AI với phòng lab sạch, phòng thử nghiệm robot, khu vực assembly và testing. Trang bị máy CNC 5 trục, máy in 3D kim loại, hệ thống motion capture và supercomputer. Không gian làm việc mở khuyến khích sáng tạo. Hệ thống bảo mật cao bảo vệ IP.",

            "Nhà máy sản xuất pin lithium công suất 10GWh/năm với công nghệ LFP và NCM. Dây chuyền tự động hóa 95% từ mixing đến packaging. Hệ thống kiểm soát môi trường nghiêm ngặt với độ ẩm <1%. Recycling 95% nước thải. Đạt chuẩn ISO 45001 về an toàn lao động.",

            "Cơ sở đào tạo kỹ thuật công nghiệp 4.0 với 20 phòng lab chuyên biệt: PLC/SCADA, robot, CNC, IoT, AI/ML. Trang bị thiết bị mới nhất từ Siemens, ABB, Fanuc, DMG MORI. Digital twin factory cho thực hành. Khóa học từ certificate đến master degree. Partnership với các tập đoàn công nghiệp."
        ];

        for ($i = 0; $i < 5; $i++) {
            $title = $architectureTitles[$i];
            $content = $architectureContents[$i];
            $user = $users->random();
            $forum = $faker->randomElement($architectureForums);

            $thread = Thread::create([
                'title' => $title,
                'slug' => Str::slug($title) . '-' . Str::random(5),
                'content' => $content,
                'user_id' => $user->id,
                'category_id' => $architectureCategory->id,
                'forum_id' => $forum->id,
                'is_sticky' => $faker->boolean(20),
                'is_locked' => $faker->boolean(10),
                'is_featured' => $faker->boolean(30),
                'view_count' => $faker->numberBetween(50, 2000),
            ]);

            // Tạo comments
            $this->createComments($faker, $thread, $users);

            // Tạo likes, saves, follows
            $this->createInteractions($faker, $thread, $users);

            // Tạo media
            $this->createMedia($faker, $thread, $user);
        }
    }

    /**
     * Tạo threads quy hoạch đô thị
     */
    private function createUrbanPlanningThreads($faker, $users, $categories, $forums): void
    {
        $urbanPlanningCategory = Category::where('slug', 'quy-hoach-do-thi')->first();
        if (!$urbanPlanningCategory) {
            $urbanPlanningCategory = $categories->first();
        }

        $urbanPlanningForums = [];
        $programming = Forum::where('name', 'Programming')->first();
        $mobile = Forum::where('name', 'Mobile')->first();

        if ($programming) {
            $urbanPlanningForums[] = $programming;
        }

        if ($mobile) {
            $urbanPlanningForums[] = $mobile;
        }

        if (empty($urbanPlanningForums)) {
            $urbanPlanningForums = [$forums->first()];
        }

        // Nội dung quy hoạch mẫu về khu công nghiệp và cơ sở hạ tầng
        $urbanPlanningTitles = [
            "Quy hoạch: Khu công nghiệp thông minh VSIP III Hải Phòng",
            "Quy hoạch: Trung tâm logistics và phân phối miền Bắc",
            "Quy hoạch: Khu công nghệ cao FPT Software Đà Nẵng",
            "Quy hoạch: Cảng tự động container Cái Mép - Thị Vải",
            "Quy hoạch: Thành phố thông minh Đông Anh - Hà Nội"
        ];

        $urbanPlanningContents = [
            "Quy hoạch khu công nghiệp thông minh VSIP III Hải Phòng diện tích 1,500 ha với 4 cụm chức năng: sản xuất, logistics, R&D và khu dân cư. Tích hợp IoT sensors, 5G network và AI management system. Hệ thống giao thông thông minh với traffic light adaptive. Smart grid và renewable energy 40%. Xử lý nước thải 100% đạt chuẩn QCVN 40:2011.",

            "Trung tâm logistics Việt Trì với diện tích 500 ha, kết nối đường bộ - đường sắt - đường thủy. Kho hàng tự động AS/RS, hệ thống sorting 50,000 package/giờ, AGV fleet và drone delivery. Cold chain facility -25°C cho nông sản. Customs clearance tự động 24/7. Công suất xử lý 10 triệu tấn hàng/năm.",

            "Khu công nghệ cao FPT Software Đà Nẵng quy mô 200 ha với 5 tòa nhà R&D, 10 phòng lab AI/IoT, data center Tier III và incubator startup. Thiết kế green building đạt LEED Platinum. Smart parking, digital twin quản lý tòa nhà. Kết nối trực tiếp với sân bay và cảng Tiên Sa.",

            "Cảng container tự động Cái Mép với 20 cần cẩu quay RTG, 50 AGV và automated stacking crane. Hệ thống TOS (Terminal Operating System) tích hợp AI optimization. Năng lực xử lý 5 triệu TEU/năm. Kết nối rail-sea intermodal. Green port với wind power và solar panel.",

            "Thành phố thông minh Đông Anh quy mô 30,000 ha với 500,000 dân. Smart city platform tích hợp 15 domain: giao thông, y tế, giáo dục, môi trường, an ninh. 5G coverage 100%, fiber optic đến từng hộ gia đình. Autonomous public transport và sharing economy. Carbon neutral city 2040."
        ];



        for ($i = 0; $i < 5; $i++) {
            $title = $urbanPlanningTitles[$i];
            $content = $urbanPlanningContents[$i];
            $user = $users->random();
            $forum = $faker->randomElement($urbanPlanningForums);

            $thread = Thread::create([
                'title' => $title,
                'slug' => Str::slug($title) . '-' . Str::random(5),
                'content' => $content,
                'user_id' => $user->id,
                'category_id' => $urbanPlanningCategory->id,
                'forum_id' => $forum->id,
                'is_sticky' => $faker->boolean(20),
                'is_locked' => $faker->boolean(10),
                'is_featured' => $faker->boolean(30),
                'view_count' => $faker->numberBetween(50, 2000),
            ]);

            // Tạo comments
            $this->createComments($faker, $thread, $users);

            // Tạo likes, saves, follows
            $this->createInteractions($faker, $thread, $users);

            // Tạo poll
            if ($faker->boolean(50)) {
                $this->createPoll($faker, $thread, $users);
            }
        }
    }

    /**
     * Tạo threads hỏi đáp
     */
    private function createQAThreads($faker, $users, $categories, $forums): void
    {
        $qaCategory = Category::where('slug', 'hoi-dap')->first();
        if (!$qaCategory) {
            $qaCategory = $categories->first();
        }

        $qaForum = Forum::where('name', 'Help & Support')->first();
        if (!$qaForum) {
            $qaForum = $forums->first();
        }

        // Nội dung Q&A mẫu về kỹ thuật cơ khí
        $qaTitles = [
            "Hỏi đáp: Làm thế nào để chọn servo motor phù hợp cho robot pick & place?",
            "Hỏi đáp: PLC Siemens S7-1500 có tương thích với sensor Omron không?",
            "Hỏi đáp: Cách khắc phục vấn đề rung động trên máy CNC khi gia công?",
            "Hỏi đáp: HMI Schneider Electric có hỗ trợ communication với Allen-Bradley PLC?",
            "Hỏi đáp: Tại sao robot ABB IRB 2600 báo lỗi axis limit exceeded?"
        ];

        $qaContents = [
            "Mình đang thiết kế robot pick & place cho ứng dụng lắp ráp linh kiện điện tử. Payload khoảng 2kg, tốc độ cần 120 chu kỳ/phút, độ chính xác ±0.1mm. Nên chọn servo motor gì? Mình đang cân nhắc giữa Mitsubishi HF-KP series và Panasonic MINAS A6. Các bác có kinh nghiệm chia sẻ với ạ!",

            "Mình có dự án cần kết nối PLC Siemens S7-1500 với các sensor proximity và photoelectric của Omron. Có bác nào đã làm chưa? Communication protocol nào tốt nhất? Cần adapter hay converter gì không? Mong các bác tư vấn giúp ạ!",

            "Máy CNC DMG MORI NLX 2500 của xưởng mình bị rung động mạnh khi gia công thép cứng SKD11. Chất lượng bề mặt không đạt yêu cầu Ra 1.6μm. Đã thử giảm tốc độ cắt và feed rate nhưng vẫn rung. Có bác nào gặp tình huống tương tự không?",

            "Dự án automation cần HMI để giám sát hệ thống có cả PLC Allen-Bradley CompactLogix và Schneider Electric M340. Mình muốn dùng HMI Schneider Magelis GTO để tiết kiệm chi phí. Không biết có support communication với Allen-Bradley không?",

            "Robot ABB IRB 2600 ở line sản xuất hay báo lỗi 'Axis 1 limit exceeded' dù chưa chạm giới hạn vật lý. Đã check mechanical limit switch, encoder và calibration position đều OK. Có phải do brake hay gear box bị hỏng không? Bác nào có kinh nghiệm sửa chữa ABB robot hướng dẫn với!"
        ];

        for ($i = 0; $i < 5; $i++) {
            $title = $qaTitles[$i];
            $content = $qaContents[$i];
            $user = $users->random();

            $thread = Thread::create([
                'title' => $title,
                'slug' => Str::slug($title) . '-' . Str::random(5),
                'content' => $content,
                'user_id' => $user->id,
                'category_id' => $qaCategory->id,
                'forum_id' => $qaForum->id,
                'is_sticky' => $faker->boolean(10),
                'is_locked' => $faker->boolean(10),
                'is_featured' => $faker->boolean(10),
                'view_count' => $faker->numberBetween(20, 500),
            ]);

            // Tạo comments
            $this->createComments($faker, $thread, $users);

            // Tạo likes, saves, follows
            $this->createInteractions($faker, $thread, $users);
        }
    }

    /**
     * Tạo threads showcase
     */
    private function createShowcaseThreads($faker, $users, $categories, $forums): void
    {
        $showcaseCategory = Category::where('slug', 'du-an')->first();
        if (!$showcaseCategory) {
            $showcaseCategory = $categories->first();
        }

        $showcaseForum = Forum::where('name', 'Introductions')->first();
        if (!$showcaseForum) {
            $showcaseForum = $forums->first();
        }

        $statuses = ['Đề xuất', 'Đã phê duyệt', 'Đang xây dựng', 'Hoàn thành', 'Tạm dừng', 'Đã hủy'];

        // Nội dung showcase mẫu về thành tựu cơ khí
        $showcaseTitles = [
            "Showcase: Hệ thống robot lắp ráp ô tô hoàn toàn tự động",
            "Showcase: Máy CNC 5 trục gia công turbine hàng không",
            "Showcase: Dây chuyền sản xuất chip bán dẫn tự động",
            "Showcase: Robot phẫu thuật y tế độ chính xác cao",
            "Showcase: Hệ thống in 3D kim loại công nghiệp"
        ];

        $showcaseContents = [
            "Hệ thống robot lắp ráp ô tô hoàn toàn tự động tại nhà máy Thaco với 45 robot ABB và Kuka. Tự động hóa 95% quy trình lắp ráp từ khung gầm đến hoàn thiện. Năng suất 60 xe/giờ với chất lượng đạt tiêu chuẩn quốc tế. Tiết kiệm 40% nhân lực so với dây chuyền truyền thống. Tích hợp AI để phát hiện lỗi real-time.",

            "Máy CNC DMG MORI NTX 2000 gia công turbine hàng không với độ chính xác μm. Sử dụng dao PCD và ceramic chịu nhiệt cao. Gia công thành công turbine titanium Ti-6Al-4V cho động cơ máy bay. Thời gian gia công giảm 50% so với phương pháp truyền thống. Đạt chứng nhận AS9100.",

            "Dây chuyền sản xuất chip bán dẫn tự động tại Samsung Việt Nam với công nghệ 7nm. Gồm 200 robot SCARA và 50 clean room module. Năng suất 10,000 wafer/tháng. Tỷ lệ lỗi dưới 0.01%. Tự động kiểm tra chất lượng bằng electron microscope và X-ray inspection.",

            "Robot phẫu thuật Da Vinci Xi tại Bệnh viện Chợ Rẫy với 4 tay robot độ chính xác 0.1mm. Thực hiện thành công 500+ ca phẫu thuật ít xâm lấn. Camera 3D độ phân giải 4K với zoom 10x. Giảm 70% thời gian phục hồi cho bệnh nhân. Được đào tạo bởi các chuyên gia từ Intuitive Surgical.",

            "Hệ thống in 3D kim loại EOS M400-4 sử dụng công nghệ Direct Metal Laser Sintering. In được titanium, aluminum và thép không gỉ với độ dày layer 20μm. Ứng dụng trong hàng không, y tế và automotive. Giảm 60% trọng lượng chi tiết so với gia công truyền thống. Post-processing tự động bằng robot."
        ];

        for ($i = 0; $i < 5; $i++) {
            $title = $showcaseTitles[$i];
            $content = $showcaseContents[$i];
            $user = $users->random();

            $thread = Thread::create([
                'title' => $title,
                'slug' => Str::slug($title) . '-' . Str::random(5),
                'content' => $content,
                'user_id' => $user->id,
                'category_id' => $showcaseCategory->id,
                'forum_id' => $showcaseForum->id,
                'status' => $faker->randomElement($statuses),
                'is_sticky' => $faker->boolean(20),
                'is_locked' => $faker->boolean(10),
                'is_featured' => true,
                'view_count' => $faker->numberBetween(500, 10000),
            ]);

            // Tạo comments
            $this->createComments($faker, $thread, $users);

            // Tạo likes, saves, follows
            $this->createInteractions($faker, $thread, $users);

            // Tạo media
            $this->createMedia($faker, $thread, $user, 3);
        }
    }

    /**
     * Tạo comments cho thread
     */
    private function createComments($faker, $thread, $users): void
    {
        $commentCount = $faker->numberBetween(3, 15);
        $commentUsers = [];

        // Mẫu comments tiếng Việt về chủ đề cơ khí
        $sampleComments = [
            "Bài viết rất hữu ích! Mình đã áp dụng tương tự cho dự án automation tại công ty.",
            "Cảm ơn bác đã chia sẻ. Có thể cho mình xin thêm thông tin về parameter setting không?",
            "Kinh nghiệm quý báu! Mình cũng gặp vấn đề tương tự với robot ABB.",
            "Excellent! Approach này mình chưa thử bao giờ. Sẽ test và feedback sau.",
            "Quality post! Đặc biệt phần troubleshooting rất chi tiết và dễ hiểu.",
            "Thanks for sharing! Mình bookmark lại để tham khảo cho project sắp tới.",
            "Hay quá! PLC programming của bác rất professional. Respect!",
            "Useful information! Có video demo hoặc hình ảnh thực tế không bác?",
            "Great work! Safety consideration trong design này rất impressive.",
            "Perfect! Exactly những gì mình đang tìm kiếm cho automation project.",
            "Bác có kinh nghiệm với Schneider PLC không? So sánh với Siemens thế nào?",
            "Code PLC của bác clean và optimize tốt. Mình học được nhiều!",
            "Implementation này có phù hợp cho small-scale production không bác?",
            "ROI của project này khoảng bao lâu? Cost comparison vs manual process?",
            "Troubleshooting guide rất detail. Saved cho future reference!",
            "Maintenance schedule cho hệ thống này như thế nào bác?",
            "Integration với ERP/MES có challenge gì không?",
            "Performance metrics impressive! Uptime đạt được bao nhiều %?",
            "Safety features comprehensive! Tuân thủ standard nào?",
            "Scalability của solution này có limitations gì không?"
        ];

        $sampleReplies = [
            "Cảm ơn feedback! Mình sẽ update thêm parameter details.",
            "Bác có thể inbox để mình gửi detailed configuration file.",
            "Yes, video demo đang prepare. Sẽ post lên YouTube sớm.",
            "ROI khoảng 18 tháng, nhưng depend on production volume.",
            "Maintenance schedule mình follow theo OEM recommendation.",
            "Integration challenge mainly ở data format và communication protocol.",
            "Uptime hiện tại đạt 98.5%, target 99% trong Q3.",
            "Tuân thủ ISO 13849 PLd và OSHA safety requirements.",
            "Scalable up to 5x current capacity với hardware upgrade minimal.",
            "Small-scale totally feasible, chỉ cần adjust configuration thôi."
        ];

        for ($i = 1; $i <= $commentCount; $i++) {
            $user = $users->random();
            $commentUsers[$user->id] = true;

            $comment = Comment::create([
                'thread_id' => $thread->id,
                'user_id' => $user->id,
                'content' => $faker->randomElement($sampleComments),
            ]);

            // Tạo likes cho comment
            $likeCount = $faker->numberBetween(0, 10);
            for ($j = 0; $j < $likeCount; $j++) {
                $likeUser = $users->random();

                CommentLike::firstOrCreate([
                    'comment_id' => $comment->id,
                    'user_id' => $likeUser->id,
                ]);
            }

            // Cập nhật like_count
            $comment->like_count = $comment->likes()->count();
            $comment->save();

            // Tạo replies cho comment (30% cơ hội)
            if ($faker->boolean(30)) {
                $replyCount = $faker->numberBetween(1, 5);

                for ($k = 0; $k < $replyCount; $k++) {
                    $replyUser = $users->random();
                    $commentUsers[$replyUser->id] = true;

                    $reply = Comment::create([
                        'thread_id' => $thread->id,
                        'user_id' => $replyUser->id,
                        'parent_id' => $comment->id,
                        'content' => $faker->randomElement($sampleReplies),
                    ]);

                    // Tạo likes cho reply
                    $replyLikeCount = $faker->numberBetween(0, 5);
                    for ($l = 0; $l < $replyLikeCount; $l++) {
                        $likeUser = $users->random();

                        CommentLike::firstOrCreate([
                            'comment_id' => $reply->id,
                            'user_id' => $likeUser->id,
                        ]);
                    }

                    // Cập nhật like_count
                    $reply->like_count = $reply->likes()->count();
                    $reply->save();
                }
            }
        }

        // Không cần cập nhật participant_count nữa vì đã loại bỏ cột này
    }

    /**
     * Tạo likes, saves, follows cho thread
     */
    private function createInteractions($faker, $thread, $users): void
    {
        // Tạo likes
        $likeCount = $faker->numberBetween(5, 30);
        for ($i = 0; $i < $likeCount; $i++) {
            $user = $users->random();

            ThreadLike::firstOrCreate([
                'thread_id' => $thread->id,
                'user_id' => $user->id,
            ]);
        }

        // Tạo saves
        $saveCount = $faker->numberBetween(2, 15);
        for ($i = 0; $i < $saveCount; $i++) {
            $user = $users->random();

            ThreadSave::firstOrCreate([
                'thread_id' => $thread->id,
                'user_id' => $user->id,
            ]);
        }

        // Tạo follows
        $followCount = $faker->numberBetween(3, 20);
        for ($i = 0; $i < $followCount; $i++) {
            $user = $users->random();

            ThreadFollow::firstOrCreate([
                'thread_id' => $thread->id,
                'user_id' => $user->id,
            ]);
        }
    }

    /**
     * Tạo media cho thread
     */
    private function createMedia($faker, $thread, $user, $count = 1): void
    {
        $imageTypes = ['jpg', 'png'];

        // Title và description mẫu cho media
        $mediaTitles = [
            "Hình ảnh chi tiết robot ABB tại hiện trường",
            "Sơ đồ kết nối PLC và HMI",
            "Giao diện SCADA monitoring system",
            "Layout thiết kế automation line",
            "Kết quả test performance máy CNC",
            "Hình ảnh lắp đặt sensor proximity",
            "Diagram electrical control panel",
            "Video demo robot pick & place",
            "Báo cáo quality control inspection",
            "3D model chi tiết cơ khí"
        ];

        $mediaDescriptions = [
            "Ảnh chụp tại factory floor cho thấy robot đang hoạt động với tốc độ và độ chính xác cao",
            "Sơ đồ wiring diagram chi tiết kết nối giữa PLC, I/O module và HMI touchscreen",
            "Screenshot giao diện SCADA hiển thị real-time data từ các sensor và actuator",
            "Layout 2D showing conveyor system, robot positions, safety fence và work stations",
            "Test report performance CNC machine bao gồm accuracy, surface finish và cycle time",
            "Hình ảnh installation proximity sensor với mounting bracket và electrical connection",
            "Electrical schematic diagram control panel bao gồm circuit breaker, contactor, relay",
            "Demo video robot thực hiện 120 chu kỳ pick & place trong 1 phút với zero defect",
            "Quality inspection report với measurement data và statistical process control chart",
            "3D CAD model mechanical assembly với exploded view và bill of materials"
        ];

        for ($i = 0; $i < $count; $i++) {
            $type = $faker->randomElement($imageTypes);
            $path = "thread-images/thread-{$thread->id}-image-{$i}.{$type}";

            Media::create([
                'user_id' => $user->id,
                'thread_id' => $thread->id,
                'file_name' => "thread-{$thread->id}-image-{$i}.{$type}",
                'file_path' => $path,
                'file_type' => "image/{$type}",
                'file_size' => $faker->numberBetween(100000, 5000000),
                'title' => $faker->randomElement($mediaTitles),
                'description' => $faker->randomElement($mediaDescriptions),
            ]);
        }
    }

    /**
     * Tạo poll cho thread
     */
    private function createPoll($faker, $thread, $users): void
    {
        // Câu hỏi poll mẫu về chủ đề cơ khí
        $pollQuestions = [
            "Theo bạn, thương hiệu PLC nào tốt nhất cho automation project?",
            "Robot nào phù hợp nhất cho ứng dụng pick & place trong electronics?",
            "Bạn thích sử dụng phần mềm CAD nào nhất?",
            "Công nghệ nào sẽ thay đổi ngành cơ khí nhiều nhất trong 5 năm tới?",
            "Kinh nghiệm bao nhiêu năm thì có thể làm automation engineer senior?"
        ];

        $pollOptionsMap = [
            // Options cho PLC brands
            ["Siemens S7-1500", "Allen-Bradley CompactLogix", "Schneider Electric M340", "Mitsubishi FX5U", "Omron CP1H"],
            // Options cho Robot brands
            ["ABB IRB series", "Fanuc M-1iD", "Kuka KR AGILUS", "Universal Robots UR", "Epson SCARA"],
            // Options cho CAD software
            ["SolidWorks", "AutoCAD", "Inventor", "Fusion 360", "CATIA V5"],
            // Options cho Technologies
            ["AI & Machine Learning", "IoT & Industry 4.0", "Collaborative Robots", "Digital Twin", "Additive Manufacturing"],
            // Options cho Years of experience
            ["3-5 năm", "5-8 năm", "8-12 năm", "Trên 12 năm", "Tùy thuộc dự án"]
        ];

        $questionIndex = $faker->numberBetween(0, count($pollQuestions) - 1);

        $poll = Poll::create([
            'thread_id' => $thread->id,
            'question' => $pollQuestions[$questionIndex],
            'max_options' => $faker->randomElement([1, 2, 3]),
            'allow_change_vote' => $faker->boolean(70),
            'show_votes_publicly' => $faker->boolean(60),
            'allow_view_without_vote' => $faker->boolean(80),
            'close_at' => $faker->boolean(30) ? $faker->dateTimeBetween('+1 week', '+1 month') : null,
        ]);

        // Tạo options
        $selectedOptions = $pollOptionsMap[$questionIndex];
        $optionCount = min(count($selectedOptions), $faker->numberBetween(3, 5));
        $options = [];

        for ($i = 0; $i < $optionCount; $i++) {
            $option = PollOption::create([
                'poll_id' => $poll->id,
                'text' => $selectedOptions[$i],
            ]);

            $options[] = $option;
        }

        // Tạo votes
        $voteCount = $faker->numberBetween(5, 30);
        $votedUsers = [];

        for ($i = 0; $i < $voteCount; $i++) {
            $user = $users->random();

            // Đảm bảo mỗi user chỉ vote một lần
            if (isset($votedUsers[$user->id])) {
                continue;
            }

            $votedUsers[$user->id] = true;
            $option = $faker->randomElement($options);

            PollVote::create([
                'poll_id' => $poll->id,
                'poll_option_id' => $option->id,
                'user_id' => $user->id,
            ]);
        }
    }
}
