<?php

namespace App\Services;

use App\Models\User;
use App\Models\Product;
use App\Models\CADFile;
use App\Models\TechnicalDocument;
use App\Models\B2BQuote;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

/**
 * B2B Features Service - Phase 3
 * Dịch vụ tính năng B2B cho business partners
 */
class B2BFeaturesService
{
    /**
     * Check if user can access B2B features
     */
    public static function canAccessB2B(User $user): bool
    {
        return $user->hasPermissionTo('access-b2b-features') && 
               $user->role_group === 'business_partners';
    }

    /**
     * Check if user can sell technical files
     */
    public static function canSellTechnicalFiles(User $user): bool
    {
        $features = PermissionService::getMarketplaceFeatures($user);
        return $features['can_sell_technical_files'] ?? false;
    }

    /**
     * Check if user can sell CAD files
     */
    public static function canSellCADFiles(User $user): bool
    {
        $features = PermissionService::getMarketplaceFeatures($user);
        return $features['can_sell_cad_files'] ?? false;
    }

    /**
     * Upload and process CAD file
     */
    public static function uploadCADFile(User $user, $file, array $metadata = []): ?CADFile
    {
        if (!self::canSellCADFiles($user)) {
            throw new \Exception('User không có quyền upload CAD files');
        }

        try {
            // Validate file type
            $allowedExtensions = ['dwg', 'dxf', 'step', 'stp', 'iges', 'igs', 'stl', 'obj'];
            $extension = strtolower($file->getClientOriginalExtension());
            
            if (!in_array($extension, $allowedExtensions)) {
                throw new \Exception('File type không được hỗ trợ');
            }

            // Check file size (max 100MB)
            if ($file->getSize() > 100 * 1024 * 1024) {
                throw new \Exception('File quá lớn (max 100MB)');
            }

            // Generate unique filename
            $filename = time() . '_' . uniqid() . '.' . $extension;
            $path = "cad_files/{$user->id}/" . date('Y/m');
            
            // Store file
            $filePath = $file->storeAs($path, $filename, 'private');
            
            // Create CAD file record
            $cadFile = CADFile::create([
                'user_id' => $user->id,
                'original_name' => $file->getClientOriginalName(),
                'filename' => $filename,
                'file_path' => $filePath,
                'file_size' => $file->getSize(),
                'file_type' => $extension,
                'mime_type' => $file->getMimeType(),
                'metadata' => array_merge($metadata, [
                    'uploaded_at' => now()->toISOString(),
                    'ip_address' => request()->ip(),
                ]),
                'status' => 'pending_review',
            ]);

            // Generate thumbnail if possible
            self::generateCADThumbnail($cadFile);

            // Log activity
            activity()
                ->performedOn($cadFile)
                ->causedBy($user)
                ->log('CAD file uploaded');

            return $cadFile;

        } catch (\Exception $e) {
            Log::error('CAD file upload failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create technical document
     */
    public static function createTechnicalDocument(User $user, array $data): ?TechnicalDocument
    {
        if (!self::canSellTechnicalFiles($user)) {
            throw new \Exception('User không có quyền tạo technical documents');
        }

        try {
            $document = TechnicalDocument::create([
                'user_id' => $user->id,
                'title' => $data['title'],
                'description' => $data['description'],
                'category' => $data['category'],
                'technical_specs' => $data['technical_specs'] ?? [],
                'price' => $data['price'],
                'currency' => $data['currency'] ?? 'VND',
                'license_type' => $data['license_type'] ?? 'single_use',
                'tags' => $data['tags'] ?? [],
                'status' => 'draft',
            ]);

            // Attach files if provided
            if (isset($data['files'])) {
                foreach ($data['files'] as $file) {
                    self::attachFileToDocument($document, $file);
                }
            }

            activity()
                ->performedOn($document)
                ->causedBy($user)
                ->log('Technical document created');

            return $document;

        } catch (\Exception $e) {
            Log::error('Technical document creation failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create B2B quote request
     */
    public static function createQuoteRequest(User $buyer, User $seller, array $data): ?B2BQuote
    {
        if (!self::canAccessB2B($buyer) || !self::canAccessB2B($seller)) {
            throw new \Exception('Một trong hai user không có quyền B2B');
        }

        try {
            $quote = B2BQuote::create([
                'buyer_id' => $buyer->id,
                'seller_id' => $seller->id,
                'product_id' => $data['product_id'] ?? null,
                'title' => $data['title'],
                'description' => $data['description'],
                'quantity' => $data['quantity'],
                'specifications' => $data['specifications'] ?? [],
                'delivery_requirements' => $data['delivery_requirements'] ?? [],
                'budget_range' => $data['budget_range'] ?? null,
                'deadline' => $data['deadline'] ?? null,
                'status' => 'pending',
                'priority' => $data['priority'] ?? 'normal',
            ]);

            // Send notification to seller
            NotificationService::send(
                $seller,
                'quote_request',
                'Yêu cầu báo giá mới',
                "Bạn có yêu cầu báo giá mới từ {$buyer->name}",
                [
                    'quote_id' => $quote->id,
                    'action_url' => route('b2b.quotes.show', $quote),
                    'priority' => 'normal'
                ],
                true
            );

            activity()
                ->performedOn($quote)
                ->causedBy($buyer)
                ->log('B2B quote request created');

            return $quote;

        } catch (\Exception $e) {
            Log::error('B2B quote creation failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Calculate commission for B2B transaction
     */
    public static function calculateB2BCommission(User $seller, float $amount): array
    {
        $features = PermissionService::getMarketplaceFeatures($seller);
        $baseRate = $features['commission_rate'] ?? 5.0;
        
        // B2B transactions may have different rates
        $b2bRate = $baseRate;
        
        // Verified partners get better rates
        if ($seller->role === 'verified_partner') {
            $b2bRate = max($baseRate - 1.0, 1.0); // 1% discount, minimum 1%
        }

        // Volume discounts for large transactions
        if ($amount > 100000000) { // > 100M VND
            $b2bRate = max($b2bRate - 0.5, 1.0);
        } elseif ($amount > 50000000) { // > 50M VND
            $b2bRate = max($b2bRate - 0.25, 1.0);
        }

        $commission = $amount * ($b2bRate / 100);
        $sellerEarnings = $amount - $commission;

        return [
            'base_rate' => $baseRate,
            'applied_rate' => $b2bRate,
            'gross_amount' => $amount,
            'commission_amount' => $commission,
            'seller_earnings' => $sellerEarnings,
            'discounts_applied' => $baseRate !== $b2bRate,
        ];
    }

    /**
     * Get B2B analytics for seller
     */
    public static function getB2BAnalytics(User $seller, int $days = 30): array
    {
        if (!self::canAccessB2B($seller)) {
            return [];
        }

        $startDate = now()->subDays($days);

        return [
            'cad_files' => [
                'total' => CADFile::where('user_id', $seller->id)->count(),
                'approved' => CADFile::where('user_id', $seller->id)
                    ->where('status', 'approved')->count(),
                'downloads' => CADFile::where('user_id', $seller->id)
                    ->sum('download_count'),
                'revenue' => self::getCADFileRevenue($seller, $startDate),
            ],
            'technical_docs' => [
                'total' => TechnicalDocument::where('user_id', $seller->id)->count(),
                'published' => TechnicalDocument::where('user_id', $seller->id)
                    ->where('status', 'published')->count(),
                'sales' => self::getTechnicalDocSales($seller, $startDate),
            ],
            'quotes' => [
                'received' => B2BQuote::where('seller_id', $seller->id)
                    ->where('created_at', '>=', $startDate)->count(),
                'accepted' => B2BQuote::where('seller_id', $seller->id)
                    ->where('status', 'accepted')
                    ->where('created_at', '>=', $startDate)->count(),
                'total_value' => B2BQuote::where('seller_id', $seller->id)
                    ->where('status', 'accepted')
                    ->where('created_at', '>=', $startDate)
                    ->sum('final_amount'),
            ],
        ];
    }

    /**
     * Validate CAD file before upload
     */
    public static function validateCADFile($file): array
    {
        $errors = [];
        
        // Check file extension
        $allowedExtensions = ['dwg', 'dxf', 'step', 'stp', 'iges', 'igs', 'stl', 'obj'];
        $extension = strtolower($file->getClientOriginalExtension());
        
        if (!in_array($extension, $allowedExtensions)) {
            $errors[] = 'File type không được hỗ trợ. Chỉ chấp nhận: ' . implode(', ', $allowedExtensions);
        }

        // Check file size
        $maxSize = 100 * 1024 * 1024; // 100MB
        if ($file->getSize() > $maxSize) {
            $errors[] = 'File quá lớn. Kích thước tối đa: 100MB';
        }

        // Check filename
        if (strlen($file->getClientOriginalName()) > 255) {
            $errors[] = 'Tên file quá dài (tối đa 255 ký tự)';
        }

        return $errors;
    }

    /**
     * Generate thumbnail for CAD file
     */
    private static function generateCADThumbnail(CADFile $cadFile): void
    {
        try {
            // TODO: Implement CAD thumbnail generation
            // This would require specialized libraries like OpenCASCADE or FreeCAD
            Log::info("Thumbnail generation queued for CAD file: {$cadFile->id}");
        } catch (\Exception $e) {
            Log::error("Thumbnail generation failed: " . $e->getMessage());
        }
    }

    /**
     * Attach file to technical document
     */
    private static function attachFileToDocument(TechnicalDocument $document, $file): void
    {
        $filename = time() . '_' . $file->getClientOriginalName();
        $path = "technical_docs/{$document->user_id}/{$document->id}";
        
        $filePath = $file->storeAs($path, $filename, 'private');
        
        $document->files()->create([
            'original_name' => $file->getClientOriginalName(),
            'filename' => $filename,
            'file_path' => $filePath,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
        ]);
    }

    /**
     * Get CAD file revenue for seller
     */
    private static function getCADFileRevenue(User $seller, $startDate): float
    {
        // TODO: Implement based on your sales/order system
        return 0.0;
    }

    /**
     * Get technical document sales for seller
     */
    private static function getTechnicalDocSales(User $seller, $startDate): int
    {
        // TODO: Implement based on your sales/order system
        return 0;
    }
}
