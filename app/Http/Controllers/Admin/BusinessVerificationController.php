<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessVerificationApplication;
use App\Models\BusinessVerificationDocument;
use App\Models\User;
use App\Services\BusinessVerificationService;
use App\Services\DocumentSecurityService;
use App\Services\VerificationNotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

/**
 * Admin Business Verification Controller
 * 
 * Handles admin interface for reviewing and managing
 * business verification applications
 */
class BusinessVerificationController extends Controller
{
    protected BusinessVerificationService $verificationService;
    protected DocumentSecurityService $documentSecurity;
    protected VerificationNotificationService $notificationService;

    public function __construct(
        BusinessVerificationService $verificationService,
        DocumentSecurityService $documentSecurity,
        VerificationNotificationService $notificationService
    ) {
        $this->verificationService = $verificationService;
        $this->documentSecurity = $documentSecurity;
        $this->notificationService = $notificationService;
        
        // Ensure only admins can access
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Display verification dashboard
     */
    public function index(Request $request): View
    {
        // Get filter parameters
        $status = $request->input('status');
        $type = $request->input('type');
        $priority = $request->input('priority');
        $search = $request->input('search');
        $sortBy = $request->input('sort_by', 'submitted_at');
        $sortOrder = $request->input('sort_order', 'desc');

        // Build query
        $query = BusinessVerificationApplication::with(['user', 'documents', 'reviewer'])
            ->when($status, fn($q) => $q->where('status', $status))
            ->when($type, fn($q) => $q->where('application_type', $type))
            ->when($priority, fn($q) => $q->where('priority_level', $priority))
            ->when($search, function($q) use ($search) {
                return $q->where(function($query) use ($search) {
                    $query->where('business_name', 'like', "%{$search}%")
                          ->orWhere('tax_id', 'like', "%{$search}%")
                          ->orWhereHas('user', function($userQuery) use ($search) {
                              $userQuery->where('name', 'like', "%{$search}%")
                                       ->orWhere('email', 'like', "%{$search}%");
                          });
                });
            });

        // Apply sorting
        if (in_array($sortBy, ['submitted_at', 'business_name', 'priority_level', 'status'])) {
            $query->orderBy($sortBy, $sortOrder === 'asc' ? 'asc' : 'desc');
        }

        // Paginate results
        $applications = $query->paginate(20)->withQueryString();

        // Get statistics
        $stats = $this->verificationService->getVerificationStats();

        // Get reviewers for assignment
        $reviewers = User::whereIn('role', ['admin', 'content_admin', 'content_moderator'])
                        ->orderBy('name')
                        ->get();

        return view('admin.verification.index', compact(
            'applications', 
            'stats', 
            'reviewers',
            'status', 
            'type', 
            'priority', 
            'search', 
            'sortBy', 
            'sortOrder'
        ));
    }

    /**
     * Show application details
     */
    public function show(BusinessVerificationApplication $application): View
    {
        $application->load([
            'user', 
            'documents', 
            'reviewer', 
            'approver', 
            'rejector',
            'auditTrail.performedBy'
        ]);

        // Get reviewers for assignment
        $reviewers = User::whereIn('role', ['admin', 'content_admin', 'content_moderator'])
                        ->orderBy('name')
                        ->get();

        return view('admin.verification.show', compact('application', 'reviewers'));
    }

    /**
     * Assign reviewer to application
     */
    public function assignReviewer(Request $request, BusinessVerificationApplication $application): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'reviewer_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid reviewer selection',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $reviewer = User::findOrFail($request->reviewer_id);
            
            $this->verificationService->assignReviewer($application, $reviewer);

            return response()->json([
                'success' => true,
                'message' => 'Reviewer assigned successfully',
                'reviewer' => [
                    'id' => $reviewer->id,
                    'name' => $reviewer->name,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Approve application
     */
    public function approve(Request $request, BusinessVerificationApplication $application): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $this->verificationService->approveApplication(
                $application, 
                auth()->user(), 
                $request->input('notes')
            );

            return response()->json([
                'success' => true,
                'message' => 'Application approved successfully',
                'status' => BusinessVerificationApplication::STATUS_APPROVED
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Reject application
     */
    public function reject(Request $request, BusinessVerificationApplication $application): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'reason' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Rejection reason is required',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $this->verificationService->rejectApplication(
                $application, 
                auth()->user(), 
                $request->input('reason')
            );

            return response()->json([
                'success' => true,
                'message' => 'Application rejected successfully',
                'status' => BusinessVerificationApplication::STATUS_REJECTED
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Request additional information
     */
    public function requestInfo(Request $request, BusinessVerificationApplication $application): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'info_requested' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Information request is required',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $this->verificationService->requestAdditionalInfo(
                $application, 
                auth()->user(), 
                $request->input('info_requested')
            );

            return response()->json([
                'success' => true,
                'message' => 'Additional information requested successfully',
                'status' => BusinessVerificationApplication::STATUS_REQUIRES_ADDITIONAL_INFO
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Verify document
     */
    public function verifyDocument(Request $request, BusinessVerificationDocument $document): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:verified,rejected,requires_resubmission',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $document->verify(
                auth()->user(),
                $request->input('status'),
                $request->input('notes')
            );

            return response()->json([
                'success' => true,
                'message' => 'Document verification updated successfully',
                'document' => [
                    'id' => $document->id,
                    'verification_status' => $document->verification_status,
                    'verification_notes' => $document->verification_notes,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Download document
     */
    public function downloadDocument(BusinessVerificationDocument $document): Response
    {
        try {
            // Log access
            $this->documentSecurity->logAccess($document, auth()->user());

            // Check if file exists
            if (!$document->canBeDownloaded()) {
                abort(404, 'Document not found');
            }

            // Get file content
            $fileContent = Storage::get($document->file_path);
            
            return response($fileContent)
                ->header('Content-Type', $document->mime_type)
                ->header('Content-Disposition', 'attachment; filename="' . $document->original_filename . '"')
                ->header('Content-Length', $document->file_size);

        } catch (\Exception $e) {
            abort(500, 'Error downloading document');
        }
    }

    /**
     * Preview document
     */
    public function previewDocument(BusinessVerificationDocument $document): Response
    {
        try {
            // Log access
            $this->documentSecurity->logAccess($document, auth()->user());

            // Check if file can be previewed
            if (!$document->canBePreewed()) {
                abort(400, 'Document cannot be previewed');
            }

            // Get file content
            $fileContent = Storage::get($document->file_path);
            
            return response($fileContent)
                ->header('Content-Type', $document->mime_type)
                ->header('Content-Disposition', 'inline; filename="' . $document->original_filename . '"');

        } catch (\Exception $e) {
            abort(500, 'Error previewing document');
        }
    }

    /**
     * Bulk actions
     */
    public function bulkAction(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'action' => 'required|in:approve,reject,assign_reviewer,set_priority',
            'application_ids' => 'required|array|min:1',
            'application_ids.*' => 'exists:business_verification_applications,id',
            'reviewer_id' => 'required_if:action,assign_reviewer|exists:users,id',
            'priority' => 'required_if:action,set_priority|in:low,medium,high,urgent',
            'reason' => 'required_if:action,reject|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $applications = BusinessVerificationApplication::whereIn('id', $request->application_ids)->get();
            $successCount = 0;
            $errors = [];

            foreach ($applications as $application) {
                try {
                    switch ($request->action) {
                        case 'approve':
                            if ($application->canBeApproved()) {
                                $this->verificationService->approveApplication($application, auth()->user());
                                $successCount++;
                            }
                            break;

                        case 'reject':
                            if ($application->canBeRejected()) {
                                $this->verificationService->rejectApplication($application, auth()->user(), $request->reason);
                                $successCount++;
                            }
                            break;

                        case 'assign_reviewer':
                            if ($application->canBeReviewed()) {
                                $reviewer = User::find($request->reviewer_id);
                                $this->verificationService->assignReviewer($application, $reviewer);
                                $successCount++;
                            }
                            break;

                        case 'set_priority':
                            $application->update(['priority_level' => $request->priority]);
                            $successCount++;
                            break;
                    }
                } catch (\Exception $e) {
                    $errors[] = "Application {$application->id}: {$e->getMessage()}";
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Bulk action completed. {$successCount} applications processed successfully.",
                'success_count' => $successCount,
                'total_count' => count($applications),
                'errors' => $errors
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get verification analytics
     */
    public function analytics(): JsonResponse
    {
        try {
            $stats = $this->verificationService->getVerificationStats();
            
            // Additional analytics data
            $monthlyStats = BusinessVerificationApplication::selectRaw('
                YEAR(submitted_at) as year,
                MONTH(submitted_at) as month,
                COUNT(*) as total,
                SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved,
                SUM(CASE WHEN status = "rejected" THEN 1 ELSE 0 END) as rejected
            ')
            ->whereNotNull('submitted_at')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get();

            return response()->json([
                'success' => true,
                'stats' => $stats,
                'monthly_stats' => $monthlyStats,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
