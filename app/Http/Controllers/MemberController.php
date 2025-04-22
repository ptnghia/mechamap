<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MemberController extends Controller
{
    /**
     * Display a listing of the members.
     */
    public function index(Request $request): View
    {
        $sort = $request->input('sort', 'created_at');
        $direction = $request->input('direction', 'desc');
        $filter = $request->input('filter', '');
        
        $query = User::query();
        
        // Apply filter
        if ($filter) {
            $query->where(function($q) use ($filter) {
                $q->where('name', 'like', "%{$filter}%")
                  ->orWhere('username', 'like', "%{$filter}%");
            });
        }
        
        // Apply sorting
        switch ($sort) {
            case 'name':
                $query->orderBy('name', $direction);
                break;
                
            case 'posts':
                $query->withCount('posts')
                      ->orderBy('posts_count', $direction);
                break;
                
            case 'threads':
                $query->withCount('threads')
                      ->orderBy('threads_count', $direction);
                break;
                
            case 'joined':
            default:
                $query->orderBy('created_at', $direction);
                break;
        }
        
        $members = $query->paginate(20);
        
        return view('members.index', compact('members', 'sort', 'direction', 'filter'));
    }
    
    /**
     * Display the online members.
     */
    public function online(): View
    {
        // Get users who were active in the last 15 minutes
        $members = User::where('last_seen_at', '>=', now()->subMinutes(15))
            ->orderBy('last_seen_at', 'desc')
            ->paginate(20);
            
        return view('members.online', compact('members'));
    }
    
    /**
     * Display the staff members.
     */
    public function staff(): View
    {
        // Get admin and moderator users
        $admins = User::where('role', 'admin')->get();
        $moderators = User::where('role', 'moderator')->get();
        
        return view('members.staff', compact('admins', 'moderators'));
    }
}
