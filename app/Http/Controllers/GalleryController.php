<?php

namespace App\Http\Controllers;

use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class GalleryController extends Controller
{
    /**
     * Display a listing of the media.
     */
    public function index(): View
    {
        // Get all media items
        $mediaItems = Media::with('user')
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
                'file_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'title' => $request->title,
                'description' => $request->description,
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
        if ($media->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        // Delete the file
        \Illuminate\Support\Facades\Storage::disk('public')->delete($media->file_path);

        // Delete the record
        $media->delete();

        return redirect()->route('gallery.index')
            ->with('success', 'Media deleted successfully.');
    }
}
