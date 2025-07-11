<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CentralizedPayment;
use App\Models\SellerPayoutRequest;
use App\Models\PaymentAuditLog;
use App\Models\MarketplaceOrder;
use App\Models\PaymentSystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * 🏦 Admin Payment Management Controller
 * 
 * Quản lý tổng quan hệ thống thanh toán tập trung
 * Dashboard, analytics và payment processing cho admin
 */
class PaymentManagementController extends Controller
{
    /**
     * Payment Management Dashboard
     */
    public function index(Request $request)
    {
        // Date range filter
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth());
        
        if (is_string($startDate)) {
            $startDate = Carbon::parse($startDate);
        }
        if (is_string($endDate)) {
            $endDate = Carbon::parse($endDate);
        }

        // Payment Statistics
        $paymentStats = $this->getPaymentStatistics($startDate, $endDate);
        
        // Revenue Analytics
        $revenueAnalytics = $this->getRevenueAnalytics($startDate, $endDate);
        
        // Payout Statistics
        $payoutStats = $this->getPayoutStatistics($startDate, $endDate);
        
        // Recent Activities
        $recentActivities = $this->getRecentActivities();
        
        // System Health
        $systemHealth = $this->getSystemHealth();

        return view('admin.payment-management.index', compact(
            'paymentStats',
            'revenueAnalytics', 
            'payoutStats',
            'recentActivities',
            'systemHealth',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Get payment statistics
     */
    protected function getPaymentStatistics($startDate, $endDate): array
    {
        $baseQuery = CentralizedPayment::whereBetween('created_at', [$startDate, $endDate]);

        return [
            'total_payments' => $baseQuery->count(),
            'completed_payments' => $baseQuery->where('status', 'completed')->count(),
            'pending_payments' => $baseQuery->where('status', 'pending')->count(),
            'failed_payments' => $baseQuery->where('status', 'failed')->count(),
            'total_revenue' => $baseQuery->where('status', 'completed')->sum('net_received'),
            'total_fees' => $baseQuery->where('status', 'completed')->sum('gateway_fee'),
            'stripe_payments' => $baseQuery->where('payment_method', 'stripe')->count(),
            'sepay_payments' => $baseQuery->where('payment_method', 'sepay')->count(),
            'average_payment_amount' => $baseQuery->where('status', 'completed')->avg('gross_amount'),
        ];
    }

    /**
     * Get revenue analytics
     */
    protected function getRevenueAnalytics($startDate, $endDate): array
    {
        // Daily revenue for chart
        $dailyRevenue = CentralizedPayment::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(net_received) as revenue'),
                DB::raw('COUNT(*) as payment_count')
            )
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Revenue by payment method
        $revenueByMethod = CentralizedPayment::select(
                'payment_method',
                DB::raw('SUM(net_received) as revenue'),
                DB::raw('COUNT(*) as payment_count')
            )
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('payment_method')
            ->get();

        // Commission earned (admin earnings)
        $commissionEarned = DB::table('marketplace_order_items as oi')
            ->join('marketplace_orders as o', 'oi.order_id', '=', 'o.id')
            ->join('centralized_payments as cp', 'o.centralized_payment_id', '=', 'cp.id')
            ->where('cp.status', 'completed')
            ->whereBetween('cp.created_at', [$startDate, $endDate])
            ->sum('oi.admin_commission');

        return [
            'daily_revenue' => $dailyRevenue,
            'revenue_by_method' => $revenueByMethod,
            'commission_earned' => $commissionEarned,
            'growth_rate' => $this->calculateGrowthRate($startDate, $endDate),
        ];
    }

    /**
     * Get payout statistics
     */
    protected function getPayoutStatistics($startDate, $endDate): array
    {
        $baseQuery = SellerPayoutRequest::whereBetween('created_at', [$startDate, $endDate]);

        return [
            'total_payout_requests' => $baseQuery->count(),
            'pending_payouts' => $baseQuery->where('status', 'pending')->count(),
            'approved_payouts' => $baseQuery->where('status', 'approved')->count(),
            'completed_payouts' => $baseQuery->where('status', 'completed')->count(),
            'rejected_payouts' => $baseQuery->where('status', 'rejected')->count(),
            'total_payout_amount' => $baseQuery->where('status', 'completed')->sum('net_payout'),
            'pending_payout_amount' => $baseQuery->where('status', 'pending')->sum('net_payout'),
            'average_payout_amount' => $baseQuery->where('status', 'completed')->avg('net_payout'),
        ];
    }

    /**
     * Get recent activities
     */
    protected function getRecentActivities(): array
    {
        $recentPayments = CentralizedPayment::with(['order', 'customer'])
            ->latest()
            ->take(10)
            ->get();

        $recentPayouts = SellerPayoutRequest::with(['seller', 'processor'])
            ->latest()
            ->take(10)
            ->get();

        $recentAuditLogs = PaymentAuditLog::with(['user', 'admin'])
            ->whereIn('event_type', ['payment_completed', 'payout_approved', 'payout_completed'])
            ->latest()
            ->take(15)
            ->get();

        return [
            'recent_payments' => $recentPayments,
            'recent_payouts' => $recentPayouts,
            'recent_audit_logs' => $recentAuditLogs,
        ];
    }

    /**
     * Get system health metrics
     */
    protected function getSystemHealth(): array
    {
        $last24Hours = Carbon::now()->subDay();
        
        return [
            'payment_success_rate' => $this->calculatePaymentSuccessRate($last24Hours),
            'average_processing_time' => $this->calculateAverageProcessingTime($last24Hours),
            'webhook_failure_rate' => $this->calculateWebhookFailureRate($last24Hours),
            'pending_review_orders' => MarketplaceOrder::where('requires_admin_review', true)
                ->where('reviewed_at', null)
                ->count(),
            'system_settings_status' => $this->checkSystemSettings(),
        ];
    }

    /**
     * Calculate payment success rate
     */
    protected function calculatePaymentSuccessRate($since): float
    {
        $totalPayments = CentralizedPayment::where('created_at', '>=', $since)->count();
        $successfulPayments = CentralizedPayment::where('created_at', '>=', $since)
            ->where('status', 'completed')
            ->count();

        return $totalPayments > 0 ? ($successfulPayments / $totalPayments) * 100 : 0;
    }

    /**
     * Calculate average processing time
     */
    protected function calculateAverageProcessingTime($since): float
    {
        $completedPayments = CentralizedPayment::where('created_at', '>=', $since)
            ->where('status', 'completed')
            ->whereNotNull('confirmed_at')
            ->get();

        if ($completedPayments->isEmpty()) {
            return 0;
        }

        $totalSeconds = $completedPayments->sum(function ($payment) {
            return $payment->confirmed_at->diffInSeconds($payment->created_at);
        });

        return $totalSeconds / $completedPayments->count();
    }

    /**
     * Calculate webhook failure rate
     */
    protected function calculateWebhookFailureRate($since): float
    {
        $totalWebhookEvents = PaymentAuditLog::where('created_at', '>=', $since)
            ->whereIn('event_type', ['payment_completed', 'payment_failed'])
            ->count();

        $failedWebhookEvents = PaymentAuditLog::where('created_at', '>=', $since)
            ->where('event_type', 'payment_failed')
            ->count();

        return $totalWebhookEvents > 0 ? ($failedWebhookEvents / $totalWebhookEvents) * 100 : 0;
    }

    /**
     * Calculate growth rate
     */
    protected function calculateGrowthRate($startDate, $endDate): float
    {
        $currentPeriodRevenue = CentralizedPayment::where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('net_received');

        $periodLength = $startDate->diffInDays($endDate);
        $previousStartDate = $startDate->copy()->subDays($periodLength);
        $previousEndDate = $startDate->copy()->subDay();

        $previousPeriodRevenue = CentralizedPayment::where('status', 'completed')
            ->whereBetween('created_at', [$previousStartDate, $previousEndDate])
            ->sum('net_received');

        if ($previousPeriodRevenue == 0) {
            return $currentPeriodRevenue > 0 ? 100 : 0;
        }

        return (($currentPeriodRevenue - $previousPeriodRevenue) / $previousPeriodRevenue) * 100;
    }

    /**
     * Check system settings status
     */
    protected function checkSystemSettings(): array
    {
        $stripeAccount = PaymentSystemSetting::get('admin_bank_account_stripe');
        $sepayAccount = PaymentSystemSetting::get('admin_bank_account_sepay');

        return [
            'stripe_configured' => !empty($stripeAccount['account_id']),
            'sepay_configured' => !empty($sepayAccount['account_number']),
            'centralized_payment_enabled' => PaymentSystemSetting::get('centralized_payment_enabled', true),
            'auto_payout_enabled' => PaymentSystemSetting::get('auto_payout_enabled', false),
        ];
    }

    /**
     * Export payment data
     */
    public function exportPayments(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'format' => 'required|in:csv,excel',
        ]);

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        $payments = CentralizedPayment::with(['order', 'customer'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        if ($request->format === 'csv') {
            return $this->exportToCsv($payments, $startDate, $endDate);
        } else {
            return $this->exportToExcel($payments, $startDate, $endDate);
        }
    }

    /**
     * Export to CSV
     */
    protected function exportToCsv($payments, $startDate, $endDate)
    {
        $filename = "payments_{$startDate->format('Y-m-d')}_to_{$endDate->format('Y-m-d')}.csv";
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($payments) {
            $file = fopen('php://output', 'w');
            
            // CSV Headers
            fputcsv($file, [
                'Payment ID',
                'Payment Reference',
                'Order Number',
                'Customer Email',
                'Payment Method',
                'Gross Amount',
                'Gateway Fee',
                'Net Received',
                'Status',
                'Created At',
                'Completed At'
            ]);

            // CSV Data
            foreach ($payments as $payment) {
                fputcsv($file, [
                    $payment->id,
                    $payment->payment_reference,
                    $payment->order->order_number ?? '',
                    $payment->customer_email,
                    $payment->payment_method,
                    $payment->gross_amount,
                    $payment->gateway_fee,
                    $payment->net_received,
                    $payment->status,
                    $payment->created_at->format('Y-m-d H:i:s'),
                    $payment->confirmed_at ? $payment->confirmed_at->format('Y-m-d H:i:s') : '',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export to Excel (placeholder - would need PhpSpreadsheet)
     */
    protected function exportToExcel($payments, $startDate, $endDate)
    {
        // For now, fallback to CSV
        return $this->exportToCsv($payments, $startDate, $endDate);
    }
}
