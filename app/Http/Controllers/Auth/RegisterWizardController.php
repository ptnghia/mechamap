<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\BasicRegistrationRequest;
use App\Http\Requests\Auth\BusinessRegistrationRequest;
use App\Models\User;
use App\Services\RegistrationWizardService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

/**
 * 🧙‍♂️ Multi-Step Registration Wizard Controller
 *
 * Handles the multi-step registration process for MechaMap users.
 * Supports both community members and business partners with different flows.
 */
class RegisterWizardController extends Controller
{
    protected RegistrationWizardService $wizardService;

    public function __construct(RegistrationWizardService $wizardService)
    {
        $this->middleware('guest');
        $this->wizardService = $wizardService;
    }

    /**
     * Show Step 1: Basic Information
     *
     * @param Request $request
     * @return View
     */
    public function showStep1(Request $request): View
    {
        // Initialize or get existing session
        $sessionId = $this->wizardService->initializeSession();
        $sessionData = $this->wizardService->getSessionData($sessionId);

        // Store session ID in Laravel session
        Session::put('registration_wizard_session', $sessionId);

        return view('auth.wizard.step1', [
            'sessionData' => $sessionData,
            'step' => 1,
            'totalSteps' => 2,
            'progress' => 50
        ]);
    }

    /**
     * Process Step 1: Basic Information
     *
     * @param BasicRegistrationRequest $request
     * @return RedirectResponse|JsonResponse
     */
    public function processStep1(BasicRegistrationRequest $request): RedirectResponse|JsonResponse
    {
        \Log::info('Processing step 1', [
            'request_data' => $request->all(),
            'session_id' => Session::getId(),
            'wizard_session' => Session::get('registration_wizard_session')
        ]);

        $sessionId = Session::get('registration_wizard_session');

        if (!$sessionId) {
            \Log::warning('No wizard session found, handling session error');
            return $this->handleSessionError($request);
        }

        // Save step 1 data to session
        $stepData = [
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => $request->password, // Will be hashed when creating user
            'account_type' => $request->account_type,
            'terms_accepted' => $request->boolean('terms'),
            'step_1_completed' => true,
            'completed_at' => now()->toISOString()
        ];

        $this->wizardService->updateSessionData($sessionId, $stepData);
        $this->wizardService->advanceStep($sessionId);

        // Determine next step based on account type
        $accountType = $request->account_type;

        if (in_array($accountType, ['member', 'student'])) {
            // Community members go directly to completion
            return $this->completeRegistration($request, $sessionId);
        } else {
            // Business partners go to step 2
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Bước 1 hoàn thành thành công.',
                    'next_step' => route('register.wizard.step2')
                ]);
            }

            return redirect()->route('register.wizard.step2')
                ->with('success', 'Thông tin cơ bản đã được lưu. Vui lòng hoàn thành thông tin doanh nghiệp.');
        }
    }

    /**
     * Show Step 2: Business Information
     *
     * @param Request $request
     * @return View|RedirectResponse
     */
    public function showStep2(Request $request): View|RedirectResponse
    {
        $sessionId = Session::get('registration_wizard_session');

        if (!$sessionId) {
            return redirect()->route('register.wizard.step1')
                ->with('error', 'Phiên đăng ký đã hết hạn. Vui lòng bắt đầu lại.');
        }

        $sessionData = $this->wizardService->getSessionData($sessionId);

        // Verify step 1 is completed
        if (!($sessionData['step_1_completed'] ?? false)) {
            return redirect()->route('register.wizard.step1')
                ->with('error', 'Vui lòng hoàn thành bước 1 trước.');
        }

        // Verify this is a business account
        $accountType = $sessionData['account_type'] ?? '';
        if (in_array($accountType, ['member', 'student'])) {
            return redirect()->route('register.wizard.step1')
                ->with('error', 'Bước này chỉ dành cho đối tác kinh doanh.');
        }

        return view('auth.wizard.step2', [
            'sessionData' => $sessionData,
            'step' => 2,
            'totalSteps' => 2,
            'progress' => 100,
            'accountType' => $accountType
        ]);
    }

    /**
     * Process Step 2: Business Information
     *
     * @param BusinessRegistrationRequest $request
     * @return RedirectResponse|JsonResponse
     */
    public function processStep2(BusinessRegistrationRequest $request): RedirectResponse|JsonResponse
    {
        $sessionId = Session::get('registration_wizard_session');

        if (!$sessionId) {
            return $this->handleSessionError($request);
        }

        // Save step 2 data to session
        $stepData = [
            'company_name' => $request->company_name,
            'business_license' => $request->business_license,
            'tax_code' => $request->tax_code,
            'business_description' => $request->business_description,
            'business_categories' => $request->business_categories,
            'business_phone' => $request->business_phone,
            'business_email' => $request->business_email,
            'business_address' => $request->business_address,
            'step_2_completed' => true,
            'completed_at' => now()->toISOString()
        ];

        // Handle document uploads if any
        if ($request->hasFile('verification_documents')) {
            $stepData['verification_documents'] = $this->handleDocumentUploads($request);
        }

        $this->wizardService->updateSessionData($sessionId, $stepData);

        // Complete business registration
        return $this->completeRegistration($request, $sessionId);
    }

    /**
     * Complete the registration process
     *
     * @param Request $request
     * @param string $sessionId
     * @return RedirectResponse|JsonResponse
     */
    protected function completeRegistration(Request $request, string $sessionId): RedirectResponse|JsonResponse
    {
        try {
            // Create the user
            $user = $this->wizardService->completeRegistration($sessionId);

            // Fire registered event
            event(new Registered($user));

            // Log the user in
            Auth::login($user);

            // Clean up session
            Session::forget('registration_wizard_session');

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Đăng ký thành công!',
                    'redirect' => $this->getRedirectUrl($user)
                ]);
            }

            return redirect($this->getRedirectUrl($user))
                ->with('success', 'Đăng ký thành công! Chào mừng bạn đến với MechaMap.');

        } catch (\Exception $e) {
            \Log::error('Registration completion failed', [
                'session_id' => $sessionId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra trong quá trình đăng ký. Vui lòng thử lại.',
                    'error' => config('app.debug') ? $e->getMessage() : null
                ], 500);
            }

            return back()->with('error', 'Có lỗi xảy ra trong quá trình đăng ký. Vui lòng thử lại.');
        }
    }

    /**
     * Show completion page
     *
     * @param Request $request
     * @return View
     */
    public function complete(Request $request): View
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('register.wizard.step1');
        }

        return view('auth.wizard.complete', [
            'user' => $user,
            'isBusiness' => in_array($user->role, ['manufacturer', 'supplier', 'brand'])
        ]);
    }

    /**
     * Restart the registration process
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function restart(Request $request): RedirectResponse
    {
        $sessionId = Session::get('registration_wizard_session');

        if ($sessionId) {
            $this->wizardService->clearSession($sessionId);
        }

        Session::forget('registration_wizard_session');

        return redirect()->route('register.wizard.step1')
            ->with('info', 'Đăng ký đã được khởi động lại.');
    }

    /**
     * AJAX: Validate individual field
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function validateField(Request $request): JsonResponse
    {
        try {
            $field = $request->input('field');
            $value = $request->input('value');
            $step = $request->input('step', 1);

            $rules = $this->getFieldValidationRules($field, $step);

            if (!$rules) {
                return response()->json([
                    'valid' => false,
                    'message' => 'Trường không hợp lệ.'
                ]);
            }

            $validator = \Validator::make([$field => $value], [$field => $rules]);

            if ($validator->fails()) {
                return response()->json([
                    'valid' => false,
                    'message' => $validator->errors()->first($field)
                ]);
            }

            return response()->json([
                'valid' => true,
                'message' => 'Hợp lệ'
            ]);
        } catch (\Exception $e) {
            \Log::error('Field validation error', [
                'field' => $request->input('field'),
                'value' => $request->input('value'),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'valid' => false,
                'message' => 'Lỗi xác thực trường.'
            ], 500);
        }
    }

    /**
     * AJAX: Check username availability
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function checkUsername(Request $request): JsonResponse
    {
        $username = $request->input('username');

        if (empty($username)) {
            return response()->json([
                'available' => false,
                'message' => 'Vui lòng nhập tên đăng nhập.'
            ]);
        }

        $exists = User::where('username', $username)->exists();

        return response()->json([
            'available' => !$exists,
            'message' => $exists ? 'Tên đăng nhập đã tồn tại.' : 'Tên đăng nhập khả dụng.'
        ]);
    }

    /**
     * AJAX: Save progress
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function saveProgress(Request $request): JsonResponse
    {
        $sessionId = Session::get('registration_wizard_session');

        if (!$sessionId) {
            return response()->json([
                'success' => false,
                'message' => 'Phiên đăng ký không hợp lệ.'
            ]);
        }

        $data = $request->input('data', []);
        $data['auto_saved_at'] = now()->toISOString();

        $success = $this->wizardService->updateSessionData($sessionId, $data);

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Đã lưu tự động.' : 'Lỗi khi lưu dữ liệu.'
        ]);
    }

    /**
     * Handle session error
     */
    protected function handleSessionError(Request $request): RedirectResponse|JsonResponse
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Phiên đăng ký đã hết hạn.',
                'redirect' => route('register.wizard.step1')
            ], 422);
        }

        return redirect()->route('register.wizard.step1')
            ->with('error', 'Phiên đăng ký đã hết hạn. Vui lòng bắt đầu lại.');
    }

    /**
     * Handle document uploads
     */
    protected function handleDocumentUploads(Request $request): array
    {
        $documents = [];

        foreach ($request->file('verification_documents') as $file) {
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('business-documents', $filename, 'public');

            $documents[] = [
                'original_name' => $file->getClientOriginalName(),
                'filename' => $filename,
                'path' => $path,
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'uploaded_at' => now()->toISOString()
            ];
        }

        return $documents;
    }

    /**
     * Get redirect URL based on user type
     */
    protected function getRedirectUrl(User $user): string
    {
        if (in_array($user->role, ['manufacturer', 'supplier', 'brand'])) {
            return route('business.dashboard');
        }

        return route('dashboard');
    }

    /**
     * Get field validation rules
     */
    protected function getFieldValidationRules(string $field, int $step): array|null
    {
        $step1Rules = [
            'name' => ['required', 'string', 'max:255', 'min:2', 'regex:/^[\p{L}\s\-\.\']+$/u'],
            'username' => ['required', 'string', 'max:255', 'min:3', 'unique:users,username', 'alpha_dash', 'not_in:admin,root,api,www,test,null,undefined,system,support,help,info,contact,about,home,index,main,default'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'account_type' => ['required', 'string', 'in:member,guest,manufacturer,supplier,brand']
        ];

        $step2Rules = [
            'company_name' => ['required', 'string', 'max:255', 'min:2'],
            'business_license' => ['required', 'string', 'max:100'],
            'tax_code' => ['required', 'string', 'max:20', 'regex:/^[0-9]{10,13}$/', 'unique:users,tax_code'],
            'business_description' => ['required', 'string', 'min:50', 'max:1000'],
            'business_phone' => ['nullable', 'string', 'max:20'],
            'business_email' => ['nullable', 'email', 'max:255']
        ];

        if ($step === 1) {
            return $step1Rules[$field] ?? null;
        } elseif ($step === 2) {
            return $step2Rules[$field] ?? null;
        }

        return null;
    }
}
