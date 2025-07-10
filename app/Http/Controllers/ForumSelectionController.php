<?php

namespace App\Http\Controllers;

use App\Models\Forum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ForumSelectionController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the forum selection page.
     *
     * @return \Illuminate\View\View
     */
    public function index(): View
    {
        // Get all forums grouped by category
        $forums = Forum::with('category')
            ->orderBy('category_id')
            ->orderBy('order')
            ->get()
            ->groupBy('category.name');

        // Get popular forums
        $popularForums = Forum::withCount('threads')
            ->orderBy('threads_count', 'desc')
            ->take(5)
            ->get();

        return view('forums.select', compact('forums', 'popularForums'));
    }

    /**
     * Redirect to thread creation page for the selected forum.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function selectForum(Request $request)
    {
        $request->validate([
            'forum_id' => 'required|exists:forums,id',
        ]);

        $forum = Forum::findOrFail($request->forum_id);

        return redirect()->route('threads.create', ['forum_id' => $forum->id]);
    }
}
