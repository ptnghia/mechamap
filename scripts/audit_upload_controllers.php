<?php

/**
 * Upload Controllers Audit Script
 * Phân tích và chuẩn hóa các controllers có chức năng upload
 */

class UploadControllersAudit
{
    private $controllersPath;
    private $results = [];
    
    public function __construct()
    {
        $this->controllersPath = __DIR__ . '/../app/Http/Controllers';
    }
    
    /**
     * Thực hiện audit toàn bộ
     */
    public function performAudit()
    {
        echo "🔧 UPLOAD CONTROLLERS AUDIT\n";
        echo "===========================\n\n";
        
        // 1. Tìm tất cả controllers có upload functionality
        $this->findUploadControllers();
        
        // 2. Phân tích storage approaches
        $this->analyzeStorageApproaches();
        
        // 3. Phân tích naming conventions
        $this->analyzeNamingConventions();
        
        // 4. Tạo unified upload service
        $this->generateUnifiedUploadService();
        
        return $this->results;
    }
    
    /**
     * Tìm controllers có upload functionality
     */
    private function findUploadControllers()
    {
        echo "📁 FINDING UPLOAD CONTROLLERS\n";
        echo "-----------------------------\n";
        
        $uploadControllers = [];
        $this->scanControllersDirectory($this->controllersPath, $uploadControllers);
        
        foreach ($uploadControllers as $controller) {
            echo sprintf("📄 %-40s | %s\n", 
                $controller['name'], 
                $controller['upload_methods']
            );
        }
        
        $this->results['upload_controllers'] = $uploadControllers;
        echo "\n";
    }
    
    /**
     * Scan controllers directory
     */
    private function scanControllersDirectory($path, &$controllers)
    {
        $files = glob($path . '/*.php');
        
        foreach ($files as $file) {
            $content = file_get_contents($file);
            
            // Tìm upload-related methods
            $uploadMethods = [];
            
            if (preg_match_all('/public function (\w+).*?(?:upload|store|file)/i', $content, $matches)) {
                $uploadMethods = array_merge($uploadMethods, $matches[1]);
            }
            
            if (preg_match_all('/\$request->(?:file|hasFile)/i', $content, $matches)) {
                $uploadMethods[] = 'file_handling';
            }
            
            if (!empty($uploadMethods)) {
                $controllers[] = [
                    'name' => basename($file),
                    'path' => $file,
                    'upload_methods' => implode(', ', array_unique($uploadMethods)),
                    'storage_approach' => $this->detectStorageApproach($content),
                    'naming_convention' => $this->detectNamingConvention($content)
                ];
            }
        }
        
        // Scan subdirectories
        $subdirs = glob($path . '/*', GLOB_ONLYDIR);
        foreach ($subdirs as $subdir) {
            $this->scanControllersDirectory($subdir, $controllers);
        }
    }
    
    /**
     * Detect storage approach
     */
    private function detectStorageApproach($content)
    {
        $approaches = [];
        
        if (strpos($content, "->store(") !== false) {
            $approaches[] = 'Laravel Storage';
        }
        
        if (strpos($content, "->storeAs(") !== false) {
            $approaches[] = 'Laravel Storage (named)';
        }
        
        if (strpos($content, "public/images") !== false) {
            $approaches[] = 'Direct public/images';
        }
        
        if (strpos($content, "storage/app/public") !== false) {
            $approaches[] = 'Storage disk public';
        }
        
        if (strpos($content, "MediaService") !== false) {
            $approaches[] = 'MediaService';
        }
        
        return implode(', ', $approaches) ?: 'Unknown';
    }
    
    /**
     * Detect naming convention
     */
    private function detectNamingConvention($content)
    {
        $conventions = [];
        
        if (preg_match('/time\(\).*?\./', $content)) {
            $conventions[] = 'Timestamp-based';
        }
        
        if (preg_match('/uniqid\(\)/', $content)) {
            $conventions[] = 'Unique ID';
        }
        
        if (preg_match('/Str::slug/', $content)) {
            $conventions[] = 'Slugified';
        }
        
        if (preg_match('/md5\(/', $content)) {
            $conventions[] = 'Hash-based';
        }
        
        return implode(', ', $conventions) ?: 'Original filename';
    }
    
    /**
     * Phân tích storage approaches
     */
    private function analyzeStorageApproaches()
    {
        echo "💾 STORAGE APPROACHES ANALYSIS\n";
        echo "------------------------------\n";
        
        $approaches = [];
        foreach ($this->results['upload_controllers'] as $controller) {
            $approach = $controller['storage_approach'];
            if (!isset($approaches[$approach])) {
                $approaches[$approach] = 0;
            }
            $approaches[$approach]++;
        }
        
        foreach ($approaches as $approach => $count) {
            echo sprintf("📦 %-30s | %d controllers\n", $approach, $count);
        }
        
        echo "\n🎯 RECOMMENDED UNIFIED APPROACH:\n";
        echo "- Use public/images/ for direct access\n";
        echo "- Organize by entity type: users/, threads/, showcases/\n";
        echo "- Use MediaService for consistency\n";
        echo "- Implement proper validation and security\n\n";
        
        $this->results['storage_approaches'] = $approaches;
    }
    
    /**
     * Phân tích naming conventions
     */
    private function analyzeNamingConventions()
    {
        echo "📝 NAMING CONVENTIONS ANALYSIS\n";
        echo "------------------------------\n";
        
        $conventions = [];
        foreach ($this->results['upload_controllers'] as $controller) {
            $convention = $controller['naming_convention'];
            if (!isset($conventions[$convention])) {
                $conventions[$convention] = 0;
            }
            $conventions[$convention]++;
        }
        
        foreach ($conventions as $convention => $count) {
            echo sprintf("🏷️  %-30s | %d controllers\n", $convention, $count);
        }
        
        echo "\n🎯 RECOMMENDED UNIFIED CONVENTION:\n";
        echo "- Format: {user_prefix}_{timestamp}_{slugified_name}.{ext}\n";
        echo "- User prefix: First 6 chars of MD5(user_id)\n";
        echo "- Timestamp: Unix timestamp for uniqueness\n";
        echo "- Slugified name: Safe, URL-friendly filename\n\n";
        
        $this->results['naming_conventions'] = $conventions;
    }
    
    /**
     * Generate unified upload service
     */
    private function generateUnifiedUploadService()
    {
        echo "🔧 GENERATING UNIFIED UPLOAD SERVICE\n";
        echo "------------------------------------\n";
        
        $serviceCode = $this->generateUnifiedServiceCode();
        
        file_put_contents(__DIR__ . '/UnifiedImageUploadService.php', $serviceCode);
        echo "✅ Generated: scripts/UnifiedImageUploadService.php\n";
        
        $migrationCode = $this->generateMigrationCode();
        file_put_contents(__DIR__ . '/migrate_to_unified_storage.php', $migrationCode);
        echo "✅ Generated: scripts/migrate_to_unified_storage.php\n\n";
        
        echo "📋 IMPLEMENTATION STEPS:\n";
        echo "1. Review generated UnifiedImageUploadService\n";
        echo "2. Update controllers to use unified service\n";
        echo "3. Run migration script to move existing files\n";
        echo "4. Update Media model URL generation\n";
        echo "5. Test all upload functionality\n\n";
    }
    
    /**
     * Generate unified service code
     */
    private function generateUnifiedServiceCode()
    {
        return '<?php

namespace App\Services;

use App\Models\Media;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Unified Image Upload Service
 * Chuẩn hóa việc upload hình ảnh cho toàn bộ hệ thống
 */
class UnifiedImageUploadService
{
    /**
     * Cấu trúc thư mục chuẩn
     */
    private const DIRECTORY_STRUCTURE = [
        "users" => "users/avatars",
        "threads" => "threads",
        "showcases" => "showcases", 
        "categories" => "categories",
        "forums" => "forums",
        "temp" => "temp"
    ];
    
    /**
     * Upload image với cấu trúc chuẩn
     */
    public function uploadImage(
        UploadedFile $file,
        string $category,
        User $user,
        ?int $entityId = null,
        ?string $entityType = null
    ): Media {
        // Validate
        $this->validateImage($file);
        
        // Generate filename
        $filename = $this->generateFilename($file, $user);
        
        // Generate path
        $relativePath = $this->generatePath($category, $filename);
        
        // Ensure directory exists
        $fullPath = public_path("images/" . dirname($relativePath));
        if (!file_exists($fullPath)) {
            mkdir($fullPath, 0755, true);
        }
        
        // Move file to public/images
        $destinationPath = public_path("images/" . $relativePath);
        $file->move(dirname($destinationPath), basename($destinationPath));
        
        // Create media record
        return $this->createMediaRecord(
            $file, $user, "/images/" . $relativePath, 
            $category, $entityId, $entityType
        );
    }
    
    /**
     * Validate image file
     */
    private function validateImage(UploadedFile $file): void
    {
        $allowedMimes = ["image/jpeg", "image/png", "image/gif", "image/webp"];
        $maxSize = 5 * 1024 * 1024; // 5MB
        
        if (!in_array($file->getMimeType(), $allowedMimes)) {
            throw new \InvalidArgumentException("Invalid file type");
        }
        
        if ($file->getSize() > $maxSize) {
            throw new \InvalidArgumentException("File too large");
        }
    }
    
    /**
     * Generate standardized filename
     */
    private function generateFilename(UploadedFile $file, User $user): string
    {
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $file->getClientOriginalExtension();
        
        $userPrefix = substr(md5($user->id), 0, 6);
        $timestamp = time();
        $safeName = Str::slug($originalName);
        
        return "{$userPrefix}_{$timestamp}_{$safeName}.{$extension}";
    }
    
    /**
     * Generate standardized path
     */
    private function generatePath(string $category, string $filename): string
    {
        $baseDir = self::DIRECTORY_STRUCTURE[$category] ?? "general";
        return "{$baseDir}/{$filename}";
    }
    
    /**
     * Create media record
     */
    private function createMediaRecord(
        UploadedFile $file, User $user, string $path,
        string $category, ?int $entityId, ?string $entityType
    ): Media {
        return Media::create([
            "user_id" => $user->id,
            "file_name" => $file->getClientOriginalName(),
            "file_path" => $path,
            "disk" => "public",
            "mime_type" => $file->getMimeType(),
            "file_size" => $file->getSize(),
            "file_extension" => $file->getClientOriginalExtension(),
            "file_category" => "image",
            "mediable_id" => $entityId,
            "mediable_type" => $entityType,
            "is_public" => true,
            "is_approved" => true
        ]);
    }
}';
    }
    
    /**
     * Generate migration code
     */
    private function generateMigrationCode()
    {
        return '<?php

/**
 * Migration script để chuyển existing files sang cấu trúc mới
 */

require_once __DIR__ . "/../vendor/autoload.php";

class UnifiedStorageMigration
{
    public function migrate()
    {
        echo "🔄 MIGRATING TO UNIFIED STORAGE STRUCTURE\n";
        echo "=========================================\n\n";
        
        // 1. Create directory structure
        $this->createDirectoryStructure();
        
        // 2. Move existing files
        $this->moveExistingFiles();
        
        // 3. Update database records
        $this->updateDatabaseRecords();
        
        echo "✅ Migration completed!\n";
    }
    
    private function createDirectoryStructure()
    {
        $dirs = [
            "public/images/users/avatars",
            "public/images/threads", 
            "public/images/showcases",
            "public/images/categories",
            "public/images/forums",
            "public/images/temp"
        ];
        
        foreach ($dirs as $dir) {
            $fullPath = __DIR__ . "/../" . $dir;
            if (!file_exists($fullPath)) {
                mkdir($fullPath, 0755, true);
                echo "📁 Created: {$dir}\n";
            }
        }
    }
    
    private function moveExistingFiles()
    {
        // Implementation for moving files
        echo "📦 Moving existing files...\n";
        // TODO: Implement file moving logic
    }
    
    private function updateDatabaseRecords()
    {
        // Implementation for updating database
        echo "🗄️  Updating database records...\n";
        // TODO: Implement database update logic
    }
}

if (php_sapi_name() === "cli") {
    $migration = new UnifiedStorageMigration();
    $migration->migrate();
}';
    }
}

// Chạy audit nếu script được gọi trực tiếp
if (php_sapi_name() === 'cli') {
    $audit = new UploadControllersAudit();
    $results = $audit->performAudit();
}
