<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SearchLog;
use App\Models\User;
use Carbon\Carbon;

class SearchLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Danh sách từ khóa tìm kiếm thực tế trong lĩnh vực cơ khí
        $mechanicalSearchTerms = [
            'thiết kế cơ khí',
            'CAD solidworks',
            'autocad 2d',
            'inventor 3d',
            'fusion 360',
            'máy gia công CNC',
            'máy tiện',
            'máy phay',
            'gia công chính xác',
            'vật liệu cơ khí',
            'thép carbon',
            'hợp kim nhôm',
            'thép không gỉ',
            'bảo trì thiết bị',
            'sửa chữa máy móc',
            'kiểm tra chất lượng',
            'gia công khuôn mẫu',
            'thiết kế khuôn',
            'ép nhựa',
            'đúc kim loại',
            'hệ thống thủy lực',
            'khí nén công nghiệp',
            'động cơ điện',
            'bearing con lăn',
            'ổ bi công nghiệp',
            'trục vít me',
            'robot công nghiệp',
            'tự động hóa',
            'PLC programming',
            'ISO 9001',
            'quản lý chất lượng',
            'lean manufacturing',
            'welding mig tig',
            'hàn robot',
            'kiểm tra mối hàn',
        ];

        $contentTypes = ['threads', 'comments', 'users'];
        $users = User::all();

        // Tạo 500 search logs với dữ liệu thực tế
        for ($i = 0; $i < 500; $i++) {
            $query = $mechanicalSearchTerms[array_rand($mechanicalSearchTerms)];
            $contentType = $contentTypes[array_rand($contentTypes)];
            $user = $users->random();

            // Tạo thời gian ngẫu nhiên trong 30 ngày qua
            $createdAt = Carbon::now()->subDays(rand(0, 30))
                ->setHour(rand(8, 18)) // Thời gian làm việc chủ yếu
                ->setMinute(rand(0, 59))
                ->setSecond(rand(0, 59));

            // Số kết quả phụ thuộc vào từ khóa phổ biến
            $resultsCount = $this->getResultsCount($query);

            // Thời gian phản hồi phụ thuộc vào số kết quả
            $responseTime = $resultsCount > 50 ? rand(200, 500) : rand(50, 200);

            SearchLog::create([
                'query' => $query,
                'user_id' => rand(1, 10) > 3 ? $user->id : null, // 70% có user_id
                'ip_address' => $this->generateRandomIP(),
                'user_agent' => $this->getRandomUserAgent(),
                'results_count' => $resultsCount,
                'response_time_ms' => $responseTime,
                'filters' => $this->getRandomFilters($contentType),
                'content_type' => $contentType,
                'created_at' => $createdAt,
            ]);
        }
    }

    /**
     * Sinh số kết quả dựa trên độ phổ biến của từ khóa
     */
    private function getResultsCount(string $query): int
    {
        // Từ khóa phổ biến có nhiều kết quả hơn
        $popularTerms = ['cad', 'autocad', 'solidworks', 'cnc', 'thiết kế'];

        $isPopular = false;
        foreach ($popularTerms as $term) {
            if (stripos($query, $term) !== false) {
                $isPopular = true;
                break;
            }
        }

        if ($isPopular) {
            return rand(20, 100); // Từ khóa phổ biến
        } else {
            // 15% trả về 0 kết quả (tìm kiếm không thành công)
            return rand(1, 100) <= 15 ? 0 : rand(1, 50);
        }
    }

    /**
     * Sinh IP address ngẫu nhiên
     */
    private function generateRandomIP(): string
    {
        return rand(1, 255) . '.' . rand(1, 255) . '.' . rand(1, 255) . '.' . rand(1, 255);
    }

    /**
     * Lấy user agent ngẫu nhiên
     */
    private function getRandomUserAgent(): string
    {
        $userAgents = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:121.0) Gecko/20100101 Firefox/121.0',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Edge/120.0.2210.144',
            'Mozilla/5.0 (iPhone; CPU iPhone OS 17_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.2 Mobile/15E148 Safari/604.1',
        ];

        return $userAgents[array_rand($userAgents)];
    }

    /**
     * Sinh filters ngẫu nhiên dựa trên content type
     */
    private function getRandomFilters(string $contentType): ?array
    {
        // 40% có filters
        if (rand(1, 100) > 40) {
            return null;
        }

        $baseFilters = [
            'sort_by' => ['date', 'relevance', 'popularity'][array_rand(['date', 'relevance', 'popularity'])],
            'time_range' => ['all', 'today', 'week', 'month'][array_rand(['all', 'today', 'week', 'month'])],
        ];

        if ($contentType === 'threads') {
            $baseFilters['category_id'] = rand(1, 10);
            $baseFilters['has_images'] = rand(0, 1) === 1;
        } elseif ($contentType === 'users') {
            $baseFilters['user_role'] = ['member', 'expert', 'moderator'][array_rand(['member', 'expert', 'moderator'])];
        }

        return $baseFilters;
    }
}
