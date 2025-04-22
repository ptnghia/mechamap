<?php

namespace App\Http\Controllers;

use App\Models\Forum;
use App\Models\Thread;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ForumController extends Controller
{
    /**
     * Display a listing of the forums.
     */
    public function index(): View
    {
        // Get all forums with their categories
        $categories = Forum::where('parent_id', null)
            ->with(['subForums' => function($query) {
                $query->withCount(['threads', 'posts']);
            }])
            ->get();
            
        return view('forums.index', compact('categories'));
    }
    
    /**
     * Display the specified forum.
     */
    public function show(Forum $forum): View
    {
        // Load the forum with its threads
        $threads = $forum->threads()
            ->with('user')
            ->withCount('posts')
            ->latest()
            ->paginate(20);
            
        return view('forums.show', compact('forum', 'threads'));
    }
    
    /**
     * Display a listing of all forums.
     */
    public function listing(): View
    {
        // Get all forums with their categories
        $categories = Forum::where('parent_id', null)
            ->with(['subForums' => function($query) {
                $query->withCount(['threads', 'posts']);
            }])
            ->get();
            
        // Get total stats
        $stats = [
            'forums' => Forum::count(),
            'threads' => Thread::count(),
            'posts' => \App\Models\Post::count(),
            'users' => \App\Models\User::count(),
        ];
            
        return view('forums.listing', compact('categories', 'stats'));
    }
}
