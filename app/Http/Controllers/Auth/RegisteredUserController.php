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
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:' . User::class, 'alpha_dash'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'account_type' => ['required', 'string', 'in:member,student,manufacturer,supplier,brand'],
        ]);

        // Xác định role_group dựa trên account_type
        $roleGroup = $this->getRoleGroup($request->account_type);

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->account_type,
            'role_group' => $roleGroup,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
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
