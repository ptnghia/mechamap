# 🔧 MechaMap - Hướng Dẫn Copilot

> **Dự án**: MechaMap - Nền tảng Forum Cộng đồng Kỹ thuật Cơ khí  
> **Tech Stack**: Laravel 10 (Backend) + Next.js 15 (Frontend)  
> **Domain**: Mechanical Engineering, CAD/CAM, Manufacturing Technology

---

## 🗣️ Ngôn Ngữ & Quy Ước Chung

### Ngôn ngữ phản hồi:
- **Comments, giải thích, thảo luận**: Tiếng Việt rõ ràng, chuyên nghiệp
- **Biến, hàm, class names**: Tiếng Anh theo chuẩn Laravel/Next.js
- **Database columns**: Snake_case (Laravel convention)
- **Frontend variables**: camelCase (TypeScript convention)

### Thuật ngữ chuyên ngành cơ khí:
- **Thread titles**: Có thể chứa thuật ngữ kỹ thuật tiếng Anh (CAD, CNC, FEA, v.v.)
- **Categories**: Mechanical Design, Manufacturing, Materials, Automation, v.v.
- **Content**: Ưu tiên tiếng Việt, thuật ngữ kỹ thuật giữ nguyên tiếng Anh

---

## 🎯 Cấu Trúc Dự Án MechaMap

### Backend (Laravel)
```
app/
├── Models/               # Eloquent Models
│   ├── User.php         # Người dùng, kỹ sư
│   ├── Thread.php       # Chủ đề thảo luận
│   ├── Post.php         # Bài viết trong thread
│   ├── Comment.php      # Bình luận
│   ├── Category.php     # Danh mục kỹ thuật
│   ├── Bookmark.php     # Đánh dấu yêu thích
│   └── Alert.php        # Thông báo
├── Http/Controllers/     # API Controllers
├── Http/Requests/       # Form Validation
├── Services/            # Business Logic
└── Policies/            # Authorization Rules
```

### Frontend (Next.js)
```
src/
├── app/                 # App Router (Next.js 15)
├── components/          # React Components
│   ├── forum/          # Forum-specific components
│   ├── ui/             # Reusable UI components
│   └── layout/         # Layout components
├── lib/                # Utilities
├── hooks/              # Custom React hooks
└── types/              # TypeScript definitions
```

---

## 🔧 Laravel Backend Guidelines

### 1. Models & Database Design

#### Quy tắc đặt tên models forum:
```php
// ✅ Đúng - Models theo domain forum
class Thread extends Model {
    protected $fillable = [
        'title', 'content', 'user_id', 'category_id',
        'is_pinned', 'is_locked', 'view_count'
    ];
    
    // Quan hệ với người tạo thread
    public function author(): BelongsTo {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    // Quan hệ với danh mục kỹ thuật
    public function category(): BelongsTo {
        return $this->belongsTo(Category::class);
    }
    
    // Các bài viết trong thread
    public function posts(): HasMany {
        return $this->hasMany(Post::class)->orderBy('created_at');
    }
}

class Category extends Model {
    protected $fillable = [
        'name', 'slug', 'description', 'icon',
        'parent_id', 'sort_order', 'is_active'
    ];
    
    // Danh mục cơ khí có thể có danh mục con
    public function children(): HasMany {
        return $this->hasMany(Category::class, 'parent_id');
    }
    
    public function parent(): BelongsTo {
        return $this->belongsTo(Category::class, 'parent_id');
    }
}
```

#### Migration patterns cho forum:
```php
// ✅ Migration cho bảng threads
Schema::create('threads', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->text('content');
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->foreignId('category_id')->constrained();
    
    // Forum-specific fields
    $table->boolean('is_pinned')->default(false);
    $table->boolean('is_locked')->default(false);
    $table->unsignedInteger('view_count')->default(0);
    $table->unsignedInteger('reply_count')->default(0);
    $table->timestamp('last_activity_at')->nullable();
    
    $table->timestamps();
    
    // Indexes cho forum
    $table->index(['category_id', 'is_pinned', 'last_activity_at']);
    $table->index(['user_id', 'created_at']);
    $table->fullText(['title', 'content']); // Tìm kiếm full-text
});
```

### 2. Controllers & API Design

#### Forum Controllers pattern:
```php
// ✅ ThreadController - Xử lý các chủ đề thảo luận
class ThreadController extends Controller
{
    public function index(ThreadIndexRequest $request): JsonResponse
    {
        // Lấy danh sách thread với pagination và filter
        $threads = Thread::query()
            ->with(['author', 'category', 'latestPost.author'])
            ->when($request->category_id, fn($q) => $q->where('category_id', $request->category_id))
            ->when($request->search, fn($q) => $q->whereFullText(['title', 'content'], $request->search))
            ->orderByPinned() // Custom scope
            ->orderBy('last_activity_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $threads,
            'message' => 'Danh sách chủ đề thảo luận'
        ]);
    }

    public function store(CreateThreadRequest $request): JsonResponse
    {
        try {
            $thread = $this->threadService->createThread(
                $request->validated(),
                auth()->user()
            );

            return response()->json([
                'success' => true,
                'data' => $thread->load(['author', 'category']),
                'message' => 'Tạo chủ đề thành công'
            ], 201);
        } catch (Exception $e) {
            Log::error('Lỗi tạo thread', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tạo chủ đề'
            ], 500);
        }
    }
}
```

#### Request Validation cho forum:
```php
// ✅ CreateThreadRequest
class CreateThreadRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Kiểm tra user có quyền tạo thread trong category này không
        $category = Category::find($this->category_id);
        return $category && $this->user()->can('create', [Thread::class, $category]);
    }

    public function rules(): array
    {
        return [
            'title' => [
                'required',
                'string',
                'max:255',
                'min:10', // Thread title phải có ít nhất 10 ký tự
            ],
            'content' => [
                'required',
                'string',
                'min:50', // Nội dung chi tiết cho forum kỹ thuật
            ],
            'category_id' => [
                'required',
                'exists:categories,id',
                new ActiveCategory(), // Custom rule
            ],
            'attachments.*' => [
                'file',
                'mimes:pdf,doc,docx,dwg,step,iges', // File kỹ thuật
                'max:10240', // 10MB
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'title.min' => 'Tiêu đề phải có ít nhất 10 ký tự',
            'content.min' => 'Nội dung phải có ít nhất 50 ký tự để mô tả chi tiết vấn đề kỹ thuật',
            'attachments.*.mimes' => 'Chỉ hỗ trợ file PDF, Word, và file CAD (DWG, STEP, IGES)',
        ];
    }
}
```

### 3. Services Layer

#### ThreadService - Business Logic:
```php
// ✅ Service xử lý logic phức tạp của forum
class ThreadService
{
    public function createThread(array $data, User $author): Thread
    {
        DB::beginTransaction();
        
        try {
            // Tạo thread mới
            $thread = Thread::create([
                'title' => $data['title'],
                'content' => $data['content'],
                'user_id' => $author->id,
                'category_id' => $data['category_id'],
                'last_activity_at' => now(),
            ]);

            // Xử lý file đính kèm nếu có
            if (!empty($data['attachments'])) {
                $this->attachmentService->handleThreadAttachments(
                    $thread,
                    $data['attachments']
                );
            }

            // Tạo bài post đầu tiên (opening post)
            $thread->posts()->create([
                'content' => $data['content'],
                'user_id' => $author->id,
                'is_opening_post' => true,
            ]);

            // Cập nhật thống kê category
            $this->updateCategoryStats($thread->category_id);

            // Tạo thông báo cho followers của category
            $this->notificationService->notifyNewThread($thread);

            DB::commit();
            return $thread;
            
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function incrementViewCount(Thread $thread, User $user = null): void
    {
        // Chỉ tăng view count nếu không phải là tác giả
        if (!$user || $user->id !== $thread->user_id) {
            $thread->increment('view_count');
            
            // Cache để tránh spam view
            if ($user) {
                cache()->put(
                    "thread_viewed_{$thread->id}_{$user->id}",
                    true,
                    now()->addHour()
                );
            }
        }
    }

    private function updateCategoryStats(int $categoryId): void
    {
        // Cập nhật số lượng thread và post trong category
        $category = Category::find($categoryId);
        $category->update([
            'thread_count' => $category->threads()->count(),
            'post_count' => Post::whereIn('thread_id', 
                $category->threads()->pluck('id')
            )->count(),
        ]);
    }
}
```

### 4. Database Seeders cho Forum Cơ khí

#### CategorySeeder - Dữ liệu danh mục thực tế:
```php
class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Thiết kế Cơ khí',
                'slug' => 'thiet-ke-co-khi',
                'description' => 'Thảo luận về thiết kế sản phẩm cơ khí, nguyên lý hoạt động',
                'icon' => 'https://api.iconify.design/material-symbols:engineering.svg',
                'children' => [
                    ['name' => 'CAD/CAM Software', 'slug' => 'cad-cam-software'],
                    ['name' => 'Phân tích FEA/CFD', 'slug' => 'phan-tich-fea-cfd'],
                    ['name' => 'Thiết kế máy móc', 'slug' => 'thiet-ke-may-moc'],
                ]
            ],
            [
                'name' => 'Công nghệ Chế tạo',
                'slug' => 'cong-nghe-che-tao',
                'description' => 'Các phương pháp gia công, công nghệ sản xuất',
                'icon' => 'https://api.iconify.design/material-symbols:precision-manufacturing.svg',
                'children' => [
                    ['name' => 'CNC Machining', 'slug' => 'cnc-machining'],
                    ['name' => 'Gia công truyền thống', 'slug' => 'gia-cong-truyen-thong'],
                    ['name' => 'In 3D & Additive Manufacturing', 'slug' => 'in-3d-additive'],
                ]
            ],
            [
                'name' => 'Vật liệu Kỹ thuật',
                'slug' => 'vat-lieu-ky-thuat',
                'description' => 'Thảo luận về tính chất, ứng dụng các loại vật liệu',
                'icon' => 'https://api.iconify.design/material-symbols:science.svg',
                'children' => [
                    ['name' => 'Kim loại & Hợp kim', 'slug' => 'kim-loai-hop-kim'],
                    ['name' => 'Polymer & Composite', 'slug' => 'polymer-composite'],
                    ['name' => 'Vật liệu Smart', 'slug' => 'vat-lieu-smart'],
                ]
            ],
            [
                'name' => 'Tự động hóa & Robotics',
                'slug' => 'tu-dong-hoa-robotics',
                'description' => 'Hệ thống tự động, robot công nghiệp, IoT',
                'icon' => 'https://api.iconify.design/material-symbols:smart-toy-outline.svg',
                'children' => [
                    ['name' => 'PLC & HMI', 'slug' => 'plc-hmi'],
                    ['name' => 'Robot công nghiệp', 'slug' => 'robot-cong-nghiep'],
                    ['name' => 'Sensors & Actuators', 'slug' => 'sensors-actuators'],
                ]
            ],
        ];

        foreach ($categories as $categoryData) {
            $children = $categoryData['children'] ?? [];
            unset($categoryData['children']);

            $parent = Category::create($categoryData);

            foreach ($children as $childData) {
                Category::create([
                    ...$childData,
                    'parent_id' => $parent->id,
                    'description' => "Thảo luận về {$childData['name']}",
                    'icon' => 'https://api.iconify.design/material-symbols:topic.svg',
                ]);
            }
        }
    }
}
```

#### ThreadSeeder - Dữ liệu thread thực tế:
```php
class ThreadSeeder extends Seeder
{
    public function run(): void
    {
        $realThreads = [
            [
                'title' => 'Hỏi về tính toán độ bền trục khi có tải trọng uốn và xoắn',
                'content' => 'Mình đang thiết kế một trục truyền động có đường kính 50mm, chiều dài 2m. Trục chịu moment xoắn 1000 Nm và lực uốn 5000N ở giữa trục. Vật liệu là thép C45. Các bạn có thể hướng dẫn cách tính toán độ bền và kiểm tra ứng suất tương đương không?',
                'category' => 'Thiết kế Cơ khí',
                'author_name' => 'Nguyễn Kỹ sư',
                'view_count' => 245,
                'reply_count' => 12,
            ],
            [
                'title' => 'So sánh các phương pháp gia công CNC 3 trục và 5 trục',
                'content' => 'Công ty mình đang cân nhắc đầu tư máy CNC mới. Hiện tại có máy 3 trục nhưng gặp khó khăn khi gia công các chi tiết phức tạp. Các bạn có kinh nghiệm về CNC 5 trục có thể chia sẻ ưu nhược điểm, chi phí đầu tư và vận hành không?',
                'category' => 'CNC Machining',
                'author_name' => 'Trần Công nghệ',
                'view_count' => 189,
                'reply_count' => 8,
            ],
            [
                'title' => 'Lựa chọn vật liệu cho bánh răng hộp số ô tô',
                'content' => 'Mình đang nghiên cứu về thiết kế hộp số cho xe du lịch. Cần tư vấn về việc lựa chọn vật liệu cho bánh răng. Yêu cầu độ bền mỏi cao, chống mài mòn tốt, chi phí hợp lý. Hiện đang cân nhắc giữa thép các bon cao và thép hợp kim. Mọi người có kinh nghiệm gì không?',
                'category' => 'Vật liệu Kỹ thuật',
                'author_name' => 'Lê Automotive',
                'view_count' => 156,
                'reply_count' => 15,
            ],
        ];

        foreach ($realThreads as $threadData) {
            $category = Category::where('name', $threadData['category'])->first()
                ?? Category::whereHas('parent', fn($q) => $q->where('name', $threadData['category']))->first();
            
            if (!$category) continue;

            $user = User::where('name', $threadData['author_name'])->first()
                ?? User::factory()->create(['name' => $threadData['author_name']]);

            $thread = Thread::create([
                'title' => $threadData['title'],
                'content' => $threadData['content'],
                'user_id' => $user->id,
                'category_id' => $category->id,
                'view_count' => $threadData['view_count'],
                'reply_count' => $threadData['reply_count'],
                'last_activity_at' => now()->subHours(rand(1, 48)),
            ]);

            // Tạo opening post
            $thread->posts()->create([
                'content' => $threadData['content'],
                'user_id' => $user->id,
                'is_opening_post' => true,
            ]);

            // Tạo một số reply posts
            for ($i = 0; $i < min($threadData['reply_count'], 5); $i++) {
                $replyUser = User::factory()->create();
                $thread->posts()->create([
                    'content' => $this->generateReplyContent($threadData['title']),
                    'user_id' => $replyUser->id,
                    'created_at' => now()->subHours(rand(1, 24)),
                ]);
            }
        }
    }

    private function generateReplyContent(string $threadTitle): string
    {
        $replies = [
            'Cảm ơn bạn đã chia sẻ vấn đề thú vị. Theo kinh nghiệm của mình...',
            'Mình có gặp tình huống tương tự. Bạn đã thử phương pháp này chưa?',
            'Có tài liệu tham khảo về vấn đề này không? Mình muốn tìm hiểu thêm.',
            'Theo tiêu chuẩn JIS/ANSI thì nên tính toán như thế này...',
            'Bạn có thể chia sẻ thêm về điều kiện làm việc cụ thể không?',
        ];

        return $replies[array_rand($replies)] . ' ' . fake()->paragraph(2);
    }
}
```

### 5. Authorization & Policies

#### ThreadPolicy - Phân quyền forum:
```php
class ThreadPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // Ai cũng có thể xem danh sách thread
    }

    public function view(User $user, Thread $thread): bool
    {
        // Kiểm tra quyền xem thread theo category
        return $thread->category->is_public || 
               $user->can('view', $thread->category);
    }

    public function create(User $user, Category $category): bool
    {
        // Kiểm tra user đã verify email và có quyền post trong category
        return $user->hasVerifiedEmail() && 
               !$user->is_banned &&
               $category->is_active &&
               $user->can('post', $category);
    }

    public function update(User $user, Thread $thread): bool
    {
        // Chỉ tác giả hoặc moderator mới sửa được
        return $user->id === $thread->user_id || 
               $user->hasRole(['moderator', 'admin']);
    }

    public function delete(User $user, Thread $thread): bool
    {
        return $user->hasRole(['moderator', 'admin']) ||
               ($user->id === $thread->user_id && $thread->posts()->count() <= 1);
    }

    public function pin(User $user, Thread $thread): bool
    {
        return $user->hasRole(['moderator', 'admin']);
    }

    public function lock(User $user, Thread $thread): bool
    {
        return $user->hasRole(['moderator', 'admin']);
    }
}
```

---

## 🚀 Next.js Frontend Guidelines

### 1. TypeScript cho Forum Components

#### Forum-specific Types:
```typescript
// types/forum.ts - Type definitions cho forum
export interface Thread {
  id: number;
  title: string;
  content: string;
  author: User;
  category: Category;
  isPinned: boolean;
  isLocked: boolean;
  viewCount: number;
  replyCount: number;
  lastActivityAt: string;
  createdAt: string;
  posts?: Post[];
}

export interface Post {
  id: number;
  content: string;
  author: User;
  threadId: number;
  isOpeningPost: boolean;
  createdAt: string;
  updatedAt: string;
}

export interface Category {
  id: number;
  name: string;
  slug: string;
  description: string;
  icon: string;
  parentId?: number;
  children?: Category[];
  threadCount: number;
  postCount: number;
  isActive: boolean;
}

export interface CreateThreadData {
  title: string;
  content: string;
  categoryId: number;
  attachments?: File[];
}

export interface ThreadFilters {
  categoryId?: number;
  search?: string;
  sortBy?: 'latest' | 'popular' | 'most_replies';
  timeRange?: 'day' | 'week' | 'month' | 'all';
}
```

#### Forum API Services:
```typescript
// services/forum.service.ts
export class ForumService {
  static async getThreads(filters: ThreadFilters = {}): Promise<PaginatedResponse<Thread>> {
    const params = new URLSearchParams();
    
    Object.entries(filters).forEach(([key, value]) => {
      if (value !== undefined) {
        params.append(key, value.toString());
      }
    });

    return apiClient.get<PaginatedResponse<Thread>>(`/threads?${params}`);
  }

  static async getThread(id: number): Promise<Thread> {
    return apiClient.get<Thread>(`/threads/${id}`);
  }

  static async createThread(data: CreateThreadData): Promise<Thread> {
    const formData = new FormData();
    formData.append('title', data.title);
    formData.append('content', data.content);
    formData.append('category_id', data.categoryId.toString());
    
    // Xử lý file đính kèm
    if (data.attachments) {
      data.attachments.forEach((file, index) => {
        formData.append(`attachments[${index}]`, file);
      });
    }

    return apiClient.post<Thread>('/threads', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    });
  }

  static async getCategories(): Promise<Category[]> {
    return apiClient.get<Category[]>('/categories');
  }

  static async getCategoryStats(categoryId: number): Promise<CategoryStats> {
    return apiClient.get<CategoryStats>(`/categories/${categoryId}/stats`);
  }
}
```

### 2. Forum Components

#### ThreadList Component:
```typescript
// components/forum/ThreadList.tsx
interface ThreadListProps {
  filters?: ThreadFilters;
  showCategory?: boolean;
  className?: string;
}

export function ThreadList({ filters = {}, showCategory = true, className }: ThreadListProps) {
  const { 
    data: threadsData, 
    isLoading, 
    error,
    fetchNextPage,
    hasNextPage,
    isFetchingNextPage
  } = useInfiniteQuery({
    queryKey: ['threads', filters],
    queryFn: ({ pageParam = 1 }) => 
      ForumService.getThreads({ ...filters, page: pageParam }),
    getNextPageParam: (lastPage) => 
      lastPage.hasMorePages ? lastPage.currentPage + 1 : undefined,
  });

  if (isLoading) return <ThreadListSkeleton />;
  if (error) return <ErrorMessage error={error} />;

  const threads = threadsData?.pages.flatMap(page => page.data) ?? [];

  return (
    <div className={cn("space-y-4", className)}>
      {threads.map((thread) => (
        <ThreadCard
          key={thread.id}
          thread={thread}
          showCategory={showCategory}
        />
      ))}
      
      {hasNextPage && (
        <Button
          onClick={() => fetchNextPage()}
          loading={isFetchingNextPage}
          variant="outline"
          className="w-full"
        >
          Tải thêm chủ đề
        </Button>
      )}
    </div>
  );
}
```

#### ThreadCard Component:
```typescript
// components/forum/ThreadCard.tsx
interface ThreadCardProps {
  thread: Thread;
  showCategory?: boolean;
  className?: string;
}

export function ThreadCard({ thread, showCategory = true, className }: ThreadCardProps) {
  const router = useRouter();
  
  const handleClick = () => {
    router.push(`/forum/threads/${thread.id}`);
  };

  return (
    <Card 
      className={cn(
        "p-4 hover:shadow-md transition-shadow cursor-pointer",
        thread.isPinned && "bg-blue-50 border-blue-200",
        className
      )}
      onClick={handleClick}
    >
      <div className="flex items-start gap-4">
        {/* User Avatar */}
        <Avatar className="h-10 w-10">
          <AvatarImage src={thread.author.avatar} />
          <AvatarFallback>
            {thread.author.name.charAt(0)}
          </AvatarFallback>
        </Avatar>

        <div className="flex-1 min-w-0">
          {/* Thread Header */}
          <div className="flex items-center gap-2 mb-2">
            {thread.isPinned && (
              <Pin className="h-4 w-4 text-blue-600" />
            )}
            {thread.isLocked && (
              <Lock className="h-4 w-4 text-gray-500" />
            )}
            {showCategory && (
              <Badge variant="secondary" className="text-xs">
                {thread.category.name}
              </Badge>
            )}
          </div>

          {/* Thread Title */}
          <h3 className="font-semibold text-gray-900 mb-1 line-clamp-2">
            {thread.title}
          </h3>

          {/* Thread Content Preview */}
          <p className="text-gray-600 text-sm line-clamp-2 mb-3">
            {stripHtml(thread.content).substring(0, 150)}...
          </p>

          {/* Thread Meta */}
          <div className="flex items-center gap-4 text-sm text-gray-500">
            <span className="flex items-center gap-1">
              <User className="h-4 w-4" />
              {thread.author.name}
            </span>
            <span className="flex items-center gap-1">
              <Eye className="h-4 w-4" />
              {formatNumber(thread.viewCount)} lượt xem
            </span>
            <span className="flex items-center gap-1">
              <MessageSquare className="h-4 w-4" />
              {formatNumber(thread.replyCount)} phản hồi
            </span>
            <span className="flex items-center gap-1">
              <Clock className="h-4 w-4" />
              {formatTimeAgo(thread.lastActivityAt)}
            </span>
          </div>
        </div>
      </div>
    </Card>
  );
}
```

#### CreateThreadForm Component:
```typescript
// components/forum/CreateThreadForm.tsx
const createThreadSchema = z.object({
  title: z.string()
    .min(10, 'Tiêu đề phải có ít nhất 10 ký tự')
    .max(255, 'Tiêu đề không được quá 255 ký tự'),
  content: z.string()
    .min(50, 'Nội dung phải có ít nhất 50 ký tự để mô tả chi tiết vấn đề'),
  categoryId: z.number().min(1, 'Vui lòng chọn danh mục'),
});

type CreateThreadFormData = z.infer<typeof createThreadSchema>;

interface CreateThreadFormProps {
  onSuccess?: (thread: Thread) => void;
  onCancel?: () => void;
}

export function CreateThreadForm({ onSuccess, onCancel }: CreateThreadFormProps) {
  const [attachments, setAttachments] = useState<File[]>([]);
  const { data: categories } = useQuery({
    queryKey: ['categories'],
    queryFn: ForumService.getCategories,
  });

  const form = useForm<CreateThreadFormData>({
    resolver: zodResolver(createThreadSchema),
    defaultValues: {
      title: '',
      content: '',
      categoryId: 0,
    },
  });

  const createThreadMutation = useMutation({
    mutationFn: ForumService.createThread,
    onSuccess: (thread) => {
      toast.success('Tạo chủ đề thành công!');
      onSuccess?.(thread);
    },
    onError: (error) => {
      toast.error('Có lỗi xảy ra khi tạo chủ đề');
      console.error('Create thread error:', error);
    },
  });

  const onSubmit = (data: CreateThreadFormData) => {
    createThreadMutation.mutate({
      ...data,
      attachments: attachments.length > 0 ? attachments : undefined,
    });
  };

  return (
    <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-6">
      {/* Category Selection */}
      <FormField
        control={form.control}
        name="categoryId"
        render={({ field }) => (
          <FormItem>
            <FormLabel>Danh mục <span className="text-red-500">*</span></FormLabel>
            <Select onValueChange={(value) => field.onChange(parseInt(value))}>
              <FormControl>
                <SelectTrigger>
                  <SelectValue placeholder="Chọn danh mục phù hợp" />
                </SelectTrigger>
              </FormControl>
              <SelectContent>
                {categories?.map((category) => (
                  <SelectItem key={category.id} value={category.id.toString()}>
                    <div className="flex items-center gap-2">
                      <img src={category.icon} alt="" className="h-4 w-4" />
                      {category.name}
                    </div>
                  </SelectItem>
                ))}
              </SelectContent>
            </Select>
            <FormMessage />
          </FormItem>
        )}
      />

      {/* Thread Title */}
      <FormField
        control={form.control}
        name="title"
        render={({ field }) => (
          <FormItem>
            <FormLabel>Tiêu đề <span className="text-red-500">*</span></FormLabel>
            <FormControl>
              <Input
                placeholder="Mô tả rõ ràng vấn đề cần thảo luận..."
                {...field}
              />
            </FormControl>
            <FormDescription>
              Tiêu đề nên mô tả rõ ràng, cụ thể vấn đề kỹ thuật cần thảo luận
            </FormDescription>
            <FormMessage />
          </FormItem>
        )}
      />

      {/* Thread Content */}
      <FormField
        control={form.control}
        name="content"
        render={({ field }) => (
          <FormItem>
            <FormLabel>Nội dung <span className="text-red-500">*</span></FormLabel>
            <FormControl>
              <RichTextEditor
                value={field.value}
                onChange={field.onChange}
                placeholder="Mô tả chi tiết vấn đề, cung cấp thông số kỹ thuật, hình ảnh..."
                minHeight={200}
              />
            </FormControl>
            <FormDescription>
              Cung cấp thông tin chi tiết: thông số kỹ thuật, điều kiện làm việc, yêu cầu cụ thể
            </FormDescription>
            <FormMessage />
          </FormItem>
        )}
      />

      {/* File Attachments */}
      <div>
        <label className="block text-sm font-medium mb-2">
          File đính kèm
        </label>
        <FileUpload
          accept=".pdf,.doc,.docx,.dwg,.step,.iges"
          multiple
          maxSize={10 * 1024 * 1024} // 10MB
          onFilesChange={setAttachments}
        />
        <p className="text-sm text-gray-500 mt-1">
          Hỗ trợ: PDF, Word, DWG, STEP, IGES (tối đa 10MB mỗi file)
        </p>
      </div>

      {/* Form Actions */}
      <div className="flex justify-end gap-4">
        <Button
          type="button"
          variant="outline"
          onClick={onCancel}
        >
          Hủy
        </Button>
        <Button
          type="submit"
          loading={createThreadMutation.isPending}
        >
          Tạo chủ đề
        </Button>
      </div>
    </form>
  );
}
```

### 3. Forum Hooks

#### useThread Hook:
```typescript
// hooks/useThread.ts
export function useThread(threadId: number) {
  return useQuery({
    queryKey: ['thread', threadId],
    queryFn: () => ForumService.getThread(threadId),
    staleTime: 5 * 60 * 1000, // 5 phút
  });
}

export function useThreadActions(threadId: number) {
  const queryClient = useQueryClient();

  const incrementView = useMutation({
    mutationFn: () => ForumService.incrementThreadView(threadId),
    onSuccess: () => {
      // Cập nhật view count trong cache
      queryClient.setQueryData(['thread', threadId], (old: Thread) => ({
        ...old,
        viewCount: old.viewCount + 1,
      }));
    },
  });

  const bookmarkThread = useMutation({
    mutationFn: () => ForumService.bookmarkThread(threadId),
    onSuccess: () => {
      queryClient.invalidateQueries(['bookmarks']);
      toast.success('Đã thêm vào danh sách yêu thích');
    },
  });

  const pinThread = useMutation({
    mutationFn: () => ForumService.pinThread(threadId),
    onSuccess: () => {
      queryClient.invalidateQueries(['thread', threadId]);
      queryClient.invalidateQueries(['threads']);
      toast.success('Đã ghim chủ đề');
    },
  });

  return {
    incrementView,
    bookmarkThread,
    pinThread,
  };
}
```

---

## 💡 Performance & SEO

### 1. Forum SEO Optimization:
```typescript
// app/forum/threads/[id]/page.tsx
export async function generateMetadata({ params }: { params: { id: string } }): Promise<Metadata> {
  const thread = await ForumService.getThread(parseInt(params.id));
  
  return {
    title: `${thread.title} | MechaMap Forum`,
    description: stripHtml(thread.content).substring(0, 160),
    keywords: [
      thread.category.name,
      'mechanical engineering',
      'forum',
      'kỹ thuật cơ khí',
      ...extractKeywords(thread.title + ' ' + thread.content)
    ].join(', '),
    openGraph: {
      title: thread.title,
      description: stripHtml(thread.content).substring(0, 200),
      type: 'article',
      publishedTime: thread.createdAt,
      modifiedTime: thread.updatedAt,
      authors: [thread.author.name],
      section: thread.category.name,
    },
    robots: {
      index: true,
      follow: true,
    },
  };
}
```

### 2. Performance Monitoring:
```typescript
// lib/performance.ts
export function trackForumPerformance() {
  // Track forum-specific metrics
  useEffect(() => {
    // Track thread load time
    const startTime = performance.now();
    
    return () => {
      const loadTime = performance.now() - startTime;
      if (loadTime > 1000) { // > 1 second
        logger.warn('Slow thread load', { loadTime });
      }
    };
  }, []);
}

// Custom hook cho forum performance
export function useForumAnalytics() {
  const trackThreadView = useCallback((threadId: number) => {
    // Track với analytics service
    gtag('event', 'thread_view', {
      thread_id: threadId,
      category: 'forum_engagement',
    });
  }, []);

  const trackThreadCreate = useCallback((categoryId: number) => {
    gtag('event', 'thread_create', {
      category_id: categoryId,
      category: 'forum_engagement',
    });
  }, []);

  return { trackThreadView, trackThreadCreate };
}
```

---

## ✅ Testing Guidelines

### API Testing cho Forum:
```php
// ✅ Feature Test cho ThreadController
class ThreadControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_thread_in_allowed_category(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['is_active' => true]);
        
        $threadData = [
            'title' => 'Test thread về thiết kế bánh răng',
            'content' => 'Nội dung chi tiết về thiết kế bánh răng trụ răng thẳng với module 2mm, số răng 30. Cần tư vấn về việc chọn vật liệu và phương pháp gia công phù hợp...',
            'category_id' => $category->id,
        ];

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/threads', $threadData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'data' => ['id', 'title', 'author', 'category'],
                'message'
            ]);

        $this->assertDatabaseHas('threads', [
            'title' => $threadData['title'],
            'user_id' => $user->id,
            'category_id' => $category->id,
        ]);
    }

    public function test_thread_title_must_be_descriptive(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/threads', [
                'title' => 'Help', // Quá ngắn
                'content' => 'I need help with something',
                'category_id' => $category->id,
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title']);
    }

    public function test_mechanical_terms_allowed_in_title(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $threadData = [
            'title' => 'Hỏi về CNC machining cho chi tiết có tolerances ±0.01mm',
            'content' => 'Mình cần gia công chi tiết có tolerances rất chặt ±0.01mm bằng CNC. Các bạn có kinh nghiệm về việc setup máy và chọn dao phù hợp không?',
            'category_id' => $category->id,
        ];

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/threads', $threadData);

        $response->assertStatus(201);
    }
}
```

### Frontend Testing:
```typescript
// __tests__/components/forum/ThreadCard.test.tsx
import { render, screen, fireEvent } from '@testing-library/react';
import { ThreadCard } from '@/components/forum/ThreadCard';

const mockThread: Thread = {
  id: 1,
  title: 'Test thread về thiết kế bánh răng',
  content: 'Nội dung test chi tiết về thiết kế bánh răng...',
  author: {
    id: 1,
    name: 'Test Engineer',
    avatar: '/test-avatar.jpg',
  },
  category: {
    id: 1,
    name: 'Thiết kế Cơ khí',
    slug: 'thiet-ke-co-khi',
  },
  isPinned: false,
  isLocked: false,
  viewCount: 150,
  replyCount: 5,
  lastActivityAt: '2024-01-01T12:00:00Z',
  createdAt: '2024-01-01T10:00:00Z',
};

describe('ThreadCard', () => {
  it('displays thread information correctly', () => {
    render(<ThreadCard thread={mockThread} />);
    
    expect(screen.getByText(mockThread.title)).toBeInTheDocument();
    expect(screen.getByText(mockThread.author.name)).toBeInTheDocument();
    expect(screen.getByText(mockThread.category.name)).toBeInTheDocument();
    expect(screen.getByText('150 lượt xem')).toBeInTheDocument();
    expect(screen.getByText('5 phản hồi')).toBeInTheDocument();
  });

  it('shows pinned indicator for pinned threads', () => {
    const pinnedThread = { ...mockThread, isPinned: true };
    render(<ThreadCard thread={pinnedThread} />);
    
    expect(screen.getByTestId('pin-icon')).toBeInTheDocument();
  });

  it('navigates to thread page on click', () => {
    const mockPush = jest.fn();
    jest.mock('next/navigation', () => ({
      useRouter: () => ({ push: mockPush }),
    }));

    render(<ThreadCard thread={mockThread} />);
    
    fireEvent.click(screen.getByTestId('thread-card'));
    expect(mockPush).toHaveBeenCalledWith('/forum/threads/1');
  });
});
```

---

## 📊 Documentation Structure

### 1. Cấu trúc thư mục docs đã được tổ chức:
```
docs/
├── testing/                    # Tất cả file test
│   ├── api-tests/             # API testing scripts
│   ├── browser-tests/         # Browser automation tests  
│   ├── integration-tests/     # Integration testing
│   ├── manual-tests/          # Manual testing procedures
│   ├── performance-tests/     # Performance benchmarks
│   ├── simple-tests/          # Basic functionality tests
│   ├── thread-tests/          # Forum thread specific tests
│   ├── utilities/             # Testing utilities
│   └── verification-tests/    # Verification procedures
├── reports/
│   └── completion/            # Project completion reports
└── development/
    └── backups/               # Development backups
```

### 2. Quy trình test cho forum:
- **Unit Tests**: Models, Services, Utilities
- **Feature Tests**: API endpoints, Business logic
- **Integration Tests**: Frontend-Backend integration
- **E2E Tests**: Complete user workflows
- **Performance Tests**: Load testing, Response times

---

## 🔒 Security Considerations

### Forum-specific Security:
```php
// ✅ Content Security cho forum posts
class PostSanitizer
{
    public static function sanitizeContent(string $content): string
    {
        // Remove dangerous HTML tags
        $allowed = '<p><br><strong><em><ul><ol><li><blockquote><code><pre>';
        $content = strip_tags($content, $allowed);
        
        // Prevent XSS trong technical content
        $content = htmlspecialchars($content, ENT_QUOTES, 'UTF-8');
        
        // Allow mathematical expressions (common in engineering)
        $content = self::allowMathExpressions($content);
        
        return $content;
    }

    private static function allowMathExpressions(string $content): string
    {
        // Allow mathematical symbols và units
        $mathPatterns = [
            '/&amp;plusmn;/' => '±',
            '/&amp;deg;/' => '°',
            '/&amp;mu;/' => 'μ',
            '/&amp;sigma;/' => 'σ',
        ];
        
        return str_replace(array_keys($mathPatterns), array_values($mathPatterns), $content);
    }
}
```

---

Đây là file `copilot-instructions.md` hoàn chỉnh cho dự án MechaMap, bao gồm:

✅ **Laravel Backend** - Models, Controllers, Services, Policies cho forum cơ khí  
✅ **Next.js Frontend** - Components, hooks, services cho forum interface  
✅ **Real Data Examples** - Dữ liệu thực tế về categories và threads kỹ thuật  
✅ **Testing Guidelines** - Test patterns cho cả backend và frontend  
✅ **Performance & Security** - Optimizations và security cho forum  
✅ **Documentation Structure** - Tham chiếu đến cấu trúc docs/ đã tổ chức

File này sẽ giúp Copilot hiểu rõ context của dự án MechaMap và sinh code phù hợp với domain mechanical engineering forum.

