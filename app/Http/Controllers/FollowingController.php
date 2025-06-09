<?php

namespace App\Http\Controllers;

use App\Models\Thread;
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
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $following = $user->following()->paginate(20);

        return view('following.index', compact('following'));
    }

    /**
     * Display a listing of the users that are following the authenticated user.
     */
    public function followers(): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $followers = $user->followers()->paginate(20);

        return view('following.followers', compact('followers'));
    }

    /**
     * Display a listing of the threads that the authenticated user is following.
     */
    public function threads(Request $request): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $query = $user->followedThreads();

        // Apply filters if needed
        if ($request->has('forum')) {
            $query->where('forum_id', $request->forum);
        }

        if ($request->has('category')) {
            $query->where('category_id', $request->category);
        }

        $threads = $query->with(['user', 'forum', 'category'])
            ->withCount(['allComments as comments_count', 'bookmarks', 'ratings'])
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('following.threads', compact('threads'));
    }

    /**
     * Display a listing of the threads that the authenticated user has participated in.
     */
    public function participated(): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $threads = Thread::whereHas('comments', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
            ->with(['user', 'forum', 'category'])
            ->withCount(['allComments as comments_count', 'bookmarks', 'ratings'])
            ->latest()
            ->paginate(20);

        return view('following.participated', compact('threads'));
    }
}
