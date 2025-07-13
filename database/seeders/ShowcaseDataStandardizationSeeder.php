<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Showcase;
use App\Models\User;
use App\Models\ShowcaseRating;
use Carbon\Carbon;

class ShowcaseDataStandardizationSeeder extends Seeder
{
    /**
     * 🔧 MechaMap Showcase Data Standardization Seeder
     *
     * Chuẩn hóa dữ liệu showcases theo yêu cầu:
     * - User validation và permissions
     * - Nội dung chất lượng bám sát chủ đề cơ khí
     * - Hình ảnh phong phú từ thư mục có sẵn
     * - Phân loại visibility (80% public, 20% private)
     * - Rating system (3.5-5.0 sao cho public showcases)
     */
    public function run(): void
    {
        $this->command->info('🚀 Bắt đầu chuẩn hóa dữ liệu showcases...');

        // Backup trước khi thực hiện
        $this->createBackup();

        // Phân tích hiện trạng
        $this->analyzeCurrentState();

        // Chuẩn hóa dữ liệu
        $this->validateUsers();
        $this->standardizeContent();
        $this->addImages();
        $this->setVisibility();
        $this->createRatings();

        $this->command->info('✅ Hoàn thành chuẩn hóa dữ liệu showcases!');
    }

    /**
     * Tạo backup trước khi chuẩn hóa
     */
    private function createBackup(): void
    {
        $this->command->info('📦 Tạo backup dữ liệu showcases...');

        $timestamp = now()->format('Y-m-d_H-i-s');
        $backupFile = storage_path("app/backups/showcases_backup_{$timestamp}.json");

        // Tạo thư mục backup nếu chưa có
        if (!file_exists(dirname($backupFile))) {
            mkdir(dirname($backupFile), 0755, true);
        }

        // Export showcases data as JSON
        $showcases = Showcase::all()->toArray();
        file_put_contents($backupFile, json_encode($showcases, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $this->command->info("✅ Backup tạo tại: {$backupFile}");
        $this->command->info("📊 Đã backup " . count($showcases) . " showcases");
    }

    /**
     * Phân tích hiện trạng dữ liệu
     */
    private function analyzeCurrentState(): void
    {
        $this->command->info('📊 Phân tích hiện trạng showcases...');

        $totalShowcases = Showcase::count();
        $this->command->info("Tổng số showcases: {$totalShowcases}");

        // Phân bố theo status
        $statusStats = Showcase::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        $this->command->info('Phân bố theo status:');
        foreach ($statusStats as $stat) {
            $this->command->info("  {$stat->status}: {$stat->count} showcases");
        }

        // Phân bố theo visibility
        $publicCount = Showcase::where('is_public', true)->count();
        $privateCount = Showcase::where('is_public', false)->count();

        $this->command->info('Phân bố visibility:');
        $this->command->info("  Public: {$publicCount}");
        $this->command->info("  Private: {$privateCount}");

        // Kiểm tra nội dung
        $emptyTitle = Showcase::where('title', '')->orWhereNull('title')->count();
        $emptyDescription = Showcase::where('description', '')->orWhereNull('description')->count();
        $noCoverImage = Showcase::whereNull('cover_image')->count();
        $shortDescription = Showcase::whereRaw('LENGTH(description) < 100')->count();

        $this->command->info('Vấn đề nội dung:');
        $this->command->info("  Thiếu title: {$emptyTitle}");
        $this->command->info("  Thiếu description: {$emptyDescription}");
        $this->command->info("  Không có cover_image: {$noCoverImage}");
        $this->command->info("  Description < 100 ký tự: {$shortDescription}");

        // Kiểm tra foreign keys
        $invalidUsers = Showcase::whereNotIn('user_id', User::pluck('id'))->count();

        $this->command->info('Vấn đề foreign keys:');
        $this->command->info("  User_id không tồn tại: {$invalidUsers}");
    }

    /**
     * Validate và fix user permissions
     */
    private function validateUsers(): void
    {
        $this->command->info('👤 Kiểm tra và sửa user permissions...');

        // Lấy showcases có user_id không tồn tại
        $invalidShowcases = Showcase::whereNotIn('user_id', User::pluck('id'))->get();

        $this->command->info("Tìm thấy {$invalidShowcases->count()} showcases với user_id không hợp lệ");

        // Lấy danh sách users có quyền tạo showcases (member trở lên)
        $validUsers = User::whereNotIn('role', ['guest'])->pluck('id')->toArray();

        if (empty($validUsers)) {
            $this->command->error('Không tìm thấy user nào có quyền tạo showcases!');
            return;
        }

        // Fix invalid showcases
        foreach ($invalidShowcases as $showcase) {
            $randomUserId = $validUsers[array_rand($validUsers)];
            $showcase->update(['user_id' => $randomUserId]);
            $this->command->info("✅ Cập nhật showcase ID {$showcase->id} với user_id {$randomUserId}");
        }
    }

    /**
     * Chuẩn hóa nội dung showcases
     */
    private function standardizeContent(): void
    {
        $this->command->info('📝 Chuẩn hóa nội dung showcases...');

        $mechanicalProjects = [
            'Hệ thống tự động hóa băng tải sản xuất',
            'Thiết kế robot hàn công nghiệp 6 trục',
            'Phân tích FEA khung gầm xe tải',
            'Tối ưu hóa quy trình gia công CNC',
            'Thiết kế hệ thống thủy lực máy ép',
            'Mô phỏng CFD hệ thống làm mát động cơ',
            'Thiết kế gear box cho máy công nghiệp',
            'Phân tích rung động máy nén khí',
            'Thiết kế khuôn ép nhựa chính xác cao',
            'Hệ thống điều khiển PLC cho dây chuyền',
            'Thiết kế cơ cấu cam cho máy đóng gói',
            'Phân tích nhiệt độ phanh đĩa ô tô',
            'Thiết kế bearing cho turbine gió',
            'Tối ưu hóa layout nhà máy sản xuất',
            'Thiết kế jig fixture cho gia công',
            'Phân tích độ bền vật liệu composite',
            'Thiết kế hệ thống pneumatic',
            'Mô phỏng quá trình đúc kim loại',
            'Thiết kế conveyor system thông minh',
            'Phân tích stress concentration'
        ];

        $mechanicalDescriptions = [
            'Dự án này tập trung vào việc thiết kế và phát triển hệ thống cơ khí tiên tiến với độ chính xác cao. Sử dụng phần mềm CAD/CAM hiện đại để mô phỏng và tối ưu hóa thiết kế. Quy trình bao gồm phân tích kỹ thuật chi tiết, lựa chọn vật liệu phù hợp, và kiểm tra chất lượng nghiêm ngặt.',
            'Nghiên cứu này khám phá các phương pháp cải tiến trong lĩnh vực cơ khí chính xác. Áp dụng công nghệ Industry 4.0 và IoT để tạo ra giải pháp thông minh. Kết quả đạt được độ chính xác cao và hiệu suất vượt trội so với các phương pháp truyền thống.',
            'Phân tích và thiết kế hệ thống với khả năng chịu tải cao và độ bền vượt trội. Sử dụng phương pháp FEA để mô phỏng và kiểm tra tính toàn vẹn cấu trúc. Tối ưu hóa trọng lượng và chi phí sản xuất mà vẫn đảm bảo an toàn và hiệu suất.',
            'Dự án tập trung vào việc tự động hóa quy trình sản xuất nhằm tăng hiệu suất và giảm chi phí. Tích hợp các cảm biến thông minh và hệ thống điều khiển tiên tiến. Kết quả cho thấy cải thiện đáng kể về năng suất và chất lượng sản phẩm.',
            'Nghiên cứu phát triển giải pháp kỹ thuật sáng tạo cho ngành công nghiệp chế tạo. Ứng dụng các công nghệ mới nhất trong lĩnh vực vật liệu và gia công. Đạt được các chỉ tiêu kỹ thuật vượt trội và khả năng ứng dụng thực tế cao.'
        ];

        $projectTypes = ['design', 'analysis', 'manufacturing', 'prototype', 'assembly', 'testing', 'research', 'optimization', 'simulation'];
        $softwareOptions = ['SolidWorks', 'AutoCAD', 'Fusion 360', 'ANSYS', 'MATLAB'];
        $materials = ['Thép carbon', 'Thép không gỉ', 'Nhôm hợp kim', 'Composite', 'Titanium'];
        $industries = ['automotive', 'aerospace', 'manufacturing', 'energy', 'construction', 'marine', 'electronics', 'medical', 'general'];

        $showcases = Showcase::all();
        $updated = 0;

        foreach ($showcases as $showcase) {
            $needsUpdate = false;
            $updates = [];

            // Cập nhật title nếu thiếu hoặc không phù hợp
            if (empty($showcase->title) || strlen($showcase->title) < 10) {
                $updates['title'] = $mechanicalProjects[array_rand($mechanicalProjects)];
                $needsUpdate = true;
            }

            // Cập nhật description nếu thiếu hoặc quá ngắn
            if (empty($showcase->description) || strlen($showcase->description) < 100) {
                $updates['description'] = $mechanicalDescriptions[array_rand($mechanicalDescriptions)];
                $needsUpdate = true;
            }

            // Cập nhật technical specs
            if (empty($showcase->project_type)) {
                $updates['project_type'] = $projectTypes[array_rand($projectTypes)];
                $needsUpdate = true;
            }

            if (empty($showcase->software_used)) {
                $updates['software_used'] = $softwareOptions[array_rand($softwareOptions)];
                $needsUpdate = true;
            }

            if (empty($showcase->materials)) {
                $updates['materials'] = $materials[array_rand($materials)];
                $needsUpdate = true;
            }

            if (empty($showcase->industry_application)) {
                $updates['industry_application'] = $industries[array_rand($industries)];
                $needsUpdate = true;
            }

            // Cập nhật complexity level
            if (empty($showcase->complexity_level)) {
                $updates['complexity_level'] = ['beginner', 'intermediate', 'advanced', 'expert'][array_rand(['beginner', 'intermediate', 'advanced', 'expert'])];
                $needsUpdate = true;
            }

            // Cập nhật technical specs array
            if (empty($showcase->technical_specs)) {
                $updates['technical_specs'] = [
                    'dimensions' => '1000x500x300 mm',
                    'weight' => '25 kg',
                    'material_thickness' => '5 mm',
                    'operating_pressure' => '10 bar',
                    'temperature_range' => '-20°C to 80°C'
                ];
                $needsUpdate = true;
            }

            if ($needsUpdate) {
                $showcase->update($updates);
                $updated++;
                $this->command->info("✅ Cập nhật nội dung showcase ID {$showcase->id}");
            }
        }

        $this->command->info("📝 Đã cập nhật nội dung cho {$updated} showcases");
    }

    /**
     * Thêm hình ảnh cho showcases
     */
    private function addImages(): void
    {
        $this->command->info('🖼️ Thêm hình ảnh cho showcases...');

        // Danh sách hình ảnh có sẵn
        $availableImages = [
            '/images/showcase/1567174641278.jpg',
            '/images/showcase/DesignEngineer.jpg',
            '/images/showcase/Mechanical-Engineering-MS-Professionals-Hero-1600x900_0.jpg',
            '/images/showcase/Mechanical-Engineering.jpg',
            '/images/showcase/PFxP5HX8oNsLtufFRMumpc.jpg',
            '/images/showcase/depositphotos_73832701-Mechanical-design-office-.jpg',
            '/images/showcase/engineering_mechanical_3042380_cropped.jpg',
            '/images/showcase/mechanical-design-vs-mechanical-engineer2.jpg.webp',
            '/images/showcase/mj_11208_2.jpg',
            '/images/showcase/mj_11226_4.jpg',
            '/images/showcases/1567174641278.jpg',
            '/images/showcases/DesignEngineer.jpg',
            '/images/showcases/Mechanical-Engineering-MS-Professionals-Hero-1600x900_0.jpg',
            '/images/showcases/Mechanical-Engineering.jpg',
            '/images/showcases/PFxP5HX8oNsLtufFRMumpc.jpg',
            '/images/showcases/demo-3.jpg',
            '/images/showcases/demo-4.jpg',
            '/images/showcases/demo-5.jpg',
            '/images/demo/showcase-1.jpg',
            '/images/demo/showcase-2.jpg',
            '/images/demo/showcase-3.jpg',
            '/images/demo/showcase-4.jpg',
            '/images/demo/showcase-5.jpg'
        ];

        $showcasesWithoutImages = Showcase::whereNull('cover_image')->get();
        $updated = 0;

        foreach ($showcasesWithoutImages as $showcase) {
            $randomImage = $availableImages[array_rand($availableImages)];

            // Tạo gallery với 2-5 hình ảnh
            $galleryCount = rand(2, 5);
            $gallery = [];
            for ($i = 0; $i < $galleryCount; $i++) {
                $gallery[] = $availableImages[array_rand($availableImages)];
            }

            $showcase->update([
                'cover_image' => $randomImage,
                'image_gallery' => $gallery
            ]);

            $updated++;
            $this->command->info("✅ Thêm hình ảnh cho showcase ID {$showcase->id}: {$randomImage}");
        }

        $this->command->info("🖼️ Đã thêm hình ảnh cho {$updated} showcases");
    }

    /**
     * Phân loại visibility (80% public, 20% private)
     */
    private function setVisibility(): void
    {
        $this->command->info('👁️ Phân loại visibility showcases...');

        $showcases = Showcase::all();
        $totalShowcases = $showcases->count();

        // 80% public, 20% private
        $publicCount = (int) ($totalShowcases * 0.8);
        $privateCount = $totalShowcases - $publicCount;

        $updated = 0;

        foreach ($showcases as $index => $showcase) {
            $isPublic = $index < $publicCount;

            $showcase->update([
                'is_public' => $isPublic,
                'status' => $isPublic ? 'approved' : 'draft',
                'allow_comments' => $isPublic,
                'allow_downloads' => $isPublic && rand(0, 1), // 50% cho phép download
            ]);

            $updated++;

            if ($updated % 20 == 0) {
                $this->command->info("✅ Đã cập nhật visibility {$updated}/{$totalShowcases} showcases");
            }
        }

        $this->command->info("👁️ Hoàn thành phân loại visibility:");
        $this->command->info("  Public: {$publicCount}");
        $this->command->info("  Private: {$privateCount}");
    }

    /**
     * Tạo rating system (3.5-5.0 sao cho public showcases)
     */
    private function createRatings(): void
    {
        $this->command->info('⭐ Tạo rating system cho showcases...');

        $publicShowcases = Showcase::where('is_public', true)->get();
        $users = User::whereNotIn('role', ['guest'])->pluck('id')->toArray();

        if (empty($users)) {
            $this->command->error('Không tìm thấy user nào để tạo ratings!');
            return;
        }

        $created = 0;

        foreach ($publicShowcases as $showcase) {
            // Tạo 3-8 ratings cho mỗi showcase
            $ratingCount = rand(3, 8);
            $selectedUsers = array_rand(array_flip($users), min($ratingCount, count($users)));

            if (!is_array($selectedUsers)) {
                $selectedUsers = [$selectedUsers];
            }

            $totalRating = 0;
            $ratingsCreated = 0;

            foreach ($selectedUsers as $userId) {
                // Kiểm tra xem rating đã tồn tại chưa
                $existingRating = ShowcaseRating::where('showcase_id', $showcase->id)
                    ->where('user_id', $userId)
                    ->first();

                if ($existingRating) {
                    $totalRating += $existingRating->overall_rating;
                    $ratingsCreated++;
                    continue;
                }

                // Tạo rating từ 3.5-5.0 sao
                $technicalQuality = rand(3, 5);
                $innovation = rand(3, 5);
                $usefulness = rand(4, 5);
                $documentation = rand(3, 5);

                $rating = ShowcaseRating::create([
                    'showcase_id' => $showcase->id,
                    'user_id' => $userId,
                    'technical_quality' => $technicalQuality,
                    'innovation' => $innovation,
                    'usefulness' => $usefulness,
                    'documentation' => $documentation,
                    'review' => 'Dự án rất chất lượng và hữu ích cho cộng đồng kỹ thuật. Tài liệu chi tiết và dễ hiểu.',
                ]);

                $totalRating += $rating->overall_rating;
                $ratingsCreated++;
            }

            // Cập nhật average rating cho showcase
            $averageRating = $totalRating / $ratingsCreated;
            $showcase->update([
                'rating_average' => $averageRating,
                'rating_count' => $ratingsCreated,
                'technical_quality_score' => $averageRating
            ]);

            $created++;
            $this->command->info("✅ Tạo {$ratingsCreated} ratings cho showcase ID {$showcase->id} (avg: {$averageRating})");
        }

        $this->command->info("⭐ Đã tạo ratings cho {$created} public showcases");
    }
}
