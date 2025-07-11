<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentRefund;
use App\Models\PaymentDispute;
use App\Models\CentralizedPayment;
use App\Models\MarketplaceOrder;
use App\Models\PaymentAuditLog;
use App\Services\CentralizedPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * 💰 Admin Refund Management Controller
 * 
 * Quản lý refund processing, approval workflow
 * Gateway integration và seller adjustments
 */
class RefundManagementController extends Controller
{
    protected $paymentService;

    public function __construct(CentralizedPaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Display refunds list
     */
    public function index(Request $request)
    {
        $query = PaymentRefund::with(['customer', 'order', 'centralizedPayment', 'dispute', 'requestedBy', 'approvedBy'])
            ->latest();

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by refund type
        if ($request->filled('refund_type')) {
            $query->where('refund_type', $request->refund_type);
        }

        // Filter by payment method
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('requested_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('requested_at', '<=', $request->end_date);
        }

        // Search by reference or customer
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('refund_reference', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('order', function($oq) use ($search) {
                      $oq->where('order_number', 'like', "%{$search}%");
                  });
            });
        }

        $refunds = $query->paginate(20);

        // Get statistics
        $statistics = PaymentRefund::getStatistics();

        return view('admin.refund-management.index', compact(
            'refunds',
            'statistics'
        ));
    }

    /**
     * Show refund details
     */
    public function show(PaymentRefund $refund)
    {
        $refund->load([
            'customer',
            'order.orderItems.product',
            'centralizedPayment',
            'dispute',
            'requestedBy',
            'approvedBy',
            'processedBy'
        ]);

        // Get audit logs for this refund
        $auditLogs = PaymentAuditLog::forEntity('payment_refund', $refund->id)
            ->with(['user', 'admin'])
            ->latest()
            ->take(20)
            ->get();

        return view('admin.refund-management.show', compact(
            'refund',
            'auditLogs'
        ));
    }

    /**
     * Show create refund form
     */
    public function create(Request $request)
    {
        $orderId = $request->get('order_id');
        $paymentId = $request->get('payment_id');

        $order = null;
        $payment = null;

        if ($orderId) {
            $order = MarketplaceOrder::with(['orderItems.product', 'centralizedPayment'])
                ->findOrFail($orderId);
            $payment = $order->centralizedPayment;
        } elseif ($paymentId) {
            $payment = CentralizedPayment::with(['order.orderItems.product'])
                ->findOrFail($paymentId);
            $order = $payment->order;
        }

        if (!$order || !$payment) {
            return redirect()->route('admin.refund-management.index')
                ->with('error', 'Không tìm thấy order hoặc payment.');
        }

        // Check if payment can be refunded
        if ($payment->status !== 'completed') {
            return redirect()->route('admin.refund-management.index')
                ->with('error', 'Chỉ có thể refund payment đã completed.');
        }

        // Calculate available refund amount
        $totalRefunded = PaymentRefund::where('centralized_payment_id', $payment->id)
            ->where('status', 'completed')
            ->sum('refund_amount');

        $availableAmount = $payment->gross_amount - $totalRefunded;

        return view('admin.refund-management.create', compact(
            'order',
            'payment',
            'availableAmount'
        ));
    }

    /**
     * Store new refund
     */
    public function store(Request $request)
    {
        $request->validate(PaymentRefund::validationRules() + [
            'admin_reason' => 'required|string|max:1000',
            'customer_reason' => 'nullable|string|max:1000',
            'refund_items' => 'nullable|array',
        ]);

        try {
            DB::beginTransaction();

            $payment = CentralizedPayment::findOrFail($request->centralized_payment_id);

            // Check available refund amount
            $totalRefunded = PaymentRefund::where('centralized_payment_id', $payment->id)
                ->where('status', 'completed')
                ->sum('refund_amount');

            $availableAmount = $payment->gross_amount - $totalRefunded;

            if ($request->refund_amount > $availableAmount) {
                return back()->withInput()
                    ->with('error', 'Số tiền refund vượt quá số tiền có thể refund.');
            }

            // Calculate gateway fee
            $gatewayFee = 0;
            if ($payment->payment_method === 'stripe') {
                // Stripe typically doesn't charge for refunds
                $gatewayFee = 0;
            }

            $refund = PaymentRefund::create([
                'centralized_payment_id' => $request->centralized_payment_id,
                'order_id' => $request->order_id,
                'customer_id' => $request->customer_id,
                'refund_type' => $request->refund_type,
                'reason' => $request->reason,
                'status' => 'pending',
                'original_amount' => $payment->gross_amount,
                'refund_amount' => $request->refund_amount,
                'gateway_fee' => $gatewayFee,
                'net_refund' => $request->refund_amount - $gatewayFee,
                'currency' => $payment->currency ?? 'VND',
                'payment_method' => $payment->payment_method,
                'admin_reason' => $request->admin_reason,
                'customer_reason' => $request->customer_reason,
                'refund_items' => $request->refund_items,
                'requested_by' => Auth::id(),
            ]);

            // Log the refund creation
            PaymentAuditLog::logPaymentEvent(
                'refund_created',
                'payment_refund',
                $refund->id,
                [
                    'admin_id' => Auth::id(),
                    'refund_amount' => $request->refund_amount,
                    'refund_type' => $request->refund_type,
                    'reason' => $request->reason,
                    'payment_id' => $payment->id,
                    'order_id' => $request->order_id,
                    'description' => "Refund created: {$request->refund_amount} VNĐ",
                ]
            );

            DB::commit();

            return redirect()->route('admin.refund-management.show', $refund)
                ->with('success', 'Refund đã được tạo thành công.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Lỗi khi tạo refund: ' . $e->getMessage());
        }
    }

    /**
     * Approve refund
     */
    public function approve(Request $request, PaymentRefund $refund)
    {
        $request->validate([
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            if (!$refund->approve(Auth::user(), $request->admin_notes)) {
                return back()->with('error', 'Không thể approve refund này.');
            }

            // Log the approval
            PaymentAuditLog::logPaymentEvent(
                'refund_approved',
                'payment_refund',
                $refund->id,
                [
                    'admin_id' => Auth::id(),
                    'admin_notes' => $request->admin_notes,
                    'description' => 'Refund approved',
                ]
            );

            DB::commit();

            return back()->with('success', 'Refund đã được approve.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi khi approve refund: ' . $e->getMessage());
        }
    }

    /**
     * Reject refund
     */
    public function reject(Request $request, PaymentRefund $refund)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            if (!$refund->reject(Auth::user(), $request->rejection_reason)) {
                return back()->with('error', 'Không thể reject refund này.');
            }

            // Log the rejection
            PaymentAuditLog::logPaymentEvent(
                'refund_rejected',
                'payment_refund',
                $refund->id,
                [
                    'admin_id' => Auth::id(),
                    'rejection_reason' => $request->rejection_reason,
                    'description' => 'Refund rejected',
                ]
            );

            DB::commit();

            return back()->with('success', 'Refund đã được reject.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi khi reject refund: ' . $e->getMessage());
        }
    }

    /**
     * Process refund through gateway
     */
    public function process(PaymentRefund $refund)
    {
        try {
            DB::beginTransaction();

            if (!$refund->canBeProcessed()) {
                return back()->with('error', 'Refund không thể được process.');
            }

            // Mark as processing
            $refund->markAsProcessing(Auth::user());

            // Process through payment service
            $result = $this->paymentService->processRefund($refund);

            if ($result['success']) {
                $refund->markAsCompleted(
                    $result['gateway_refund_id'] ?? null,
                    $result['gateway_response'] ?? null
                );

                // Log successful processing
                PaymentAuditLog::logPaymentEvent(
                    'refund_processed_success',
                    'payment_refund',
                    $refund->id,
                    [
                        'admin_id' => Auth::id(),
                        'gateway_refund_id' => $result['gateway_refund_id'] ?? null,
                        'description' => 'Refund processed successfully',
                    ]
                );

                DB::commit();
                return back()->with('success', 'Refund đã được process thành công.');

            } else {
                $refund->markAsFailed(
                    $result['error'] ?? 'Unknown error',
                    $result['gateway_response'] ?? null
                );

                // Log failed processing
                PaymentAuditLog::logPaymentEvent(
                    'refund_processed_failed',
                    'payment_refund',
                    $refund->id,
                    [
                        'admin_id' => Auth::id(),
                        'error' => $result['error'] ?? 'Unknown error',
                        'description' => 'Refund processing failed',
                    ]
                );

                DB::commit();
                return back()->with('error', 'Lỗi khi process refund: ' . ($result['error'] ?? 'Unknown error'));
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi khi process refund: ' . $e->getMessage());
        }
    }

    /**
     * Bulk approve refunds
     */
    public function bulkApprove(Request $request)
    {
        $request->validate([
            'refund_ids' => 'required|array',
            'refund_ids.*' => 'exists:payment_refunds,id',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $refunds = PaymentRefund::whereIn('id', $request->refund_ids)
                ->where('status', 'pending')
                ->get();

            $approvedCount = 0;

            foreach ($refunds as $refund) {
                if ($refund->approve(Auth::user(), $request->admin_notes)) {
                    $approvedCount++;
                }
            }

            // Log bulk approval
            PaymentAuditLog::logPaymentEvent(
                'refunds_bulk_approved',
                'payment_refund',
                0,
                [
                    'admin_id' => Auth::id(),
                    'metadata' => [
                        'refund_ids' => $request->refund_ids,
                        'approved_count' => $approvedCount,
                        'admin_notes' => $request->admin_notes,
                    ],
                    'description' => "Bulk approved {$approvedCount} refunds",
                ]
            );

            DB::commit();

            return back()->with('success', "Đã approve {$approvedCount} refunds.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi bulk approve: ' . $e->getMessage());
        }
    }

    /**
     * Export refunds report
     */
    public function export(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'format' => 'required|in:csv,excel',
        ]);

        $refunds = PaymentRefund::with(['customer', 'order', 'requestedBy', 'approvedBy'])
            ->whereBetween('requested_at', [$request->start_date, $request->end_date])
            ->get();

        $data = [
            ['Refund Reference', 'Customer', 'Order', 'Type', 'Status', 'Amount', 'Reason', 'Requested Date', 'Requested By', 'Approved By']
        ];

        foreach ($refunds as $refund) {
            $data[] = [
                $refund->refund_reference,
                $refund->customer->name ?? 'N/A',
                $refund->order->order_number ?? 'N/A',
                $refund->refund_type_display,
                $refund->status,
                number_format($refund->refund_amount, 0, ',', '.') . ' VNĐ',
                $refund->reason_display,
                $refund->requested_at->format('d/m/Y H:i'),
                $refund->requestedBy->name ?? 'System',
                $refund->approvedBy->name ?? 'N/A',
            ];
        }

        return $this->generateExport($data, "refunds_report_{$request->start_date}_{$request->end_date}", $request->format);
    }

    /**
     * Generate export file
     */
    protected function generateExport($data, $filename, $format)
    {
        if ($format === 'csv') {
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"{$filename}.csv\"",
            ];

            $callback = function() use ($data) {
                $file = fopen('php://output', 'w');
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM
                foreach ($data as $row) {
                    fputcsv($file, $row);
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        // Excel format (simplified)
        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}.xlsx\"",
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM
            foreach ($data as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
