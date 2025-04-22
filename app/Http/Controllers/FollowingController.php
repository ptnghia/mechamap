<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class FollowingController extends Controller
{
    /**
     * Display a listing of the users that the authenticated user is following.
     */
    public function index(): View
    {
        $user = Auth::user();
        $following = $user->following()->paginate(20);
        
        return view('following.index', compact('following'));
    }
    
    /**
     * Display a listing of the users that are following the authenticated user.
     */
    public function followers(): View
    {
        $user = Auth::user();
        $followers = $user->followers()->paginate(20);
        
        return view('following.followers', compact('followers'));
    }
}
