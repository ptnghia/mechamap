<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     */
    public function __invoke(Request $request): RedirectResponse|View
    {
        return $request->user()->hasVerifiedEmail()
                    ? redirect()->intended(route('dashboard', absolute: false))
                    : view('auth.verify-email');
    }

    /**
     * Check email verification status via AJAX.
     */
    public function check(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'verified' => true,
                'redirect_url' => route('dashboard')
            ]);
        }

        return response()->json([
            'verified' => false
        ]);
    }
}
