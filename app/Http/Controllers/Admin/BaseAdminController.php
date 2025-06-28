<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Base Admin Controller
 * 
 * Controller cơ sở cho tất cả admin controllers
 * Cung cấp các chức năng chung cho admin panel
 */
class BaseAdminController extends Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {
        // Apply admin authentication middleware
        $this->middleware('admin.auth');
        
        // Log admin activities
        $this->middleware(function ($request, $next) {
            $this->logAdminActivity($request);
            return $next($request);
        });
    }

    /**
     * Get current admin user
     */
    protected function getCurrentAdmin()
    {
        return Auth::guard('admin')->user();
    }

    /**
     * Check if current user has specific permission
     */
    protected function hasPermission(string $permission): bool
    {
        $user = $this->getCurrentAdmin();
        
        if (!$user) {
            return false;
        }

        // Admin có tất cả quyền
        if ($user->role === 'admin') {
            return true;
        }

        // Kiểm tra quyền cụ thể cho moderator
        return $user->hasPermission($permission);
    }

    /**
     * Authorize admin action
     */
    protected function authorizeAdmin(string $permission)
    {
        if (!$this->hasPermission($permission)) {
            abort(403, 'Bạn không có quyền thực hiện hành động này.');
        }
    }

    /**
     * Log admin activities
     */
    protected function logAdminActivity(Request $request)
    {
        $user = $this->getCurrentAdmin();
        
        if ($user && $request->method() !== 'GET') {
            Log::channel('admin')->info('Admin Activity', [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_role' => $user->role,
                'action' => $request->method(),
                'url' => $request->fullUrl(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'timestamp' => now()
            ]);
        }
    }

    /**
     * Return success response for AJAX requests
     */
    protected function successResponse(string $message = 'Thành công', array $data = [])
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ]);
    }

    /**
     * Return error response for AJAX requests
     */
    protected function errorResponse(string $message = 'Có lỗi xảy ra', array $errors = [], int $status = 400)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ], $status);
    }

    /**
     * Get common admin dashboard data
     */
    protected function getCommonDashboardData(): array
    {
        return [
            'current_admin' => $this->getCurrentAdmin(),
            'admin_permissions' => $this->getAdminPermissions(),
            'system_status' => $this->getSystemStatus(),
        ];
    }

    /**
     * Get admin permissions
     */
    protected function getAdminPermissions(): array
    {
        $user = $this->getCurrentAdmin();
        
        if (!$user) {
            return [];
        }

        if ($user->role === 'admin') {
            return [
                'manage_users' => true,
                'manage_content' => true,
                'manage_system' => true,
                'manage_marketplace' => true,
                'view_analytics' => true,
                'moderate_content' => true,
            ];
        }

        // Moderator permissions
        return [
            'manage_users' => false,
            'manage_content' => true,
            'manage_system' => false,
            'manage_marketplace' => false,
            'view_analytics' => true,
            'moderate_content' => true,
        ];
    }

    /**
     * Get system status
     */
    protected function getSystemStatus(): array
    {
        return [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'environment' => app()->environment(),
            'debug_mode' => config('app.debug'),
            'timezone' => config('app.timezone'),
            'locale' => app()->getLocale(),
        ];
    }

    /**
     * Handle bulk actions
     */
    protected function handleBulkAction(Request $request, string $model, array $allowedActions = [])
    {
        $action = $request->input('bulk_action');
        $ids = $request->input('selected_ids', []);

        if (!in_array($action, $allowedActions)) {
            return $this->errorResponse('Hành động không được phép.');
        }

        if (empty($ids)) {
            return $this->errorResponse('Vui lòng chọn ít nhất một mục.');
        }

        try {
            $count = 0;
            
            switch ($action) {
                case 'delete':
                    $count = $model::whereIn('id', $ids)->delete();
                    break;
                    
                case 'activate':
                    $count = $model::whereIn('id', $ids)->update(['status' => 'active']);
                    break;
                    
                case 'deactivate':
                    $count = $model::whereIn('id', $ids)->update(['status' => 'inactive']);
                    break;
            }

            return $this->successResponse("Đã thực hiện hành động cho {$count} mục.");
            
        } catch (\Exception $e) {
            Log::error('Bulk action error: ' . $e->getMessage());
            return $this->errorResponse('Có lỗi xảy ra khi thực hiện hành động.');
        }
    }

    /**
     * Validate admin request
     */
    protected function validateAdminRequest(Request $request, array $rules, array $messages = [])
    {
        return $request->validate($rules, $messages);
    }

    /**
     * Get pagination data for admin tables
     */
    protected function getPaginationData($query, int $perPage = 20)
    {
        return $query->paginate($perPage)->appends(request()->query());
    }
}
