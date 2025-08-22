<?php

namespace App\Http\Controllers;

use App\Models\Translation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

/**
 * Development-only Translation Management Controller
 *
 * ⚠️ WARNING: This controller is for DEVELOPMENT ONLY!
 * ⚠️ Remove this file and its routes before deploying to production!
 * ⚠️ This controller has no authentication and should never be accessible in production!
 */
class DevTranslationController extends Controller
{
    /**
     * Display the development translation management page
     */
    public function index()
    {
        // Double-check environment safety
        if (!$this->isDevelopmentEnvironment()) {
            abort(404, 'This feature is only available in development environment.');
        }

        // Get statistics for dashboard
        $stats = [
            'total_translations' => Translation::count(),
            'vietnamese_translations' => Translation::where('locale', 'vi')->count(),
            'english_translations' => Translation::where('locale', 'en')->count(),
            'total_groups' => Translation::distinct('group_name')->count(),
        ];

        // Get all groups for dropdown
        $groups = Translation::select('group_name')
            ->distinct()
            ->orderBy('group_name')
            ->pluck('group_name');

        return view('dev.translations.index', compact('stats', 'groups'));
    }

    /**
     * Get translations data for DataTables (AJAX)
     */
    public function getData(Request $request)
    {
        if (!$this->isDevelopmentEnvironment()) {
            return response()->json(['error' => 'Not available in production'], 403);
        }

        $query = Translation::where('locale', 'vi')
            ->select(['id', 'key', 'content', 'group_name', 'is_active', 'created_at', 'updated_at']);

        // Handle DataTables search
        if ($request->has('search') && !empty($request->search['value'])) {
            $searchValue = $request->search['value'];
            $query->where(function ($q) use ($searchValue) {
                $q->where('key', 'LIKE', "%{$searchValue}%")
                  ->orWhere('content', 'LIKE', "%{$searchValue}%")
                  ->orWhere('group_name', 'LIKE', "%{$searchValue}%");
            });
        }

        // Handle DataTables ordering
        if ($request->has('order')) {
            $columns = ['id', 'key', 'content', 'group_name', 'is_active', 'created_at'];
            $orderColumn = $columns[$request->order[0]['column']] ?? 'key';
            $orderDirection = $request->order[0]['dir'] ?? 'asc';
            $query->orderBy($orderColumn, $orderDirection);
        } else {
            $query->orderBy('group_name')->orderBy('key');
        }

        // Get total count before pagination
        $totalRecords = Translation::where('locale', 'vi')->count();
        $filteredRecords = $query->count();

        // Handle DataTables pagination
        if ($request->has('start') && $request->has('length')) {
            $query->skip($request->start)->take($request->length);
        }

        $translations = $query->get();

        // Format data for DataTables
        $data = $translations->map(function ($translation) {
            return [
                'id' => $translation->id,
                'key' => $translation->key,
                'content' => $translation->content,
                'group_name' => $translation->group_name,
                'is_active' => $translation->is_active ? 'Active' : 'Inactive',
                'created_at' => $translation->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $translation->updated_at->format('Y-m-d H:i:s'),
                'actions' => $this->generateActionButtons($translation),
            ];
        });

        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data,
        ]);
    }

    /**
     * Store a new translation (AJAX)
     */
    public function store(Request $request)
    {
        if (!$this->isDevelopmentEnvironment()) {
            return response()->json(['error' => 'Not available in production'], 403);
        }

        $validator = Validator::make($request->all(), [
            'key' => 'required|string|max:255',
            'content_vi' => 'required|string',
            'content_en' => 'required|string',
            'group_name' => 'required|string|max:100',
        ], [
            'key.required' => 'Translation key is required',
            'key.max' => 'Translation key cannot exceed 255 characters',
            'content_vi.required' => 'Vietnamese content is required',
            'content_en.required' => 'English content is required',
            'group_name.required' => 'Group name is required',
            'group_name.max' => 'Group name cannot exceed 100 characters',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        // Check if key already exists for either locale
        $existingVi = Translation::where('key', $request->key)->where('locale', 'vi')->exists();
        $existingEn = Translation::where('key', $request->key)->where('locale', 'en')->exists();

        if ($existingVi || $existingEn) {
            return response()->json([
                'success' => false,
                'message' => 'Translation key already exists for ' . ($existingVi && $existingEn ? 'both locales' : ($existingVi ? 'Vietnamese' : 'English')),
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Create Vietnamese translation
            $translationVi = Translation::create([
                'key' => $request->key,
                'content' => $request->content_vi,
                'locale' => 'vi',
                'group_name' => $request->group_name,
                'is_active' => true,
                'created_by' => 1, // System user for dev
                'updated_by' => 1,
            ]);

            // Create English translation
            $translationEn = Translation::create([
                'key' => $request->key,
                'content' => $request->content_en,
                'locale' => 'en',
                'group_name' => $request->group_name,
                'is_active' => true,
                'created_by' => 1, // System user for dev
                'updated_by' => 1,
            ]);

            // Record history for both translations (skip if table doesn't exist)
            try {
                $translationVi->recordHistory(null, $request->content_vi, 'Created via dev interface');
                $translationEn->recordHistory(null, $request->content_en, 'Created via dev interface');
            } catch (\Exception $historyError) {
                // Continue without history if table doesn't exist
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Translation added successfully for both Vietnamese and English!',
                'data' => [
                    'key' => $request->key,
                    'group_name' => $request->group_name,
                    'vi_content' => $request->content_vi,
                    'en_content' => $request->content_en,
                    'vi_id' => $translationVi->id,
                    'en_id' => $translationEn->id,
                ],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error creating translation: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update an existing translation (AJAX)
     */
    public function update(Request $request, $id)
    {
        if (!$this->isDevelopmentEnvironment()) {
            return response()->json(['error' => 'Not available in production'], 403);
        }

        $translation = Translation::where('locale', 'vi')->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'content' => 'required|string',
            'group_name' => 'required|string|max:100',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $oldContent = $translation->content;

            $translation->update([
                'content' => $request->content,
                'group_name' => $request->group_name,
                'is_active' => $request->boolean('is_active', true),
                'updated_by' => 1,
            ]);

            // Record history if content changed
            if ($oldContent !== $request->content) {
                $translation->recordHistory($oldContent, $request->content, 'Updated via dev interface');
            }

            return response()->json([
                'success' => true,
                'message' => 'Translation updated successfully!',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating translation: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a translation (AJAX)
     */
    public function destroy($id)
    {
        if (!$this->isDevelopmentEnvironment()) {
            return response()->json(['error' => 'Not available in production'], 403);
        }

        try {
            $translation = Translation::where('locale', 'vi')->findOrFail($id);
            $translation->delete();

            return response()->json([
                'success' => true,
                'message' => 'Translation deleted successfully!',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting translation: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Check if we're in development environment
     */
    private function isDevelopmentEnvironment(): bool
    {
        return app()->environment('local') || config('app.debug') === true;
    }

    /**
     * Auto-generate missing English translations (AJAX)
     */
    public function generateMissingEnglish(Request $request)
    {
        if (!$this->isDevelopmentEnvironment()) {
            return response()->json(['error' => 'Not available in production'], 403);
        }

        try {
            DB::beginTransaction();

            // Get all Vietnamese translations that don't have English counterparts
            $missingTranslations = Translation::where('locale', 'vi')
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('translations as t2')
                        ->whereRaw('t2.key = translations.key')
                        ->where('t2.locale', 'en');
                })
                ->where('is_active', true)
                ->get();

            $created = 0;
            $errors = [];

            foreach ($missingTranslations as $viTranslation) {
                try {
                    $englishContent = $this->translateToEnglish($viTranslation->content);

                    Translation::create([
                        'key' => $viTranslation->key,
                        'content' => $englishContent,
                        'locale' => 'en',
                        'group_name' => $viTranslation->group_name,
                        'namespace' => $viTranslation->namespace,
                        'is_active' => true,
                        'created_by' => 1, // System user for dev
                        'updated_by' => 1,
                    ]);

                    $created++;
                } catch (\Exception $e) {
                    $errors[] = "Error creating English translation for key '{$viTranslation->key}': " . $e->getMessage();
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Successfully created {$created} English translations.",
                'created' => $created,
                'errors' => $errors,
                'total_missing' => $missingTranslations->count(),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error generating English translations: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Translate Vietnamese text to English using mapping dictionary
     */
    private function translateToEnglish($vietnameseText)
    {
        // Comprehensive Vietnamese to English translation mapping
        $translations = [
            // Common UI terms
            'Trang chủ' => 'Home',
            'Đăng nhập' => 'Login',
            'Đăng ký' => 'Register',
            'Đăng xuất' => 'Logout',
            'Tìm kiếm' => 'Search',
            'Thông báo' => 'Notification',
            'Tin nhắn' => 'Message',
            'Hồ sơ' => 'Profile',
            'Cài đặt' => 'Settings',
            'Quản lý' => 'Management',
            'Danh sách' => 'List',
            'Chi tiết' => 'Details',
            'Thêm mới' => 'Add New',
            'Chỉnh sửa' => 'Edit',
            'Xóa' => 'Delete',
            'Lưu' => 'Save',
            'Hủy' => 'Cancel',
            'Xác nhận' => 'Confirm',
            'Gửi' => 'Send',
            'Tải lên' => 'Upload',
            'Tải xuống' => 'Download',
            'Xem' => 'View',
            'Chia sẻ' => 'Share',
            'Thích' => 'Like',
            'Bình luận' => 'Comment',
            'Trả lời' => 'Reply',
            'Theo dõi' => 'Follow',
            'Đóng' => 'Close',
            'Mở' => 'Open',
            'Tạo' => 'Create',
            'Cập nhật' => 'Update',
            'Sửa' => 'Edit',
            'Xem' => 'View',
            'Quay lại' => 'Back',
            'Tiếp theo' => 'Next',
            'Gửi' => 'Submit',
            'Lọc' => 'Filter',
            'Sắp xếp' => 'Sort',
            'Đang tải' => 'Loading',
            'Lỗi' => 'Error',
            'Thành công' => 'Success',
            'Cảnh báo' => 'Warning',
            'Thông tin' => 'Info',
            'Trạng thái' => 'Status',
            'Thao tác' => 'Actions',
            'Thứ tự' => 'Order',
            'Hoàn thành' => 'Completed',
            'ID' => 'ID',

            // Forum & Community terms
            'Diễn đàn' => 'Forums',
            'Chủ đề' => 'Thread',
            'Bài viết' => 'Post',
            'Danh mục' => 'Category',
            'Thành viên' => 'Member',
            'Cộng đồng' => 'Community',
            'Thảo luận' => 'Discussion',
            'Câu hỏi' => 'Question',
            'Câu trả lời' => 'Answer',
            'Đánh giá' => 'Rating',
            'Điểm' => 'Points',
            'Cấp độ' => 'Level',
            'Huy hiệu' => 'Badge',
            'Thành tích' => 'Achievement',

            // Marketplace terms
            'Marketplace' => 'Marketplace',
            'Sản phẩm' => 'Product',
            'Mua' => 'Buy',
            'Bán' => 'Sell',
            'Giá' => 'Price',
            'Đơn hàng' => 'Order',
            'Thanh toán' => 'Payment',
            'Giao hàng' => 'Delivery',
            'Đánh giá' => 'Review',
            'Nhà cung cấp' => 'Supplier',
            'Nhà sản xuất' => 'Manufacturer',
            'Thương hiệu' => 'Brand',

            // Engineering terms
            'Kỹ thuật' => 'Engineering',
            'Cơ khí' => 'Mechanical',
            'Thiết kế' => 'Design',
            'Dự án' => 'Project',
            'Showcase' => 'Showcase',
            'Công cụ' => 'Tools',
            'Phần mềm' => 'Software',
            'Tài liệu' => 'Document',
            'Hướng dẫn' => 'Guide',
            'Giải pháp' => 'Solution',

            // Time & Date terms
            'Hôm nay' => 'Today',
            'Hôm qua' => 'Yesterday',
            'Tuần này' => 'This Week',
            'Tháng này' => 'This Month',
            'Năm nay' => 'This Year',
            'Tất cả thời gian' => 'All Time',
            'Khoảng thời gian' => 'Time Period',

            // Activity terms
            'Hoạt động' => 'Activity',
            'Tạo chủ đề' => 'Thread Created',
            'Đăng bình luận' => 'Comment Posted',
            'Đánh dấu chủ đề' => 'Thread Bookmarked',
            'Đánh giá chủ đề' => 'Thread Rated',
            'Theo dõi người dùng' => 'User Followed',
            'Chuỗi hoạt động' => 'Activity Streak',
            'Tất cả hoạt động' => 'All Activities',
            'Chưa có hoạt động nào' => 'No Activities Yet',
            'Bắt đầu tham gia' => 'Start Participating',

            // Common phrases
            'Chưa có' => 'None',
            'Không có' => 'No',
            'Có' => 'Yes',
            'Tất cả' => 'All',
            'Mới' => 'New',
            'Cũ' => 'Old',
            'Phổ biến' => 'Popular',
            'Xu hướng' => 'Trending',
            'Mới nhất' => 'Latest',
            'Cũ nhất' => 'Oldest',
        ];

        // Check for exact match first
        if (isset($translations[$vietnameseText])) {
            return $translations[$vietnameseText];
        }

        // Try partial matches for longer texts
        foreach ($translations as $vi => $en) {
            if (str_contains($vietnameseText, $vi)) {
                return str_replace($vi, $en, $vietnameseText);
            }
        }

        // If no translation found, return with [EN] prefix
        return "[EN] {$vietnameseText}";
    }

    /**
     * Generate action buttons for DataTable rows
     */
    private function generateActionButtons($translation): string
    {
        return '
            <div class="btn-group btn-group-sm" role="group">
                <button type="button" class="btn btn-outline-primary btn-edit"
                        data-id="' . $translation->id . '"
                        data-key="' . htmlspecialchars($translation->key) . '"
                        data-content="' . htmlspecialchars($translation->content) . '"
                        data-group="' . htmlspecialchars($translation->group_name) . '"
                        data-active="' . ($translation->is_active ? '1' : '0') . '"
                        title="Edit">
                    <i class="fas fa-edit"></i>
                </button>
                <button type="button" class="btn btn-outline-danger btn-delete"
                        data-id="' . $translation->id . '"
                        data-key="' . htmlspecialchars($translation->key) . '"
                        title="Delete">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        ';
    }
}
