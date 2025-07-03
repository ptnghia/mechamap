<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Showcase;
use App\Models\Thread;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ShowcaseController extends Controller
{
    /**
     * Hiển thị danh sách showcases
     */
    public function index(Request $request): View
    {
        $query = Showcase::with(['user', 'showcaseable']);

        // Tìm kiếm
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('title', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        }

        // Lọc theo trạng thái
        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'featured') {
                $query->whereNotNull('featured_at');
            } elseif ($status === 'active') {
                $query->where('status', 'published');
            } elseif ($status === 'inactive') {
                $query->where('status', '!=', 'published');
            }
        }

        // Lọc theo loại
        if ($request->filled('type')) {
            $type = $request->input('type');
            $query->where('showcaseable_type', $type);
        }

        // Sắp xếp
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $showcases = $query->paginate(20)->withQueryString();

        // Thống kê nhanh
        $stats = [
            'total' => Showcase::count(),
            'featured' => Showcase::whereNotNull('featured_at')->count(),
            'active' => Showcase::where('status', 'published')->count(),
            'threads' => Showcase::where('showcaseable_type', Thread::class)->count(),
            'posts' => Showcase::where('showcaseable_type', Post::class)->count(),
        ];

        // Breadcrumbs
        $breadcrumbs = [
            ['title' => 'Quản lý Showcases', 'url' => route('admin.showcases.index')]
        ];

        return view('admin.showcases.index', compact('showcases', 'stats', 'breadcrumbs'));
    }

    /**
     * Hiển thị form tạo showcase mới
     */
    public function create(): View
    {
        // Lấy danh sách threads và posts có thể showcase
        // Sử dụng polymorphic relationship thay vì many-to-many
        $availableThreads = Thread::where('is_featured', false)
            ->whereDoesntHave('showcase') // Sử dụng morphOne relationship
            ->with('user')
            ->orderBy('view_count', 'desc')
            ->limit(20)
            ->get();

        $availablePosts = Post::whereDoesntHave('showcase') // Sử dụng morphOne relationship
            ->with(['user', 'thread'])
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        // Breadcrumbs
        $breadcrumbs = [
            ['title' => 'Quản lý Showcases', 'url' => route('admin.showcases.index')],
            ['title' => 'Tạo showcase mới', 'url' => route('admin.showcases.create')]
        ];

        return view('admin.showcases.create', compact('availableThreads', 'availablePosts', 'breadcrumbs'));
    }

    /**
     * Lưu showcase mới
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'showcaseable_id' => ['required', 'integer'],
            'showcaseable_type' => ['required', 'string', 'in:' . Thread::class . ',' . Post::class],
            'order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_featured' => ['boolean'],
            'is_active' => ['boolean'],
            'featured_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
            'tags' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            // Kiểm tra xem item đã được showcase chưa
            $exists = Showcase::where('showcaseable_id', $request->showcaseable_id)
                ->where('showcaseable_type', $request->showcaseable_type)
                ->exists();

            if ($exists) {
                return back()->with('error', 'Item này đã được showcase rồi!')->withInput();
            }

            $showcaseData = [
                'title' => $request->title,
                'description' => $request->description,
                'showcaseable_id' => $request->showcaseable_id,
                'showcaseable_type' => $request->showcaseable_type,
                'order' => $request->order ?? 0,
                'is_featured' => $request->boolean('is_featured'),
                'is_active' => $request->boolean('is_active', true),
                'user_id' => Auth::id(),
            ];

            // Xử lý upload ảnh featured
            if ($request->hasFile('featured_image')) {
                $image = $request->file('featured_image');
                $filename = 'showcase_' . time() . '.' . $image->getClientOriginalExtension();
                $image->storeAs('public/showcases', $filename);
                $showcaseData['featured_image'] = 'showcases/' . $filename;
            }

            // Xử lý tags
            if ($request->filled('tags')) {
                $showcaseData['tags'] = json_encode(array_map('trim', explode(',', $request->tags)));
            }

            $showcase = Showcase::create($showcaseData);

            Log::info('Showcase created', [
                'id' => $showcase->id,
                'admin' => Auth::user()->email
            ]);

            return redirect()->route('admin.showcases.index')
                ->with('success', 'Showcase đã được tạo thành công!');
        } catch (\Exception $e) {
            Log::error('Showcase creation failed: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Không thể tạo showcase: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified showcase from storage.
     */
    public function destroy($id)
    {
        try {
            $showcase = Showcase::findOrFail($id);

            // Xóa ảnh featured nếu có
            if ($showcase->featured_image) {
                Storage::delete('public/' . $showcase->featured_image);
            }

            Log::info('Showcase deleted', [
                'id' => $showcase->id,
                'admin' => Auth::user()->email
            ]);

            $showcase->delete();

            return redirect()->route('admin.showcases.index')
                ->with('success', 'Showcase đã được xóa thành công!');
        } catch (\Exception $e) {
            Log::error('Showcase deletion failed: ' . $e->getMessage());
            return back()->with('error', 'Không thể xóa showcase: ' . $e->getMessage());
        }
    }

    /**
     * Hiển thị form chỉnh sửa showcase
     */
    public function edit($id): View
    {
        $showcase = Showcase::with(['user', 'showcaseable'])->findOrFail($id);

        // Lấy danh sách threads và posts có thể showcase (bao gồm cả item hiện tại)
        $availableThreads = Thread::where(function ($query) use ($showcase) {
            $query->where('is_featured', false)
                ->whereDoesntHave('showcases')
                ->orWhere('id', $showcase->showcaseable_type === Thread::class ? $showcase->showcaseable_id : 0);
        })
            ->with('user')
            ->orderBy('view_count', 'desc')
            ->limit(20)
            ->get();

        $availablePosts = Post::where(function ($query) use ($showcase) {
            $query->whereDoesntHave('showcases')
                ->orWhere('id', $showcase->showcaseable_type === Post::class ? $showcase->showcaseable_id : 0);
        })
            ->with(['user', 'thread'])
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        // Breadcrumbs
        $breadcrumbs = [
            ['title' => 'Quản lý Showcases', 'url' => route('admin.showcases.index')],
            ['title' => 'Chỉnh sửa showcase', 'url' => route('admin.showcases.edit', $id)]
        ];

        return view('admin.showcases.edit', compact('showcase', 'availableThreads', 'availablePosts', 'breadcrumbs'));
    }

    /**
     * Cập nhật showcase
     */
    public function update(Request $request, $id)
    {
        $showcase = Showcase::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'showcaseable_id' => ['required', 'integer'],
            'showcaseable_type' => ['required', 'string', 'in:' . Thread::class . ',' . Post::class],
            'order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_featured' => ['boolean'],
            'is_active' => ['boolean'],
            'featured_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
            'tags' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            // Kiểm tra xem item đã được showcase chưa (trừ showcase hiện tại)
            $exists = Showcase::where('showcaseable_id', $request->showcaseable_id)
                ->where('showcaseable_type', $request->showcaseable_type)
                ->where('id', '!=', $id)
                ->exists();

            if ($exists) {
                return back()->with('error', 'Item này đã được showcase rồi!')->withInput();
            }

            $showcaseData = [
                'title' => $request->title,
                'description' => $request->description,
                'showcaseable_id' => $request->showcaseable_id,
                'showcaseable_type' => $request->showcaseable_type,
                'order' => $request->order ?? 0,
                'is_featured' => $request->boolean('is_featured'),
                'is_active' => $request->boolean('is_active', true),
            ];

            // Xử lý upload ảnh featured mới
            if ($request->hasFile('featured_image')) {
                // Xóa ảnh cũ
                if ($showcase->featured_image) {
                    Storage::delete('public/' . $showcase->featured_image);
                }

                $image = $request->file('featured_image');
                $filename = 'showcase_' . time() . '.' . $image->getClientOriginalExtension();
                $image->storeAs('public/showcases', $filename);
                $showcaseData['featured_image'] = 'showcases/' . $filename;
            }

            // Xử lý tags
            if ($request->filled('tags')) {
                $showcaseData['tags'] = json_encode(array_map('trim', explode(',', $request->tags)));
            } else {
                $showcaseData['tags'] = null;
            }

            $showcase->update($showcaseData);

            Log::info('Showcase updated', [
                'id' => $showcase->id,
                'admin' => Auth::user()->email
            ]);

            return redirect()->route('admin.showcases.index')
                ->with('success', 'Showcase đã được cập nhật thành công!');
        } catch (\Exception $e) {
            Log::error('Showcase update failed: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Không thể cập nhật showcase: ' . $e->getMessage()]);
        }
    }

    /**
     * Hiển thị chi tiết showcase
     */
    public function show($id): View
    {
        $showcase = Showcase::with(['user', 'showcaseable'])->findOrFail($id);

        // Breadcrumbs
        $breadcrumbs = [
            ['title' => 'Quản lý Showcases', 'url' => route('admin.showcases.index')],
            ['title' => 'Chi tiết showcase', 'url' => route('admin.showcases.show', $id)]
        ];

        return view('admin.showcases.show', compact('showcase', 'breadcrumbs'));
    }

    /**
     * Toggle trạng thái featured
     */
    public function toggleFeatured($id)
    {
        try {
            $showcase = Showcase::findOrFail($id);
            $showcase->update(['is_featured' => !$showcase->is_featured]);

            Log::info('Showcase featured status toggled', [
                'id' => $showcase->id,
                'is_featured' => $showcase->is_featured,
                'admin' => Auth::user()->email
            ]);

            $status = $showcase->is_featured ? 'featured' : 'unfeatured';
            return back()->with('success', "Showcase đã được {$status} thành công!");
        } catch (\Exception $e) {
            Log::error('Failed to toggle showcase featured status: ' . $e->getMessage());
            return back()->with('error', 'Có lỗi xảy ra khi thay đổi trạng thái featured.');
        }
    }

    /**
     * Toggle trạng thái active
     */
    public function toggleActive($id)
    {
        try {
            $showcase = Showcase::findOrFail($id);
            $showcase->update(['is_active' => !$showcase->is_active]);

            Log::info('Showcase active status toggled', [
                'id' => $showcase->id,
                'is_active' => $showcase->is_active,
                'admin' => Auth::user()->email
            ]);

            $status = $showcase->is_active ? 'kích hoạt' : 'vô hiệu hóa';
            return back()->with('success', "Showcase đã được {$status} thành công!");
        } catch (\Exception $e) {
            Log::error('Failed to toggle showcase active status: ' . $e->getMessage());
            return back()->with('error', 'Có lỗi xảy ra khi thay đổi trạng thái active.');
        }
    }

    /**
     * Cập nhật thứ tự showcases
     */
    public function updateOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'showcases' => ['required', 'array'],
            'showcases.*.id' => ['required', 'integer', 'exists:showcases,id'],
            'showcases.*.order' => ['required', 'integer', 'min:0'],
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()]);
        }

        try {
            foreach ($request->showcases as $showcaseData) {
                Showcase::where('id', $showcaseData['id'])
                    ->update(['order' => $showcaseData['order']]);
            }

            Log::info('Showcase order updated', [
                'admin' => Auth::user()->email
            ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Failed to update showcase order: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Bulk actions cho showcases
     */
    public function bulkAction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'action' => ['required', 'string', 'in:activate,deactivate,feature,unfeature,delete'],
            'showcase_ids' => ['required', 'array'],
            'showcase_ids.*' => ['integer', 'exists:showcases,id'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        try {
            $showcaseIds = $request->showcase_ids;
            $action = $request->action;
            $count = 0;

            switch ($action) {
                case 'activate':
                    $count = Showcase::whereIn('id', $showcaseIds)->update(['is_active' => true]);
                    break;
                case 'deactivate':
                    $count = Showcase::whereIn('id', $showcaseIds)->update(['is_active' => false]);
                    break;
                case 'feature':
                    $count = Showcase::whereIn('id', $showcaseIds)->update(['is_featured' => true]);
                    break;
                case 'unfeature':
                    $count = Showcase::whereIn('id', $showcaseIds)->update(['is_featured' => false]);
                    break;
                case 'delete':
                    $showcases = Showcase::whereIn('id', $showcaseIds)->get();
                    foreach ($showcases as $showcase) {
                        if ($showcase->featured_image) {
                            Storage::delete('public/' . $showcase->featured_image);
                        }
                    }
                    $count = Showcase::whereIn('id', $showcaseIds)->delete();
                    break;
            }

            Log::info('Showcase bulk action performed', [
                'admin' => Auth::user()->email,
                'action' => $action,
                'affected_count' => $count
            ]);

            return back()->with('success', "Đã thực hiện thao tác {$action} cho {$count} showcases!");
        } catch (\Exception $e) {
            Log::error('Showcase bulk action failed: ' . $e->getMessage());
            return back()->with('error', 'Có lỗi xảy ra khi thực hiện thao tác: ' . $e->getMessage());
        }
    }
}
