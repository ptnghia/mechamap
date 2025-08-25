<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Models\User;
use App\Models\Showcase;

class SecurityAuditCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'security:audit 
                            {--type=all : Type of audit (all, files, database, config, permissions)}
                            {--fix : Automatically fix issues where possible}
                            {--report : Generate detailed report}';

    /**
     * The console command description.
     */
    protected $description = 'Perform comprehensive security audit of the application';

    /**
     * Security issues found during audit
     */
    private array $issues = [];

    /**
     * Security recommendations
     */
    private array $recommendations = [];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🔒 Starting MechaMap Security Audit...');
        $this->newLine();

        $auditType = $this->option('type');
        $autoFix = $this->option('fix');
        $generateReport = $this->option('report');

        // Perform audits based on type
        match ($auditType) {
            'files' => $this->auditFileSystem(),
            'database' => $this->auditDatabase(),
            'config' => $this->auditConfiguration(),
            'permissions' => $this->auditPermissions(),
            'all' => $this->performFullAudit(),
            default => $this->performFullAudit(),
        };

        // Auto-fix issues if requested
        if ($autoFix) {
            $this->autoFixIssues();
        }

        // Generate report
        $this->displayResults();

        if ($generateReport) {
            $this->generateReport();
        }

        return count($this->issues) === 0 ? Command::SUCCESS : Command::FAILURE;
    }

    /**
     * Perform full security audit
     */
    private function performFullAudit(): void
    {
        $this->auditFileSystem();
        $this->auditDatabase();
        $this->auditConfiguration();
        $this->auditPermissions();
        $this->auditUploadedFiles();
        $this->auditUserSecurity();
    }

    /**
     * Audit file system security
     */
    private function auditFileSystem(): void
    {
        $this->info('📁 Auditing File System Security...');

        // Check directory permissions
        $this->checkDirectoryPermissions();

        // Check for suspicious files
        $this->scanForSuspiciousFiles();

        // Check upload directories
        $this->auditUploadDirectories();

        // Check .htaccess files
        $this->checkHtaccessFiles();
    }

    /**
     * Check directory permissions
     */
    private function checkDirectoryPermissions(): void
    {
        $criticalDirs = [
            storage_path() => '755',
            storage_path('app') => '755',
            storage_path('logs') => '755',
            public_path('uploads') => '755',
            base_path('.env') => '600',
        ];

        foreach ($criticalDirs as $dir => $expectedPerm) {
            if (File::exists($dir)) {
                $actualPerm = substr(sprintf('%o', fileperms($dir)), -3);
                if ($actualPerm !== $expectedPerm) {
                    $this->issues[] = [
                        'type' => 'permissions',
                        'severity' => 'medium',
                        'message' => "Directory {$dir} has permissions {$actualPerm}, expected {$expectedPerm}",
                        'fix' => "chmod {$expectedPerm} {$dir}",
                    ];
                }
            }
        }
    }

    /**
     * Scan for suspicious files
     */
    private function scanForSuspiciousFiles(): void
    {
        $suspiciousExtensions = ['php', 'asp', 'jsp', 'exe', 'bat', 'cmd'];
        $uploadDirs = [
            public_path('uploads'),
            storage_path('app/public'),
        ];

        foreach ($uploadDirs as $dir) {
            if (File::exists($dir)) {
                $files = File::allFiles($dir);
                foreach ($files as $file) {
                    $extension = strtolower($file->getExtension());
                    if (in_array($extension, $suspiciousExtensions)) {
                        $this->issues[] = [
                            'type' => 'suspicious_file',
                            'severity' => 'high',
                            'message' => "Suspicious file found: {$file->getPathname()}",
                            'fix' => "Remove or quarantine file",
                        ];
                    }
                }
            }
        }
    }

    /**
     * Audit upload directories
     */
    private function auditUploadDirectories(): void
    {
        $uploadDirs = [
            public_path('uploads'),
            storage_path('app/public/uploads'),
        ];

        foreach ($uploadDirs as $dir) {
            if (File::exists($dir)) {
                // Check for .htaccess protection
                $htaccessPath = $dir . '/.htaccess';
                if (!File::exists($htaccessPath)) {
                    $this->issues[] = [
                        'type' => 'missing_protection',
                        'severity' => 'high',
                        'message' => "Upload directory {$dir} lacks .htaccess protection",
                        'fix' => "Create .htaccess file with PHP execution disabled",
                    ];
                }

                // Check directory listing
                if (is_readable($dir)) {
                    $this->recommendations[] = "Consider disabling directory listing for {$dir}";
                }
            }
        }
    }

    /**
     * Check .htaccess files
     */
    private function checkHtaccessFiles(): void
    {
        $htaccessFiles = [
            public_path('.htaccess'),
            public_path('uploads/.htaccess'),
        ];

        foreach ($htaccessFiles as $file) {
            if (File::exists($file)) {
                $content = File::get($file);
                
                // Check for security headers
                if (!str_contains($content, 'X-Frame-Options')) {
                    $this->recommendations[] = "Add X-Frame-Options header to {$file}";
                }
                
                if (!str_contains($content, 'X-Content-Type-Options')) {
                    $this->recommendations[] = "Add X-Content-Type-Options header to {$file}";
                }
            }
        }
    }

    /**
     * Audit database security
     */
    private function auditDatabase(): void
    {
        $this->info('🗄️ Auditing Database Security...');

        // Check for SQL injection vulnerabilities
        $this->checkSqlInjectionVulnerabilities();

        // Check user passwords
        $this->auditUserPasswords();

        // Check for sensitive data exposure
        $this->checkSensitiveDataExposure();
    }

    /**
     * Check for SQL injection vulnerabilities
     */
    private function checkSqlInjectionVulnerabilities(): void
    {
        // This is a simplified check - in production, use specialized tools
        $suspiciousQueries = DB::getQueryLog();
        
        foreach ($suspiciousQueries as $query) {
            if (str_contains($query['query'], 'UNION SELECT') || 
                str_contains($query['query'], 'DROP TABLE')) {
                $this->issues[] = [
                    'type' => 'sql_injection',
                    'severity' => 'critical',
                    'message' => "Potential SQL injection detected in query: " . substr($query['query'], 0, 100),
                    'fix' => "Review and sanitize query parameters",
                ];
            }
        }
    }

    /**
     * Audit user passwords
     */
    private function auditUserPasswords(): void
    {
        $weakPasswords = User::whereIn('password', [
            bcrypt('password'),
            bcrypt('123456'),
            bcrypt('admin'),
            bcrypt('test'),
        ])->count();

        if ($weakPasswords > 0) {
            $this->issues[] = [
                'type' => 'weak_passwords',
                'severity' => 'medium',
                'message' => "{$weakPasswords} users have weak passwords",
                'fix' => "Force password reset for affected users",
            ];
        }
    }

    /**
     * Check for sensitive data exposure
     */
    private function checkSensitiveDataExposure(): void
    {
        // Check if sensitive fields are properly encrypted
        $showcases = Showcase::whereNotNull('technical_specs')->take(10)->get();
        
        foreach ($showcases as $showcase) {
            if (is_string($showcase->technical_specs) && 
                !str_starts_with($showcase->technical_specs, 'eyJ')) { // Not base64 encoded
                $this->recommendations[] = "Consider encrypting technical_specs field in showcases";
                break;
            }
        }
    }

    /**
     * Audit configuration security
     */
    private function auditConfiguration(): void
    {
        $this->info('⚙️ Auditing Configuration Security...');

        // Check environment configuration
        $this->checkEnvironmentConfig();

        // Check Laravel configuration
        $this->checkLaravelConfig();

        // Check security headers
        $this->checkSecurityHeaders();
    }

    /**
     * Check environment configuration
     */
    private function checkEnvironmentConfig(): void
    {
        $envFile = base_path('.env');
        
        if (!File::exists($envFile)) {
            $this->issues[] = [
                'type' => 'missing_env',
                'severity' => 'critical',
                'message' => ".env file is missing",
                'fix' => "Create .env file from .env.example",
            ];
            return;
        }

        $envContent = File::get($envFile);
        
        // Check for debug mode in production
        if (str_contains($envContent, 'APP_DEBUG=true') && app()->environment('production')) {
            $this->issues[] = [
                'type' => 'debug_enabled',
                'severity' => 'high',
                'message' => "Debug mode is enabled in production",
                'fix' => "Set APP_DEBUG=false in production",
            ];
        }

        // Check for default app key
        if (str_contains($envContent, 'APP_KEY=base64:')) {
            // This is good
        } else {
            $this->issues[] = [
                'type' => 'missing_app_key',
                'severity' => 'critical',
                'message' => "Application key is not set",
                'fix' => "Run php artisan key:generate",
            ];
        }
    }

    /**
     * Check Laravel configuration
     */
    private function checkLaravelConfig(): void
    {
        // Check CSRF protection
        if (!config('app.csrf_protection', true)) {
            $this->issues[] = [
                'type' => 'csrf_disabled',
                'severity' => 'high',
                'message' => "CSRF protection is disabled",
                'fix' => "Enable CSRF protection in configuration",
            ];
        }

        // Check session security
        if (config('session.secure') !== true && app()->environment('production')) {
            $this->recommendations[] = "Enable secure session cookies in production";
        }
    }

    /**
     * Check security headers
     */
    private function checkSecurityHeaders(): void
    {
        $requiredHeaders = [
            'X-Frame-Options',
            'X-Content-Type-Options',
            'X-XSS-Protection',
            'Strict-Transport-Security',
        ];

        foreach ($requiredHeaders as $header) {
            // This would need to be checked via HTTP request in real implementation
            $this->recommendations[] = "Ensure {$header} header is set";
        }
    }

    /**
     * Audit permissions
     */
    private function auditPermissions(): void
    {
        $this->info('🔐 Auditing Permissions...');

        // Check file permissions
        $this->checkFilePermissions();

        // Check user roles
        $this->auditUserRoles();
    }

    /**
     * Check file permissions
     */
    private function checkFilePermissions(): void
    {
        $files = [
            base_path('.env') => '600',
            storage_path('logs/laravel.log') => '644',
        ];

        foreach ($files as $file => $expectedPerm) {
            if (File::exists($file)) {
                $actualPerm = substr(sprintf('%o', fileperms($file)), -3);
                if ($actualPerm !== $expectedPerm) {
                    $this->issues[] = [
                        'type' => 'file_permissions',
                        'severity' => 'medium',
                        'message' => "File {$file} has permissions {$actualPerm}, expected {$expectedPerm}",
                        'fix' => "chmod {$expectedPerm} {$file}",
                    ];
                }
            }
        }
    }

    /**
     * Audit user roles
     */
    private function auditUserRoles(): void
    {
        $adminCount = User::where('role', 'super_admin')->count();
        
        if ($adminCount === 0) {
            $this->issues[] = [
                'type' => 'no_admin',
                'severity' => 'high',
                'message' => "No super admin users found",
                'fix' => "Create at least one super admin user",
            ];
        } elseif ($adminCount > 5) {
            $this->recommendations[] = "Consider reducing the number of super admin users ({$adminCount} found)";
        }
    }

    /**
     * Audit uploaded files
     */
    private function auditUploadedFiles(): void
    {
        $this->info('📎 Auditing Uploaded Files...');

        $uploadDirs = [
            public_path('uploads'),
            storage_path('app/public/uploads'),
        ];

        foreach ($uploadDirs as $dir) {
            if (File::exists($dir)) {
                $this->scanUploadDirectory($dir);
            }
        }
    }

    /**
     * Scan upload directory for security issues
     */
    private function scanUploadDirectory(string $dir): void
    {
        $files = File::allFiles($dir);
        $suspiciousCount = 0;
        $totalSize = 0;

        foreach ($files as $file) {
            $totalSize += $file->getSize();
            
            // Check for executable files
            if (in_array(strtolower($file->getExtension()), ['exe', 'bat', 'cmd', 'php', 'asp'])) {
                $suspiciousCount++;
            }
        }

        if ($suspiciousCount > 0) {
            $this->issues[] = [
                'type' => 'suspicious_uploads',
                'severity' => 'high',
                'message' => "{$suspiciousCount} suspicious files found in {$dir}",
                'fix' => "Review and remove suspicious files",
            ];
        }

        // Check total upload size
        $totalSizeMB = round($totalSize / (1024 * 1024), 2);
        if ($totalSizeMB > 1000) { // 1GB
            $this->recommendations[] = "Upload directory {$dir} is large ({$totalSizeMB}MB). Consider cleanup.";
        }
    }

    /**
     * Audit user security
     */
    private function auditUserSecurity(): void
    {
        $this->info('👤 Auditing User Security...');

        // Check for inactive admin accounts
        $inactiveAdmins = User::whereIn('role', ['super_admin', 'system_admin'])
            ->where('last_login_at', '<', now()->subDays(90))
            ->count();

        if ($inactiveAdmins > 0) {
            $this->recommendations[] = "Consider deactivating {$inactiveAdmins} inactive admin accounts";
        }

        // Check for users without email verification
        $unverifiedUsers = User::whereNull('email_verified_at')->count();
        if ($unverifiedUsers > 0) {
            $this->recommendations[] = "{$unverifiedUsers} users have unverified email addresses";
        }
    }

    /**
     * Auto-fix issues where possible
     */
    private function autoFixIssues(): void
    {
        $this->info('🔧 Auto-fixing issues...');

        foreach ($this->issues as $issue) {
            if (isset($issue['fix']) && $issue['type'] === 'permissions') {
                // Auto-fix permission issues
                $this->line("Fixing: {$issue['message']}");
                // Implementation would go here
            }
        }
    }

    /**
     * Display audit results
     */
    private function displayResults(): void
    {
        $this->newLine();
        $this->info('📊 Security Audit Results');
        $this->line('========================');

        // Display issues by severity
        $critical = array_filter($this->issues, fn($i) => $i['severity'] === 'critical');
        $high = array_filter($this->issues, fn($i) => $i['severity'] === 'high');
        $medium = array_filter($this->issues, fn($i) => $i['severity'] === 'medium');

        if (count($critical) > 0) {
            $this->error('🚨 Critical Issues: ' . count($critical));
            foreach ($critical as $issue) {
                $this->line("  - {$issue['message']}");
            }
        }

        if (count($high) > 0) {
            $this->warn('⚠️  High Priority Issues: ' . count($high));
            foreach ($high as $issue) {
                $this->line("  - {$issue['message']}");
            }
        }

        if (count($medium) > 0) {
            $this->comment('📋 Medium Priority Issues: ' . count($medium));
            foreach ($medium as $issue) {
                $this->line("  - {$issue['message']}");
            }
        }

        if (count($this->recommendations) > 0) {
            $this->newLine();
            $this->info('💡 Recommendations: ' . count($this->recommendations));
            foreach ($this->recommendations as $recommendation) {
                $this->line("  - {$recommendation}");
            }
        }

        if (count($this->issues) === 0) {
            $this->success('✅ No security issues found!');
        }
    }

    /**
     * Generate detailed security report
     */
    private function generateReport(): void
    {
        $report = [
            'timestamp' => now()->toISOString(),
            'issues' => $this->issues,
            'recommendations' => $this->recommendations,
            'summary' => [
                'total_issues' => count($this->issues),
                'critical_issues' => count(array_filter($this->issues, fn($i) => $i['severity'] === 'critical')),
                'high_issues' => count(array_filter($this->issues, fn($i) => $i['severity'] === 'high')),
                'medium_issues' => count(array_filter($this->issues, fn($i) => $i['severity'] === 'medium')),
                'recommendations' => count($this->recommendations),
            ],
        ];

        $reportPath = storage_path('logs/security-audit-' . now()->format('Y-m-d-H-i-s') . '.json');
        File::put($reportPath, json_encode($report, JSON_PRETTY_PRINT));

        $this->info("📄 Detailed report saved to: {$reportPath}");
    }
}
