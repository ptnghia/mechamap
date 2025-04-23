<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityController extends Controller
{
    /**
     * Display all activities for a user.
     */
    public function index(User $user): View
    {
        // Lấy tất cả hoạt động của người dùng
        $activities = $user->activities()
            ->with(['thread', 'comment.thread'])
            ->latest()
            ->paginate(20);

        return view('profile.activities', compact('user', 'activities'));
    }
}
