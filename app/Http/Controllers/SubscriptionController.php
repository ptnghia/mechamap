<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    /**
     * Display the subscription plans page.
     */
    public function index(): View
    {
        $plans = [
            [
                'id' => 'basic',
                'name' => 'Basic',
                'price' => 4.99,
                'duration' => 'monthly',
                'features' => [
                    'Ad-free browsing',
                    'Access to premium forums',
                    'Custom profile badge',
                ],
            ],
            [
                'id' => 'premium',
                'name' => 'Premium',
                'price' => 9.99,
                'duration' => 'monthly',
                'features' => [
                    'All Basic features',
                    'Unlimited private messages',
                    'Custom signature',
                    'Priority support',
                ],
            ],
            [
                'id' => 'pro',
                'name' => 'Professional',
                'price' => 19.99,
                'duration' => 'monthly',
                'features' => [
                    'All Premium features',
                    'Business profile',
                    'Featured listings',
                    'Analytics dashboard',
                    'Dedicated account manager',
                ],
            ],
        ];
        
        $user = Auth::user();
        $currentSubscription = $user->subscription;
        
        return view('subscription.index', compact('plans', 'currentSubscription'));
    }
    
    /**
     * Process the subscription purchase.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'plan_id' => 'required|string|in:basic,premium,pro',
        ]);
        
        // Here you would integrate with a payment gateway like Stripe
        // For now, we'll just simulate a successful subscription
        
        $user = Auth::user();
        
        // Update or create subscription
        if ($user->subscription) {
            $user->subscription->update([
                'plan_id' => $request->plan_id,
                'status' => 'active',
                'expires_at' => now()->addMonth(),
            ]);
        } else {
            $user->subscription()->create([
                'plan_id' => $request->plan_id,
                'status' => 'active',
                'expires_at' => now()->addMonth(),
            ]);
        }
        
        return redirect()->route('subscription.success');
    }
    
    /**
     * Display the subscription success page.
     */
    public function success(): View
    {
        return view('subscription.success');
    }
    
    /**
     * Display the subscription cancellation page.
     */
    public function cancel(): View
    {
        return view('subscription.cancel');
    }
}
