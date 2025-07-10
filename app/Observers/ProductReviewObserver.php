<?php

namespace App\Observers;

use App\Models\ProductReview;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Log;

class ProductReviewObserver
{
    /**
     * Handle the ProductReview "created" event.
     */
    public function created(ProductReview $review): void
    {
        try {
            // Get the product and its seller
            $product = $review->product;
            if (!$product) {
                Log::warning('Product not found for review', ['review_id' => $review->id]);
                return;
            }

            $seller = $product->seller?->user;
            if (!$seller) {
                Log::warning('Seller not found for product review', [
                    'review_id' => $review->id,
                    'product_id' => $product->id
                ]);
                return;
            }

            // Don't notify if seller is reviewing their own product
            if ($seller->id === $review->user_id) {
                Log::info('Skipping notification - seller reviewing own product', [
                    'review_id' => $review->id,
                    'seller_id' => $seller->id
                ]);
                return;
            }

            Log::info('New product review created', [
                'review_id' => $review->id,
                'product_id' => $product->id,
                'product_name' => $product->name,
                'reviewer_id' => $review->user_id,
                'seller_id' => $seller->id,
                'rating' => $review->rating
            ]);

            // Send notification to seller
            $this->notifySellerOfNewReview($review, $product, $seller);

        } catch (\Exception $e) {
            Log::error('Failed to handle product review creation', [
                'review_id' => $review->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle the ProductReview "updated" event.
     */
    public function updated(ProductReview $review): void
    {
        try {
            // Check if status changed to approved
            if ($review->wasChanged('status') && $review->status === 'approved') {
                $this->handleReviewApproved($review);
            }

            // Check if rating changed significantly
            if ($review->wasChanged('rating')) {
                $this->handleRatingChanged($review);
            }

        } catch (\Exception $e) {
            Log::error('Failed to handle product review update', [
                'review_id' => $review->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send notification to seller about new review
     */
    private function notifySellerOfNewReview(ProductReview $review, $product, $seller): void
    {
        $reviewer = $review->user;
        $ratingText = $this->getRatingText($review->rating);
        
        $title = 'Nhận được đánh giá mới';
        $message = "Sản phẩm \"{$product->name}\" đã nhận được đánh giá {$review->rating} sao từ {$reviewer->name}";

        $data = [
            'review_id' => $review->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'product_slug' => $product->slug,
            'reviewer_id' => $review->user_id,
            'reviewer_name' => $reviewer->name,
            'rating' => $review->rating,
            'rating_text' => $ratingText,
            'review_title' => $review->title,
            'review_content' => $review->content,
            'is_verified_purchase' => $review->is_verified_purchase,
            'action_url' => route('marketplace.products.show', $product->slug) . '#reviews',
        ];

        // Send notification (with email for 4-5 star reviews, without email for low ratings)
        $sendEmail = $review->rating >= 4;
        
        $result = NotificationService::send(
            $seller,
            'review_received',
            $title,
            $message,
            $data,
            $sendEmail
        );

        if ($result) {
            Log::info('Review notification sent to seller', [
                'review_id' => $review->id,
                'seller_id' => $seller->id,
                'rating' => $review->rating,
                'email_sent' => $sendEmail
            ]);
        }
    }

    /**
     * Handle review approval
     */
    private function handleReviewApproved(ProductReview $review): void
    {
        $product = $review->product;
        $seller = $product->seller?->user;

        if (!$seller) {
            return;
        }

        Log::info('Product review approved', [
            'review_id' => $review->id,
            'product_id' => $product->id,
            'rating' => $review->rating
        ]);

        // Send notification about approval
        $title = 'Đánh giá được phê duyệt';
        $message = "Đánh giá {$review->rating} sao cho sản phẩm \"{$product->name}\" đã được phê duyệt và hiển thị công khai";

        $data = [
            'review_id' => $review->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'rating' => $review->rating,
            'action_url' => route('marketplace.products.show', $product->slug) . '#reviews',
        ];

        NotificationService::send(
            $seller,
            'review_approved',
            $title,
            $message,
            $data,
            false // No email for approval notifications
        );
    }

    /**
     * Handle significant rating changes
     */
    private function handleRatingChanged(ProductReview $review): void
    {
        $oldRating = $review->getOriginal('rating');
        $newRating = $review->rating;
        
        // Only notify for significant changes (2+ stars difference)
        if (abs($newRating - $oldRating) < 2) {
            return;
        }

        $product = $review->product;
        $seller = $product->seller?->user;

        if (!$seller) {
            return;
        }

        Log::info('Product review rating changed significantly', [
            'review_id' => $review->id,
            'product_id' => $product->id,
            'old_rating' => $oldRating,
            'new_rating' => $newRating
        ]);

        $title = 'Đánh giá được cập nhật';
        $message = "Đánh giá cho sản phẩm \"{$product->name}\" đã được cập nhật từ {$oldRating} sao thành {$newRating} sao";

        $data = [
            'review_id' => $review->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'old_rating' => $oldRating,
            'new_rating' => $newRating,
            'action_url' => route('marketplace.products.show', $product->slug) . '#reviews',
        ];

        NotificationService::send(
            $seller,
            'review_updated',
            $title,
            $message,
            $data,
            false
        );
    }

    /**
     * Get rating text description
     */
    private function getRatingText(int $rating): string
    {
        return match($rating) {
            1 => 'Rất tệ',
            2 => 'Tệ',
            3 => 'Trung bình',
            4 => 'Tốt',
            5 => 'Xuất sắc',
            default => 'Không xác định'
        };
    }
}
