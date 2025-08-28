<?php

namespace App\Http\Controllers;

use App\Models\Media;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class GalleryController extends Controller
{
    /**
     * Display a listing of the media.
     */
    public function index(): View
    {
        // Get media items from public sources only
        $mediaItems = Media::with(['user', 'thread'])
            ->where(function ($query) {
                // Include standalone gallery uploads (not attached to threads/showcases)
                $query->where(function ($subQuery) {
                    $subQuery->whereNull('thread_id')
                        ->whereNull('mediable_type')
                        ->whereNull('mediable_id');
                });

                // Include media from public threads
                $query->orWhereHas('thread', function ($threadQuery) {
                    $threadQuery->where('moderation_status', 'approved')
                        ->where('is_spam', false)
                        ->whereNull('hidden_at')
                        ->whereNull('archived_at');
                });

                // Include media from approved showcases (using specific model check)
                $query->orWhere(function ($showcaseQuery) {
                    $showcaseQuery->where('mediable_type', 'App\\Models\\Showcase')
                        ->whereExists(function ($existsQuery) {
                            $existsQuery->select(DB::raw(1))
                                ->from('showcases')
                                ->whereColumn('showcases.id', 'media.mediable_id')
                                ->where('showcases.status', 'approved');
                        });
                });
            })
            ->latest()
            ->paginate(24);

        return view('gallery.index', compact('mediaItems'));
    }

    /**
     * Display the specified media.
     */
    public function show(Media $media): View
    {
        return view('gallery.show', compact('media'));
    }

    /**
     * Show the form for creating a new media.
     */
    public function create(): View
    {
        return view('gallery.create');
    }

    /**
     * Store a newly created media in storage.
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:jpeg,png,jpg,gif,webp,avif|max:5120',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        /** @var User $user */
        $user = Auth::user();

        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('uploads/gallery', $fileName, 'public');

            // Create media record
            $media = $user->media()->create([
                'file_name' => $fileName,
                'file_path' => $filePath,
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'file_extension' => $file->getClientOriginalExtension(),
                'file_category' => 'image',
                'title' => $request->title,
                'description' => $request->description,
                'is_public' => true,
                'is_approved' => true,
            ]);

            return redirect()->route('gallery.show', $media)
                ->with('success', 'Media uploaded successfully.');
        }

        return back()->with('error', 'Failed to upload media.');
    }

    /**
     * Remove the specified media from storage.
     */
    public function destroy(Media $media): \Illuminate\Http\RedirectResponse
    {
        // Check if the media belongs to the authenticated user
        /** @var User $currentUser */
        $currentUser = Auth::user();
        if ($media->user_id !== Auth::id() && !$currentUser->isAdmin()) {
            abort(403);
        }

        // Delete the file
        Storage::disk('public')->delete($media->file_path);

        // Delete the record
        $media->delete();

        return redirect()->route('gallery.index')
            ->with('success', 'Media deleted successfully.');
    }
}
