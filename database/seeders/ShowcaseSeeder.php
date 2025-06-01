<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Forum;
use App\Models\Showcase;
use App\Models\ShowcaseAttachment;
use App\Models\User;
use App\Models\Thread;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ShowcaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $threads = Thread::all();

        if ($users->count() === 0 || $threads->count() === 0) {
            return;
        }

        // Danh sách các bản vẽ CAD mẫu
        $showcases = [
            [
                'title' => 'Thiết kế hệ thống truyền động bánh răng cho máy CNC',
                'description' => 'Bản vẽ chi tiết hệ thống truyền động bánh răng cho máy CNC 5 trục, tối ưu cho độ chính xác cao và giảm thiểu rung động.',
                'content' => '<p>Hệ thống truyền động bánh răng này được thiết kế đặc biệt cho máy CNC 5 trục, với các tính năng sau:</p>
                <ul>
                    <li>Hệ số truyền động: 1:5</li>
                    <li>Vật liệu: Thép hợp kim 40CrMnMo</li>
                    <li>Xử lý nhiệt: Tôi cứng bề mặt HRC 58-62</li>
                    <li>Độ chính xác: Cấp 6</li>
                    <li>Góc áp lực: 20°</li>
                </ul>
                <p>Thiết kế này giúp giảm thiểu rung động và tăng độ chính xác cho máy CNC, đồng thời kéo dài tuổi thọ của hệ thống truyền động.</p>',
                'category' => 'Thiết kế máy',
                'forum' => 'CNC & Gia công chính xác',
                'is_featured' => true,
                'status' => 'published',
                'images' => [
                    'https://www.researchgate.net/publication/342751251/figure/fig1/AS:910108383318016@1594021355535/Gear-transmission-system-of-the-CNC-machine-tool.png',
                    'https://www.researchgate.net/publication/342751251/figure/fig2/AS:910108383326208@1594021355667/Transmission-system-of-the-CNC-machine-tool.png',
                    'https://i.pinimg.com/originals/e0/d0/1d/e0d01d0c4b2c6312d8c8815eb4e3a3c8.jpg'
                ]
            ],
            [
                'title' => 'Mô hình 3D chi tiết động cơ V8',
                'description' => 'Mô hình 3D chi tiết của động cơ V8 với đầy đủ các bộ phận, phù hợp cho nghiên cứu và học tập.',
                'content' => '<p>Mô hình 3D này bao gồm tất cả các chi tiết của một động cơ V8 hiện đại:</p>
                <ul>
                    <li>Khối xi-lanh và nắp xi-lanh</li>
                    <li>Hệ thống trục khuỷu và thanh truyền</li>
                    <li>Piston và xupap</li>
                    <li>Hệ thống phun nhiên liệu</li>
                    <li>Hệ thống làm mát</li>
                    <li>Turbo và hệ thống xả</li>
                </ul>
                <p>Mô hình được thiết kế với độ chính xác cao, phù hợp cho việc nghiên cứu, học tập và mô phỏng hoạt động của động cơ V8.</p>',
                'category' => 'Động cơ & Hệ thống truyền động',
                'forum' => 'Thiết kế ô tô',
                'is_featured' => true,
                'status' => 'published',
                'images' => [
                    'https://www.enginelabs.com/wp-content/uploads/2016/03/3D-Engine-CAD.jpg',
                    'https://grabcad.com/thumbnails/0a01e7a2-3d9a-4203-9a59-a2b6ac7e2afe/1300',
                    'https://i.pinimg.com/originals/a5/ff/e4/a5ffe4c2ea93e46a3c6adcd80881e00b.jpg'
                ]
            ],
            [
                'title' => 'Thiết kế hệ thống thủy lực cho máy ép thủy lực 100 tấn',
                'description' => 'Bản vẽ chi tiết hệ thống thủy lực cho máy ép công suất 100 tấn, bao gồm sơ đồ mạch và danh sách linh kiện.',
                'content' => '<p>Hệ thống thủy lực này được thiết kế cho máy ép công suất 100 tấn với các thông số kỹ thuật sau:</p>
                <ul>
                    <li>Áp suất làm việc: 250 bar</li>
                    <li>Lưu lượng bơm: 120 lít/phút</li>
                    <li>Dung tích xi-lanh: 25 lít</li>
                    <li>Đường kính piston: 200mm</li>
                    <li>Hành trình: 800mm</li>
                    <li>Thời gian chu kỳ: 45 giây</li>
                </ul>
                <p>Bản vẽ bao gồm sơ đồ mạch thủy lực chi tiết, danh sách các van và linh kiện cần thiết, cũng như thông số kỹ thuật của từng thành phần.</p>',
                'category' => 'Hệ thống thủy lực & khí nén',
                'forum' => 'Máy công nghiệp',
                'is_featured' => false,
                'status' => 'published',
                'images' => [
                    'https://www.researchgate.net/publication/335624388/figure/fig2/AS:798937661853697@1567563304269/Hydraulic-circuit-diagram-of-the-hydraulic-press.png',
                    'https://www.researchgate.net/publication/337720107/figure/fig1/AS:831784952573953@1575039435179/Hydraulic-circuit-of-the-press.png',
                    'https://www.researchgate.net/publication/343403223/figure/fig3/AS:920023355047939@1596424558258/Hydraulic-circuit-of-the-press.jpg'
                ]
            ],
            [
                'title' => 'Thiết kế robot 6 bậc tự do cho ứng dụng hàn tự động',
                'description' => 'Bản vẽ chi tiết robot 6 bậc tự do được tối ưu hóa cho ứng dụng hàn tự động trong sản xuất ô tô.',
                'content' => '<p>Robot 6 bậc tự do này được thiết kế đặc biệt cho ứng dụng hàn tự động trong ngành sản xuất ô tô với các đặc điểm sau:</p>
                <ul>
                    <li>Tải trọng: 10kg</li>
                    <li>Tầm với: 1800mm</li>
                    <li>Độ chính xác lặp lại: ±0.05mm</li>
                    <li>Tốc độ tối đa: 180°/s (trục 1-3), 250°/s (trục 4-6)</li>
                    <li>Trọng lượng: 280kg</li>
                    <li>Nguồn điện: 380V AC, 3 pha</li>
                </ul>
                <p>Thiết kế bao gồm các bản vẽ chi tiết của từng khớp, hệ thống truyền động, và bộ điều khiển. Robot được tối ưu hóa để đạt được độ chính xác cao trong quá trình hàn tự động.</p>',
                'category' => 'Robot & Tự động hóa',
                'forum' => 'Tự động hóa sản xuất',
                'is_featured' => true,
                'status' => 'published',
                'images' => [
                    'https://www.researchgate.net/publication/330415325/figure/fig1/AS:715471166918656@1547599585599/CAD-model-of-the-6-DOF-robot-arm.png',
                    'https://www.researchgate.net/publication/341787514/figure/fig2/AS:896096202330113@1590736212142/CAD-model-of-the-6-DOF-robot-manipulator.jpg',
                    'https://www.researchgate.net/publication/343267033/figure/fig3/AS:919117017473025@1596102575951/Kinematic-diagram-of-the-6-DOF-robot-arm.png'
                ]
            ],
            [
                'title' => 'Thiết kế khuôn đúc áp lực cho linh kiện ô tô',
                'description' => 'Bản vẽ chi tiết khuôn đúc áp lực nhôm cho vỏ hộp số ô tô, bao gồm hệ thống làm mát và thoát khí.',
                'content' => '<p>Khuôn đúc áp lực này được thiết kế cho sản xuất vỏ hộp số ô tô bằng hợp kim nhôm với các thông số kỹ thuật sau:</p>
                <ul>
                    <li>Vật liệu khuôn: Thép H13</li>
                    <li>Xử lý nhiệt: Tôi và ram, độ cứng 48-52 HRC</li>
                    <li>Nhiệt độ làm việc: 180-220°C</li>
                    <li>Áp suất đúc: 700-900 bar</li>
                    <li>Thời gian chu kỳ: 45 giây</li>
                    <li>Hệ thống làm mát: 8 kênh làm mát</li>
                </ul>
                <p>Thiết kế bao gồm hệ thống thoát khí tiên tiến để giảm thiểu lỗi khí trong sản phẩm, cũng như hệ thống làm mát tối ưu để kéo dài tuổi thọ của khuôn.</p>',
                'category' => 'Khuôn mẫu & Đúc',
                'forum' => 'Công nghệ đúc',
                'is_featured' => false,
                'status' => 'published',
                'images' => [
                    'https://www.researchgate.net/publication/343048004/figure/fig1/AS:915786035965953@1595348330298/Die-casting-mold-design.jpg',
                    'https://www.researchgate.net/publication/341165345/figure/fig4/AS:886611487272966@1588513240532/Die-casting-mold-design.png',
                    'https://www.researchgate.net/publication/340865933/figure/fig6/AS:884575458279428@1588090935251/Cooling-system-of-die-casting-mold.jpg'
                ]
            ],
            [
                'title' => 'Thiết kế hệ thống băng tải phân loại sản phẩm tự động',
                'description' => 'Bản vẽ chi tiết hệ thống băng tải tự động với khả năng phân loại sản phẩm dựa trên kích thước và trọng lượng.',
                'content' => '<p>Hệ thống băng tải phân loại tự động này được thiết kế với các thông số và tính năng sau:</p>
                <ul>
                    <li>Chiều dài tổng thể: 15m</li>
                    <li>Tốc độ băng tải: 0.5-1.2 m/s (điều chỉnh được)</li>
                    <li>Công suất: 5.5kW</li>
                    <li>Khả năng tải: 100kg/m</li>
                    <li>Cảm biến: Laser đo kích thước, loadcell đo trọng lượng</li>
                    <li>Hệ thống phân loại: 5 ngăn phân loại với xy-lanh khí nén</li>
                </ul>
                <p>Hệ thống được tích hợp với PLC Siemens S7-1200 và HMI KTP700 để điều khiển và giám sát quá trình phân loại. Thiết kế bao gồm chi tiết về cơ khí, điện, và lập trình PLC.</p>',
                'category' => 'Hệ thống vận chuyển & Băng tải',
                'forum' => 'Tự động hóa sản xuất',
                'is_featured' => false,
                'status' => 'published',
                'images' => [
                    'https://www.researchgate.net/publication/335624388/figure/fig1/AS:798937661849600@1567563304113/Conveyor-system-design.png',
                    'https://www.researchgate.net/publication/334164082/figure/fig1/AS:832270551240704@1575105775373/Sorting-conveyor-system.png',
                    'https://www.researchgate.net/publication/333057422/figure/fig1/AS:761482731601920@1558635336364/Automated-sorting-conveyor-system.jpg'
                ]
            ],
            [
                'title' => 'Thiết kế máy phay CNC 3 trục cho gia công nhôm',
                'description' => 'Bản vẽ chi tiết máy phay CNC 3 trục nhỏ gọn, phù hợp cho xưởng cơ khí nhỏ và gia công chi tiết nhôm.',
                'content' => '<p>Máy phay CNC 3 trục này được thiết kế đặc biệt cho các xưởng cơ khí nhỏ với các thông số kỹ thuật sau:</p>
                <ul>
                    <li>Kích thước gia công: 500 x 300 x 200mm</li>
                    <li>Công suất trục chính: 2.2kW</li>
                    <li>Tốc độ trục chính: 100-24000 vòng/phút</li>
                    <li>Tốc độ di chuyển tối đa: 10m/phút</li>
                    <li>Độ chính xác lặp lại: ±0.01mm</li>
                    <li>Hệ điều khiển: Mach3</li>
                </ul>
                <p>Thiết kế sử dụng khung thép hàn với xử lý ủ để giảm ứng suất, ray trượt và vít me bi chất lượng cao, và động cơ bước lai với driver vi bước. Máy được tối ưu hóa cho gia công nhôm và nhựa cứng.</p>',
                'category' => 'Máy công cụ',
                'forum' => 'CNC & Gia công chính xác',
                'is_featured' => false,
                'status' => 'published',
                'images' => [
                    'https://www.researchgate.net/publication/329403205/figure/fig2/AS:700900978782209@1544096193794/3-axis-CNC-milling-machine-design.png',
                    'https://www.researchgate.net/publication/335443178/figure/fig1/AS:796392988721152@1566985598239/3-axis-CNC-milling-machine.jpg',
                    'https://www.researchgate.net/publication/344781326/figure/fig1/AS:949785135742976@1603387001736/3-axis-CNC-machine-design.png'
                ]
            ],
            [
                'title' => 'Thiết kế hệ thống làm mát cho khuôn ép nhựa',
                'description' => 'Bản vẽ chi tiết hệ thống làm mát conformal cooling cho khuôn ép nhựa, tối ưu hóa bằng mô phỏng CFD.',
                'content' => '<p>Hệ thống làm mát conformal cooling này được thiết kế cho khuôn ép nhựa với các đặc điểm sau:</p>
                <ul>
                    <li>Đường kính kênh làm mát: 8mm</li>
                    <li>Khoảng cách giữa các kênh: 25mm</li>
                    <li>Khoảng cách từ bề mặt khuôn: 12mm</li>
                    <li>Lưu lượng nước làm mát: 15 lít/phút</li>
                    <li>Nhiệt độ nước vào: 15°C</li>
                    <li>Vật liệu khuôn: Thép P20</li>
                </ul>
                <p>Thiết kế được tối ưu hóa bằng phần mềm mô phỏng CFD để đảm bảo sự phân bố nhiệt đồng đều trên bề mặt khuôn, giảm thời gian chu kỳ và tăng chất lượng sản phẩm. Bản vẽ bao gồm chi tiết về vị trí, kích thước của các kênh làm mát và kết quả mô phỏng.</p>',
                'category' => 'Khuôn mẫu & Đúc',
                'forum' => 'Công nghệ nhựa',
                'is_featured' => false,
                'status' => 'published',
                'images' => [
                    'https://www.researchgate.net/publication/329403205/figure/fig5/AS:700900978782212@1544096193863/Conformal-cooling-channels-in-injection-mold.png',
                    'https://www.researchgate.net/publication/337720107/figure/fig3/AS:831784952573955@1575039435258/Cooling-system-design-for-injection-mold.png',
                    'https://www.researchgate.net/publication/343403223/figure/fig5/AS:920023355047941@1596424558364/CFD-simulation-of-cooling-channels.jpg'
                ]
            ],
            [
                'title' => 'Thiết kế hệ thống truyền động cho xe điện',
                'description' => 'Bản vẽ chi tiết hệ thống truyền động cho xe điện công suất 50kW, bao gồm động cơ, hộp số và hệ thống điều khiển.',
                'content' => '<p>Hệ thống truyền động này được thiết kế cho xe điện với các thông số kỹ thuật sau:</p>
                <ul>
                    <li>Động cơ: PMSM 50kW, 400V</li>
                    <li>Mô-men xoắn cực đại: 250Nm</li>
                    <li>Tốc độ tối đa: 12000 vòng/phút</li>
                    <li>Hộp số: Giảm tốc 1 cấp, tỷ số truyền 8.5:1</li>
                    <li>Hiệu suất hệ thống: >92%</li>
                    <li>Trọng lượng: 85kg</li>
                </ul>
                <p>Thiết kế bao gồm chi tiết về động cơ, hộp số, hệ thống làm mát bằng chất lỏng, và bộ điều khiển vector. Hệ thống được tối ưu hóa để đạt được hiệu suất cao và trọng lượng thấp.</p>',
                'category' => 'Động cơ & Hệ thống truyền động',
                'forum' => 'Xe điện',
                'is_featured' => false,
                'status' => 'published',
                'images' => [
                    'https://www.researchgate.net/publication/335443178/figure/fig3/AS:796392988721154@1566985598312/Electric-vehicle-drivetrain-design.jpg',
                    'https://www.researchgate.net/publication/344781326/figure/fig3/AS:949785135742978@1603387001830/Electric-motor-and-gearbox-assembly.png',
                    'https://www.researchgate.net/publication/343267033/figure/fig5/AS:919117017473027@1596102575997/Cooling-system-for-electric-drivetrain.png'
                ]
            ],
            [
                'title' => 'Thiết kế hệ thống điều khiển PLC cho dây chuyền đóng gói',
                'description' => 'Sơ đồ chi tiết hệ thống điều khiển PLC Siemens S7-1500 cho dây chuyền đóng gói tự động, bao gồm sơ đồ I/O và chương trình mẫu.',
                'content' => '<p>Hệ thống điều khiển PLC này được thiết kế cho dây chuyền đóng gói tự động với các thành phần sau:</p>
                <ul>
                    <li>PLC: Siemens S7-1500 CPU 1516-3 PN/DP</li>
                    <li>HMI: Siemens TP1200 Comfort</li>
                    <li>Mô-đun I/O: 128 DI, 96 DO, 32 AI, 16 AO</li>
                    <li>Truyền thông: PROFINET, PROFIBUS DP, Modbus TCP</li>
                    <li>Servo drive: Siemens SINAMICS S120</li>
                    <li>Biến tần: Siemens SINAMICS G120</li>
                </ul>
                <p>Thiết kế bao gồm sơ đồ kết nối chi tiết, cấu hình phần cứng, danh sách I/O, và chương trình mẫu cho các chức năng chính của dây chuyền đóng gói. Hệ thống được thiết kế theo tiêu chuẩn IEC 61131-3 và hỗ trợ các chức năng giám sát từ xa.</p>',
                'category' => 'Điều khiển & Tự động hóa',
                'forum' => 'Tự động hóa sản xuất',
                'is_featured' => false,
                'status' => 'published',
                'images' => [
                    'https://www.researchgate.net/publication/334164082/figure/fig3/AS:832270551240706@1575105775430/PLC-control-system-architecture.png',
                    'https://www.researchgate.net/publication/333057422/figure/fig3/AS:761482731601922@1558635336422/HMI-design-for-packaging-line.jpg',
                    'https://www.researchgate.net/publication/330415325/figure/fig4/AS:715471166918659@1547599585731/Electrical-cabinet-layout.png'
                ]
            ],
        ];

        // Lấy danh sách người dùng, danh mục và diễn đàn
        $users = User::all();

        // Tạo dữ liệu mẫu
        $createdShowcases = [];
        foreach ($showcases as $index => $showcaseData) {
            // Đảm bảo unique combination của user_id và showcaseable_id
            $user = $users->random();
            $thread = $threads->random();

            // Kiểm tra xem combination này đã tồn tại chưa
            $uniqueKey = $user->id . '-' . $thread->id;
            while (in_array($uniqueKey, $createdShowcases)) {
                $user = $users->random();
                $thread = $threads->random();
                $uniqueKey = $user->id . '-' . $thread->id;
            }
            $createdShowcases[] = $uniqueKey;

            // Tìm hoặc tạo danh mục
            $category = Category::firstOrCreate([
                'name' => $showcaseData['category']
            ], [
                'slug' => Str::slug($showcaseData['category']),
                'description' => 'Danh mục ' . $showcaseData['category']
            ]);

            // Tìm hoặc tạo diễn đàn
            $forum = Forum::firstOrCreate([
                'name' => $showcaseData['forum']
            ], [
                'slug' => Str::slug($showcaseData['forum']),
                'description' => 'Diễn đàn ' . $showcaseData['forum']
            ]);

            // Tạo showcase
            $showcase = Showcase::create([
                'user_id' => $user->id,
                'showcaseable_id' => $thread->id,
                'showcaseable_type' => Thread::class,
                'title' => $showcaseData['title'],
                'description' => $showcaseData['description'],
                'content' => $showcaseData['content'],
                'category_id' => $category->id,
                'forum_id' => $forum->id,
                'views_count' => rand(50, 5000),
                'likes_count' => rand(10, 500),
                'comments_count' => rand(0, 50),
                'downloads_count' => rand(5, 200),
                'is_featured' => $showcaseData['is_featured'],
                'status' => $showcaseData['status'],
                'created_at' => now()->subDays(rand(1, 60)),
                'updated_at' => now()->subDays(rand(0, 30)),
            ]);

            // Tạo các tệp đính kèm
            foreach ($showcaseData['images'] as $index => $imageUrl) {
                // Tải hình ảnh từ URL
                $tempPath = storage_path('app/temp_' . basename($imageUrl));
                try {
                    // Tạo thư mục nếu chưa tồn tại
                    if (!File::exists(storage_path('app/public/showcase_attachments'))) {
                        File::makeDirectory(storage_path('app/public/showcase_attachments'), 0755, true);
                    }

                    // Tải hình ảnh từ URL
                    $imageContent = @file_get_contents($imageUrl);
                    if ($imageContent) {
                        file_put_contents($tempPath, $imageContent);

                        // Lưu vào storage
                        $filename = Str::uuid() . '.jpg';
                        $path = 'showcase_attachments/' . $filename;
                        Storage::disk('public')->put($path, file_get_contents($tempPath));

                        // Tạo attachment
                        $showcase->attachments()->create([
                            'filename' => $filename,
                            'original_filename' => 'image_' . ($index + 1) . '.jpg',
                            'file_path' => $path,
                            'file_type' => 'jpg',
                            'file_size' => filesize($tempPath),
                            'is_cover' => $index === 0, // Hình đầu tiên là ảnh bìa
                            'order' => $index,
                        ]);

                        // Xóa file tạm
                        @unlink($tempPath);
                    }
                } catch (\Exception $e) {
                    // Bỏ qua lỗi khi tải hình ảnh
                    continue;
                }
            }
        }
    }
}
