<?php

namespace App\Http\Controllers;

use App\Models\Showcase;
use App\Models\Thread;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ShowcaseController extends Controller
{
    /**
     * Display a listing of the user's showcase items.
     */
    public function index(): View
    {
        $user = Auth::user();
        $showcaseItems = $user->showcaseItems()
            ->with('showcaseable')
            ->latest()
            ->paginate(20);

        return view('showcase.index', compact('showcaseItems'));
    }

    /**
     * Display the public showcase page.
     */
    public function publicShowcase(): View
    {
        // Get featured content
        $featuredThreads = Thread::where('is_featured', true)
            ->with(['user', 'forum'])
            ->whereHas('forum') // Ensure forum exists
            ->latest()
            ->take(5)
            ->get();

        // Get most popular threads
        $popularThreads = Thread::withCount('posts')
            ->orderBy('posts_count', 'desc')
            ->with(['user', 'forum'])
            ->whereHas('forum') // Ensure forum exists
            ->take(10)
            ->get();

        // Get user showcases
        $userShowcases = Showcase::with(['user', 'showcaseable'])
            ->whereHas('showcaseable') // Ensure showcaseable exists
            ->latest()
            ->paginate(20);

        return view('showcase.public', compact('featuredThreads', 'popularThreads', 'userShowcases'));
    }

    /**
     * Store a newly created showcase item in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'showcaseable_id' => 'required|integer',
            'showcaseable_type' => 'required|string',
            'description' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();

        // Check if the item is already in the showcase
        $exists = $user->showcaseItems()
            ->where('showcaseable_id', $request->showcaseable_id)
            ->where('showcaseable_type', $request->showcaseable_type)
            ->exists();

        if ($exists) {
            return back()->with('info', 'This item is already in your showcase.');
        }

        // Create the showcase item
        $user->showcaseItems()->create([
            'showcaseable_id' => $request->showcaseable_id,
            'showcaseable_type' => $request->showcaseable_type,
            'description' => $request->description,
        ]);

        return back()->with('success', 'Item added to showcase successfully.');
    }

    /**
     * Remove the specified showcase item from storage.
     */
    public function destroy(Showcase $showcase): RedirectResponse
    {
        // Check if the showcase item belongs to the authenticated user
        if ($showcase->user_id !== Auth::id()) {
            abort(403);
        }

        $showcase->delete();

        return back()->with('success', 'Item removed from showcase successfully.');
    }
}
