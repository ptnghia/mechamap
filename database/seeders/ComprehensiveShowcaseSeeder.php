<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Http\UploadedFile;
use App\Models\Showcase;
use App\Models\User;
use App\Models\ShowcaseCategory;
use App\Models\ShowcaseType;
use App\Models\ShowcaseMedia;
use App\Models\ShowcaseAttachment;

class ComprehensiveShowcaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚀 Creating comprehensive showcase with full data...');

        // Get a user to create showcase for
        $user = User::where('role', 'member')->first();
        if (!$user) {
            $user = User::factory()->create([
                'role' => 'member',
                'username' => 'showcaseuser01',
                'email' => 'showcaseuser01@mechamap.test',
                'name' => 'Showcase User 01',
            ]);
        }

        // Ensure showcase categories exist
        $this->ensureShowcaseCategories();

        // Create comprehensive showcase
        $showcase = $this->createComprehensiveShowcase($user);

        // Update image_gallery and file_attachments arrays
        $this->updateShowcaseArrays($showcase, $user);

        $this->command->info("✅ Created comprehensive showcase: {$showcase->title}");
        $this->command->info("📁 Showcase ID: {$showcase->id}");
        $this->command->info("👤 User: {$user->name} ({$user->username})");
        $this->command->info("🔗 URL: https://mechamap.test/showcase/{$showcase->id}");
    }

    /**
     * Ensure showcase categories exist
     */
    private function ensureShowcaseCategories(): void
    {
        $categories = [
            ['name' => 'Design', 'slug' => 'design', 'description' => 'CAD Design và Modeling'],
            ['name' => 'Manufacturing', 'slug' => 'manufacturing', 'description' => 'Quy trình sản xuất'],
            ['name' => 'Analysis', 'slug' => 'analysis', 'description' => 'Phân tích kỹ thuật'],
            ['name' => 'Automation', 'slug' => 'automation', 'description' => 'Tự động hóa'],
        ];

        foreach ($categories as $category) {
            ShowcaseCategory::firstOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }
    }

    /**
     * Create comprehensive showcase with all fields
     */
    private function createComprehensiveShowcase(User $user): Showcase
    {
        // Create cover image
        $coverImagePath = $this->createSampleCoverImage($user);

        $showcase = Showcase::create([
            'user_id' => $user->id,
            'showcaseable_type' => 'App\\Models\\Thread', // Required field
            'showcaseable_id' => 1, // Use existing thread ID or create one
            'title' => 'Thiết kế và Phân tích Hệ thống Truyền động Robot Công nghiệp',
            'slug' => 'thiet-ke-phan-tich-he-thong-truyen-dong-robot-cong-nghiep',
            'description' => 'Dự án thiết kế và phân tích hệ thống truyền động cho robot công nghiệp 6 bậc tự do, bao gồm tính toán động học, động lực học và tối ưu hóa hiệu suất. Dự án sử dụng SolidWorks cho thiết kế 3D, ANSYS cho phân tích FEA và MATLAB cho mô phỏng điều khiển. Địa điểm: TP. Hồ Chí Minh, Việt Nam. Ứng dụng: Robot công nghiệp cho dây chuyền sản xuất ô tô.',
            'cover_image' => $coverImagePath,
            'status' => 'approved',
            'category' => 'Design',

            // Enhanced fields for mechanical engineering
            'project_type' => 'design',
            'software_used' => json_encode([
                'SolidWorks', 'ANSYS Mechanical', 'MATLAB', 'Simulink',
                'AutoCAD', 'KeyShot', 'ADAMS', 'RobotStudio'
            ]),
            'materials' => 'Thép không gỉ 316L, Nhôm 7075-T6, Composite carbon fiber, Bearing SKF, Motor servo Siemens',
            'manufacturing_process' => 'CNC machining, 3D printing (SLA), Welding TIG, Heat treatment, Surface coating',
            'technical_specs' => json_encode([
                'payload' => '50 kg',
                'reach' => '1800 mm',
                'repeatability' => '±0.05 mm',
                'max_speed' => '2.5 m/s',
                'degrees_of_freedom' => 6,
                'weight' => '850 kg',
                'power_consumption' => '7.5 kW',
                'operating_temperature' => '-10°C to +50°C',
                'protection_rating' => 'IP65',
                'control_system' => 'Siemens SINUMERIK',
                'communication' => 'EtherCAT, Profinet',
                'safety_features' => 'Emergency stop, Light curtains, Safety PLC'
            ]),
            'complexity_level' => 'advanced',
            'industry_application' => 'Automotive manufacturing, Assembly line automation',
            'has_tutorial' => true,
            'has_calculations' => true,
            'has_cad_files' => true,
            'learning_objectives' => json_encode([
                'Hiểu nguyên lý thiết kế robot công nghiệp',
                'Nắm vững phương pháp tính toán động học và động lực học',
                'Thành thạo sử dụng SolidWorks cho thiết kế cơ khí',
                'Áp dụng ANSYS cho phân tích FEA',
                'Lập trình điều khiển robot với MATLAB/Simulink',
                'Tối ưu hóa hiệu suất và độ chính xác'
            ]),
            'image_gallery' => json_encode([]),
            'file_attachments' => json_encode([]),
            'is_public' => true,
            'allow_downloads' => true,
            'allow_comments' => true,
            'view_count' => rand(100, 1000),
            'like_count' => rand(10, 50),
            'download_count' => rand(5, 25),
            'share_count' => rand(2, 15),
            'rating_average' => round(rand(35, 50) / 10, 1), // 3.5 - 5.0
            'rating_count' => rand(5, 20),
            'technical_quality_score' => round(rand(80, 95) / 10, 1), // 8.0 - 9.5
            'display_order' => 1,
            'featured_at' => now(),
            'approved_at' => now(),
            'approved_by' => 1,
            'showcase_category_id' => ShowcaseCategory::where('slug', 'design')->first()?->id,
        ]);

        return $showcase;
    }

    /**
     * Create sample cover image
     */
    private function createSampleCoverImage(User $user): string
    {
        // Create user directory if not exists
        $userDir = "public/uploads/showcases/{$user->id}";
        if (!Storage::exists($userDir)) {
            Storage::makeDirectory($userDir);
        }

        // Ensure the physical directory exists
        $physicalDir = storage_path("app/{$userDir}");
        if (!File::exists($physicalDir)) {
            File::makeDirectory($physicalDir, 0755, true);
        }

        // Create a simple placeholder image
        $imagePath = storage_path("app/{$userDir}/cover_robot_design.jpg");

        // Create a simple colored rectangle as placeholder
        $image = imagecreate(800, 600);
        $background = imagecolorallocate($image, 45, 55, 72); // Dark blue-gray
        $textColor = imagecolorallocate($image, 255, 255, 255); // White
        $accentColor = imagecolorallocate($image, 0, 123, 255); // Blue

        // Fill background
        imagefill($image, 0, 0, $background);

        // Add some geometric shapes
        imagefilledrectangle($image, 50, 50, 750, 100, $accentColor);
        imagefilledrectangle($image, 50, 150, 400, 450, $accentColor);
        imagefilledrectangle($image, 450, 200, 750, 500, $accentColor);

        // Add text using built-in fonts
        $title = "Robot Industrial Design";
        $subtitle = "6-DOF Robotic Arm System";

        // Use built-in fonts (more reliable)
        imagestring($image, 5, 60, 60, $title, $textColor);
        imagestring($image, 3, 60, 120, $subtitle, $textColor);
        imagestring($image, 2, 60, 150, "MechaMap Showcase Demo", $textColor);

        // Save image
        imagejpeg($image, $imagePath, 90);
        imagedestroy($image);

        return "public/uploads/showcases/{$user->id}/cover_robot_design.jpg";
    }

    /**
     * Update showcase arrays with sample data
     */
    private function updateShowcaseArrays(Showcase $showcase, User $user): void
    {
        // Create sample image paths
        $imageGallery = [];
        $imageData = [
            ['name' => 'robot_assembly.jpg', 'title' => 'Robot Assembly View', 'color' => [70, 130, 180]],
            ['name' => 'joint_detail.jpg', 'title' => 'Joint Detail Design', 'color' => [60, 179, 113]],
            ['name' => 'control_system.jpg', 'title' => 'Control System Layout', 'color' => [255, 140, 0]],
            ['name' => 'fea_analysis.jpg', 'title' => 'FEA Stress Analysis', 'color' => [220, 20, 60]],
            ['name' => 'workspace_envelope.jpg', 'title' => 'Workspace Envelope', 'color' => [138, 43, 226]],
        ];

        foreach ($imageData as $index => $imageInfo) {
            $imagePath = $this->createSampleImage($user, $imageInfo['name'], $imageInfo['title'], $imageInfo['color']);

            $imageGallery[] = [
                'path' => $imagePath,
                'name' => $imageInfo['name'],
                'title' => $imageInfo['title'],
                'description' => "Detailed view of {$imageInfo['title']} in the robot design project",
                'order' => $index + 1,
                'size' => rand(500000, 2000000),
                'mime_type' => 'image/jpeg',
                'uploaded_at' => now()->toISOString(),
            ];
        }

        // Create sample file attachments
        $fileAttachments = $this->createSampleAttachmentArray($user);

        // Update showcase with arrays
        $showcase->update([
            'image_gallery' => $imageGallery,
            'file_attachments' => $fileAttachments,
        ]);
    }

    /**
     * Create sample image file
     */
    private function createSampleImage(User $user, string $filename, string $title, array $color): string
    {
        $userDir = "public/uploads/showcases/{$user->id}/gallery";
        if (!Storage::exists($userDir)) {
            Storage::makeDirectory($userDir);
        }

        // Ensure the physical directory exists
        $physicalDir = storage_path("app/{$userDir}");
        if (!File::exists($physicalDir)) {
            File::makeDirectory($physicalDir, 0755, true);
        }

        $imagePath = storage_path("app/{$userDir}/{$filename}");

        // Create image
        $image = imagecreate(600, 400);
        $background = imagecolorallocate($image, $color[0], $color[1], $color[2]);
        $textColor = imagecolorallocate($image, 255, 255, 255);
        $borderColor = imagecolorallocate($image, 200, 200, 200);

        // Fill and add border
        imagefill($image, 0, 0, $background);
        imagerectangle($image, 0, 0, 599, 399, $borderColor);

        // Add title using built-in font
        imagestring($image, 4, 20, 180, $title, $textColor);

        // Save image
        imagejpeg($image, $imagePath, 85);
        imagedestroy($image);

        return "public/uploads/showcases/{$user->id}/gallery/{$filename}";
    }

    /**
     * Create sample attachments array
     */
    private function createSampleAttachmentArray(User $user): array
    {
        $attachments = [
            [
                'name' => 'Robot_Design_Calculations.pdf',
                'title' => 'Tính toán thiết kế robot',
                'description' => 'Tài liệu tính toán chi tiết động học và động lực học',
                'type' => 'document'
            ],
            [
                'name' => 'SolidWorks_Assembly.zip',
                'title' => 'File SolidWorks Assembly',
                'description' => 'File 3D CAD hoàn chỉnh của robot assembly',
                'type' => 'cad'
            ],
            [
                'name' => 'ANSYS_FEA_Results.zip',
                'title' => 'Kết quả phân tích FEA',
                'description' => 'Kết quả phân tích ứng suất và biến dạng',
                'type' => 'analysis'
            ],
            [
                'name' => 'MATLAB_Control_Code.zip',
                'title' => 'Code điều khiển MATLAB',
                'description' => 'Source code điều khiển và mô phỏng',
                'type' => 'code'
            ],
            [
                'name' => 'Technical_Specifications.docx',
                'title' => 'Thông số kỹ thuật',
                'description' => 'Bảng thông số kỹ thuật chi tiết',
                'type' => 'document'
            ],
        ];

        $fileAttachments = [];
        foreach ($attachments as $index => $attachmentInfo) {
            $filePath = $this->createSampleAttachment($user, $attachmentInfo);

            $fileAttachments[] = [
                'path' => $filePath,
                'name' => $attachmentInfo['name'],
                'original_name' => $attachmentInfo['name'],
                'title' => $attachmentInfo['title'],
                'description' => $attachmentInfo['description'],
                'type' => $attachmentInfo['type'],
                'mime_type' => $this->getMimeType($attachmentInfo['name']),
                'size' => rand(1000000, 10000000), // 1MB - 10MB
                'download_count' => rand(0, 50),
                'order' => $index + 1,
                'uploaded_at' => now()->toISOString(),
            ];
        }

        return $fileAttachments;
    }

    /**
     * Create sample attachment file
     */
    private function createSampleAttachment(User $user, array $attachmentInfo): string
    {
        $userDir = "public/uploads/showcases/{$user->id}/attachments";
        if (!Storage::exists($userDir)) {
            Storage::makeDirectory($userDir);
        }

        // Ensure the physical directory exists
        $physicalDir = storage_path("app/{$userDir}");
        if (!File::exists($physicalDir)) {
            File::makeDirectory($physicalDir, 0755, true);
        }

        $filename = time() . '_' . $attachmentInfo['name'];
        $filePath = storage_path("app/{$userDir}/{$filename}");

        // Create sample file content
        $content = "Sample file: {$attachmentInfo['title']}\n";
        $content .= "Description: {$attachmentInfo['description']}\n";
        $content .= "Created: " . now()->toDateTimeString() . "\n";
        $content .= "Type: {$attachmentInfo['type']}\n\n";
        $content .= str_repeat("Sample content for demonstration purposes.\n", 100);

        file_put_contents($filePath, $content);

        return "public/uploads/showcases/{$user->id}/attachments/{$filename}";
    }

    /**
     * Get MIME type for file extension
     */
    private function getMimeType(string $filename): string
    {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        return match ($extension) {
            'pdf' => 'application/pdf',
            'zip' => 'application/zip',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'doc' => 'application/msword',
            'txt' => 'text/plain',
            default => 'application/octet-stream',
        };
    }
}
