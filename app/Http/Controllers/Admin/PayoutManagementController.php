<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SellerPayoutRequest;
use App\Models\SellerPayoutItem;
use App\Models\MarketplaceSeller;
use App\Models\PaymentAuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * 💸 Admin Payout Management Controller
 * 
 * Quản lý yêu cầu thanh toán cho sellers
 * Review, approve, reject và process payouts
 */
class PayoutManagementController extends Controller
{
    /**
     * Display payout requests list
     */
    public function index(Request $request)
    {
        $query = SellerPayoutRequest::with(['seller', 'sellerAccount', 'processor'])
            ->latest();

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by seller
        if ($request->filled('seller_id')) {
            $query->where('seller_id', $request->seller_id);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $payoutRequests = $query->paginate(20);

        // Statistics
        $stats = [
            'pending_count' => SellerPayoutRequest::where('status', 'pending')->count(),
            'pending_amount' => SellerPayoutRequest::where('status', 'pending')->sum('net_payout'),
            'approved_count' => SellerPayoutRequest::where('status', 'approved')->count(),
            'approved_amount' => SellerPayoutRequest::where('status', 'approved')->sum('net_payout'),
            'completed_today' => SellerPayoutRequest::where('status', 'completed')
                ->whereDate('completed_at', today())->count(),
            'total_paid_this_month' => SellerPayoutRequest::where('status', 'completed')
                ->whereMonth('completed_at', now()->month)
                ->sum('net_payout'),
        ];

        // Get sellers for filter dropdown
        $sellers = User::whereHas('sellerAccount')->get(['id', 'name']);

        return view('admin.payout-management.index', compact(
            'payoutRequests',
            'stats',
            'sellers'
        ));
    }

    /**
     * Show payout request details
     */
    public function show(SellerPayoutRequest $payoutRequest)
    {
        $payoutRequest->load([
            'seller',
            'sellerAccount',
            'processor',
            'payoutItems.order',
            'payoutItems.product',
            'auditLogs.admin'
        ]);

        return view('admin.payout-management.show', compact('payoutRequest'));
    }

    /**
     * Approve payout request
     */
    public function approve(Request $request, SellerPayoutRequest $payoutRequest)
    {
        $request->validate([
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        try {
            if ($payoutRequest->status !== 'pending') {
                return back()->with('error', 'Chỉ có thể duyệt payout đang chờ xử lý.');
            }

            $payoutRequest->approve(Auth::id(), $request->admin_notes ?? '');

            return back()->with('success', 'Payout request đã được duyệt thành công.');

        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi khi duyệt payout: ' . $e->getMessage());
        }
    }

    /**
     * Reject payout request
     */
    public function reject(Request $request, SellerPayoutRequest $payoutRequest)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        try {
            if ($payoutRequest->status !== 'pending') {
                return back()->with('error', 'Chỉ có thể từ chối payout đang chờ xử lý.');
            }

            $payoutRequest->reject(Auth::id(), $request->rejection_reason);

            return back()->with('success', 'Payout request đã được từ chối.');

        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi khi từ chối payout: ' . $e->getMessage());
        }
    }

    /**
     * Mark payout as completed (payment sent)
     */
    public function markCompleted(Request $request, SellerPayoutRequest $payoutRequest)
    {
        $request->validate([
            'completion_notes' => 'nullable|string|max:1000',
            'transaction_reference' => 'nullable|string|max:255',
        ]);

        try {
            if ($payoutRequest->status !== 'approved') {
                return back()->with('error', 'Chỉ có thể hoàn thành payout đã được duyệt.');
            }

            $notes = $request->completion_notes ?? '';
            if ($request->filled('transaction_reference')) {
                $notes .= "\nTransaction Reference: " . $request->transaction_reference;
            }

            $payoutRequest->markAsCompleted(Auth::id(), $notes);

            return back()->with('success', 'Payout đã được đánh dấu hoàn thành.');

        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi khi hoàn thành payout: ' . $e->getMessage());
        }
    }

    /**
     * Bulk approve payouts
     */
    public function bulkApprove(Request $request)
    {
        $request->validate([
            'payout_ids' => 'required|array',
            'payout_ids.*' => 'exists:seller_payout_requests,id',
            'bulk_notes' => 'nullable|string|max:1000',
        ]);

        try {
            $approvedCount = 0;
            $errors = [];

            foreach ($request->payout_ids as $payoutId) {
                $payoutRequest = SellerPayoutRequest::find($payoutId);
                
                if ($payoutRequest && $payoutRequest->status === 'pending') {
                    try {
                        $payoutRequest->approve(Auth::id(), $request->bulk_notes ?? 'Bulk approval');
                        $approvedCount++;
                    } catch (\Exception $e) {
                        $errors[] = "Payout {$payoutRequest->payout_reference}: " . $e->getMessage();
                    }
                }
            }

            $message = "Đã duyệt {$approvedCount} payout requests.";
            if (!empty($errors)) {
                $message .= " Lỗi: " . implode(', ', $errors);
            }

            return back()->with('success', $message);

        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi bulk approve: ' . $e->getMessage());
        }
    }

    /**
     * Export payout data
     */
    public function export(Request $request)
    {
        $request->validate([
            'export_status' => 'nullable|in:pending,approved,completed,rejected',
            'export_date_from' => 'nullable|date',
            'export_date_to' => 'nullable|date|after_or_equal:export_date_from',
            'format' => 'required|in:csv,excel',
        ]);

        $query = SellerPayoutRequest::with(['seller', 'sellerAccount']);

        if ($request->filled('export_status')) {
            $query->where('status', $request->export_status);
        }

        if ($request->filled('export_date_from')) {
            $query->whereDate('created_at', '>=', $request->export_date_from);
        }

        if ($request->filled('export_date_to')) {
            $query->whereDate('created_at', '<=', $request->export_date_to);
        }

        $payouts = $query->get();

        if ($request->format === 'csv') {
            return $this->exportToCsv($payouts);
        } else {
            return $this->exportToExcel($payouts);
        }
    }

    /**
     * Export to CSV
     */
    protected function exportToCsv($payouts)
    {
        $filename = "payout_requests_" . date('Y-m-d_H-i-s') . ".csv";
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($payouts) {
            $file = fopen('php://output', 'w');
            
            // CSV Headers
            fputcsv($file, [
                'Payout Reference',
                'Seller Name',
                'Seller Email',
                'Total Sales',
                'Commission Amount',
                'Net Payout',
                'Order Count',
                'Period From',
                'Period To',
                'Status',
                'Bank Details',
                'Created At',
                'Approved At',
                'Completed At',
                'Admin Notes'
            ]);

            // CSV Data
            foreach ($payouts as $payout) {
                $bankDetails = is_array($payout->bank_details) 
                    ? json_encode($payout->bank_details) 
                    : $payout->bank_details;

                fputcsv($file, [
                    $payout->payout_reference,
                    $payout->seller->name ?? '',
                    $payout->seller->email ?? '',
                    $payout->total_sales,
                    $payout->commission_amount,
                    $payout->net_payout,
                    $payout->order_count,
                    $payout->period_from ? $payout->period_from->format('Y-m-d') : '',
                    $payout->period_to ? $payout->period_to->format('Y-m-d') : '',
                    $payout->status,
                    $bankDetails,
                    $payout->created_at->format('Y-m-d H:i:s'),
                    $payout->approved_at ? $payout->approved_at->format('Y-m-d H:i:s') : '',
                    $payout->completed_at ? $payout->completed_at->format('Y-m-d H:i:s') : '',
                    $payout->admin_notes ?? ''
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export to Excel (placeholder)
     */
    protected function exportToExcel($payouts)
    {
        // For now, fallback to CSV
        return $this->exportToCsv($payouts);
    }

    /**
     * Get payout analytics
     */
    public function analytics(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth());
        
        if (is_string($startDate)) {
            $startDate = Carbon::parse($startDate);
        }
        if (is_string($endDate)) {
            $endDate = Carbon::parse($endDate);
        }

        // Payout trends
        $payoutTrends = SellerPayoutRequest::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as request_count'),
                DB::raw('SUM(net_payout) as total_amount'),
                'status'
            )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date', 'status')
            ->orderBy('date')
            ->get();

        // Top sellers by payout amount
        $topSellers = SellerPayoutRequest::select(
                'seller_id',
                DB::raw('SUM(net_payout) as total_payout'),
                DB::raw('COUNT(*) as payout_count')
            )
            ->with('seller:id,name,email')
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('seller_id')
            ->orderByDesc('total_payout')
            ->take(10)
            ->get();

        // Processing time analytics
        $processingTimes = SellerPayoutRequest::whereNotNull('approved_at')
            ->whereNotNull('completed_at')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get()
            ->map(function ($payout) {
                return [
                    'payout_reference' => $payout->payout_reference,
                    'approval_time' => $payout->created_at->diffInHours($payout->approved_at),
                    'completion_time' => $payout->approved_at->diffInHours($payout->completed_at),
                    'total_time' => $payout->created_at->diffInHours($payout->completed_at),
                ];
            });

        return view('admin.payout-management.analytics', compact(
            'payoutTrends',
            'topSellers',
            'processingTimes',
            'startDate',
            'endDate'
        ));
    }
}
