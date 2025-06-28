<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class AdminHeaderComposer
{
    /**
     * Bind data to the view.
     *
     * @param  \Illuminate\View\View  $view
     * @return void
     */
    public function compose(View $view)
    {
        // Get unread notifications count
        $unreadNotifications = $this->getUnreadNotificationsCount();
        
        // Get recent notifications
        $recentNotifications = $this->getRecentNotifications();
        
        $view->with([
            'unreadNotifications' => $unreadNotifications,
            'recentNotifications' => $recentNotifications,
        ]);
    }
    
    /**
     * Get unread notifications count
     *
     * @return int
     */
    private function getUnreadNotificationsCount()
    {
        // Placeholder - will be implemented with actual notification system
        return 0;
    }
    
    /**
     * Get recent notifications
     *
     * @return array
     */
    private function getRecentNotifications()
    {
        // Placeholder - will be implemented with actual notification system
        return [
            (object) [
                'id' => 1,
                'title' => 'Người dùng mới đăng ký',
                'message' => 'Nguyễn Văn A đã đăng ký tài khoản mới',
                'type_color' => 'primary',
                'icon' => 'account-plus',
                'action_url' => '#',
                'created_at' => now()->subHours(2),
            ],
            (object) [
                'id' => 2,
                'title' => 'Báo cáo vi phạm',
                'message' => 'Có báo cáo vi phạm mới cần xử lý',
                'type_color' => 'warning',
                'icon' => 'alert-circle',
                'action_url' => '#',
                'created_at' => now()->subHours(5),
            ],
            (object) [
                'id' => 3,
                'title' => 'Sản phẩm được duyệt',
                'message' => 'Sản phẩm "Máy tiện CNC" đã được phê duyệt',
                'type_color' => 'success',
                'icon' => 'check-circle',
                'action_url' => '#',
                'created_at' => now()->subDay(),
            ],
        ];
    }
}
