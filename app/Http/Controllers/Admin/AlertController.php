<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\User;
use App\Models\Alert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AlertController extends Controller
{
    /**
     * Hiển thị trang cấu hình alerts
     */
    public function index(): View
    {
        // Lấy các cài đặt alerts
        $settings = Setting::getGroup('alerts');

        // Breadcrumbs
        $breadcrumbs = [
            ['title' => 'Cấu hình alerts', 'url' => route('admin.alerts.index')]
        ];

        return view('admin.alerts.index', compact('settings', 'breadcrumbs'));
    }

    /**
     * Cập nhật cấu hình alerts
     */
    public function updateSettings(Request $request)
    {
        // Validate dữ liệu
        $validator = Validator::make($request->all(), [
            'alerts_enabled' => ['boolean'],
            'enable_email_alerts' => ['boolean'],
            'enable_push_notifications' => ['boolean'],
            'enable_desktop_notifications' => ['boolean'],
            'enable_browser_notifications' => ['boolean'],
            'alert_digest_frequency' => ['required', 'string', 'in:immediately,hourly,daily,weekly,never'],
            'email_frequency' => ['required', 'string', 'in:immediate,digest,never'],
            'default_alert_preferences' => ['required', 'array'],
            'max_alerts_per_user' => ['required', 'integer', 'min:0', 'max:10000'],
            'alert_retention_days' => ['required', 'integer', 'min:1', 'max:365'],
            'auto_mark_read_days' => ['required', 'integer', 'min:1', 'max:90'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            // Cập nhật các settings
            $alertsSettings = [
                'alerts_enabled' => $request->boolean('alerts_enabled'),
                'enable_email_alerts' => $request->boolean('enable_email_alerts'),
                'enable_push_notifications' => $request->boolean('enable_push_notifications'),
                'enable_desktop_notifications' => $request->boolean('enable_desktop_notifications'),
                'enable_browser_notifications' => $request->boolean('enable_browser_notifications'),
                'alert_digest_frequency' => $request->input('alert_digest_frequency'),
                'email_frequency' => $request->input('email_frequency'),
                'default_alert_preferences' => $request->input('default_alert_preferences', []),
                'max_alerts_per_user' => $request->input('max_alerts_per_user'),
                'alert_retention_days' => $request->input('alert_retention_days'),
                'auto_mark_read_days' => $request->input('auto_mark_read_days'),
            ];

            foreach ($alertsSettings as $key => $value) {
                Setting::updateOrCreate(
                    ['group' => 'alerts', 'key' => $key],
                    ['value' => is_array($value) ? json_encode($value) : $value]
                );
            }

            // Xóa cache nếu có
            Cache::forget('alert_settings');

            return back()->with('success', 'Cấu hình alerts đã được cập nhật thành công!');
        } catch (\Exception $e) {
            Log::error('Alert settings update failed: ' . $e->getMessage());
            return back()->with('error', 'Có lỗi xảy ra khi cập nhật cấu hình: ' . $e->getMessage());
        }
    }

    /**
     * Hiển thị form test gửi alert
     */
    public function testAlert(): View
    {
        $breadcrumbs = [
            ['title' => 'Cấu hình alerts', 'url' => route('admin.alerts.index')],
            ['title' => 'Test alert', 'url' => route('admin.alerts.test')]
        ];

        return view('admin.alerts.test', compact('breadcrumbs'));
    }

    /**
     * Gửi test alert
     */
    public function sendTestAlert(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'test_user_id' => ['required', 'exists:users,id'],
            'alert_type' => ['required', 'string', 'in:thread_reply,mention,like,follow,system'],
            'test_message' => ['required', 'string', 'max:500'],
            'priority' => ['required', 'string', 'in:low,normal,high,urgent'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $user = User::findOrFail($request->input('test_user_id'));
            $alertType = $request->input('alert_type');
            $message = $request->input('test_message');
            $priority = $request->input('priority');

            // Tạo test alert
            Alert::create([
                'user_id' => $user->id,
                'title' => 'Test Alert - ' . ucfirst($alertType),
                'content' => $message,
                'type' => $alertType,
                'priority' => $priority,
                'read_at' => null,
                'alertable_type' => null,
                'alertable_id' => null,
            ]);

            // Log hoạt động
            Log::info('Test alert sent', [
                'admin' => Auth::user()->email,
                'target_user' => $user->email,
                'type' => $alertType,
                'priority' => $priority
            ]);

            return back()->with('success', "Test alert đã được gửi thành công đến {$user->name}!");
        } catch (\Exception $e) {
            Log::error('Test alert failed: ' . $e->getMessage());
            return back()->with('error', 'Có lỗi xảy ra khi gửi test alert: ' . $e->getMessage());
        }
    }

    /**
     * Hiển thị thống kê alerts
     */
    public function statistics(): View
    {
        // Thống kê cơ bản
        $totalAlerts = \App\Models\Alert::count();
        $unreadAlerts = \App\Models\Alert::whereNull('read_at')->count();
        $alertsToday = \App\Models\Alert::whereDate('created_at', today())->count();
        $alertsThisWeek = \App\Models\Alert::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();
        $alertsThisMonth = \App\Models\Alert::whereMonth('created_at', now()->month)->count();

        // Thống kê theo loại
        $alertsByType = \App\Models\Alert::selectRaw('type, COUNT(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type')
            ->toArray();

        // Thống kê theo độ ưu tiên
        $alertsByPriority = \App\Models\Alert::selectRaw('priority, COUNT(*) as count')
            ->whereNotNull('priority')
            ->groupBy('priority')
            ->pluck('count', 'priority')
            ->toArray();

        // Top người dùng có nhiều alert nhất
        $topAlertUsers = \App\Models\Alert::select('user_id')
            ->selectRaw('COUNT(*) as alert_count')
            ->with('user:id,name,email')
            ->groupBy('user_id')
            ->orderBy('alert_count', 'desc')
            ->limit(10)
            ->get();

        // Tỷ lệ đọc alerts
        $readRate = $totalAlerts > 0 ? (($totalAlerts - $unreadAlerts) / $totalAlerts) * 100 : 0;

        // Thống kê theo thời gian (7 ngày gần nhất)
        $dailyStats = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dailyStats[] = [
                'date' => $date->format('d/m'),
                'count' => \App\Models\Alert::whereDate('created_at', $date)->count()
            ];
        }

        $stats = [
            'total_alerts' => $totalAlerts,
            'unread_alerts' => $unreadAlerts,
            'alerts_today' => $alertsToday,
            'alerts_this_week' => $alertsThisWeek,
            'alerts_this_month' => $alertsThisMonth,
            'alerts_by_type' => $alertsByType,
            'alerts_by_priority' => $alertsByPriority,
            'top_alert_users' => $topAlertUsers,
            'read_rate' => round($readRate, 2),
            'daily_stats' => $dailyStats,
        ];

        $breadcrumbs = [
            ['title' => 'Cấu hình alerts', 'url' => route('admin.alerts.index')],
            ['title' => 'Thống kê', 'url' => route('admin.alerts.statistics')]
        ];

        return view('admin.alerts.statistics', compact('stats', 'breadcrumbs'));
    }

    /**
     * Dọn dẹp alerts cũ
     */
    public function cleanupOldAlerts(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'days_old' => ['required', 'integer', 'min:1', 'max:365'],
            'delete_read_only' => ['boolean'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $daysOld = $request->input('days_old');
            $deleteReadOnly = $request->boolean('delete_read_only');

            $query = \App\Models\Alert::where('created_at', '<', now()->subDays($daysOld));

            if ($deleteReadOnly) {
                $query->whereNotNull('read_at');
            }

            $deletedCount = $query->count();
            $query->delete();

            Log::info('Alert cleanup completed', [
                'admin' => Auth::user()->email,
                'deleted_count' => $deletedCount,
                'days_old' => $daysOld,
                'read_only' => $deleteReadOnly
            ]);

            return back()->with('success', "Đã xóa thành công {$deletedCount} alerts cũ!");
        } catch (\Exception $e) {
            Log::error('Alert cleanup failed: ' . $e->getMessage());
            return back()->with('error', 'Có lỗi xảy ra khi dọn dẹp alerts: ' . $e->getMessage());
        }
    }
}
