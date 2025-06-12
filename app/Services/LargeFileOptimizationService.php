<?php

namespace App\Services;

use App\Models\ProtectedFile;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Large File Download Optimization Service
 * Handles efficient streaming of large technical files (CAD, videos, etc.)
 */
class LargeFileOptimizationService
{
    private const CHUNK_SIZE = 8192; // 8KB chunks
    private const LARGE_FILE_THRESHOLD = 50 * 1024 * 1024; // 50MB

    /**
     * Stream large file with resume support
     */
    public function streamLargeFile(ProtectedFile $file, array $headers = []): StreamedResponse
    {
        $filePath = storage_path('app/protected/' . $file->file_path);

        if (!file_exists($filePath)) {
            abort(404, 'File not found');
        }

        $fileSize = filesize($filePath);
        $isLargeFile = $fileSize > self::LARGE_FILE_THRESHOLD;

        // Handle range requests for resume support
        $range = request()->header('Range');
        $start = 0;
        $end = $fileSize - 1;

        if ($range && $isLargeFile) {
            [$start, $end] = $this->parseRangeHeader($range, $fileSize);
        }

        $length = $end - $start + 1;

        // Prepare headers
        $responseHeaders = array_merge([
            'Content-Type' => $file->mime_type,
            'Content-Length' => $length,
            'Content-Disposition' => 'attachment; filename="' . $file->original_filename . '"',
            'Accept-Ranges' => 'bytes',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'DENY',
        ], $headers);

        // Add range headers if partial content
        if ($range && $isLargeFile) {
            $responseHeaders['Content-Range'] = "bytes {$start}-{$end}/{$fileSize}";
            $status = 206; // Partial Content
        } else {
            $status = 200;
        }

        Log::info('Streaming large file', [
            'file_id' => $file->id,
            'file_size' => $fileSize,
            'is_large_file' => $isLargeFile,
            'range_request' => $range !== null,
            'start' => $start,
            'end' => $end,
            'length' => $length
        ]);

        return new StreamedResponse(
            function () use ($filePath, $start, $end) {
                $this->streamFileContent($filePath, $start, $end);
            },
            $status,
            $responseHeaders
        );
    }

    /**
     * Stream file content in chunks
     */
    private function streamFileContent(string $filePath, int $start, int $end): void
    {
        $file = fopen($filePath, 'rb');

        if (!$file) {
            throw new \Exception('Cannot open file for reading');
        }

        // Seek to start position
        fseek($file, $start);

        $bytesRemaining = $end - $start + 1;

        while ($bytesRemaining > 0 && !feof($file)) {
            $chunkSize = min(self::CHUNK_SIZE, $bytesRemaining);
            $chunk = fread($file, $chunkSize);

            if ($chunk === false) {
                break;
            }

            echo $chunk;

            $bytesRemaining -= strlen($chunk);

            // Flush output to prevent memory issues
            if (ob_get_level()) {
                ob_flush();
            }
            flush();
        }

        fclose($file);
    }

    /**
     * Parse HTTP Range header
     */
    private function parseRangeHeader(string $range, int $fileSize): array
    {
        // Format: bytes=start-end
        if (!preg_match('/bytes=(\d*)-(\d*)/', $range, $matches)) {
            return [0, $fileSize - 1];
        }

        $start = $matches[1] !== '' ? (int)$matches[1] : 0;
        $end = $matches[2] !== '' ? (int)$matches[2] : $fileSize - 1;

        // Validate range
        $start = max(0, min($start, $fileSize - 1));
        $end = max($start, min($end, $fileSize - 1));

        return [$start, $end];
    }

    /**
     * Generate optimized download headers for different file types
     */
    public function getOptimizedHeaders(ProtectedFile $file): array
    {
        $fileSize = $file->file_size;
        $mimeType = $file->mime_type;

        $headers = [
            'Content-Type' => $mimeType,
            'Content-Length' => $fileSize,
            'Content-Disposition' => 'attachment; filename="' . $file->original_filename . '"',
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'DENY',
        ];

        // File type specific optimizations
        if ($this->isCADFile($file)) {
            $headers = array_merge($headers, $this->getCADFileHeaders());
        } elseif ($this->isVideoFile($file)) {
            $headers = array_merge($headers, $this->getVideoFileHeaders());
        } elseif ($this->isDocumentFile($file)) {
            $headers = array_merge($headers, $this->getDocumentFileHeaders());
        }

        // Large file optimizations
        if ($fileSize > self::LARGE_FILE_THRESHOLD) {
            $headers['Accept-Ranges'] = 'bytes';
            $headers['Connection'] = 'keep-alive';
        }

        return $headers;
    }

    /**
     * Check if file is a CAD file
     */
    private function isCADFile(ProtectedFile $file): bool
    {
        $cadExtensions = ['dwg', 'dxf', 'step', 'stp', 'iges', 'igs', 'stl', 'obj'];
        $extension = strtolower(pathinfo($file->original_filename, PATHINFO_EXTENSION));

        return in_array($extension, $cadExtensions);
    }

    /**
     * Check if file is a video file
     */
    private function isVideoFile(ProtectedFile $file): bool
    {
        return str_starts_with($file->mime_type, 'video/');
    }

    /**
     * Check if file is a document file
     */
    private function isDocumentFile(ProtectedFile $file): bool
    {
        $documentMimes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ];

        return in_array($file->mime_type, $documentMimes);
    }

    /**
     * Get CAD file specific headers
     */
    private function getCADFileHeaders(): array
    {
        return [
            'Cache-Control' => 'private, max-age=3600', // Allow 1 hour cache for CAD files
            'X-File-Type' => 'CAD',
        ];
    }

    /**
     * Get video file specific headers
     */
    private function getVideoFileHeaders(): array
    {
        return [
            'Accept-Ranges' => 'bytes',
            'Cache-Control' => 'private, max-age=7200', // Allow 2 hour cache for videos
            'X-File-Type' => 'Video',
        ];
    }

    /**
     * Get document file specific headers
     */
    private function getDocumentFileHeaders(): array
    {
        return [
            'Cache-Control' => 'private, max-age=1800', // Allow 30 min cache for documents
            'X-File-Type' => 'Document',
        ];
    }

    /**
     * Compress file if beneficial
     */
    public function shouldCompress(ProtectedFile $file): bool
    {
        // Don't compress already compressed formats
        $nonCompressibleTypes = [
            'application/zip',
            'application/x-rar-compressed',
            'application/x-7z-compressed',
            'image/jpeg',
            'image/png',
            'video/',
            'audio/'
        ];

        foreach ($nonCompressibleTypes as $type) {
            if (str_contains($file->mime_type, $type)) {
                return false;
            }
        }

        // Compress text-based files and small binary files
        return $file->file_size < 10 * 1024 * 1024; // 10MB limit for compression
    }

    /**
     * Get download speed recommendations
     */
    public function getDownloadSpeedRecommendations(ProtectedFile $file): array
    {
        $fileSize = $file->file_size;

        if ($fileSize < 1024 * 1024) { // < 1MB
            return [
                'priority' => 'high',
                'concurrent_downloads' => 10,
                'chunk_size' => 4096
            ];
        } elseif ($fileSize < 50 * 1024 * 1024) { // < 50MB
            return [
                'priority' => 'medium',
                'concurrent_downloads' => 5,
                'chunk_size' => 8192
            ];
        } else { // >= 50MB
            return [
                'priority' => 'low',
                'concurrent_downloads' => 2,
                'chunk_size' => 16384
            ];
        }
    }

    /**
     * Monitor download performance
     */
    public function monitorDownloadPerformance(ProtectedFile $file, float $startTime): array
    {
        $endTime = microtime(true);
        $duration = $endTime - $startTime;
        $speed = $file->file_size / $duration; // bytes per second

        $performance = [
            'file_id' => $file->id,
            'file_size' => $file->file_size,
            'duration_seconds' => $duration,
            'speed_bytes_per_second' => $speed,
            'speed_mbps' => ($speed * 8) / (1024 * 1024), // Convert to Mbps
            'efficiency_rating' => $this->calculateEfficiencyRating($file->file_size, $duration)
        ];

        Log::info('Download performance monitored', $performance);

        return $performance;
    }

    /**
     * Calculate efficiency rating
     */
    private function calculateEfficiencyRating(int $fileSize, float $duration): string
    {
        $expectedDuration = $fileSize / (10 * 1024 * 1024); // Expect 10MB/s baseline

        if ($duration <= $expectedDuration * 0.5) {
            return 'excellent';
        } elseif ($duration <= $expectedDuration) {
            return 'good';
        } elseif ($duration <= $expectedDuration * 2) {
            return 'average';
        } else {
            return 'poor';
        }
    }
}
