<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Thread;
use App\Models\User;
use App\Models\Forum;
use App\Models\Poll;
use App\Models\PollOption;
use Carbon\Carbon;

class ThreadDataStandardizationSeeder extends Seeder
{
    /**
     * 🔧 MechaMap Thread Data Standardization Seeder
     *
     * Chuẩn hóa dữ liệu threads theo yêu cầu:
     * - User validation và permissions
     * - Nội dung chất lượng bám sát chủ đề cơ khí
     * - Hình ảnh đầy đủ từ thư mục có sẵn
     * - Polls cho 30% threads
     * - Trạng thái đa dạng
     */
    public function run(): void
    {
        $this->command->info('🚀 Bắt đầu chuẩn hóa dữ liệu threads...');

        // Backup trước khi thực hiện
        $this->createBackup();

        // Phân tích hiện trạng
        $this->analyzeCurrentState();

        // Chuẩn hóa dữ liệu
        $this->validateUsers();
        $this->standardizeContent();
        $this->addImages();
        $this->createPolls();
        $this->diversifyStatuses();

        $this->command->info('✅ Hoàn thành chuẩn hóa dữ liệu threads!');
    }

    /**
     * Tạo backup trước khi chuẩn hóa
     */
    private function createBackup(): void
    {
        $this->command->info('📦 Tạo backup dữ liệu threads...');

        $timestamp = now()->format('Y-m-d_H-i-s');
        $backupFile = storage_path("app/backups/threads_backup_{$timestamp}.json");

        // Tạo thư mục backup nếu chưa có
        if (!file_exists(dirname($backupFile))) {
            mkdir(dirname($backupFile), 0755, true);
        }

        // Export threads data as JSON
        $threads = Thread::all()->toArray();
        file_put_contents($backupFile, json_encode($threads, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $this->command->info("✅ Backup tạo tại: {$backupFile}");
        $this->command->info("📊 Đã backup " . count($threads) . " threads");
    }

    /**
     * Phân tích hiện trạng dữ liệu
     */
    private function analyzeCurrentState(): void
    {
        $this->command->info('📊 Phân tích hiện trạng threads...');

        $totalThreads = Thread::count();
        $this->command->info("Tổng số threads: {$totalThreads}");

        // Phân bố theo moderation status
        $statusStats = Thread::select('moderation_status', DB::raw('count(*) as count'))
            ->groupBy('moderation_status')
            ->get();

        $this->command->info('Phân bố theo moderation_status:');
        foreach ($statusStats as $stat) {
            $this->command->info("  {$stat->moderation_status}: {$stat->count} threads");
        }

        // Kiểm tra nội dung
        $emptyTitle = Thread::where('title', '')->orWhereNull('title')->count();
        $emptyContent = Thread::where('content', '')->orWhereNull('content')->count();
        $noFeaturedImage = Thread::whereNull('featured_image')->count();
        $shortContent = Thread::whereRaw('LENGTH(content) < 50')->count();

        $this->command->info('Vấn đề nội dung:');
        $this->command->info("  Thiếu title: {$emptyTitle}");
        $this->command->info("  Thiếu content: {$emptyContent}");
        $this->command->info("  Không có featured_image: {$noFeaturedImage}");
        $this->command->info("  Content < 50 ký tự: {$shortContent}");

        // Kiểm tra foreign keys
        $invalidUsers = Thread::whereNotIn('user_id', User::pluck('id'))->count();
        $invalidForums = Thread::whereNotIn('forum_id', Forum::pluck('id'))->count();

        $this->command->info('Vấn đề foreign keys:');
        $this->command->info("  User_id không tồn tại: {$invalidUsers}");
        $this->command->info("  Forum_id không tồn tại: {$invalidForums}");
    }

    /**
     * Validate và fix user permissions
     */
    private function validateUsers(): void
    {
        $this->command->info('👤 Kiểm tra và sửa user permissions...');

        // Lấy threads có user_id không tồn tại hoặc user không có quyền
        $invalidThreads = Thread::whereNotIn('user_id', User::pluck('id'))->get();

        // Lấy threads của guest users (không được phép tạo threads)
        $guestThreads = Thread::whereHas('user', function($query) {
            $query->where('role', 'guest');
        })->get();

        $this->command->info("Tìm thấy {$invalidThreads->count()} threads với user_id không hợp lệ");
        $this->command->info("Tìm thấy {$guestThreads->count()} threads của guest users");

        // Lấy danh sách users có quyền tạo threads (member trở lên)
        $validUsers = User::whereNotIn('role', ['guest'])->pluck('id')->toArray();

        if (empty($validUsers)) {
            $this->command->error('Không tìm thấy user nào có quyền tạo threads!');
            return;
        }

        // Fix invalid threads
        foreach ($invalidThreads as $thread) {
            $randomUserId = $validUsers[array_rand($validUsers)];
            $thread->update(['user_id' => $randomUserId]);
            $this->command->info("✅ Cập nhật thread ID {$thread->id} với user_id {$randomUserId}");
        }

        // Fix guest threads
        foreach ($guestThreads as $thread) {
            $randomUserId = $validUsers[array_rand($validUsers)];
            $thread->update(['user_id' => $randomUserId]);
            $this->command->info("✅ Chuyển thread ID {$thread->id} từ guest sang user_id {$randomUserId}");
        }
    }

    /**
     * Chuẩn hóa nội dung threads
     */
    private function standardizeContent(): void
    {
        $this->command->info('📝 Chuẩn hóa nội dung threads...');

        $mechanicalTopics = [
            'CAD Design và Modeling',
            'CNC Machining và Programming',
            'Robot Automation Systems',
            'Materials Engineering',
            'Hydraulic Systems Design',
            'Pneumatic Control Systems',
            'Mechanical Assembly Techniques',
            'Quality Control và Testing',
            'Manufacturing Process Optimization',
            'Industrial Maintenance',
            'Welding và Fabrication',
            'Bearing và Lubrication Systems',
            'Gear Design và Analysis',
            'Vibration Analysis',
            'Thermal Management',
            'Precision Measurement',
            'Tool Design và Manufacturing',
            'Lean Manufacturing',
            'Safety Systems Design',
            'Project Management trong Cơ khí'
        ];

        $mechanicalContent = [
            'Trong quá trình thiết kế hệ thống cơ khí này, tôi đã áp dụng các nguyên lý kỹ thuật tiên tiến để đảm bảo hiệu suất tối ưu. Hệ thống được thiết kế với độ chính xác cao, sử dụng vật liệu chất lượng và tuân thủ các tiêu chuẩn quốc tế.',
            'Dự án này tập trung vào việc tối ưu hóa quy trình sản xuất thông qua việc ứng dụng công nghệ tự động hóa hiện đại. Chúng tôi đã phân tích chi tiết các thông số kỹ thuật và đưa ra giải pháp phù hợp với yêu cầu thực tế.',
            'Nghiên cứu này khám phá các phương pháp cải tiến trong lĩnh vực cơ khí chính xác. Bằng cách áp dụng các công cụ CAD/CAM tiên tiến, chúng tôi đã đạt được độ chính xác cao trong quá trình gia công.',
            'Hệ thống điều khiển được thiết kế với khả năng giám sát và điều chỉnh tự động các thông số vận hành. Điều này giúp tăng hiệu suất và giảm thiểu sai sót trong quá trình sản xuất.',
            'Phân tích kỹ thuật cho thấy việc sử dụng vật liệu composite trong ứng dụng này mang lại nhiều ưu điểm về trọng lượng và độ bền. Chúng tôi đã thực hiện các thử nghiệm để xác minh tính khả thi của giải pháp.'
        ];

        $threads = Thread::all();
        $updated = 0;

        foreach ($threads as $thread) {
            $needsUpdate = false;
            $updates = [];

            // Cập nhật title nếu thiếu hoặc không phù hợp
            if (empty($thread->title) || strlen($thread->title) < 10) {
                $updates['title'] = $mechanicalTopics[array_rand($mechanicalTopics)] . ' - ' .
                                   ['Hướng dẫn', 'Thảo luận', 'Kinh nghiệm', 'Giải pháp', 'Phân tích'][array_rand(['Hướng dẫn', 'Thảo luận', 'Kinh nghiệm', 'Giải pháp', 'Phân tích'])];
                $needsUpdate = true;
            }

            // Cập nhật content nếu thiếu hoặc quá ngắn
            if (empty($thread->content) || strlen($thread->content) < 50) {
                $updates['content'] = $mechanicalContent[array_rand($mechanicalContent)] .
                                     ' Các thông số kỹ thuật chi tiết và phương pháp thực hiện sẽ được trình bày trong các phần tiếp theo.';
                $needsUpdate = true;
            }

            // Cập nhật meta_description từ content
            if (isset($updates['content']) || empty($thread->meta_description)) {
                $content = $updates['content'] ?? $thread->content;
                $updates['meta_description'] = substr(strip_tags($content), 0, 150) . '...';
                $needsUpdate = true;
            }

            if ($needsUpdate) {
                $thread->update($updates);
                $updated++;
                $this->command->info("✅ Cập nhật nội dung thread ID {$thread->id}");
            }
        }

        $this->command->info("📝 Đã cập nhật nội dung cho {$updated} threads");
    }

    /**
     * Thêm hình ảnh cho threads
     */
    private function addImages(): void
    {
        $this->command->info('🖼️ Thêm hình ảnh cho threads...');

        // Danh sách hình ảnh có sẵn
        $availableImages = [
            '/images/threads/ImageForArticle_20492_16236782958233468.webp',
            '/images/threads/Mechanical-Engineer-1-1024x536.webp',
            '/images/threads/Mechanical-Engineering-thumbnail.jpg',
            '/images/threads/Mechanical_components.png',
            '/images/threads/Professional Engineer.webp',
            '/images/threads/compressed_2151589656.jpg',
            '/images/threads/images.jpg',
            '/images/threads/male-asian-engineer-professional-having-discussion-standing-by-machine-factory-two-asian-coworker-brainstorm-explaining-solves-process-curcuit-mother-board-machine.webp',
            '/images/threads/male-worker-factory.webp',
            '/images/threads/man-woman-engineering-computer-mechanical.jpg',
            '/images/threads/mechanical-engineering-la-gi-7.webp',
            '/images/threads/mechanical-mini-projects-cover-pic.webp',
            '/images/threads/mechanical-update_0.jpg',
            '/images/threads/mj_11351_4.jpg',
            '/images/threads/program-mech-eng.jpg',
            '/images/threads/success-story-schuetz-industrie-anlagenmechanikerin-2128x1330-c.jpg.webp',
            '/images/demo/thread-1.jpg',
            '/images/demo/thread-2.jpg',
            '/images/demo/thread-3.jpg',
            '/images/demo/thread-4.jpg',
            '/images/demo/thread-5.jpg'
        ];

        $threadsWithoutImages = Thread::whereNull('featured_image')->get();
        $updated = 0;

        foreach ($threadsWithoutImages as $thread) {
            $randomImage = $availableImages[array_rand($availableImages)];
            $thread->update(['featured_image' => $randomImage]);
            $updated++;
            $this->command->info("✅ Thêm featured_image cho thread ID {$thread->id}: {$randomImage}");
        }

        $this->command->info("🖼️ Đã thêm hình ảnh cho {$updated} threads");
    }

    /**
     * Tạo polls cho 30% threads
     */
    private function createPolls(): void
    {
        $this->command->info('📊 Tạo polls cho threads...');

        $threadsWithoutPolls = Thread::whereDoesntHave('poll')->get();
        $targetCount = (int) ($threadsWithoutPolls->count() * 0.3);
        $selectedThreads = $threadsWithoutPolls->random(min($targetCount, $threadsWithoutPolls->count()));

        $pollQuestions = [
            'Phần mềm CAD nào bạn thường sử dụng nhất?',
            'Vật liệu nào phù hợp nhất cho ứng dụng này?',
            'Phương pháp gia công nào hiệu quả nhất?',
            'Tiêu chuẩn chất lượng nào nên áp dụng?',
            'Công nghệ tự động hóa nào đáng đầu tư?',
            'Phương pháp bảo trì nào tối ưu nhất?',
            'Giải pháp an toàn nào quan trọng nhất?',
            'Xu hướng công nghệ nào đáng chú ý?'
        ];

        $pollOptions = [
            ['AutoCAD', 'SolidWorks', 'Fusion 360', 'Inventor'],
            ['Thép carbon', 'Thép không gỉ', 'Nhôm hợp kim', 'Composite'],
            ['CNC Milling', 'CNC Turning', 'EDM', 'Laser Cutting'],
            ['ISO 9001', 'ASME', 'JIS', 'DIN'],
            ['Robot công nghiệp', 'PLC', 'IoT sensors', 'AI/ML'],
            ['Preventive', 'Predictive', 'Reactive', 'Condition-based'],
            ['Safety guards', 'Emergency stops', 'Training', 'PPE'],
            ['Industry 4.0', 'Additive Manufacturing', 'Digital Twin', 'Green Technology']
        ];

        $created = 0;
        foreach ($selectedThreads as $thread) {
            $questionIndex = array_rand($pollQuestions);
            $question = $pollQuestions[$questionIndex];
            $options = $pollOptions[$questionIndex];

            $poll = Poll::create([
                'thread_id' => $thread->id,
                'question' => $question,
                'max_options' => 1,
                'allow_change_vote' => true,
                'show_votes_publicly' => true,
                'allow_view_without_vote' => false,
                'close_at' => now()->addDays(30),
            ]);

            foreach ($options as $optionText) {
                PollOption::create([
                    'poll_id' => $poll->id,
                    'text' => $optionText,
                ]);
            }

            $created++;
            $this->command->info("✅ Tạo poll cho thread ID {$thread->id}: {$question}");
        }

        $this->command->info("📊 Đã tạo {$created} polls");
    }

    /**
     * Đa dạng hóa trạng thái threads
     */
    private function diversifyStatuses(): void
    {
        $this->command->info('🎯 Đa dạng hóa trạng thái threads...');

        $threads = Thread::all();
        $totalThreads = $threads->count();

        // Phân bố mục tiêu: 70% approved, 15% pending, 10% pinned, 3% locked, 2% banned
        $statusDistribution = [
            'approved' => (int) ($totalThreads * 0.70),
            'pending' => (int) ($totalThreads * 0.15),
            'flagged' => (int) ($totalThreads * 0.02), // banned
        ];

        // Đặc biệt: pinned và locked
        $pinnedCount = (int) ($totalThreads * 0.10);
        $lockedCount = (int) ($totalThreads * 0.03);

        $updated = 0;
        $statusCounts = ['approved' => 0, 'pending' => 0, 'flagged' => 0];

        foreach ($threads as $index => $thread) {
            $updates = [];

            // Xác định moderation_status
            if ($statusCounts['approved'] < $statusDistribution['approved']) {
                $updates['moderation_status'] = 'approved';
                $statusCounts['approved']++;
            } elseif ($statusCounts['pending'] < $statusDistribution['pending']) {
                $updates['moderation_status'] = 'pending';
                $statusCounts['pending']++;
            } elseif ($statusCounts['flagged'] < $statusDistribution['flagged']) {
                $updates['moderation_status'] = 'flagged';
                $statusCounts['flagged']++;
            } else {
                $updates['moderation_status'] = 'approved';
            }

            // Xác định pinned status
            if ($index < $pinnedCount) {
                $updates['is_sticky'] = true;
            }

            // Xác định locked status
            if ($index >= $pinnedCount && $index < $pinnedCount + $lockedCount) {
                $updates['is_locked'] = true;
            }

            $thread->update($updates);
            $updated++;

            if ($updated % 20 == 0) {
                $this->command->info("✅ Đã cập nhật {$updated}/{$totalThreads} threads");
            }
        }

        $this->command->info("🎯 Hoàn thành đa dạng hóa trạng thái cho {$updated} threads");
        $this->command->info("📊 Phân bố cuối cùng:");
        $this->command->info("  Approved: {$statusCounts['approved']}");
        $this->command->info("  Pending: {$statusCounts['pending']}");
        $this->command->info("  Flagged: {$statusCounts['flagged']}");
        $this->command->info("  Pinned: {$pinnedCount}");
        $this->command->info("  Locked: {$lockedCount}");
    }
}
