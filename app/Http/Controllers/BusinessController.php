<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class BusinessController extends Controller
{
    /**
     * Display the business growth page.
     */
    public function index(): View
    {
        return view('business.index');
    }
    
    /**
     * Display the business services page.
     */
    public function services(): View
    {
        $services = [
            [
                'name' => 'Premium Listing',
                'description' => 'Get your business featured at the top of search results.',
                'price' => 99.99,
                'duration' => 'monthly',
            ],
            [
                'name' => 'Business Profile',
                'description' => 'Create a professional business profile with custom branding.',
                'price' => 149.99,
                'duration' => 'one-time',
            ],
            [
                'name' => 'Sponsored Content',
                'description' => 'Publish sponsored articles and content on the platform.',
                'price' => 299.99,
                'duration' => 'per post',
            ],
            [
                'name' => 'Analytics Dashboard',
                'description' => 'Access detailed analytics about your business performance.',
                'price' => 49.99,
                'duration' => 'monthly',
            ],
        ];
        
        return view('business.services', compact('services'));
    }
}
