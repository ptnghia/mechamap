<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CentralizedPayment;
use App\Models\SellerPayoutRequest;
use App\Models\MarketplaceOrder;
use App\Models\MarketplaceOrderItem;
use App\Models\PaymentAuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * 📊 Admin Financial Reports Controller
 *
 * Comprehensive financial reporting và analytics
 * Revenue tracking, commission analysis, payout reports
 */
class FinancialReportsController extends Controller
{
    /**
     * Financial Reports Dashboard
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

        // Financial Overview
        $financialOverview = $this->getFinancialOverview($startDate, $endDate);

        // Revenue Analytics
        $revenueAnalytics = $this->getRevenueAnalytics($startDate, $endDate);

        // Commission Analytics
        $commissionAnalytics = $this->getCommissionAnalytics($startDate, $endDate);

        // Payout Analytics
        $payoutAnalytics = $this->getPayoutAnalytics($startDate, $endDate);

        // Top Performers
        $topPerformers = $this->getTopPerformers($startDate, $endDate);

        return view('admin.financial-reports.index', compact(
            'financialOverview',
            'revenueAnalytics',
            'commissionAnalytics',
            'payoutAnalytics',
            'topPerformers',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Revenue Report
     */
    public function revenueReport(Request $request)
    {
        $startDate = Carbon::parse($request->get('start_date', Carbon::now()->startOfMonth()));
        $endDate = Carbon::parse($request->get('end_date', Carbon::now()->endOfMonth()));
        $groupBy = $request->get('group_by', 'day'); // day, week, month

        // Revenue by time period
        $revenueData = $this->getRevenueByPeriod($startDate, $endDate, $groupBy);

        // Revenue by payment method
        $revenueByMethod = $this->getRevenueByPaymentMethod($startDate, $endDate);

        // Revenue by seller type
        $revenueBySeller = $this->getRevenueBySellerType($startDate, $endDate);

        // Revenue trends
        $revenueTrends = $this->getRevenueTrends($startDate, $endDate);

        return view('admin.financial-reports.revenue', compact(
            'revenueData',
            'revenueByMethod',
            'revenueBySeller',
            'revenueTrends',
            'startDate',
            'endDate',
            'groupBy'
        ));
    }

    /**
     * Commission Report
     */
    public function commissionReport(Request $request)
    {
        $startDate = Carbon::parse($request->get('start_date', Carbon::now()->startOfMonth()));
        $endDate = Carbon::parse($request->get('end_date', Carbon::now()->endOfMonth()));

        // Commission overview
        $commissionOverview = $this->getCommissionOverview($startDate, $endDate);

        // Commission by seller role
        $commissionByRole = $this->getCommissionBySellerRole($startDate, $endDate);

        // Commission by product type
        $commissionByProduct = $this->getCommissionByProductType($startDate, $endDate);

        // Top commission earners
        $topCommissionEarners = $this->getTopCommissionEarners($startDate, $endDate);

        return view('admin.financial-reports.commission', compact(
            'commissionOverview',
            'commissionByRole',
            'commissionByProduct',
            'topCommissionEarners',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Payout Report
     */
    public function payoutReport(Request $request)
    {
        $startDate = Carbon::parse($request->get('start_date', Carbon::now()->startOfMonth()));
        $endDate = Carbon::parse($request->get('end_date', Carbon::now()->endOfMonth()));

        // Payout overview
        $payoutOverview = $this->getPayoutOverview($startDate, $endDate);

        // Payout trends
        $payoutTrends = $this->getPayoutTrends($startDate, $endDate);

        // Payout by seller
        $payoutBySeller = $this->getPayoutBySeller($startDate, $endDate);

        // Processing time analysis
        $processingTimeAnalysis = $this->getPayoutProcessingTimeAnalysis($startDate, $endDate);

        return view('admin.financial-reports.payout', compact(
            'payoutOverview',
            'payoutTrends',
            'payoutBySeller',
            'processingTimeAnalysis',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Seller Performance Report
     */
    public function sellerPerformanceReport(Request $request)
    {
        $startDate = Carbon::parse($request->get('start_date', Carbon::now()->startOfMonth()));
        $endDate = Carbon::parse($request->get('end_date', Carbon::now()->endOfMonth()));
        $sellerId = $request->get('seller_id');

        // Seller performance metrics
        $sellerMetrics = $this->getSellerPerformanceMetrics($startDate, $endDate, $sellerId);

        // Revenue comparison
        $revenueComparison = $this->getSellerRevenueComparison($startDate, $endDate);

        // Growth analysis
        $growthAnalysis = $this->getSellerGrowthAnalysis($startDate, $endDate);

        // Get sellers for filter
        $sellers = User::whereHas('sellerAccount')->get(['id', 'name']);

        return view('admin.financial-reports.seller-performance', compact(
            'sellerMetrics',
            'revenueComparison',
            'growthAnalysis',
            'sellers',
            'startDate',
            'endDate',
            'sellerId'
        ));
    }

    /**
     * Export Financial Report
     */
    public function exportReport(Request $request)
    {
        $request->validate([
            'report_type' => 'required|in:revenue,commission,payout,seller_performance',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'format' => 'required|in:csv,excel,pdf',
        ]);

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        switch ($request->report_type) {
            case 'revenue':
                return $this->exportRevenueReport($startDate, $endDate, $request->format);
            case 'commission':
                return $this->exportCommissionReport($startDate, $endDate, $request->format);
            case 'payout':
                return $this->exportPayoutReport($startDate, $endDate, $request->format);
            case 'seller_performance':
                return $this->exportSellerPerformanceReport($startDate, $endDate, $request->format);
        }
    }

    /**
     * Get Financial Overview
     */
    protected function getFinancialOverview($startDate, $endDate): array
    {
        $payments = CentralizedPayment::whereBetween('created_at', [$startDate, $endDate]);
        $payouts = SellerPayoutRequest::whereBetween('created_at', [$startDate, $endDate]);

        return [
            'total_revenue' => $payments->where('status', 'completed')->sum('net_received'),
            'total_commission' => MarketplaceOrderItem::whereHas('order', function($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate])
                  ->where('payment_status', 'paid');
            })->sum('admin_commission'),
            'total_payouts' => $payouts->where('status', 'completed')->sum('net_payout'),
            'pending_payouts' => $payouts->where('status', 'pending')->sum('net_payout'),
            'net_profit' => $this->calculateNetProfit($startDate, $endDate),
            'payment_count' => $payments->count(),
            'average_order_value' => $payments->where('status', 'completed')->avg('gross_amount'),
            'growth_rate' => $this->calculateGrowthRate($startDate, $endDate),
        ];
    }

    /**
     * Get Revenue Analytics
     */
    protected function getRevenueAnalytics($startDate, $endDate): array
    {
        // Daily revenue for chart
        $dailyRevenue = CentralizedPayment::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(net_received) as revenue'),
                DB::raw('COUNT(*) as payment_count'),
                DB::raw('AVG(net_received) as avg_payment')
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
                DB::raw('COUNT(*) as payment_count'),
                DB::raw('AVG(net_received) as avg_payment')
            )
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('payment_method')
            ->get();

        return [
            'daily_revenue' => $dailyRevenue,
            'revenue_by_method' => $revenueByMethod,
            'total_revenue' => $dailyRevenue->sum('revenue'),
            'total_payments' => $dailyRevenue->sum('payment_count'),
            'peak_day' => $dailyRevenue->sortByDesc('revenue')->first(),
        ];
    }

    /**
     * Get Commission Analytics
     */
    protected function getCommissionAnalytics($startDate, $endDate): array
    {
        $commissionData = MarketplaceOrderItem::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(admin_commission) as total_commission'),
                DB::raw('COUNT(*) as order_count'),
                DB::raw('AVG(commission_rate) as avg_commission_rate')
            )
            ->whereHas('order', function($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate])
                  ->where('payment_status', 'paid');
            })
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'daily_commission' => $commissionData,
            'total_commission' => $commissionData->sum('total_commission'),
            'average_commission_rate' => $commissionData->avg('avg_commission_rate'),
            'commission_growth' => $this->calculateCommissionGrowth($startDate, $endDate),
        ];
    }

    /**
     * Get Payout Analytics
     */
    protected function getPayoutAnalytics($startDate, $endDate): array
    {
        $payouts = SellerPayoutRequest::whereBetween('created_at', [$startDate, $endDate]);

        return [
            'total_requested' => $payouts->sum('net_payout'),
            'total_approved' => $payouts->where('status', 'approved')->sum('net_payout'),
            'total_completed' => $payouts->where('status', 'completed')->sum('net_payout'),
            'pending_amount' => $payouts->where('status', 'pending')->sum('net_payout'),
            'average_payout' => $payouts->where('status', 'completed')->avg('net_payout'),
            'payout_count' => $payouts->count(),
            'approval_rate' => $this->calculatePayoutApprovalRate($startDate, $endDate),
        ];
    }

    /**
     * Get Top Performers
     */
    protected function getTopPerformers($startDate, $endDate): array
    {
        // Top sellers by revenue
        $topSellers = MarketplaceOrderItem::select(
                'seller_id',
                DB::raw('SUM(total_amount) as total_revenue'),
                DB::raw('SUM(seller_earnings) as seller_earnings'),
                DB::raw('COUNT(*) as order_count')
            )
            ->whereHas('order', function($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate])
                  ->where('payment_status', 'paid');
            })
            ->with('seller:id,name,email')
            ->groupBy('seller_id')
            ->orderByDesc('total_revenue')
            ->take(10)
            ->get();

        return [
            'top_sellers' => $topSellers,
        ];
    }

    /**
     * Calculate Net Profit
     */
    protected function calculateNetProfit($startDate, $endDate): float
    {
        $totalCommission = MarketplaceOrderItem::whereHas('order', function($q) use ($startDate, $endDate) {
            $q->whereBetween('created_at', [$startDate, $endDate])
              ->where('payment_status', 'paid');
        })->sum('admin_commission');

        $totalGatewayFees = CentralizedPayment::where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('gateway_fee');

        return $totalCommission - $totalGatewayFees;
    }

    /**
     * Calculate Growth Rate
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
     * Calculate Commission Growth
     */
    protected function calculateCommissionGrowth($startDate, $endDate): float
    {
        // Similar to revenue growth but for commission
        $currentCommission = MarketplaceOrderItem::whereHas('order', function($q) use ($startDate, $endDate) {
            $q->whereBetween('created_at', [$startDate, $endDate])
              ->where('payment_status', 'paid');
        })->sum('admin_commission');

        $periodLength = $startDate->diffInDays($endDate);
        $previousStartDate = $startDate->copy()->subDays($periodLength);
        $previousEndDate = $startDate->copy()->subDay();

        $previousCommission = MarketplaceOrderItem::whereHas('order', function($q) use ($previousStartDate, $previousEndDate) {
            $q->whereBetween('created_at', [$previousStartDate, $previousEndDate])
              ->where('payment_status', 'paid');
        })->sum('admin_commission');

        if ($previousCommission == 0) {
            return $currentCommission > 0 ? 100 : 0;
        }

        return (($currentCommission - $previousCommission) / $previousCommission) * 100;
    }

    /**
     * Calculate Payout Approval Rate
     */
    protected function calculatePayoutApprovalRate($startDate, $endDate): float
    {
        $totalRequests = SellerPayoutRequest::whereBetween('created_at', [$startDate, $endDate])->count();
        $approvedRequests = SellerPayoutRequest::whereBetween('created_at', [$startDate, $endDate])
            ->whereIn('status', ['approved', 'completed'])
            ->count();

        return $totalRequests > 0 ? ($approvedRequests / $totalRequests) * 100 : 0;
    }

    /**
     * Export Revenue Report
     */
    protected function exportRevenueReport($startDate, $endDate, $format)
    {
        $revenueData = $this->getRevenueByPeriod($startDate, $endDate, 'day');

        $data = [
            ['Ngày', 'Doanh Thu (VNĐ)', 'Số Giao Dịch', 'Giá Trị Trung Bình (VNĐ)']
        ];

        foreach ($revenueData as $row) {
            $data[] = [
                Carbon::parse($row->date)->format('d/m/Y'),
                $row->revenue,
                $row->payment_count,
                $row->avg_payment
            ];
        }

        return $this->generateExport($data, "revenue_report_{$startDate->format('Y-m-d')}_{$endDate->format('Y-m-d')}", $format);
    }

    /**
     * Export Commission Report
     */
    protected function exportCommissionReport($startDate, $endDate, $format)
    {
        $commissionData = MarketplaceOrderItem::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(admin_commission) as total_commission'),
                DB::raw('COUNT(*) as order_count'),
                DB::raw('AVG(commission_rate) as avg_commission_rate')
            )
            ->whereHas('order', function($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate])
                  ->where('payment_status', 'paid');
            })
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $data = [
            ['Ngày', 'Tổng Hoa Hồng (VNĐ)', 'Số Đơn Hàng', 'Tỷ Lệ Hoa Hồng TB (%)']
        ];

        foreach ($commissionData as $row) {
            $data[] = [
                Carbon::parse($row->date)->format('d/m/Y'),
                $row->total_commission,
                $row->order_count,
                round($row->avg_commission_rate, 2)
            ];
        }

        return $this->generateExport($data, "commission_report_{$startDate->format('Y-m-d')}_{$endDate->format('Y-m-d')}", $format);
    }

    /**
     * Export Payout Report
     */
    protected function exportPayoutReport($startDate, $endDate, $format)
    {
        $payoutData = SellerPayoutRequest::with(['seller', 'sellerAccount'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $data = [
            ['Ngày Tạo', 'Mã Payout', 'Seller', 'Tổng Bán (VNĐ)', 'Hoa Hồng (VNĐ)', 'Payout Ròng (VNĐ)', 'Trạng Thái']
        ];

        foreach ($payoutData as $row) {
            $data[] = [
                $row->created_at->format('d/m/Y'),
                $row->payout_reference,
                $row->seller->name ?? 'N/A',
                $row->total_sales,
                $row->commission_amount,
                $row->net_payout,
                $row->status
            ];
        }

        return $this->generateExport($data, "payout_report_{$startDate->format('Y-m-d')}_{$endDate->format('Y-m-d')}", $format);
    }

    /**
     * Export Seller Performance Report
     */
    protected function exportSellerPerformanceReport($startDate, $endDate, $format)
    {
        $sellerData = MarketplaceOrderItem::select(
                'seller_id',
                DB::raw('SUM(total_amount) as total_revenue'),
                DB::raw('SUM(seller_earnings) as seller_earnings'),
                DB::raw('SUM(admin_commission) as total_commission'),
                DB::raw('COUNT(*) as order_count'),
                DB::raw('AVG(total_amount) as avg_order_value')
            )
            ->whereHas('order', function($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate])
                  ->where('payment_status', 'paid');
            })
            ->with('seller:id,name,email')
            ->groupBy('seller_id')
            ->orderByDesc('total_revenue')
            ->get();

        $data = [
            ['Seller', 'Email', 'Tổng Doanh Thu (VNĐ)', 'Thu Nhập Seller (VNĐ)', 'Hoa Hồng Admin (VNĐ)', 'Số Đơn Hàng', 'Giá Trị TB/Đơn (VNĐ)']
        ];

        foreach ($sellerData as $row) {
            $data[] = [
                $row->seller->name ?? 'N/A',
                $row->seller->email ?? 'N/A',
                $row->total_revenue,
                $row->seller_earnings,
                $row->total_commission,
                $row->order_count,
                round($row->avg_order_value, 0)
            ];
        }

        return $this->generateExport($data, "seller_performance_report_{$startDate->format('Y-m-d')}_{$endDate->format('Y-m-d')}", $format);
    }

    /**
     * Generate Export File
     */
    protected function generateExport($data, $filename, $format)
    {
        switch ($format) {
            case 'csv':
                return $this->generateCSV($data, $filename);
            case 'excel':
                return $this->generateExcel($data, $filename);
            case 'pdf':
                return $this->generatePDF($data, $filename);
            default:
                return response()->json(['error' => 'Invalid format'], 400);
        }
    }

    /**
     * Generate CSV Export
     */
    protected function generateCSV($data, $filename)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}.csv\"",
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');

            // Add BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            foreach ($data as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Generate Excel Export (Simple CSV with .xlsx extension)
     */
    protected function generateExcel($data, $filename)
    {
        // For simplicity, we'll generate CSV with .xlsx extension
        // In production, you might want to use a proper Excel library like PhpSpreadsheet
        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}.xlsx\"",
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');

            // Add BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            foreach ($data as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Generate PDF Export
     */
    protected function generatePDF($data, $filename)
    {
        // For simplicity, we'll return a formatted HTML that can be printed as PDF
        // In production, you might want to use a proper PDF library like DomPDF or TCPDF

        $html = '<html><head><meta charset="UTF-8"><title>' . $filename . '</title>';
        $html .= '<style>table{border-collapse:collapse;width:100%;}th,td{border:1px solid #ddd;padding:8px;text-align:left;}th{background-color:#f2f2f2;}</style>';
        $html .= '</head><body>';
        $html .= '<h1>MechaMap Financial Report</h1>';
        $html .= '<p>Generated on: ' . now()->format('d/m/Y H:i:s') . '</p>';
        $html .= '<table>';

        foreach ($data as $index => $row) {
            $html .= $index === 0 ? '<thead><tr>' : '<tr>';
            foreach ($row as $cell) {
                $html .= $index === 0 ? '<th>' . htmlspecialchars($cell) . '</th>' : '<td>' . htmlspecialchars($cell) . '</td>';
            }
            $html .= $index === 0 ? '</tr></thead><tbody>' : '</tr>';
        }

        $html .= '</tbody></table></body></html>';

        return response($html, 200, [
            'Content-Type' => 'text/html',
            'Content-Disposition' => "attachment; filename=\"{$filename}.html\"",
        ]);
    }

    /**
     * Get Revenue by Period
     */
    protected function getRevenueByPeriod($startDate, $endDate, $groupBy)
    {
        $dateFormat = match($groupBy) {
            'week' => '%Y-%u',
            'month' => '%Y-%m',
            default => '%Y-%m-%d'
        };

        return CentralizedPayment::select(
                DB::raw("DATE_FORMAT(created_at, '{$dateFormat}') as date"),
                DB::raw('SUM(net_received) as revenue'),
                DB::raw('COUNT(*) as payment_count'),
                DB::raw('AVG(net_received) as avg_payment')
            )
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    /**
     * Get Revenue by Payment Method
     */
    protected function getRevenueByPaymentMethod($startDate, $endDate)
    {
        return CentralizedPayment::select(
                'payment_method',
                DB::raw('SUM(net_received) as revenue'),
                DB::raw('COUNT(*) as payment_count'),
                DB::raw('AVG(net_received) as avg_payment')
            )
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('payment_method')
            ->get();
    }

    /**
     * Get Revenue by Seller Type
     */
    protected function getRevenueBySellerType($startDate, $endDate)
    {
        return MarketplaceOrderItem::select(
                'marketplace_sellers.seller_type',
                DB::raw('SUM(marketplace_order_items.total_amount) as revenue'),
                DB::raw('COUNT(*) as order_count')
            )
            ->join('marketplace_sellers', 'marketplace_order_items.seller_id', '=', 'marketplace_sellers.id')
            ->whereHas('order', function($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate])
                  ->where('payment_status', 'paid');
            })
            ->groupBy('marketplace_sellers.seller_type')
            ->get();
    }

    /**
     * Get Revenue Trends
     */
    protected function getRevenueTrends($startDate, $endDate)
    {
        $currentRevenue = CentralizedPayment::where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('net_received');

        $periodLength = $startDate->diffInDays($endDate);
        $previousStartDate = $startDate->copy()->subDays($periodLength);
        $previousEndDate = $startDate->copy()->subDay();

        $previousRevenue = CentralizedPayment::where('status', 'completed')
            ->whereBetween('created_at', [$previousStartDate, $previousEndDate])
            ->sum('net_received');

        $growthRate = $previousRevenue > 0 ? (($currentRevenue - $previousRevenue) / $previousRevenue) * 100 : null;

        return [
            'current_revenue' => $currentRevenue,
            'previous_revenue' => $previousRevenue,
            'growth_rate' => $growthRate,
            'average_growth' => $growthRate ? $growthRate / $periodLength : 0,
        ];
    }
}
