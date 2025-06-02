# Tính Năng Showcase - MechaMap

## Tổng Quan

Tính năng **Showcase** cho phép người dùng trình bày và chia sẻ các dự án, sản phẩm hoặc kiến thức chuyên môn của mình với cộng đồng. Đây là một hệ thống tương tác đầy đủ với khả năng bình luận, thích và theo dõi.

## Cấu Trúc Database

### Các Bảng Chính

1. **showcases** - Lưu trữ thông tin showcase chính
2. **showcase_comments** - Hệ thống bình luận với support nested replies
3. **showcase_likes** - Lưu trữ likes/thích cho các showcase
4. **showcase_follows** - Hệ thống theo dõi tác giả showcase

### Models

- `App\Models\Showcase` - Model chính cho showcase
- `App\Models\ShowcaseComment` - Bình luận và replies
- `App\Models\ShowcaseLike` - Likes/thích
- `App\Models\ShowcaseFollow` - Theo dõi người dùng

## Tính Năng

### 1. Quản Lý Showcase
- Tạo, sửa, xóa showcase cá nhân
- Upload hình ảnh và file đính kèm
- Tổ chức showcase theo thứ tự ưu tiên

### 2. Trang Chi Tiết Showcase
- Layout 2 cột (9-3) với nội dung chính và sidebar
- Hiển thị đầy đủ thông tin: title, description, content, attachments
- Thông tin tác giả và thống kê tương tác

### 3. Hệ Thống Tương Tác

#### Like/Thích
- User có thể like/unlike showcase
- Đếm tổng số likes
- AJAX support cho tương tác mượt mà

#### Follow/Theo Dõi
- Theo dõi tác giả của showcase
- Nhận thông báo khi có showcase mới
- Quản lý danh sách đang theo dõi

#### Comments/Bình Luận
- Bình luận đa cấp (nested replies)
- Real-time comment form
- Quyền xóa bình luận (owner + admin)

### 4. Trang Public Showcase
- Hiển thị tất cả showcase công khai
- Filter và search theo nội dung
- Pagination cho hiệu suất tốt

## Routes

```php
// Public showcase
GET /public-showcase -> showcase.public

// User showcase management  
GET /showcase -> showcase.index
POST /showcase -> showcase.store
GET /showcase/create -> showcase.create
GET /showcase/{showcase} -> showcase.show
PUT /showcase/{showcase} -> showcase.update
DELETE /showcase/{showcase} -> showcase.destroy
GET /showcase/{showcase}/edit -> showcase.edit

// Interactions
POST /showcase/{showcase}/like -> showcase.toggle-like
POST /showcase/{showcase}/follow -> showcase.toggle-follow
POST /showcase/{showcase}/comment -> showcase.comment
DELETE /showcase/comment/{comment} -> showcase.comment.delete

// Utilities
POST /showcase/upload-temp -> showcase.upload.temp
GET /showcase/attachment/{attachment}/download -> showcase.download
POST /showcase/{showcase}/attach-to-thread -> showcase.attach-to-thread
```

## Controller Methods

### ShowcaseController

- `index()` - Quản lý showcase cá nhân
- `publicShowcase()` - Trang public showcase
- `show()` - Chi tiết showcase với tương tác
- `create()`, `store()`, `edit()`, `update()`, `destroy()` - CRUD
- `toggleLike()` - Like/unlike showcase
- `toggleFollow()` - Follow/unfollow tác giả
- `addComment()`, `deleteComment()` - Quản lý comments

## Views

### Layouts
- `resources/views/showcase/index.blade.php` - Quản lý cá nhân
- `resources/views/showcase/public.blade.php` - Trang public
- `resources/views/showcase/show.blade.php` - Chi tiết showcase với tương tác

### Features
- **Responsive Design**: Bootstrap 5 với layout 2 cột
- **AJAX Interactions**: Like, follow không reload trang
- **Comment System**: Nested replies với form dynamically
- **File Management**: Upload, preview, download attachments

## Tính Năng Bảo Mật

- **Authorization**: Chỉ owner mới được edit/delete
- **Validation**: Input validation cho tất cả forms
- **File Security**: Kiểm tra file type và size
- **SQL Injection**: Sử dụng Eloquent ORM
- **XSS Protection**: Escape output, CSRF tokens

## Cách Sử Dụng

### Cho Developer

1. **Tạo Showcase Mới**:
```php
$showcase = auth()->user()->showcaseItems()->create([
    'showcaseable_id' => $item->id,
    'showcaseable_type' => get_class($item),
    'description' => 'Mô tả showcase',
    'order' => 1
]);
```

2. **Kiểm Tra Like Status**:
```php
$isLiked = $showcase->isLikedBy(auth()->id());
```

3. **Đếm Interactions**:
```php
$likesCount = $showcase->likesCount();
$followsCount = $showcase->followsCount();
$commentsCount = $showcase->commentsCount();
```

### Cho User

1. **Truy cập trang quản lý**: `/showcase`
2. **Xem public showcases**: `/public-showcase`  
3. **Xem chi tiết**: `/showcase/{id}` với đầy đủ tương tác
4. **Tương tác**: Like, follow, comment trực tiếp trên trang

## Performance & SEO

- **Lazy Loading**: Comments load theo demand
- **Pagination**: Tránh load quá nhiều data
- **Caching**: Cache counts và frequently accessed data
- **SEO Friendly**: Meta tags, structured data
- **Mobile Responsive**: Bootstrap responsive grid

## Testing

Các file test API đã được tổ chức tại:
- `tests/api/` - Chứa các file test API
- `docs/testing/` - Documentation về testing

## Deployment Notes

- Đảm bảo storage symlink đã được tạo: `php artisan storage:link`
- Config upload limits phù hợp trong php.ini
- Set proper file permissions cho storage directory
- Configure queue workers cho real-time notifications (future)

---

**Phiên bản**: 1.0  
**Cập nhật**: 02/06/2025  
**Tác giả**: MechaMap Development Team
