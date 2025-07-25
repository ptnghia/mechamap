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
            $query->where(function ($q) use ($filter) {
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

        // Always load counts for display
        $query->withCount(['posts', 'threads', 'followers']);

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
        // Get admin users (admin, super_admin, system_admin)
        $admins = User::whereIn('role', ['admin', 'super_admin', 'system_admin'])
            ->withCount(['posts', 'threads', 'followers'])
            ->get();

        // Get moderator users (all types of moderators)
        $moderators = User::whereIn('role', [
            'community_moderator',
            'content_moderator',
            'marketplace_moderator'
        ])
        ->withCount(['posts', 'threads', 'followers'])
        ->get();

        return view('members.staff', compact('admins', 'moderators'));
    }

    /**
     * Display the members leaderboard.
     */
    public function leaderboard(): View
    {
        // Get top members by different criteria
        $topPosters = User::withCount('posts')
            ->where('posts_count', '>', 0)
            ->orderBy('posts_count', 'desc')
            ->take(10)
            ->get();

        $topThreadCreators = User::withCount('threads')
            ->where('threads_count', '>', 0)
            ->orderBy('threads_count', 'desc')
            ->take(10)
            ->get();

        // Calculate likes received by counting likes on user's threads and comments
        $topLikedUsers = User::select('users.*')
            ->selectRaw('(
                SELECT COUNT(*)
                FROM thread_likes
                INNER JOIN threads ON thread_likes.thread_id = threads.id
                WHERE threads.user_id = users.id
            ) + (
                SELECT COUNT(*)
                FROM comment_likes
                INNER JOIN comments ON comment_likes.comment_id = comments.id
                WHERE comments.user_id = users.id
            ) as total_likes_received')
            ->having('total_likes_received', '>', 0)
            ->orderBy('total_likes_received', 'desc')
            ->take(10)
            ->get();

        $newestMembers = User::orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('members.leaderboard', compact(
            'topPosters',
            'topThreadCreators',
            'topLikedUsers',
            'newestMembers'
        ));
    }
}
