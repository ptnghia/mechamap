# 📋 Hệ Thống Trạng Thái Thread trong Laravel Forum

## 📖 Tổng Quan

Hệ thống forum Laravel sử dụng **hai hệ thống trạng thái song song** để quản lý threads:

1. **Project Status System** (`status` field) - Quản lý vòng đời của dự án
2. **Moderation Status System** (`moderation_status` field) - Quản lý kiểm duyệt nội dung

---

## 🏗️ Project Status System

### Trường dữ liệu
- **Tên trường**: `status`
- **Kiểu dữ liệu**: `string` (nullable)
- **Mục đích**: Theo dõi vòng đời của dự án/thread

### Các trạng thái có sẵn:

| Trạng thái | Tiếng Việt | Mô tả | Màu Badge |
|------------|------------|--------|-----------|
| `Proposed` | Đề xuất | Dự án vừa được đề xuất, chưa được phê duyệt | `bg-light text-dark` |
| `Approved` | Đã phê duyệt | Dự án đã được chấp thuận để thực hiện | `bg-success` |
| `Under Construction` | Đang thi công | Dự án đang trong quá trình xây dựng/thực hiện | `bg-warning` |
| `Completed` | Hoàn thành | Dự án đã hoàn thành | `bg-success` |
| `On Hold` | Tạm hoãn | Dự án tạm dừng vì lý do nào đó | `bg-secondary` |
| `Cancelled` | Đã hủy | Dự án bị hủy bỏ | `bg-danger` |

### Cách sử dụng:

```php
// Tạo thread với status
$thread = Thread::create([
    'title' => 'Dự án nhà ở mới',
    'content' => '...',
    'status' => 'Proposed',
    // ... other fields
]);

// Cập nhật status
$thread->update(['status' => 'Approved']);

// Kiểm tra status
if ($thread->status === 'Completed') {
    // Logic xử lý cho dự án hoàn thành
}
```

### Hiển thị trong view:

```php
<!-- resources/views/threads/edit.blade.php -->
<select class="form-select" name="status">
    <option value="">Select status</option>
    <option value="Proposed" {{ old('status', $thread->status) == 'Proposed' ? 'selected' : '' }}>Proposed</option>
    <option value="Approved" {{ old('status', $thread->status) == 'Approved' ? 'selected' : '' }}>Approved</option>
    <option value="Under Construction" {{ old('status', $thread->status) == 'Under Construction' ? 'selected' : '' }}>Under Construction</option>
    <option value="Completed" {{ old('status', $thread->status) == 'Completed' ? 'selected' : '' }}>Completed</option>
    <option value="On Hold" {{ old('status', $thread->status) == 'On Hold' ? 'selected' : '' }}>On Hold</option>
    <option value="Cancelled" {{ old('status', $thread->status) == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
</select>
```

```php
<!-- Hiển thị badge status -->
@if(isset($thread->status) && $thread->status)
<div class="mb-2 small">
    <span class="badge bg-light text-dark">
        <i class="bi bi-info-circle me-1"></i>{{ $thread->status }}
    </span>
</div>
@endif
```

---

## 🛡️ Moderation Status System

### Trường dữ liệu
- **Tên trường**: `moderation_status`
- **Kiểu dữ liệu**: `enum`
- **Mục đích**: Quản lý việc kiểm duyệt nội dung

### Các trạng thái có sẵy:

| Trạng thái | Tiếng Việt | Mô tả | Màu Badge |
|------------|------------|--------|-----------|
| `clean` | Sạch | Nội dung không có vấn đề | `bg-success` |
| `pending` | Chờ duyệt | Đang chờ kiểm duyệt | `bg-warning` |
| `approved` | Đã duyệt | Đã được phê duyệt | `bg-success` |
| `flagged` | Đã báo cáo | Có người dùng báo cáo | `bg-danger` |
| `under_review` | Đang xem xét | Đang được kiểm tra chi tiết | `bg-info` |
| `spam` | Spam | Được xác định là spam | `bg-danger` |

### Các trường liên quan:

```php
// Migration: add_moderation_states_to_threads_table.php
$table->boolean('is_flagged')->default(false);
$table->boolean('is_spam')->default(false);
$table->enum('moderation_status', ['clean', 'flagged', 'under_review', 'spam', 'approved'])->default('clean');
$table->integer('reports_count')->default(0);
$table->timestamp('flagged_at')->nullable();
$table->unsignedBigInteger('flagged_by')->nullable();
$table->text('moderation_notes')->nullable();
```

### Hiển thị trong admin:

```php
<!-- resources/views/admin/moderation/threads.blade.php -->
@php
$statusClasses = [
    'pending' => 'bg-warning',
    'approved' => 'bg-success',
    'rejected' => 'bg-danger',
    'flagged' => 'bg-danger'
];
$statusLabels = [
    'pending' => 'Chờ duyệt',
    'approved' => 'Đã duyệt',
    'rejected' => 'Từ chối',
    'flagged' => 'Đã báo cáo'
];
@endphp

<span class="badge {{ $statusClasses[$thread->moderation_status] ?? 'bg-secondary' }}">
    {{ $statusLabels[$thread->moderation_status] ?? $thread->moderation_status }}
</span>
```

---

## 🔄 Lifecycle States (Trạng thái vòng đời)

### Các trạng thái bổ sung:

```php
// Migration: add_lifecycle_states_to_threads_table.php
$table->timestamp('archived_at')->nullable();
$table->string('archived_reason')->nullable();
$table->timestamp('hidden_at')->nullable();
$table->string('hidden_reason')->nullable();
$table->softDeletes(); // deleted_at
```

### Ý nghĩa:
- **Archived**: Thread được lưu trữ (không hiển thị công khai nhưng vẫn truy cập được)
- **Hidden**: Thread bị ẩn (chỉ admin/moderator xem được)
- **Deleted**: Thread bị xóa mềm (có thể khôi phục)

---

## 🎯 Quality States (Trạng thái chất lượng)

### Các trường chất lượng:

```php
// Migration: add_quality_states_to_threads_table.php
$table->boolean('is_solved')->default(false);
$table->unsignedBigInteger('solution_comment_id')->nullable();
$table->unsignedBigInteger('solved_by')->nullable();
$table->timestamp('solved_at')->nullable();
$table->decimal('quality_score', 3, 2)->nullable();
$table->decimal('average_rating', 3, 2)->nullable();
$table->integer('ratings_count')->default(0);
$table->enum('thread_type', ['discussion', 'question', 'announcement', 'tutorial', 'showcase', 'project'])->default('discussion');
```

---

## 📊 Thống Kê Trạng Thái

### Admin có thể xem thống kê theo:

1. **Status distribution** (phân bố theo project status)
2. **Moderation status** (phân bố theo trạng thái kiểm duyệt)
3. **Thread types** (phân bố theo loại thread)
4. **Quality metrics** (điểm chất lượng, solved threads)

```php
// Controller logic để thống kê
$statusStats = Thread::select('status', DB::raw('count(*) as total'))
    ->whereNotNull('status')
    ->groupBy('status')
    ->get();

$moderationStats = Thread::select('moderation_status', DB::raw('count(*) as total'))
    ->groupBy('moderation_status')
    ->get();
```

---

## 🛠️ Quản Lý Trạng Thái

### 1. User có thể:
- Tạo thread với status `Proposed`
- Cập nhật status của thread riêng (giới hạn một số trạng thái)

### 2. Moderator/Admin có thể:
- Thay đổi `moderation_status`
- Flag/unflag threads
- Archive/hide threads
- Thay đổi `quality_score`

### 3. Hệ thống tự động:
- Đặt `moderation_status = 'clean'` mặc định cho thread mới
- Cập nhật `reports_count` khi có báo cáo
- Đặt `is_solved = true` khi có solution được chấp nhận

---

## 🎨 CSS Classes cho Status Badges

```css
/* public/css/views/threads.css */
.status-badge {
    padding: 0.25rem 0.5rem;
    border-radius: var(--border-radius-sm);
    font-size: 0.75rem;
    font-weight: 500;
}

.status-pinned {
    background: #fef3c7;
    color: #92400e;
}

.status-solved {
    background: #d1fae5;
    color: #065f46;
}

.status-locked {
    background: #fee2e2;
    color: #991b1b;
}
```

---

## 🔍 Khi Nào Sử Dụng Trạng Thái Nào?

### Project Status (`status` field):
- ✅ **Proposed**: Khi user tạo thread đề xuất dự án mới
- ✅ **Approved**: Khi admin/authority phê duyệt dự án
- ✅ **Under Construction**: Khi dự án bắt đầu thi công
- ✅ **Completed**: Khi dự án hoàn thành
- ✅ **On Hold**: Khi dự án tạm dừng
- ✅ **Cancelled**: Khi dự án bị hủy bỏ

### Moderation Status (`moderation_status` field):
- ✅ **clean**: Thread bình thường, không có vấn đề
- ✅ **pending**: Thread mới chờ kiểm duyệt
- ✅ **flagged**: Thread bị báo cáo bởi users
- ✅ **under_review**: Thread đang được moderator xem xét
- ✅ **spam**: Thread bị xác định là spam
- ✅ **approved**: Thread đã được duyệt

---

## 📝 Ví Dụ Thực Tế

### Scenario 1: Dự án mới
1. User tạo thread → `status = 'Proposed'`, `moderation_status = 'clean'`
2. Admin phê duyệt → `status = 'Approved'`
3. Bắt đầu thi công → `status = 'Under Construction'`
4. Hoàn thành → `status = 'Completed'`

### Scenario 2: Thread có vấn đề
1. User báo cáo thread → `is_flagged = true`, `reports_count++`
2. Moderator xem xét → `moderation_status = 'under_review'`
3. Quyết định: Spam → `moderation_status = 'spam'`, `is_spam = true`

### Scenario 3: Thread thảo luận
1. User tạo câu hỏi → `thread_type = 'question'`
2. Có câu trả lời hay → `is_solved = true`, `solution_comment_id = X`
3. Thread chất lượng cao → `quality_score = 4.5`

---

Hệ thống này cho phép quản lý toàn diện threads từ perspective của cả nội dung (project status) và kiểm duyệt (moderation status), đảm bảo chất lượng và tính tổ chức của forum.
