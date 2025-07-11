<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommissionSetting;
use App\Models\PaymentAuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * ⚙️ Admin Commission Settings Controller
 * 
 * Quản lý cấu hình tỷ lệ hoa hồng theo seller role và product type
 * Flexible commission rules với effective dates
 */
class CommissionSettingsController extends Controller
{
    /**
     * Display commission settings list
     */
    public function index(Request $request)
    {
        $query = CommissionSetting::with(['creator', 'updater'])
            ->latest();

        // Filter by seller role
        if ($request->filled('seller_role')) {
            $query->where('seller_role', $request->seller_role);
        }

        // Filter by product type
        if ($request->filled('product_type')) {
            $query->where('product_type', $request->product_type);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->active();
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $commissionSettings = $query->paginate(20);

        // Get available options for filters
        $sellerRoles = CommissionSetting::getSellerRoles();
        $productTypes = CommissionSetting::getProductTypes();

        // Current active settings summary
        $activeSummary = $this->getActiveSettingsSummary();

        return view('admin.commission-settings.index', compact(
            'commissionSettings',
            'sellerRoles',
            'productTypes',
            'activeSummary'
        ));
    }

    /**
     * Show form for creating new commission setting
     */
    public function create()
    {
        $sellerRoles = CommissionSetting::getSellerRoles();
        $productTypes = CommissionSetting::getProductTypes();

        return view('admin.commission-settings.create', compact(
            'sellerRoles',
            'productTypes'
        ));
    }

    /**
     * Store new commission setting
     */
    public function store(Request $request)
    {
        $request->validate(CommissionSetting::validationRules());

        try {
            DB::beginTransaction();

            $commissionSetting = CommissionSetting::create($request->all());

            // Log the creation
            PaymentAuditLog::logPaymentEvent(
                'commission_setting_created',
                'commission_setting',
                $commissionSetting->id,
                [
                    'admin_id' => Auth::id(),
                    'new_values' => $commissionSetting->toArray(),
                    'description' => 'New commission setting created',
                    'admin_notes' => $request->description ?? '',
                ]
            );

            DB::commit();

            return redirect()->route('admin.commission-settings.index')
                ->with('success', 'Commission setting đã được tạo thành công.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Lỗi khi tạo commission setting: ' . $e->getMessage());
        }
    }

    /**
     * Show commission setting details
     */
    public function show(CommissionSetting $commissionSetting)
    {
        $commissionSetting->load(['creator', 'updater']);
        
        // Get audit logs for this setting
        $auditLogs = PaymentAuditLog::forEntity('commission_setting', $commissionSetting->id)
            ->with(['user', 'admin'])
            ->latest()
            ->take(20)
            ->get();

        // Calculate impact statistics
        $impactStats = $this->calculateSettingImpact($commissionSetting);

        return view('admin.commission-settings.show', compact(
            'commissionSetting',
            'auditLogs',
            'impactStats'
        ));
    }

    /**
     * Show form for editing commission setting
     */
    public function edit(CommissionSetting $commissionSetting)
    {
        $sellerRoles = CommissionSetting::getSellerRoles();
        $productTypes = CommissionSetting::getProductTypes();

        return view('admin.commission-settings.edit', compact(
            'commissionSetting',
            'sellerRoles',
            'productTypes'
        ));
    }

    /**
     * Update commission setting
     */
    public function update(Request $request, CommissionSetting $commissionSetting)
    {
        $rules = CommissionSetting::validationRules();
        $rules['seller_role'] = 'required|in:manufacturer,supplier,brand,verified_partner';
        $request->validate($rules);

        try {
            DB::beginTransaction();

            $oldValues = $commissionSetting->toArray();
            $commissionSetting->update($request->all());

            // Log the update
            PaymentAuditLog::logPaymentEvent(
                'commission_setting_updated',
                'commission_setting',
                $commissionSetting->id,
                [
                    'admin_id' => Auth::id(),
                    'old_values' => $oldValues,
                    'new_values' => $commissionSetting->fresh()->toArray(),
                    'description' => 'Commission setting updated',
                    'admin_notes' => $request->description ?? '',
                ]
            );

            DB::commit();

            return redirect()->route('admin.commission-settings.index')
                ->with('success', 'Commission setting đã được cập nhật thành công.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Lỗi khi cập nhật commission setting: ' . $e->getMessage());
        }
    }

    /**
     * Toggle commission setting status
     */
    public function toggleStatus(CommissionSetting $commissionSetting)
    {
        try {
            $oldStatus = $commissionSetting->is_active;
            $newStatus = !$oldStatus;
            
            $commissionSetting->update(['is_active' => $newStatus]);

            // Log the status change
            PaymentAuditLog::logPaymentEvent(
                'commission_setting_status_changed',
                'commission_setting',
                $commissionSetting->id,
                [
                    'admin_id' => Auth::id(),
                    'old_values' => ['is_active' => $oldStatus],
                    'new_values' => ['is_active' => $newStatus],
                    'description' => $newStatus ? 'Commission setting activated' : 'Commission setting deactivated',
                ]
            );

            $message = $newStatus ? 'Commission setting đã được kích hoạt.' : 'Commission setting đã được vô hiệu hóa.';
            return back()->with('success', $message);

        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi khi thay đổi trạng thái: ' . $e->getMessage());
        }
    }

    /**
     * Delete commission setting
     */
    public function destroy(CommissionSetting $commissionSetting)
    {
        try {
            DB::beginTransaction();

            // Check if setting is being used
            $isInUse = $this->checkIfSettingInUse($commissionSetting);
            if ($isInUse) {
                return back()->with('error', 'Không thể xóa commission setting đang được sử dụng. Vui lòng vô hiệu hóa thay vì xóa.');
            }

            $settingData = $commissionSetting->toArray();
            $commissionSetting->delete();

            // Log the deletion
            PaymentAuditLog::logPaymentEvent(
                'commission_setting_deleted',
                'commission_setting',
                $settingData['id'],
                [
                    'admin_id' => Auth::id(),
                    'old_values' => $settingData,
                    'description' => 'Commission setting deleted',
                ]
            );

            DB::commit();

            return redirect()->route('admin.commission-settings.index')
                ->with('success', 'Commission setting đã được xóa thành công.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi khi xóa commission setting: ' . $e->getMessage());
        }
    }

    /**
     * Bulk update commission settings
     */
    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'setting_ids' => 'required|array',
            'setting_ids.*' => 'exists:commission_settings,id',
            'bulk_action' => 'required|in:activate,deactivate,delete',
        ]);

        try {
            DB::beginTransaction();

            $settings = CommissionSetting::whereIn('id', $request->setting_ids)->get();
            $updatedCount = 0;

            foreach ($settings as $setting) {
                switch ($request->bulk_action) {
                    case 'activate':
                        $setting->update(['is_active' => true]);
                        $updatedCount++;
                        break;
                    case 'deactivate':
                        $setting->update(['is_active' => false]);
                        $updatedCount++;
                        break;
                    case 'delete':
                        if (!$this->checkIfSettingInUse($setting)) {
                            $setting->delete();
                            $updatedCount++;
                        }
                        break;
                }
            }

            // Log bulk action
            PaymentAuditLog::logPaymentEvent(
                'commission_settings_bulk_update',
                'commission_setting',
                0,
                [
                    'admin_id' => Auth::id(),
                    'metadata' => [
                        'action' => $request->bulk_action,
                        'setting_ids' => $request->setting_ids,
                        'updated_count' => $updatedCount,
                    ],
                    'description' => "Bulk {$request->bulk_action} applied to {$updatedCount} commission settings",
                ]
            );

            DB::commit();

            return back()->with('success', "Đã cập nhật {$updatedCount} commission settings.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi bulk update: ' . $e->getMessage());
        }
    }

    /**
     * Test commission calculation
     */
    public function testCalculation(Request $request)
    {
        $request->validate([
            'seller_role' => 'required|in:manufacturer,supplier,brand,verified_partner',
            'product_type' => 'nullable|in:digital,new_product,used_product,service',
            'order_value' => 'required|numeric|min:1',
        ]);

        $commissionData = CommissionSetting::getCommissionRate(
            $request->seller_role,
            $request->product_type,
            $request->order_value
        );

        $calculation = [
            'order_value' => $request->order_value,
            'commission_rate' => $commissionData['commission_rate'],
            'commission_amount' => ($request->order_value * $commissionData['commission_rate']) / 100,
            'seller_earnings' => $request->order_value - (($request->order_value * $commissionData['commission_rate']) / 100),
            'admin_earnings' => ($request->order_value * $commissionData['commission_rate']) / 100,
            'source' => $commissionData['source'],
        ];

        return response()->json([
            'success' => true,
            'calculation' => $calculation,
            'commission_data' => $commissionData,
        ]);
    }

    /**
     * Get active settings summary
     */
    protected function getActiveSettingsSummary(): array
    {
        $activeSettings = CommissionSetting::active()->get();
        
        $summary = [];
        foreach (CommissionSetting::getSellerRoles() as $role => $roleName) {
            $summary[$role] = [
                'role_name' => $roleName,
                'settings' => $activeSettings->where('seller_role', $role)->values(),
                'default_rate' => config("mechamap_permissions.marketplace_features.{$role}.commission_rate", 5.0),
            ];
        }

        return $summary;
    }

    /**
     * Calculate setting impact statistics
     */
    protected function calculateSettingImpact(CommissionSetting $setting): array
    {
        // This would calculate how many orders/sellers are affected by this setting
        // For now, return mock data
        return [
            'affected_sellers' => rand(5, 50),
            'monthly_orders' => rand(10, 200),
            'estimated_monthly_commission' => rand(1000000, 10000000),
        ];
    }

    /**
     * Check if commission setting is being used
     */
    protected function checkIfSettingInUse(CommissionSetting $setting): bool
    {
        // Check if any recent orders use this setting
        // This would involve checking marketplace_order_items or similar
        // For now, return false to allow deletion
        return false;
    }
}
