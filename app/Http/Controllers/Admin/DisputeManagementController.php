<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentDispute;
use App\Models\PaymentRefund;
use App\Models\CentralizedPayment;
use App\Models\PaymentAuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * 🚨 Admin Dispute Management Controller
 * 
 * Quản lý disputes, chargebacks và customer complaints
 * Resolution workflow và evidence management
 */
class DisputeManagementController extends Controller
{
    /**
     * Display disputes list
     */
    public function index(Request $request)
    {
        $query = PaymentDispute::with(['customer', 'order', 'centralizedPayment', 'assignedTo'])
            ->latest();

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Filter by dispute type
        if ($request->filled('dispute_type')) {
            $query->where('dispute_type', $request->dispute_type);
        }

        // Filter by assigned admin
        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        // Search by reference or customer
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('dispute_reference', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $disputes = $query->paginate(20);

        // Get statistics
        $statistics = PaymentDispute::getStatistics();

        // Get filter options
        $admins = \App\Models\User::whereHas('roles', function($q) {
            $q->whereIn('name', ['admin', 'moderator']);
        })->get(['id', 'name']);

        return view('admin.dispute-management.index', compact(
            'disputes',
            'statistics',
            'admins'
        ));
    }

    /**
     * Show dispute details
     */
    public function show(PaymentDispute $dispute)
    {
        $dispute->load([
            'customer',
            'order.orderItems.product',
            'centralizedPayment',
            'assignedTo',
            'refunds'
        ]);

        // Get audit logs for this dispute
        $auditLogs = PaymentAuditLog::forEntity('payment_dispute', $dispute->id)
            ->with(['user', 'admin'])
            ->latest()
            ->take(20)
            ->get();

        return view('admin.dispute-management.show', compact(
            'dispute',
            'auditLogs'
        ));
    }

    /**
     * Assign dispute to admin
     */
    public function assign(Request $request, PaymentDispute $dispute)
    {
        $request->validate([
            'admin_id' => 'required|exists:users,id',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $admin = \App\Models\User::findOrFail($request->admin_id);
            
            if (!$dispute->markAsInvestigating($admin)) {
                return back()->with('error', 'Không thể assign dispute này.');
            }

            if ($request->filled('notes')) {
                $dispute->addInternalNote($request->notes, Auth::user());
            }

            // Log the assignment
            PaymentAuditLog::logPaymentEvent(
                'dispute_assigned',
                'payment_dispute',
                $dispute->id,
                [
                    'admin_id' => Auth::id(),
                    'assigned_to' => $admin->id,
                    'assigned_to_name' => $admin->name,
                    'notes' => $request->notes,
                    'description' => "Dispute assigned to {$admin->name}",
                ]
            );

            DB::commit();

            return back()->with('success', "Dispute đã được assign cho {$admin->name}.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi khi assign dispute: ' . $e->getMessage());
        }
    }

    /**
     * Update dispute status
     */
    public function updateStatus(Request $request, PaymentDispute $dispute)
    {
        $request->validate([
            'status' => 'required|in:pending,investigating,evidence_required,escalated,resolved,lost,withdrawn,expired',
            'notes' => 'nullable|string|max:1000',
            'resolution_type' => 'required_if:status,resolved|in:full_refund,partial_refund,no_refund,replacement,store_credit,other',
            'resolution_summary' => 'required_if:status,resolved|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $oldStatus = $dispute->status;

            if ($request->status === 'resolved') {
                $dispute->markAsResolved(
                    $request->resolution_type,
                    $request->resolution_summary,
                    Auth::user()
                );
            } else {
                $dispute->update([
                    'status' => $request->status,
                ]);

                if ($request->filled('notes')) {
                    $dispute->addInternalNote($request->notes, Auth::user());
                }
            }

            // Log the status change
            PaymentAuditLog::logPaymentEvent(
                'dispute_status_updated',
                'payment_dispute',
                $dispute->id,
                [
                    'admin_id' => Auth::id(),
                    'old_status' => $oldStatus,
                    'new_status' => $request->status,
                    'resolution_type' => $request->resolution_type,
                    'resolution_summary' => $request->resolution_summary,
                    'notes' => $request->notes,
                    'description' => "Dispute status changed from {$oldStatus} to {$request->status}",
                ]
            );

            DB::commit();

            return back()->with('success', 'Trạng thái dispute đã được cập nhật.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi khi cập nhật trạng thái: ' . $e->getMessage());
        }
    }

    /**
     * Add evidence to dispute
     */
    public function addEvidence(Request $request, PaymentDispute $dispute)
    {
        $request->validate([
            'evidence_type' => 'required|in:merchant_response,additional_evidence',
            'evidence_text' => 'required|string|max:2000',
            'evidence_files' => 'nullable|array',
            'evidence_files.*' => 'file|max:10240', // 10MB max per file
        ]);

        try {
            DB::beginTransaction();

            $evidence = [
                'type' => $request->evidence_type,
                'text' => $request->evidence_text,
                'files' => [],
                'added_by' => Auth::user()->name,
                'added_at' => now()->toISOString(),
            ];

            // Handle file uploads
            if ($request->hasFile('evidence_files')) {
                foreach ($request->file('evidence_files') as $file) {
                    $path = $file->store('dispute-evidence', 'public');
                    $evidence['files'][] = [
                        'name' => $file->getClientOriginalName(),
                        'path' => $path,
                        'size' => $file->getSize(),
                        'type' => $file->getMimeType(),
                    ];
                }
            }

            // Add to merchant evidence
            $merchantEvidence = $dispute->merchant_evidence ?? [];
            $merchantEvidence[] = $evidence;

            $dispute->update([
                'merchant_evidence' => $merchantEvidence,
                'merchant_response' => $request->evidence_text,
            ]);

            $dispute->addInternalNote("Evidence added: {$request->evidence_text}", Auth::user());

            // Log the evidence addition
            PaymentAuditLog::logPaymentEvent(
                'dispute_evidence_added',
                'payment_dispute',
                $dispute->id,
                [
                    'admin_id' => Auth::id(),
                    'evidence_type' => $request->evidence_type,
                    'evidence_text' => $request->evidence_text,
                    'file_count' => count($evidence['files']),
                    'description' => 'Evidence added to dispute',
                ]
            );

            DB::commit();

            return back()->with('success', 'Evidence đã được thêm vào dispute.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi khi thêm evidence: ' . $e->getMessage());
        }
    }

    /**
     * Create refund from dispute
     */
    public function createRefund(Request $request, PaymentDispute $dispute)
    {
        $request->validate([
            'refund_type' => 'required|in:full,partial,shipping,tax,item,goodwill',
            'refund_amount' => 'required|numeric|min:0|max:' . $dispute->disputed_amount,
            'reason' => 'required|in:dispute_resolution,goodwill,admin_error,other',
            'admin_reason' => 'required|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            // Calculate gateway fee (if applicable)
            $gatewayFee = 0;
            if ($dispute->centralizedPayment->payment_method === 'stripe') {
                // Stripe typically doesn't charge for refunds, but let's be safe
                $gatewayFee = 0;
            }

            $refund = PaymentRefund::create([
                'centralized_payment_id' => $dispute->centralized_payment_id,
                'order_id' => $dispute->order_id,
                'customer_id' => $dispute->customer_id,
                'dispute_id' => $dispute->id,
                'refund_type' => $request->refund_type,
                'reason' => $request->reason,
                'status' => 'approved', // Auto-approve dispute refunds
                'original_amount' => $dispute->disputed_amount,
                'refund_amount' => $request->refund_amount,
                'gateway_fee' => $gatewayFee,
                'net_refund' => $request->refund_amount - $gatewayFee,
                'currency' => $dispute->currency,
                'payment_method' => $dispute->centralizedPayment->payment_method,
                'admin_reason' => $request->admin_reason,
                'requested_by' => Auth::id(),
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);

            // Update dispute
            $dispute->update([
                'refund_amount' => $request->refund_amount,
            ]);

            $dispute->addInternalNote("Refund created: {$request->refund_amount} VNĐ", Auth::user());

            // Log the refund creation
            PaymentAuditLog::logPaymentEvent(
                'dispute_refund_created',
                'payment_dispute',
                $dispute->id,
                [
                    'admin_id' => Auth::id(),
                    'refund_id' => $refund->id,
                    'refund_amount' => $request->refund_amount,
                    'refund_type' => $request->refund_type,
                    'reason' => $request->reason,
                    'description' => "Refund created from dispute: {$request->refund_amount} VNĐ",
                ]
            );

            DB::commit();

            return redirect()->route('admin.refund-management.show', $refund)
                ->with('success', 'Refund đã được tạo từ dispute.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi khi tạo refund: ' . $e->getMessage());
        }
    }

    /**
     * Bulk update disputes
     */
    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'dispute_ids' => 'required|array',
            'dispute_ids.*' => 'exists:payment_disputes,id',
            'bulk_action' => 'required|in:assign,update_status,update_priority',
            'admin_id' => 'required_if:bulk_action,assign|exists:users,id',
            'status' => 'required_if:bulk_action,update_status|in:pending,investigating,evidence_required,escalated,resolved,lost,withdrawn,expired',
            'priority' => 'required_if:bulk_action,update_priority|in:low,medium,high,urgent',
        ]);

        try {
            DB::beginTransaction();

            $disputes = PaymentDispute::whereIn('id', $request->dispute_ids)->get();
            $updatedCount = 0;

            foreach ($disputes as $dispute) {
                switch ($request->bulk_action) {
                    case 'assign':
                        $admin = \App\Models\User::findOrFail($request->admin_id);
                        if ($dispute->markAsInvestigating($admin)) {
                            $updatedCount++;
                        }
                        break;
                    case 'update_status':
                        $dispute->update(['status' => $request->status]);
                        $updatedCount++;
                        break;
                    case 'update_priority':
                        $dispute->update(['priority' => $request->priority]);
                        $updatedCount++;
                        break;
                }
            }

            // Log bulk action
            PaymentAuditLog::logPaymentEvent(
                'disputes_bulk_update',
                'payment_dispute',
                0,
                [
                    'admin_id' => Auth::id(),
                    'metadata' => [
                        'action' => $request->bulk_action,
                        'dispute_ids' => $request->dispute_ids,
                        'updated_count' => $updatedCount,
                        'admin_id' => $request->admin_id,
                        'status' => $request->status,
                        'priority' => $request->priority,
                    ],
                    'description' => "Bulk {$request->bulk_action} applied to {$updatedCount} disputes",
                ]
            );

            DB::commit();

            return back()->with('success', "Đã cập nhật {$updatedCount} disputes.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi bulk update: ' . $e->getMessage());
        }
    }

    /**
     * Export disputes report
     */
    public function export(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'format' => 'required|in:csv,excel',
        ]);

        $disputes = PaymentDispute::with(['customer', 'order', 'assignedTo'])
            ->whereBetween('dispute_date', [$request->start_date, $request->end_date])
            ->get();

        $data = [
            ['Dispute Reference', 'Customer', 'Order', 'Type', 'Status', 'Priority', 'Amount', 'Date', 'Assigned To']
        ];

        foreach ($disputes as $dispute) {
            $data[] = [
                $dispute->dispute_reference,
                $dispute->customer->name ?? 'N/A',
                $dispute->order->order_number ?? 'N/A',
                $dispute->dispute_type_display,
                $dispute->status,
                $dispute->priority,
                number_format($dispute->disputed_amount, 0, ',', '.') . ' VNĐ',
                $dispute->dispute_date->format('d/m/Y'),
                $dispute->assignedTo->name ?? 'Unassigned',
            ];
        }

        return $this->generateExport($data, "disputes_report_{$request->start_date}_{$request->end_date}", $request->format);
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
