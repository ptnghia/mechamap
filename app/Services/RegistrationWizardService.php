<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * 🧙‍♂️ Registration Wizard Service
 * 
 * Manages multi-step registration sessions and user creation
 */
class RegistrationWizardService
{
    protected string $cachePrefix = 'registration_session:';
    protected int $sessionTimeout = 1800; // 30 minutes

    /**
     * Initialize a new registration session
     * 
     * @return string Session ID
     */
    public function initializeSession(): string
    {
        $sessionId = Str::uuid()->toString();
        
        $initialData = [
            'session_id' => $sessionId,
            'step' => 1,
            'created_at' => now()->toISOString(),
            'expires_at' => now()->addSeconds($this->sessionTimeout)->toISOString(),
            'step_1_completed' => false,
            'step_2_completed' => false,
        ];

        Cache::put(
            $this->cachePrefix . $sessionId,
            $initialData,
            $this->sessionTimeout
        );

        return $sessionId;
    }

    /**
     * Get session data
     * 
     * @param string $sessionId
     * @return array
     */
    public function getSessionData(string $sessionId): array
    {
        $data = Cache::get($this->cachePrefix . $sessionId, []);
        
        // Check if session has expired
        if (empty($data) || $this->isSessionExpired($data)) {
            return [];
        }

        return $data;
    }

    /**
     * Update session data
     * 
     * @param string $sessionId
     * @param array $data
     * @return bool
     */
    public function updateSessionData(string $sessionId, array $data): bool
    {
        $existingData = $this->getSessionData($sessionId);
        
        if (empty($existingData)) {
            return false;
        }

        // Merge new data with existing data
        $updatedData = array_merge($existingData, $data);
        $updatedData['updated_at'] = now()->toISOString();

        // Extend session timeout
        $updatedData['expires_at'] = now()->addSeconds($this->sessionTimeout)->toISOString();

        Cache::put(
            $this->cachePrefix . $sessionId,
            $updatedData,
            $this->sessionTimeout
        );

        return true;
    }

    /**
     * Advance to next step
     * 
     * @param string $sessionId
     * @return bool
     */
    public function advanceStep(string $sessionId): bool
    {
        $data = $this->getSessionData($sessionId);
        
        if (empty($data)) {
            return false;
        }

        $currentStep = $data['step'] ?? 1;
        $nextStep = min($currentStep + 1, 2); // Max 2 steps

        return $this->updateSessionData($sessionId, ['step' => $nextStep]);
    }

    /**
     * Complete registration and create user
     * 
     * @param string $sessionId
     * @return User
     * @throws \Exception
     */
    public function completeRegistration(string $sessionId): User
    {
        $data = $this->getSessionData($sessionId);
        
        if (empty($data)) {
            throw new \Exception('Session data not found or expired.');
        }

        // Validate required data
        $this->validateRegistrationData($data);

        // Create user
        $user = $this->createUser($data);

        // Clear session after successful registration
        $this->clearSession($sessionId);

        return $user;
    }

    /**
     * Clear session data
     * 
     * @param string $sessionId
     * @return bool
     */
    public function clearSession(string $sessionId): bool
    {
        return Cache::forget($this->cachePrefix . $sessionId);
    }

    /**
     * Clean up expired sessions
     * 
     * @return int Number of cleaned sessions
     */
    public function cleanupExpiredSessions(): int
    {
        // This would be implemented with a proper cache store that supports pattern matching
        // For now, we rely on cache TTL to handle cleanup automatically
        return 0;
    }

    /**
     * Check if session has expired
     * 
     * @param array $data
     * @return bool
     */
    protected function isSessionExpired(array $data): bool
    {
        if (!isset($data['expires_at'])) {
            return true;
        }

        return now()->isAfter($data['expires_at']);
    }

    /**
     * Validate registration data before creating user
     * 
     * @param array $data
     * @throws \Exception
     */
    protected function validateRegistrationData(array $data): void
    {
        // Check step 1 completion
        if (!($data['step_1_completed'] ?? false)) {
            throw new \Exception('Step 1 not completed.');
        }

        // Required fields for all users
        $requiredFields = ['name', 'username', 'email', 'password', 'account_type'];
        
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                throw new \Exception("Required field missing: {$field}");
            }
        }

        // Check business users have completed step 2
        $businessRoles = ['manufacturer', 'supplier', 'brand'];
        $accountType = $data['account_type'];
        
        if (in_array($accountType, $businessRoles)) {
            if (!($data['step_2_completed'] ?? false)) {
                throw new \Exception('Business users must complete step 2.');
            }

            // Required business fields
            $businessFields = ['company_name', 'business_license', 'tax_code', 'business_description'];
            
            foreach ($businessFields as $field) {
                if (empty($data[$field])) {
                    throw new \Exception("Required business field missing: {$field}");
                }
            }
        }
    }

    /**
     * Create user from session data
     * 
     * @param array $data
     * @return User
     */
    protected function createUser(array $data): User
    {
        $accountType = $data['account_type'];
        
        // Determine role group
        $roleGroup = $this->getRoleGroup($accountType);
        
        // Base user data
        $userData = [
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $accountType,
            'role_group' => $roleGroup,
            'status' => 'active',
            'email_verified_at' => null, // Will be verified via email
        ];

        // Add business fields if applicable
        if (in_array($accountType, ['manufacturer', 'supplier', 'brand'])) {
            $userData = array_merge($userData, [
                'company_name' => $data['company_name'],
                'business_license' => $data['business_license'],
                'tax_code' => $data['tax_code'],
                'business_description' => $data['business_description'],
                'business_categories' => json_encode($data['business_categories'] ?? []),
                'business_phone' => $data['business_phone'] ?? null,
                'business_email' => $data['business_email'] ?? null,
                'business_address' => $data['business_address'] ?? null,
                'verification_documents' => json_encode($data['verification_documents'] ?? []),
                'is_verified_business' => false, // Requires admin approval
                'verified_at' => null,
                'verified_by' => null,
                'verification_notes' => null,
            ]);
        }

        return User::create($userData);
    }

    /**
     * Get role group for account type
     * 
     * @param string $accountType
     * @return string
     */
    protected function getRoleGroup(string $accountType): string
    {
        $roleGroups = [
            'member' => 'community_members',
            'student' => 'community_members',
            'manufacturer' => 'business_partners',
            'supplier' => 'business_partners',
            'brand' => 'business_partners',
        ];

        return $roleGroups[$accountType] ?? 'community_members';
    }

    /**
     * Get session statistics
     * 
     * @return array
     */
    public function getSessionStatistics(): array
    {
        // This would require a cache store that supports pattern matching
        // For now, return basic info
        return [
            'active_sessions' => 0, // Would count active sessions
            'total_registrations_today' => User::whereDate('created_at', today())->count(),
            'business_registrations_pending' => User::where('is_verified_business', false)
                ->whereIn('role', ['manufacturer', 'supplier', 'brand'])
                ->count(),
        ];
    }

    /**
     * Extend session timeout
     * 
     * @param string $sessionId
     * @param int $additionalSeconds
     * @return bool
     */
    public function extendSession(string $sessionId, int $additionalSeconds = 1800): bool
    {
        $data = $this->getSessionData($sessionId);
        
        if (empty($data)) {
            return false;
        }

        $newExpiry = now()->addSeconds($additionalSeconds)->toISOString();
        
        return $this->updateSessionData($sessionId, [
            'expires_at' => $newExpiry,
            'extended_at' => now()->toISOString()
        ]);
    }

    /**
     * Get session progress
     * 
     * @param string $sessionId
     * @return array
     */
    public function getSessionProgress(string $sessionId): array
    {
        $data = $this->getSessionData($sessionId);
        
        if (empty($data)) {
            return [
                'step' => 1,
                'progress' => 0,
                'completed_steps' => [],
                'next_step' => 1
            ];
        }

        $step = $data['step'] ?? 1;
        $completedSteps = [];
        
        if ($data['step_1_completed'] ?? false) {
            $completedSteps[] = 1;
        }
        
        if ($data['step_2_completed'] ?? false) {
            $completedSteps[] = 2;
        }

        $progress = (count($completedSteps) / 2) * 100;
        $nextStep = $step;

        return [
            'step' => $step,
            'progress' => $progress,
            'completed_steps' => $completedSteps,
            'next_step' => $nextStep,
            'account_type' => $data['account_type'] ?? null,
            'requires_business_info' => in_array($data['account_type'] ?? '', ['manufacturer', 'supplier', 'brand'])
        ];
    }
}
