<?php

namespace App\Services;

use App\Models\User;
use App\Models\MarketplaceProduct;
use App\Models\MarketplaceSeller;
use App\Notifications\ProductSubmittedForApproval;
use App\Notifications\ProductApproved;
use App\Notifications\ProductRejected;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

/**
 * 🏪 Guest Product Approval Service
 * 
 * Quản lý quy trình duyệt sản phẩm cho Guest users:
 * - Guest có thể bán digital products
 * - Tất cả sản phẩm Guest cần admin duyệt
 * - Workflow approval với notifications
 */
class GuestProductApprovalService
{
    /**
     * Submit product for approval (Guest users)
     */
    public static function submitForApproval(User $guest, array $productData): array
    {
        try {
            // Validate guest can sell
            if (!self::canGuestSell($guest)) {
                return [
                    'success' => false,
                    'message' => 'Bạn không có quyền bán sản phẩm.',
                ];
            }

            // Validate product type
            if ($productData['product_type'] !== MarketplaceProduct::TYPE_DIGITAL) {
                return [
                    'success' => false,
                    'message' => 'Guest chỉ có thể bán sản phẩm kỹ thuật số.',
                ];
            }

            // Get or create seller profile
            $seller = self::getOrCreateGuestSeller($guest);

            // Create product with pending status
            $product = MarketplaceProduct::create(array_merge($productData, [
                'seller_id' => $seller->id,
                'seller_type' => 'guest',
                'status' => 'pending_approval',
                'submitted_at' => now(),
                'submitted_by' => $guest->id,
            ]));

            // Send notification to admins
            self::notifyAdminsForApproval($product);

            // Send confirmation to guest
            $guest->notify(new ProductSubmittedForApproval($product));

            Log::info('Guest product submitted for approval', [
                'guest_id' => $guest->id,
                'product_id' => $product->id,
                'product_name' => $product->name,
            ]);

            return [
                'success' => true,
                'message' => 'Sản phẩm đã được gửi để duyệt. Bạn sẽ nhận được thông báo khi có kết quả.',
                'product_id' => $product->id,
            ];

        } catch (\Exception $e) {
            Log::error('Guest product submission failed', [
                'guest_id' => $guest->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Có lỗi xảy ra khi gửi sản phẩm. Vui lòng thử lại.',
            ];
        }
    }

    /**
     * Approve guest product
     */
    public static function approveProduct(MarketplaceProduct $product, User $admin, string $notes = null): array
    {
        try {
            $product->update([
                'status' => 'approved',
                'approved_at' => now(),
                'approved_by' => $admin->id,
                'approval_notes' => $notes,
            ]);

            // Notify guest
            $guest = User::find($product->submitted_by);
            if ($guest) {
                $guest->notify(new ProductApproved($product, $notes));
            }

            Log::info('Guest product approved', [
                'product_id' => $product->id,
                'admin_id' => $admin->id,
                'guest_id' => $product->submitted_by,
            ]);

            return [
                'success' => true,
                'message' => 'Sản phẩm đã được duyệt thành công.',
            ];

        } catch (\Exception $e) {
            Log::error('Product approval failed', [
                'product_id' => $product->id,
                'admin_id' => $admin->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Có lỗi xảy ra khi duyệt sản phẩm.',
            ];
        }
    }

    /**
     * Reject guest product
     */
    public static function rejectProduct(MarketplaceProduct $product, User $admin, string $reason): array
    {
        try {
            $product->update([
                'status' => 'rejected',
                'rejected_at' => now(),
                'rejected_by' => $admin->id,
                'rejection_reason' => $reason,
            ]);

            // Notify guest
            $guest = User::find($product->submitted_by);
            if ($guest) {
                $guest->notify(new ProductRejected($product, $reason));
            }

            Log::info('Guest product rejected', [
                'product_id' => $product->id,
                'admin_id' => $admin->id,
                'guest_id' => $product->submitted_by,
                'reason' => $reason,
            ]);

            return [
                'success' => true,
                'message' => 'Sản phẩm đã bị từ chối.',
            ];

        } catch (\Exception $e) {
            Log::error('Product rejection failed', [
                'product_id' => $product->id,
                'admin_id' => $admin->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Có lỗi xảy ra khi từ chối sản phẩm.',
            ];
        }
    }

    /**
     * Check if guest can sell products
     */
    public static function canGuestSell(User $guest): bool
    {
        return $guest->role === 'guest' && 
               $guest->is_active && 
               !$guest->is_banned &&
               auth()->check();
    }

    /**
     * Get or create seller profile for guest
     */
    private static function getOrCreateGuestSeller(User $guest): MarketplaceSeller
    {
        $seller = MarketplaceSeller::where('user_id', $guest->id)->first();

        if (!$seller) {
            $seller = MarketplaceSeller::create([
                'user_id' => $guest->id,
                'business_name' => $guest->name . ' (Cá nhân)',
                'seller_type' => 'guest',
                'status' => 'active',
                'is_verified' => false,
                'verification_status' => 'not_required',
                'created_at' => now(),
            ]);
        }

        return $seller;
    }

    /**
     * Notify admins for product approval
     */
    private static function notifyAdminsForApproval(MarketplaceProduct $product): void
    {
        $admins = User::whereIn('role', [
            'super_admin', 
            'system_admin', 
            'content_admin',
            'marketplace_moderator'
        ])->get();

        Notification::send($admins, new ProductSubmittedForApproval($product));
    }

    /**
     * Get pending products for admin review
     */
    public static function getPendingProducts(): \Illuminate\Database\Eloquent\Collection
    {
        return MarketplaceProduct::where('status', 'pending_approval')
            ->where('seller_type', 'guest')
            ->with(['seller.user'])
            ->orderBy('submitted_at', 'asc')
            ->get();
    }

    /**
     * Get guest product statistics
     */
    public static function getGuestProductStats(): array
    {
        return [
            'pending' => MarketplaceProduct::where('status', 'pending_approval')
                ->where('seller_type', 'guest')->count(),
            'approved' => MarketplaceProduct::where('status', 'approved')
                ->where('seller_type', 'guest')->count(),
            'rejected' => MarketplaceProduct::where('status', 'rejected')
                ->where('seller_type', 'guest')->count(),
            'total_guests_selling' => MarketplaceSeller::where('seller_type', 'guest')
                ->whereHas('products')->count(),
        ];
    }

    /**
     * Get approval workflow rules
     */
    public static function getApprovalRules(): array
    {
        return [
            'required_fields' => [
                'name', 'description', 'price', 'digital_files'
            ],
            'validation_rules' => [
                'content_quality' => 'Nội dung phải chất lượng và không vi phạm',
                'file_format' => 'File phải đúng định dạng và không chứa virus',
                'pricing' => 'Giá cả phải hợp lý và cạnh tranh',
                'description' => 'Mô tả phải chi tiết và chính xác',
            ],
            'approval_criteria' => [
                'technical_quality' => 'Chất lượng kỹ thuật của sản phẩm',
                'market_relevance' => 'Tính phù hợp với thị trường',
                'legal_compliance' => 'Tuân thủ pháp luật và bản quyền',
                'platform_guidelines' => 'Tuân thủ quy định của platform',
            ],
        ];
    }
}
