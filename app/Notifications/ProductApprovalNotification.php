<?php

namespace App\Notifications;

use App\Models\MarketplaceProduct;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProductApprovalNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $product;
    public $status;
    public $rejectionReason;

    /**
     * Create a new notification instance.
     */
    public function __construct(MarketplaceProduct $product, string $status, string $rejectionReason = null)
    {
        $this->product = $product;
        $this->status = $status;
        $this->rejectionReason = $rejectionReason;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $mailMessage = new MailMessage();

        if ($this->status === 'approved') {
            $mailMessage
                ->subject('🎉 Sản phẩm của bạn đã được duyệt - MechaMap')
                ->greeting('Chúc mừng!')
                ->line("Sản phẩm \"{$this->product->name}\" của bạn đã được duyệt và hiện đã có mặt trên MechaMap Marketplace.")
                ->line('Khách hàng có thể tìm thấy và mua sản phẩm của bạn ngay bây giờ.')
                ->action('Xem Sản Phẩm', route('marketplace.products.show', $this->product->slug))
                ->line('Cảm ơn bạn đã tin tưởng MechaMap!')
                ->salutation('Trân trọng,<br>Đội ngũ MechaMap');
        } else {
            $mailMessage
                ->subject('❌ Sản phẩm của bạn cần được chỉnh sửa - MechaMap')
                ->greeting('Thông báo về sản phẩm')
                ->line("Sản phẩm \"{$this->product->name}\" của bạn chưa được duyệt và cần chỉnh sửa.")
                ->line("**Lý do:** {$this->rejectionReason}")
                ->line('Vui lòng chỉnh sửa sản phẩm theo yêu cầu và gửi lại để được duyệt.')
                ->action('Chỉnh Sửa Sản Phẩm', $this->getEditUrl())
                ->line('Nếu có thắc mắc, vui lòng liên hệ với chúng tôi.')
                ->salutation('Trân trọng,<br>Đội ngũ MechaMap');
        }

        return $mailMessage;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'status' => $this->status,
            'rejection_reason' => $this->rejectionReason,
            'message' => $this->getNotificationMessage(),
            'action_url' => $this->getActionUrl(),
        ];
    }

    /**
     * Get notification message based on status
     */
    private function getNotificationMessage(): string
    {
        if ($this->status === 'approved') {
            return "Sản phẩm \"{$this->product->name}\" đã được duyệt và hiện có mặt trên marketplace.";
        } else {
            return "Sản phẩm \"{$this->product->name}\" cần chỉnh sửa: {$this->rejectionReason}";
        }
    }

    /**
     * Get action URL based on status
     */
    private function getActionUrl(): string
    {
        if ($this->status === 'approved') {
            return route('marketplace.products.show', $this->product->slug);
        } else {
            return $this->getEditUrl();
        }
    }

    /**
     * Get edit URL based on seller type
     */
    private function getEditUrl(): string
    {
        switch ($this->product->seller_type) {
            case 'supplier':
                return route('supplier.products.edit', $this->product);
            case 'manufacturer':
                return route('manufacturer.products.edit', $this->product);
            case 'brand':
                return route('brand.products.edit', $this->product);
            default:
                return route('marketplace.products.show', $this->product->slug);
        }
    }
}
