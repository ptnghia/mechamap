<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class ThemeController extends Controller
{
    /**
     * Toggle between dark and light mode.
     */
    public function toggleDarkMode(Request $request): \Illuminate\Http\RedirectResponse
    {
        $currentMode = $request->cookie('dark_mode', 'light');
        $newMode = $currentMode === 'dark' ? 'light' : 'dark';
        
        // Set cookie for 1 year
        $cookie = Cookie::make('dark_mode', $newMode, 60 * 24 * 365);
        
        return back()->withCookie($cookie);
    }
    
    /**
     * Toggle between original and new view.
     */
    public function toggleOriginalView(Request $request): \Illuminate\Http\RedirectResponse
    {
        $currentView = $request->cookie('original_view', 'new');
        $newView = $currentView === 'original' ? 'new' : 'original';
        
        // Set cookie for 1 year
        $cookie = Cookie::make('original_view', $newView, 60 * 24 * 365);
        
        return back()->withCookie($cookie);
    }
}
