<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Showcase;
use App\Models\Thread;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShowcaseController extends Controller
{
    /**
     * Display a listing of showcases.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $showcases = Showcase::with(['user', 'showcaseable'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.showcases.index', compact('showcases'));
    }

    /**
     * Show the form for creating a new showcase.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.showcases.create');
    }

    /**
     * Store a newly created showcase in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'showcaseable_id' => 'required|integer',
            'showcaseable_type' => 'required|string|in:thread,post',
            'description' => 'nullable|string',
            'order' => 'nullable|integer',
        ]);

        // Check if item exists
        $showcaseableType = $request->showcaseable_type === 'thread' 
            ? Thread::class 
            : Post::class;
        
        $showcaseableId = $request->showcaseable_id;
        
        $item = $showcaseableType::find($showcaseableId);
        
        if (!$item) {
            return redirect()->back()
                ->with('error', 'Không tìm thấy nội dung cần thêm vào showcase.')
                ->withInput();
        }

        // Check if already in showcase
        $existingShowcase = Showcase::where('showcaseable_id', $showcaseableId)
            ->where('showcaseable_type', $showcaseableType)
            ->first();

        if ($existingShowcase) {
            return redirect()->back()
                ->with('error', 'Nội dung này đã được thêm vào showcase.')
                ->withInput();
        }

        // Create showcase
        $showcase = new Showcase();
        $showcase->user_id = Auth::id();
        $showcase->showcaseable_id = $showcaseableId;
        $showcase->showcaseable_type = $showcaseableType;
        $showcase->description = $request->description;
        $showcase->order = $request->order ?? 0;
        $showcase->save();

        return redirect()->route('admin.showcases.index')
            ->with('success', 'Thêm vào showcase thành công.');
    }

    /**
     * Remove the specified showcase from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $showcase = Showcase::findOrFail($id);
        $showcase->delete();

        return redirect()->route('admin.showcases.index')
            ->with('success', 'Xóa khỏi showcase thành công.');
    }
}
