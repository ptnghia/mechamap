<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\BusinessVerificationApplication;
use App\Services\BusinessVerificationService;
use App\Services\DocumentSecurityService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

/**
 * Frontend Business Registration Controller
 * 
 * Handles business user registration with verification workflow
 * Multi-step wizard for collecting business information and documents
 */
class BusinessRegistrationController extends Controller
{
    protected BusinessVerificationService $verificationService;
    protected DocumentSecurityService $documentSecurity;

    public function __construct(
        BusinessVerificationService $verificationService,
        DocumentSecurityService $documentSecurity
    ) {
        $this->verificationService = $verificationService;
        $this->documentSecurity = $documentSecurity;
    }

    /**
     * Show business registration wizard
     */
    public function showRegistrationWizard(): View
    {
        // If user is already logged in and has an application, redirect to status
        if (Auth::check()) {
            $application = BusinessVerificationApplication::where('user_id', Auth::id())
                ->whereNotIn('status', ['approved', 'rejected'])
                ->first();
                
            if ($application) {
                return redirect()->route('business.verification.status');
            }
        }

        return view('frontend.business.registration-wizard');
    }

    /**
     * Process step 1: Account creation
     */
    public function processStep1(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'terms_accepted' => 'required|accepted',
            'privacy_accepted' => 'required|accepted',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Create user account
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'role' => 'member', // Start as member, will be upgraded after verification
                'email_verified_at' => now(), // Auto-verify for business users
            ]);

            // Log in the user
            Auth::login($user);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Account created successfully',
                'user_id' => $user->id,
                'next_step' => 2
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create account: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process step 2: Business information
     */
    public function processStep2(Request $request): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'application_type' => 'required|in:manufacturer,supplier,brand,verified_partner',
            'business_name' => 'required|string|max:255',
            'business_type' => 'required|string|max:100',
            'tax_id' => 'required|string|max:50|unique:business_verification_applications,tax_id',
            'registration_number' => 'nullable|string|max:100',
            'business_address' => 'required|string',
            'business_phone' => 'nullable|string|max:20',
            'business_email' => 'nullable|email|max:255',
            'business_website' => 'nullable|url|max:255',
            'business_description' => 'nullable|string|max:1000',
            'years_in_business' => 'nullable|integer|min:0|max:100',
            'employee_count' => 'nullable|integer|min:1',
            'annual_revenue' => 'nullable|numeric|min:0',
            'business_categories' => 'nullable|array',
            'service_areas' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Store business information in session for now
            session(['business_info' => $request->all()]);

            return response()->json([
                'success' => true,
                'message' => 'Business information saved',
                'next_step' => 3
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save business information: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process step 3: Document upload
     */
    public function uploadDocument(Request $request): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'document_type' => 'required|in:business_license,tax_certificate,registration_certificate,identity_document,bank_statement,utility_bill,other',
            'document' => 'required|file|max:10240', // 10MB max
            'document_name' => 'nullable|string|max:255',
            'document_description' => 'nullable|string|max:500',
            'document_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after:today',
            'issuing_authority' => 'nullable|string|max:255',
            'document_number' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Get or create temporary application for document storage
            $tempApplicationId = session('temp_application_id');
            
            if (!$tempApplicationId) {
                // Create temporary application record
                $businessInfo = session('business_info', []);
                
                if (empty($businessInfo)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Business information not found. Please complete step 2 first.'
                    ], 400);
                }

                $application = $this->verificationService->submitApplication(Auth::user(), $businessInfo);
                session(['temp_application_id' => $application->id]);
                $tempApplicationId = $application->id;
            }

            $application = BusinessVerificationApplication::findOrFail($tempApplicationId);

            // Upload document
            $document = $this->verificationService->uploadDocument(
                $application,
                $request->file('document'),
                $request->document_type,
                [
                    'document_name' => $request->document_name ?? $request->file('document')->getClientOriginalName(),
                    'description' => $request->document_description,
                    'document_date' => $request->document_date,
                    'expiry_date' => $request->expiry_date,
                    'issuing_authority' => $request->issuing_authority,
                    'document_number' => $request->document_number,
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Document uploaded successfully',
                'document' => [
                    'id' => $document->id,
                    'name' => $document->document_name,
                    'type' => $document->document_type_display,
                    'size' => $document->file_size_human,
                    'status' => $document->verification_status,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload document: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove uploaded document
     */
    public function removeDocument(Request $request): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'document_id' => 'required|exists:business_verification_documents,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid document ID'
            ], 422);
        }

        try {
            $document = BusinessVerificationDocument::findOrFail($request->document_id);
            
            // Verify ownership
            if ($document->application->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }

            // Delete document
            $document->delete();

            return response()->json([
                'success' => true,
                'message' => 'Document removed successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove document: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process step 4: Review and submit
     */
    public function processStep4(Request $request): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'communication_preferences' => 'nullable|array',
            'preferred_language' => 'nullable|in:vi,en',
            'sms_notifications_enabled' => 'boolean',
            'email_notifications_enabled' => 'boolean',
            'final_confirmation' => 'required|accepted',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $tempApplicationId = session('temp_application_id');
            
            if (!$tempApplicationId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Application not found. Please restart the registration process.'
                ], 400);
            }

            $application = BusinessVerificationApplication::findOrFail($tempApplicationId);

            // Update application with final preferences
            $application->update([
                'communication_preferences' => $request->communication_preferences ?? [],
                'preferred_language' => $request->preferred_language ?? 'vi',
                'sms_notifications_enabled' => $request->sms_notifications_enabled ?? false,
                'email_notifications_enabled' => $request->email_notifications_enabled ?? true,
                'status' => BusinessVerificationApplication::STATUS_PENDING,
                'submitted_at' => now(),
            ]);

            // Clear session data
            session()->forget(['business_info', 'temp_application_id']);

            return response()->json([
                'success' => true,
                'message' => 'Application submitted successfully',
                'application_id' => $application->id,
                'redirect_url' => route('business.verification.status')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit application: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show verification status
     */
    public function showVerificationStatus(): View
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $application = BusinessVerificationApplication::where('user_id', Auth::id())
            ->with(['documents', 'reviewer', 'auditTrail.performedBy'])
            ->latest()
            ->first();

        if (!$application) {
            return redirect()->route('business.registration.wizard');
        }

        return view('frontend.business.verification-status', compact('application'));
    }

    /**
     * Get application status (AJAX)
     */
    public function getApplicationStatus(): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required'
            ], 401);
        }

        $application = BusinessVerificationApplication::where('user_id', Auth::id())
            ->with(['documents', 'reviewer'])
            ->latest()
            ->first();

        if (!$application) {
            return response()->json([
                'success' => false,
                'message' => 'No application found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'application' => [
                'id' => $application->id,
                'status' => $application->status,
                'status_display' => $application->status_badge,
                'business_name' => $application->business_name,
                'application_type' => $application->application_type_display,
                'submitted_at' => $application->submitted_at?->format('d/m/Y H:i'),
                'completion_percentage' => $application->completion_percentage,
                'estimated_review_time' => $application->estimated_review_time,
                'days_in_review' => $application->days_in_review,
                'is_overdue' => $application->is_overdue,
                'documents_count' => $application->documents->count(),
                'verified_documents_count' => $application->documents->where('verification_status', 'verified')->count(),
                'reviewer' => $application->reviewer ? [
                    'name' => $application->reviewer->name,
                    'assigned_at' => $application->reviewed_at?->format('d/m/Y H:i'),
                ] : null,
            ]
        ]);
    }

    /**
     * Get required documents list
     */
    public function getRequiredDocuments(Request $request): JsonResponse
    {
        $applicationType = $request->input('application_type', 'verified_partner');

        $requiredDocuments = [
            'manufacturer' => [
                'business_license' => 'Giấy phép kinh doanh',
                'tax_certificate' => 'Giấy chứng nhận thuế',
                'identity_document' => 'Giấy tờ tùy thân',
                'quality_certificate' => 'Chứng nhận chất lượng',
                'insurance_certificate' => 'Giấy chứng nhận bảo hiểm',
            ],
            'supplier' => [
                'business_license' => 'Giấy phép kinh doanh',
                'tax_certificate' => 'Giấy chứng nhận thuế',
                'identity_document' => 'Giấy tờ tùy thân',
                'trade_license' => 'Giấy phép thương mại',
                'bank_statement' => 'Sao kê ngân hàng',
            ],
            'brand' => [
                'business_license' => 'Giấy phép kinh doanh',
                'tax_certificate' => 'Giấy chứng nhận thuế',
                'identity_document' => 'Giấy tờ tùy thân',
                'registration_certificate' => 'Giấy chứng nhận đăng ký',
            ],
            'verified_partner' => [
                'business_license' => 'Giấy phép kinh doanh',
                'tax_certificate' => 'Giấy chứng nhận thuế',
                'identity_document' => 'Giấy tờ tùy thân',
            ],
        ];

        return response()->json([
            'success' => true,
            'required_documents' => $requiredDocuments[$applicationType] ?? $requiredDocuments['verified_partner']
        ]);
    }
}
