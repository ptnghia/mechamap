<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     * Redirect directly to new wizard
     */
    public function create(): RedirectResponse
    {
        // Redirect directly to new multi-step registration wizard
        return redirect()->route('register.wizard.step1');
    }

    /**
     * Handle an incoming registration request.
     * Redirect to new wizard-based registration
     */
    public function store(Request $request): RedirectResponse
    {
        // Redirect POST requests to new wizard
        return redirect()->route('register.wizard.step1')
            ->withInput($request->only(['name', 'username', 'email', 'account_type']))
            ->with('info', 'Vui lòng sử dụng quy trình đăng ký mới để có trải nghiệm tốt hơn!');
    }

    /**
     * Xác định role_group dựa trên account_type
     *
     * @param string $accountType
     * @return string
     */
    private function getRoleGroup(string $accountType): string
    {
        return match ($accountType) {
            'member', 'student' => 'community_members',
            'manufacturer', 'supplier', 'brand' => 'business_partners',
            default => 'community_members'
        };
    }
}
