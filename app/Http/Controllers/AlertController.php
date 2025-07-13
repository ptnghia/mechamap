<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AlertController extends Controller
{
    /**
     * Display a listing of the user's alerts.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $filter = $request->input('filter');

        $query = $user->alerts();

        // Apply filters
        if ($filter === 'unread') {
            $query->whereNull('read_at');
        } elseif ($filter === 'read') {
            $query->whereNotNull('read_at');
        } elseif ($filter === 'messages') {
            $query->where('alertable_type', 'App\\Models\\Conversation');
        }

        $alerts = $query->latest()->paginate(20)->withQueryString();

        return view('alerts.index', compact('alerts', 'filter'));
    }

    /**
     * Mark an alert as read.
     */
    public function markAsRead(Alert $alert)
    {
        // Check if the alert belongs to the authenticated user
        if ($alert->user_id !== Auth::id()) {
            abort(403);
        }

        $alert->update(['read_at' => now()]);

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Alert marked as read.']);
        }

        return back()->with('success', 'Alert marked as read.');
    }

    /**
     * Mark an alert as unread.
     */
    public function markAsUnread(Alert $alert)
    {
        // Check if the alert belongs to the authenticated user
        if ($alert->user_id !== Auth::id()) {
            abort(403);
        }

        $alert->update(['read_at' => null]);

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Alert marked as unread.']);
        }

        return back()->with('success', 'Alert marked as unread.');
    }

    /**
     * Mark all alerts as read.
     */
    public function markAllAsRead()
    {
        $user = Auth::user();
        $user->alerts()->whereNull('read_at')->update(['read_at' => now()]);

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'All alerts marked as read.']);
        }

        return back()->with('success', 'All alerts marked as read.');
    }

    /**
     * Clear all alerts for the authenticated user.
     */
    public function clearAll()
    {
        $user = Auth::user();
        $user->alerts()->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'All alerts cleared.']);
        }

        return back()->with('success', 'All alerts cleared.');
    }

    /**
     * Remove the specified alert from storage.
     */
    public function destroy(Alert $alert)
    {
        // Check if the alert belongs to the authenticated user
        if ($alert->user_id !== Auth::id()) {
            abort(403);
        }

        $alert->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Alert deleted successfully.']);
        }

        return back()->with('success', 'Alert deleted successfully.');
    }
}
