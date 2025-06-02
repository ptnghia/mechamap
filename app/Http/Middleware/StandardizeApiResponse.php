<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use App\Services\ApiPerformanceService;

class StandardizeApiResponse
{
    protected $performanceService;

    public function __construct(ApiPerformanceService $performanceService)
    {
        $this->performanceService = $performanceService;
    }

    /**
     * Chuẩn hóa format response cho API và tracking performance
     */
    public function handle(Request $request, Closure $next)
    {
        $startTime = microtime(true);
        $response = $next($request);

        // Chỉ xử lý cho API routes
        if (!$request->is('api/*')) {
            return $response;
        }

        // Chỉ xử lý JSON responses
        if (!$response instanceof JsonResponse) {
            return $response;
        }

        $data = $response->getData(true);
        $statusCode = $response->getStatusCode();

        // Nếu response đã có format chuẩn thì không thay đổi
        if (isset($data['success']) && isset($data['message'])) {
            $response = $this->addMetadata($response, $data);
        } else {
            // Chuẩn hóa response format
            $standardizedData = $this->standardizeResponse($data, $statusCode);
            $response = response()->json($standardizedData, $statusCode)
                ->withHeaders($response->headers->all());
        }

        // Thêm performance headers
        $duration = microtime(true) - $startTime;
        $response->headers->set('X-Response-Time', round($duration * 1000, 2) . 'ms');
        $response->headers->set('X-Request-ID', uniqid());
        $response->headers->set('X-API-Version', 'v1');

        // Track performance metrics
        try {
            $this->performanceService->trackApiPerformance($request, $startTime, $response);
        } catch (\Exception $e) {
            // Log error nhưng không làm gián đoạn response
            Log::error('Performance tracking failed: ' . $e->getMessage());
        }

        return $response;
    }

    /**
     * Chuẩn hóa response theo format chuẩn
     */
    protected function standardizeResponse(array $data, int $statusCode): array
    {
        $isSuccess = $statusCode >= 200 && $statusCode < 300;

        $standardized = [
            'success' => $isSuccess,
            'message' => $this->getStatusMessage($statusCode),
            'data' => $isSuccess ? $data : null,
            'meta' => [
                'timestamp' => now()->toISOString(),
                'api_version' => 'v1',
                'status_code' => $statusCode
            ]
        ];

        // Thêm error details nếu không thành công
        if (!$isSuccess) {
            $standardized['error'] = [
                'code' => $statusCode,
                'details' => $data,
                'type' => $this->getErrorType($statusCode)
            ];
        }

        return $standardized;
    }

    /**
     * Thêm metadata vào response đã có format chuẩn
     */
    protected function addMetadata(JsonResponse $response, array $data): JsonResponse
    {
        if (!isset($data['meta'])) {
            $data['meta'] = [
                'timestamp' => now()->toISOString(),
                'api_version' => 'v1',
                'status_code' => $response->getStatusCode()
            ];
        }

        return $response->setData($data);
    }

    /**
     * Lấy message tương ứng với status code
     */
    protected function getStatusMessage(int $statusCode): string
    {
        $messages = [
            200 => 'Yêu cầu thành công',
            201 => 'Tạo mới thành công',
            204 => 'Xử lý thành công',
            400 => 'Dữ liệu yêu cầu không hợp lệ',
            401 => 'Chưa xác thực',
            403 => 'Không có quyền truy cập',
            404 => 'Không tìm thấy tài nguyên',
            422 => 'Dữ liệu không đúng định dạng',
            429 => 'Quá nhiều yêu cầu',
            500 => 'Lỗi máy chủ nội bộ',
            503 => 'Dịch vụ tạm thời không khả dụng'
        ];

        return $messages[$statusCode] ?? 'Trạng thái không xác định';
    }

    /**
     * Lấy error type theo status code
     */
    protected function getErrorType(int $statusCode): string
    {
        if ($statusCode >= 400 && $statusCode < 500) {
            return 'client_error';
        }

        if ($statusCode >= 500) {
            return 'server_error';
        }

        return 'unknown_error';
    }
}
