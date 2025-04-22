<?php

namespace App\Http\Controllers;

use App\Models\Thread;
use App\Models\Post;
use App\Models\User;
use App\Models\Forum;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    /**
     * Display the search results.
     */
    public function index(Request $request): View
    {
        $query = $request->input('query');
        $type = $request->input('type', 'all');
        
        $threads = collect();
        $posts = collect();
        $users = collect();
        
        if ($query) {
            // Search threads
            if ($type == 'all' || $type == 'threads') {
                $threads = Thread::where('title', 'like', "%{$query}%")
                    ->orWhere('content', 'like', "%{$query}%")
                    ->with(['user', 'forum'])
                    ->latest()
                    ->take(10)
                    ->get();
            }
            
            // Search posts
            if ($type == 'all' || $type == 'posts') {
                $posts = Post::where('content', 'like', "%{$query}%")
                    ->with(['user', 'thread'])
                    ->latest()
                    ->take(10)
                    ->get();
            }
            
            // Search users
            if ($type == 'all' || $type == 'users') {
                $users = User::where('name', 'like', "%{$query}%")
                    ->orWhere('username', 'like', "%{$query}%")
                    ->latest()
                    ->take(10)
                    ->get();
            }
        }
        
        return view('search.index', compact('query', 'type', 'threads', 'posts', 'users'));
    }
    
    /**
     * Display the advanced search form.
     */
    public function advanced(): View
    {
        $forums = Forum::all();
        
        return view('search.advanced', compact('forums'));
    }
    
    /**
     * Process the advanced search.
     */
    public function advancedSearch(Request $request): View
    {
        $request->validate([
            'keywords' => 'nullable|string|max:255',
            'author' => 'nullable|string|max:255',
            'forum_id' => 'nullable|exists:forums,id',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'sort_by' => 'nullable|in:relevance,date,replies',
            'sort_dir' => 'nullable|in:asc,desc',
        ]);
        
        $keywords = $request->input('keywords');
        $author = $request->input('author');
        $forumId = $request->input('forum_id');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $sortBy = $request->input('sort_by', 'date');
        $sortDir = $request->input('sort_dir', 'desc');
        
        // Start with a base query
        $threadsQuery = Thread::query();
        $postsQuery = Post::query();
        
        // Apply keyword filter
        if ($keywords) {
            $threadsQuery->where(function($query) use ($keywords) {
                $query->where('title', 'like', "%{$keywords}%")
                    ->orWhere('content', 'like', "%{$keywords}%");
            });
            
            $postsQuery->where('content', 'like', "%{$keywords}%");
        }
        
        // Apply author filter
        if ($author) {
            $user = User::where('username', $author)->first();
            
            if ($user) {
                $threadsQuery->where('user_id', $user->id);
                $postsQuery->where('user_id', $user->id);
            } else {
                // No user found, return empty results
                $threadsQuery->where('user_id', 0);
                $postsQuery->where('user_id', 0);
            }
        }
        
        // Apply forum filter
        if ($forumId) {
            $threadsQuery->where('forum_id', $forumId);
            $postsQuery->whereHas('thread', function($query) use ($forumId) {
                $query->where('forum_id', $forumId);
            });
        }
        
        // Apply date filters
        if ($dateFrom) {
            $threadsQuery->whereDate('created_at', '>=', $dateFrom);
            $postsQuery->whereDate('created_at', '>=', $dateFrom);
        }
        
        if ($dateTo) {
            $threadsQuery->whereDate('created_at', '<=', $dateTo);
            $postsQuery->whereDate('created_at', '<=', $dateTo);
        }
        
        // Apply sorting
        switch ($sortBy) {
            case 'relevance':
                // For relevance, we would need a more complex scoring system
                // For simplicity, we'll just sort by date
                $threadsQuery->orderBy('created_at', $sortDir);
                $postsQuery->orderBy('created_at', $sortDir);
                break;
                
            case 'date':
                $threadsQuery->orderBy('created_at', $sortDir);
                $postsQuery->orderBy('created_at', $sortDir);
                break;
                
            case 'replies':
                $threadsQuery->withCount('posts')
                    ->orderBy('posts_count', $sortDir);
                // Posts don't have replies, so we'll just sort by date
                $postsQuery->orderBy('created_at', $sortDir);
                break;
        }
        
        // Get the results
        $threads = $threadsQuery->with(['user', 'forum'])->paginate(10);
        $posts = $postsQuery->with(['user', 'thread'])->paginate(10);
        
        $forums = Forum::all();
        
        return view('search.advanced-results', compact(
            'threads', 
            'posts', 
            'keywords', 
            'author', 
            'forumId', 
            'dateFrom', 
            'dateTo', 
            'sortBy', 
            'sortDir',
            'forums'
        ));
    }
}
