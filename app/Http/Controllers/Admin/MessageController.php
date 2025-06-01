<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\Message;
use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class MessageController extends Controller
{
    /**
     * Hiển thị trang cấu hình messages
     */
    public function index(): View
    {
        // Lấy các cài đặt messages
        $settings = Setting::getGroup('messages');

        // Breadcrumbs
        $breadcrumbs = [
            ['title' => 'Cấu hình messages', 'url' => route('admin.messages.index')]
        ];

        return view('admin.messages.index', compact('settings', 'breadcrumbs'));
    }

    /**
     * Cập nhật cấu hình messages
     */
    public function updateSettings(Request $request)
    {
        // Validate dữ liệu
        $validator = Validator::make($request->all(), [
            'enable_private_messages' => ['boolean'],
            'enable_message_encryption' => ['boolean'],
            'enable_message_search' => ['boolean'],
            'enable_file_attachments' => ['boolean'],
            'enable_message_reactions' => ['boolean'],
            'enable_message_editing' => ['boolean'],
            'max_conversation_participants' => ['required', 'integer', 'min:2', 'max:100'],
            'max_message_length' => ['required', 'integer', 'min:100', 'max:10000'],
            'max_attachments_per_message' => ['required', 'integer', 'min:0', 'max:10'],
            'max_attachment_size_mb' => ['required', 'integer', 'min:1', 'max:100'],
            'allowed_attachment_types' => ['required', 'array'],
            'message_retention_days' => ['required', 'integer', 'min:0', 'max:3650'],
            'auto_delete_read_messages' => ['boolean'],
            'enable_message_notifications' => ['boolean'],
            'enable_conversation_typing_indicators' => ['boolean'],
            'enable_read_receipts' => ['boolean'],
            'enable_online_status' => ['boolean'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            // Cập nhật các settings
            $messagesSettings = [
                'enable_private_messages' => $request->boolean('enable_private_messages'),
                'enable_message_encryption' => $request->boolean('enable_message_encryption'),
                'enable_message_search' => $request->boolean('enable_message_search'),
                'enable_file_attachments' => $request->boolean('enable_file_attachments'),
                'enable_message_reactions' => $request->boolean('enable_message_reactions'),
                'enable_message_editing' => $request->boolean('enable_message_editing'),
                'max_conversation_participants' => $request->input('max_conversation_participants'),
                'max_message_length' => $request->input('max_message_length'),
                'max_attachments_per_message' => $request->input('max_attachments_per_message'),
                'max_attachment_size_mb' => $request->input('max_attachment_size_mb'),
                'allowed_attachment_types' => $request->input('allowed_attachment_types', []),
                'message_retention_days' => $request->input('message_retention_days'),
                'auto_delete_read_messages' => $request->boolean('auto_delete_read_messages'),
                'enable_message_notifications' => $request->boolean('enable_message_notifications'),
                'enable_conversation_typing_indicators' => $request->boolean('enable_conversation_typing_indicators'),
                'enable_read_receipts' => $request->boolean('enable_read_receipts'),
                'enable_online_status' => $request->boolean('enable_online_status'),
            ];

            foreach ($messagesSettings as $key => $value) {
                Setting::updateOrCreate(
                    ['group' => 'messages', 'key' => $key],
                    ['value' => is_array($value) ? json_encode($value) : $value]
                );
            }

            // Xóa cache nếu có
            Cache::forget('message_settings');

            return back()->with('success', 'Cấu hình messages đã được cập nhật thành công!');
        } catch (\Exception $e) {
            Log::error('Message settings update failed: ' . $e->getMessage());
            return back()->with('error', 'Có lỗi xảy ra khi cập nhật cấu hình: ' . $e->getMessage());
        }
    }

    /**
     * Hiển thị danh sách conversations để quản lý
     */
    public function conversations(Request $request): View
    {
        $query = Conversation::with(['participants.user', 'lastMessage'])
            ->withCount('messages');

        // Tìm kiếm
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('participants.user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Lọc theo trạng thái
        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'active') {
                $query->whereHas('messages', function ($q) {
                    $q->where('created_at', '>=', now()->subDays(7));
                });
            } elseif ($status === 'inactive') {
                $query->whereDoesntHave('messages', function ($q) {
                    $q->where('created_at', '>=', now()->subDays(30));
                });
            }
        }

        $conversations = $query->orderBy('updated_at', 'desc')->paginate(20);

        $breadcrumbs = [
            ['title' => 'Cấu hình messages', 'url' => route('admin.messages.index')],
            ['title' => 'Quản lý conversations', 'url' => route('admin.messages.conversations')]
        ];

        return view('admin.messages.conversations', compact('conversations', 'breadcrumbs'));
    }

    /**
     * Hiển thị chi tiết conversation
     */
    public function showConversation($id): View
    {
        $conversation = Conversation::with([
            'participants.user',
            'messages.sender',
            'messages.media'
        ])->findOrFail($id);

        $breadcrumbs = [
            ['title' => 'Cấu hình messages', 'url' => route('admin.messages.index')],
            ['title' => 'Quản lý conversations', 'url' => route('admin.messages.conversations')],
            ['title' => 'Chi tiết conversation', 'url' => route('admin.messages.conversation', $id)]
        ];

        return view('admin.messages.conversation', compact('conversation', 'breadcrumbs'));
    }

    /**
     * Xóa conversation
     */
    public function deleteConversation($id)
    {
        try {
            $conversation = Conversation::findOrFail($id);

            // Xóa tất cả messages trong conversation
            $conversation->messages()->delete();

            // Xóa conversation
            $conversation->delete();

            return redirect()->route('admin.messages.conversations')
                ->with('success', 'Conversation đã được xóa thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra khi xóa conversation: ' . $e->getMessage());
        }
    }

    /**
     * Hiển thị thống kê messages
     */
    public function statistics(): View
    {
        // Thống kê cơ bản
        $totalConversations = Conversation::count();
        $totalMessages = Message::count();
        $activeConversations = Conversation::whereHas('messages', function ($q) {
            $q->where('created_at', '>=', now()->subDays(7));
        })->count();
        $messagesToday = Message::whereDate('created_at', today())->count();
        $messagesThisWeek = Message::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();
        $messagesThisMonth = Message::whereMonth('created_at', now()->month)->count();

        // Thống kê chi tiết
        $averageMessagesPerConversation = $totalConversations > 0
            ? round($totalMessages / $totalConversations, 2)
            : 0;

        // Top người dùng gửi messages nhiều nhất
        $topMessageSenders = Message::select('user_id')
            ->selectRaw('COUNT(*) as message_count')
            ->with('user:id,name,email')
            ->groupBy('user_id')
            ->orderBy('message_count', 'desc')
            ->limit(10)
            ->get();

        // Thống kê theo loại conversation
        $conversationStats = [
            'private' => Conversation::whereHas('participants', function ($q) {
                $q->havingRaw('COUNT(*) = 2');
            })->count(),
            'group' => Conversation::whereHas('participants', function ($q) {
                $q->havingRaw('COUNT(*) > 2');
            })->count(),
        ];

        // Thống kê theo thời gian (7 ngày gần nhất)
        $dailyStats = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dailyStats[] = [
                'date' => $date->format('d/m'),
                'messages' => Message::whereDate('created_at', $date)->count(),
                'conversations' => Conversation::whereDate('created_at', $date)->count()
            ];
        }

        // Thống kê attachments
        $attachmentStats = [
            'total_with_attachments' => Message::whereHas('media')->count(),
            'total_attachments' => \App\Models\Media::where('mediable_type', Message::class)->count(),
            'average_size_mb' => \App\Models\Media::where('mediable_type', Message::class)->avg('size') / 1024 / 1024,
        ];

        $stats = [
            'total_conversations' => $totalConversations,
            'total_messages' => $totalMessages,
            'active_conversations' => $activeConversations,
            'messages_today' => $messagesToday,
            'messages_this_week' => $messagesThisWeek,
            'messages_this_month' => $messagesThisMonth,
            'average_messages_per_conversation' => $averageMessagesPerConversation,
            'top_message_senders' => $topMessageSenders,
            'conversation_stats' => $conversationStats,
            'daily_stats' => $dailyStats,
            'attachment_stats' => $attachmentStats,
        ];

        $breadcrumbs = [
            ['title' => 'Cấu hình messages', 'url' => route('admin.messages.index')],
            ['title' => 'Thống kê', 'url' => route('admin.messages.statistics')]
        ];

        return view('admin.messages.statistics', compact('stats', 'breadcrumbs'));
    }

    /**
     * Export message statistics
     */
    public function exportStatistics(Request $request)
    {
        try {
            $format = $request->input('format', 'csv');
            $dateFrom = $request->input('date_from', now()->subMonth()->format('Y-m-d'));
            $dateTo = $request->input('date_to', now()->format('Y-m-d'));

            // Lấy dữ liệu trong khoảng thời gian
            $messages = Message::whereBetween('created_at', [$dateFrom, $dateTo])
                ->with(['user', 'conversation'])
                ->get();

            $filename = "message_statistics_{$dateFrom}_to_{$dateTo}.{$format}";

            if ($format === 'csv') {
                return $this->exportToCsv($messages, $filename);
            } elseif ($format === 'json') {
                return $this->exportToJson($messages, $filename);
            }

            return back()->with('error', 'Format export không được hỗ trợ!');
        } catch (\Exception $e) {
            Log::error('Message export failed: ' . $e->getMessage());
            return back()->with('error', 'Có lỗi xảy ra khi export dữ liệu: ' . $e->getMessage());
        }
    }

    /**
     * Dọn dẹp messages cũ
     */
    public function cleanup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'days_old' => ['required', 'integer', 'min:1', 'max:3650'],
            'delete_read_only' => ['boolean'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $daysOld = $request->input('days_old');
            $deleteReadOnly = $request->boolean('delete_read_only');

            $query = Message::where('created_at', '<', now()->subDays($daysOld));

            if ($deleteReadOnly) {
                $query->whereNotNull('read_at');
            }

            $deletedCount = $query->count();
            $query->delete();

            return back()->with('success', "Đã xóa {$deletedCount} messages cũ thành công!");
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra khi dọn dẹp messages: ' . $e->getMessage());
        }
    }
}
