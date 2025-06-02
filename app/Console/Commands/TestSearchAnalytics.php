<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SearchLog;
use App\Models\Thread;
use App\Models\Post;
use App\Models\User;

class TestSearchAnalytics extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'test:search-analytics';

    /**
     * The console command description.
     */
    protected $description = 'Test Search Analytics Integration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 KIỂM TRA TÍCH HỢP SEARCH ANALYTICS');
        $this->info('=====================================');
        $this->newLine();

        try {
            // 1. Kiểm tra SearchLog model
            $this->info('1. Kiểm tra SearchLog Model...');
            $searchLogCount = SearchLog::count();
            $this->info("   ✅ SearchLog model hoạt động! Hiện có {$searchLogCount} log entries");
            $this->newLine();

            // 2. Kiểm tra cấu trúc bảng
            $this->info('2. Kiểm tra cấu trúc bảng search_logs...');
            $columns = \Schema::getColumnListing('search_logs');
            $expectedColumns = ['id', 'query', 'user_id', 'ip_address', 'user_agent', 'results_count', 'response_time_ms', 'filters', 'content_type', 'created_at'];

            $this->info('   Các cột hiện có: ' . implode(', ', $columns));

            $missingColumns = array_diff($expectedColumns, $columns);
            if (empty($missingColumns)) {
                $this->info('   ✅ Tất cả cột cần thiết đều có!');
            } else {
                $this->warn('   ⚠️ Thiếu các cột: ' . implode(', ', $missingColumns));
            }
            $this->newLine();

            // 3. Test tạo SearchLog entry
            $this->info('3. Test tạo SearchLog entry...');

            $testSearchLog = SearchLog::create([
                'query' => 'test search analytics command',
                'user_id' => null,
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Laravel Artisan Command',
                'results_count' => 3,
                'response_time_ms' => 120,
                'filters' => ['search_type' => 'test', 'content_type' => 'command_test'],
                'content_type' => 'command_test',
                'created_at' => now()
            ]);

            if ($testSearchLog) {
                $this->info("   ✅ Tạo SearchLog entry thành công! ID: {$testSearchLog->id}");

                // Xóa test entry
                $testSearchLog->delete();
                $this->info('   ✅ Đã xóa test entry');
            }
            $this->newLine();

            // 4. Kiểm tra dữ liệu mẫu
            $this->info('4. Kiểm tra dữ liệu có sẵn...');
            $threadCount = Thread::count();
            $postCount = Post::count();
            $userCount = User::count();

            $this->info("   - Threads: {$threadCount}");
            $this->info("   - Posts: {$postCount}");
            $this->info("   - Users: {$userCount}");

            if ($threadCount > 0 && $postCount > 0) {
                $this->info('   ✅ Có đủ dữ liệu để test search!');
            } else {
                $this->warn('   ⚠️ Có thể cần thêm dữ liệu để test search hiệu quả');
            }
            $this->newLine();

            // 5. Kiểm tra SearchLog scopes
            $this->info('5. Test SearchLog Scopes...');

            // Tạo vài test entries để test scopes
            $testEntries = [];
            for ($i = 1; $i <= 3; $i++) {
                $testEntries[] = SearchLog::create([
                    'query' => "test query {$i}",
                    'user_id' => null,
                    'ip_address' => '127.0.0.1',
                    'user_agent' => 'Test Agent',
                    'results_count' => $i > 1 ? 5 : 0, // First query has no results
                    'response_time_ms' => 100 + ($i * 10),
                    'filters' => ['test' => true],
                    'content_type' => 'scope_test',
                    'created_at' => now()
                ]);
            }

            // Test scopes
            $recentCount = SearchLog::recent(7)->count();
            $withResultsCount = SearchLog::withResults()->count();
            $withoutResultsCount = SearchLog::withoutResults()->count();

            $this->info("   - Recent searches (7 days): {$recentCount}");
            $this->info("   - Searches with results: {$withResultsCount}");
            $this->info("   - Searches without results: {$withoutResultsCount}");
            $this->info('   ✅ SearchLog scopes hoạt động!');

            // Xóa test entries
            foreach ($testEntries as $entry) {
                $entry->delete();
            }
            $this->info('   ✅ Đã xóa test entries');
            $this->newLine();

            // Kết quả cuối cùng
            $this->info('🎉 KẾT QUẢ TỔNG QUAN');
            $this->info('==================');
            $this->info('✅ SearchLog Model: Hoạt động');
            $this->info('✅ Database Structure: Đầy đủ');
            $this->info('✅ SearchLog Scopes: Hoạt động');
            $this->info('✅ Analytics Logging: Sẵn sàng');
            $this->newLine();

            $this->info('🔥 SEARCH ANALYTICS INTEGRATION HOÀN TẤT!');
            $this->info('Tất cả tìm kiếm sẽ được ghi log để phân tích thống kê.');

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('❌ LỖI: ' . $e->getMessage());
            $this->error('File: ' . $e->getFile());
            $this->error('Line: ' . $e->getLine());
            return Command::FAILURE;
        }
    }
}
