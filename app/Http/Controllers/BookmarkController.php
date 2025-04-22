<?php

namespace App\Http\Controllers;

use App\Models\Bookmark;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BookmarkController extends Controller
{
    /**
     * Display a listing of the user's bookmarks.
     */
    public function index(): View
    {
        $user = Auth::user();
        $bookmarks = $user->bookmarks()
            ->with('bookmarkable')
            ->latest()
            ->paginate(20);
        
        return view('bookmarks.index', compact('bookmarks'));
    }
    
    /**
     * Store a newly created bookmark in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'bookmarkable_id' => 'required|integer',
            'bookmarkable_type' => 'required|string',
        ]);
        
        $user = Auth::user();
        
        // Check if the bookmark already exists
        $exists = $user->bookmarks()
            ->where('bookmarkable_id', $request->bookmarkable_id)
            ->where('bookmarkable_type', $request->bookmarkable_type)
            ->exists();
        
        if ($exists) {
            return back()->with('info', 'You have already bookmarked this item.');
        }
        
        // Create the bookmark
        $user->bookmarks()->create([
            'bookmarkable_id' => $request->bookmarkable_id,
            'bookmarkable_type' => $request->bookmarkable_type,
        ]);
        
        return back()->with('success', 'Item bookmarked successfully.');
    }
    
    /**
     * Remove the specified bookmark from storage.
     */
    public function destroy(Bookmark $bookmark): RedirectResponse
    {
        // Check if the bookmark belongs to the authenticated user
        if ($bookmark->user_id !== Auth::id()) {
            abort(403);
        }
        
        $bookmark->delete();
        
        return back()->with('success', 'Bookmark removed successfully.');
    }
}
