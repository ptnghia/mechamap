<?php

namespace App\Http\Controllers;

use App\Models\Thread;
use App\Models\ThreadBookmark;
use App\Models\ThreadRating;
use App\Models\Forum;
use App\Models\Tag;
use App\Services\ModerationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Controller xử lý threads cho người dùng thông thường
 * Tích hợp ModerationService để kiểm tra quyền xem threads/comments
 */
class UserThreadController extends Controller
{
    protected $moderationService;

    public function __construct(ModerationService $moderationService)
    {
        $this->moderationService = $moderationService;
    }

    /**
     * Hiển thị danh sách threads cho users (trang chủ forum).
     */
    public function index(Request $request)
    {
        $query = Thread::with(['user', 'forum', 'tags'])
            ->withCount(['allComments as comments_count', 'bookmarks', 'ratings']);

        // Chỉ hiển thị threads public visible (sử dụng ModerationService)
        $query->where(function ($q) {
            $q->where('moderation_status', 'approved')
                ->orWhere(function ($subQ) {
                    if (Auth::check()) {
                        // Nếu user đã login, cho phép xem thread của chính mình
                        $subQ->where('user_id', Auth::id());
                    }
                });
        });

        // Search functionality
        if ($request->search) {
            $query->search($request->search);
        }

        // Filter theo forum
        if ($request->forum) {
            $query->where('forum_id', $request->forum);
        }

        // Filter theo thread type
        if ($request->type && in_array($request->type, ['discussion', 'question', 'tutorial', 'showcase', 'project', 'announcement'])) {
            $query->ofType($request->type);
        }

        // Filter theo solved status (cho questions)
        if ($request->solved === 'yes') {
            $query->solved();
        } elseif ($request->solved === 'no') {
            $query->unsolved();
        }

        // Filter theo rating
        if ($request->min_rating) {
            $minRating = (float) $request->min_rating;
            if ($minRating >= 1 && $minRating <= 5) {
                $query->minRating($minRating);
            }
        }

        // Filter theo period
        if ($request->period && in_array($request->period, ['today', 'week', 'month', 'year'])) {
            $query->fromPeriod($request->period);
        }

        // Sorting
        switch ($request->sort) {
            case 'trending':
                $query->trending();
                break;
            case 'rating':
                $query->orderByDesc('average_rating');
                break;
            case 'bookmarks':
                $query->orderByDesc('bookmark_count');
                break;
            case 'views':
                $query->byPopularity();
                break;
            case 'activity':
                $query->byRecentActivity();
                break;
            case 'oldest':
                $query->oldest();
                break;
            default: // 'latest'
                $query->latest();
        }

        $threads = $query->paginate(20);

        // Sidebar data với moderation filtering
        $forums = Cache::remember('forums_with_counts', 3600, function () {
            return Forum::withCount(['threads' => function ($q) {
                $q->where('moderation_status', 'approved');
            }])->get();
        });

        $tags = Cache::remember('popular_tags', 3600, function () {
            return Tag::withCount(['threads' => function ($q) {
                $q->where('moderation_status', 'approved');
            }])
                ->having('threads_count', '>', 0)
                ->orderByDesc('threads_count')
                ->limit(20)
                ->get();
        });

        // Thread stats với moderation filtering
        $stats = [
            'total_threads' => Thread::where('moderation_status', 'approved')->count(),
            'solved_questions' => Thread::where('moderation_status', 'approved')->ofType('question')->solved()->count(),
            'top_rated' => Thread::where('moderation_status', 'approved')->where('ratings_count', '>=', 5)->avg('average_rating'),
        ];

        return view('threads.index', compact('threads', 'forums', 'tags', 'stats'));
    }

    /**
     * Hiển thị chi tiết thread.
     */
    public function show(Request $request, Thread $thread)
    {
        // Kiểm tra quyền xem thread bằng ModerationService
        if (!$this->moderationService->canViewThreadPublic($thread)) {
            // Nếu là thread của chính user và đang pending/rejected, cho phép xem với thông báo
            if (Auth::check() && $thread->user_id === Auth::id()) {
                $statusMessage = $this->getModerationStatusMessage($thread->moderation_status);
                // Load thread với thông báo trạng thái moderation
                return $this->showThreadWithModerationNotice($thread, $statusMessage);
            }

            abort(404, 'Thread không tồn tại hoặc đang chờ duyệt');
        }

        return $this->showApprovedThread($thread);
    }

    /**
     * Hiển thị thread đã được approved
     */
    private function showApprovedThread(Thread $thread)
    {
        // Increment view count (chỉ 1 lần per session)
        $viewKey = "thread_viewed_{$thread->id}_" . session()->getId();
        if (!Cache::has($viewKey)) {
            $thread->increment('view_count');
            Cache::put($viewKey, true, 3600); // 1 giờ
        }

        // Load relationships với filtering cho comments
        $thread->load([
            'user',
            'forum',
            'tags',
            'ratings' => function ($q) {
                $q->with('user')->latest();
            },
            'comments' => function ($q) {
                $q->with(['user', 'likes'])
                    ->whereNull('parent_id')
                    ->where(function ($query) {
                        $query->where('moderation_status', 'approved')
                            ->orWhere(function ($subQ) {
                                if (Auth::check()) {
                                    $subQ->where('user_id', Auth::id());
                                }
                            });
                    })
                    ->orderBy('created_at', 'asc');
            },
        ]);

        // User interactions (nếu đã login)
        $userInteractions = [];
        if (Auth::check()) {
            $userId = Auth::id();
            $userInteractions = [
                'bookmarked' => $thread->bookmarks()->where('user_id', $userId)->exists(),
                'rated' => $thread->ratings()->where('user_id', $userId)->exists(),
                'user_rating' => $thread->ratings()->where('user_id', $userId)->value('rating'),
                'can_rate' => $thread->user_id !== $userId, // Không thể rate thread của mình
            ];
        }

        // Related threads (cùng forum hoặc tags)
        $relatedThreads = Thread::with(['user', 'forum'])
            ->where('moderation_status', 'approved')
            ->where('id', '!=', $thread->id)
            ->where(function ($q) use ($thread) {
                $q->where('forum_id', $thread->forum_id)
                    ->orWhereHas('tags', function ($tagQ) use ($thread) {
                        $tagQ->whereIn('tags.id', $thread->tags->pluck('id'));
                    });
            })
            ->withCount(['allComments as comments_count', 'bookmarks', 'ratings'])
            ->orderByDesc('average_rating')
            ->limit(5)
            ->get();

        return view('threads.show', compact('thread', 'userInteractions', 'relatedThreads'));
    }

    /**
     * Hiển thị thread với thông báo trạng thái moderation
     */
    private function showThreadWithModerationNotice(Thread $thread, string $statusMessage)
    {
        $thread->load(['user', 'forum', 'tags']);

        // Không load comments cho thread chưa được approved
        $userInteractions = [
            'bookmarked' => false,
            'rated' => false,
            'user_rating' => null,
            'can_rate' => false,
        ];

        return view('threads.show', compact('thread', 'userInteractions'))
            ->with('moderationNotice', $statusMessage);
    }

    /**
     * Lấy thông báo dựa trên trạng thái moderation
     */
    private function getModerationStatusMessage(string $status): string
    {
        return match ($status) {
            'pending' => 'Thread của bạn đang chờ được duyệt bởi moderator.',
            'rejected' => 'Thread của bạn đã bị từ chối. Vui lòng kiểm tra lại nội dung và quy định diễn đàn.',
            'flagged' => 'Thread của bạn đã bị báo cáo và đang được xem xét.',
            default => 'Thread của bạn đang được xem xét.',
        };
    }

    /**
     * Hiển thị threads theo tag.
     */
    public function byTag(Request $request, Tag $tag)
    {
        $query = $tag->threads()
            ->with(['user', 'forum'])
            ->withCount(['allComments as comments_count', 'bookmarks', 'ratings'])
            ->where('moderation_status', 'approved');

        // Apply sorting
        switch ($request->sort) {
            case 'rating':
                $query->orderByDesc('average_rating');
                break;
            case 'popular':
                $query->trending();
                break;
            default:
                $query->latest();
        }

        $threads = $query->paginate(20);

        return view('threads.by-tag', compact('tag', 'threads'));
    }

    /**
     * Hiển thị threads theo forum.
     */
    public function byForum(Request $request, Forum $forum)
    {
        $query = $forum->threads()
            ->with(['user', 'tags'])
            ->withCount(['allComments as comments_count', 'bookmarks', 'ratings'])
            ->where('moderation_status', 'approved');

        // Apply filters và sorting tương tự index()
        if ($request->type) {
            $query->ofType($request->type);
        }

        if ($request->solved === 'yes') {
            $query->solved();
        } elseif ($request->solved === 'no') {
            $query->unsolved();
        }

        switch ($request->sort) {
            case 'trending':
                $query->trending();
                break;
            case 'rating':
                $query->orderByDesc('average_rating');
                break;
            default:
                $query->latest();
        }

        $threads = $query->paginate(20);

        // Forum stats
        $forumStats = [
            'total_threads' => $forum->threads()->where('moderation_status', 'approved')->count(),
            'solved_questions' => $forum->threads()->where('moderation_status', 'approved')->ofType('question')->solved()->count(),
            'avg_rating' => $forum->threads()->where('moderation_status', 'approved')->where('ratings_count', '>=', 3)->avg('average_rating'),
        ];

        return view('threads.by-forum', compact('forum', 'threads', 'forumStats'));
    }

    /**
     * Hiển thị top rated threads.
     */
    public function topRated(Request $request)
    {
        $period = $request->period ?? 'all';

        // Sử dụng ModerationService để lấy top rated threads
        $threads = $this->moderationService->getTopRatedThreadsPublic($period, 20);

        return view('threads.top-rated', compact('threads', 'period'));
    }

    /**
     * Hiển thị trending threads.
     */
    public function trending(Request $request)
    {
        $period = $request->period ?? 'week';

        // Sử dụng ModerationService để lấy trending threads
        $threads = $this->moderationService->getTrendingThreadsPublic($period, 20);

        return view('threads.trending', compact('threads', 'period'));
    }

    /**
     * Search threads với advanced options.
     */
    public function search(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:2|max:100',
        ]);

        $searchTerm = $request->q;

        $query = Thread::with(['user', 'forum', 'tags'])
            ->withCount(['allComments as comments_count', 'bookmarks', 'ratings'])
            ->where('moderation_status', 'approved')
            ->search($searchTerm);

        // Advanced filters
        if ($request->forum_id) {
            $query->where('forum_id', $request->forum_id);
        }

        if ($request->thread_type) {
            $query->ofType($request->thread_type);
        }

        if ($request->min_rating) {
            $query->minRating((float) $request->min_rating);
        }

        if ($request->has_solution) {
            $query->solved();
        }

        $threads = $query->paginate(20);

        // Search stats
        $stats = [
            'total_results' => $threads->total(),
            'search_term' => $searchTerm,
        ];

        return view('threads.search', compact('threads', 'stats', 'searchTerm'));
    }
}
