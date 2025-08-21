# Showcase Rating System Implementation Guide

## 📋 Tổng quan

Hệ thống đánh giá và bình luận tích hợp mới cho MechaMap Showcase với các tính năng:

- ✅ **Unified Rating & Comment Form** - Gộp đánh giá sao và nhận xét
- ✅ **Media Support** - Hỗ trợ hình ảnh trong ratings và replies
- ✅ **Threaded Discussions** - Replies và nested replies
- ✅ **Like System** - Like cho ratings và replies
- ✅ **Performance Optimized** - Indexes và caching

## 🗄️ Database Schema

### **Bảng mới được tạo:**

1. **`showcase_rating_replies`** - Replies cho ratings
2. **`showcase_rating_likes`** - Likes cho ratings  
3. **`showcase_rating_reply_likes`** - Likes cho replies

### **Bảng được mở rộng:**

1. **`showcase_ratings`** - Thêm `has_media`, `images`, `like_count`

## 📁 Files đã tạo/cập nhật

### **Migrations:**
```
database/migrations/2025_01_22_000001_extend_showcase_ratings_for_media.php
database/migrations/2025_01_22_000002_create_showcase_rating_replies_table.php
database/migrations/2025_01_22_000003_create_showcase_rating_likes_table.php
database/migrations/2025_01_22_000004_create_showcase_rating_reply_likes_table.php
database/migrations/2025_01_22_000005_create_rating_system_indexes.php
```

### **Models:**
```
app/Models/ShowcaseRating.php (updated)
app/Models/ShowcaseRatingReply.php (new)
app/Models/ShowcaseRatingLike.php (new)
app/Models/ShowcaseRatingReplyLike.php (new)
```

### **Seeders:**
```
database/seeders/ShowcaseRatingSystemSeeder.php
```

### **Scripts:**
```
scripts/setup-rating-system.sh
```

### **Views (đã có từ trước):**
```
resources/views/showcases/partials/rating-comment-form.blade.php
resources/views/showcases/partials/ratings-list.blade.php
resources/views/showcases/partials/rating-summary.blade.php
```

## 🚀 Cài đặt

### **Cách 1: Sử dụng script tự động**
```bash
chmod +x scripts/setup-rating-system.sh
./scripts/setup-rating-system.sh
```

### **Cách 2: Chạy thủ công**
```bash
# 1. Chạy migrations
php artisan migrate --path=database/migrations/2025_01_22_000001_extend_showcase_ratings_for_media.php
php artisan migrate --path=database/migrations/2025_01_22_000002_create_showcase_rating_replies_table.php
php artisan migrate --path=database/migrations/2025_01_22_000003_create_showcase_rating_likes_table.php
php artisan migrate --path=database/migrations/2025_01_22_000004_create_showcase_rating_reply_likes_table.php
php artisan migrate --path=database/migrations/2025_01_22_000005_create_rating_system_indexes.php

# 2. Clear caches
php artisan config:clear
php artisan cache:clear

# 3. Seed test data (optional)
php artisan db:seed --class=ShowcaseRatingSystemSeeder
```

## 🔧 Model Relationships

### **ShowcaseRating Model:**
```php
// New relationships
public function replies(): HasMany
public function likes(): HasMany
public function isLikedBy(User $user): bool
public function toggleLike(User $user): bool

// New attributes
protected $fillable = [..., 'has_media', 'images', 'like_count'];
protected $casts = [..., 'has_media' => 'boolean', 'images' => 'array'];
```

### **ShowcaseRatingReply Model:**
```php
public function rating(): BelongsTo
public function user(): BelongsTo
public function parent(): BelongsTo
public function replies(): HasMany
public function likes(): HasMany
public function isLikedBy(User $user): bool
public function toggleLike(User $user): bool
```

## 🎨 Frontend Integration

### **Form tích hợp (đã có):**
- `rating-comment-form.blade.php` - Form gộp rating + comment
- CKEditor5 integration
- Image upload component
- Star rating component

### **Display components (đã có):**
- `ratings-list.blade.php` - Hiển thị ratings với thread style
- `rating-summary.blade.php` - Tóm tắt ratings
- Like buttons và reply forms

## 📊 Performance Features

### **Database Indexes:**
```sql
-- Showcase ratings
idx_ratings_showcase_likes (showcase_id, like_count)
idx_ratings_popular (like_count, created_at)
idx_ratings_with_media (has_media, created_at)

-- Rating replies
idx_replies_rating_time (rating_id, created_at)
idx_replies_thread_popular (rating_id, parent_id, like_count)

-- Likes
unique_rating_like (rating_id, user_id)
unique_reply_like (reply_id, user_id)
```

### **Query Optimization:**
- Eager loading với `with(['user', 'likes'])`
- Scopes cho popular, with_media queries
- Efficient like counting với `updateLikeCount()`

## 🧪 Testing

### **Test Data:**
```bash
php artisan db:seed --class=ShowcaseRatingSystemSeeder
```

### **Manual Testing:**
1. Truy cập `/showcase/{id}`
2. Test form đánh giá tích hợp
3. Test upload hình ảnh
4. Test like/unlike ratings
5. Test reply system
6. Test nested replies

## 🔄 API Endpoints (cần implement)

### **Rating Management:**
```php
POST   /api/showcases/{id}/ratings          // Create rating
PUT    /api/ratings/{id}                    // Update rating
DELETE /api/ratings/{id}                    // Delete rating
POST   /api/ratings/{id}/like               // Toggle like
```

### **Reply Management:**
```php
POST   /api/ratings/{id}/replies            // Create reply
PUT    /api/replies/{id}                    // Update reply
DELETE /api/replies/{id}                    // Delete reply
POST   /api/replies/{id}/like               // Toggle like
```

## 📈 Analytics & Metrics

### **Available Metrics:**
```php
// Rating metrics
$showcase->ratings()->count()
$showcase->ratings()->avg('overall_rating')
$showcase->ratings()->withMedia()->count()
$showcase->ratings()->popular()->count()

// Reply metrics
$rating->replies()->count()
$rating->replies()->withMedia()->count()

// Like metrics
$rating->likes()->count()
$reply->likes()->count()
```

## 🔒 Security Considerations

### **Validation Rules:**
```php
// Rating validation
'technical_quality' => 'required|integer|min:1|max:5'
'innovation' => 'required|integer|min:1|max:5'
'usefulness' => 'required|integer|min:1|max:5'
'documentation' => 'required|integer|min:1|max:5'
'review' => 'nullable|string|max:2000'
'images' => 'nullable|array|max:10'
'images.*' => 'image|mimes:jpg,jpeg,png,gif,webp|max:5120'

// Reply validation
'content' => 'required|string|max:1000'
'images' => 'nullable|array|max:5'
```

### **Authorization:**
```php
// Only rating author can edit/delete
Gate::define('update-rating', function ($user, $rating) {
    return $user->id === $rating->user_id;
});

// Only reply author can edit/delete
Gate::define('update-reply', function ($user, $reply) {
    return $user->id === $reply->user_id;
});
```

## 🚨 Troubleshooting

### **Common Issues:**

1. **Migration fails:**
   ```bash
   php artisan migrate:rollback --step=5
   php artisan migrate
   ```

2. **Models not found:**
   ```bash
   composer dump-autoload
   php artisan config:clear
   ```

3. **Relationships not working:**
   ```bash
   php artisan ide-helper:models --write
   ```

4. **Performance issues:**
   ```bash
   php artisan optimize
   php artisan config:cache
   ```

## 📚 Next Steps

### **Phase 1: Backend (1-2 days)**
- [ ] Implement API controllers
- [ ] Add validation rules
- [ ] Add authorization policies
- [ ] Write unit tests

### **Phase 2: Frontend (1 day)**
- [ ] Connect forms to API endpoints
- [ ] Add AJAX handlers for likes
- [ ] Implement real-time updates
- [ ] Add loading states

### **Phase 3: Enhancement (ongoing)**
- [ ] Add notification system
- [ ] Implement moderation tools
- [ ] Add analytics dashboard
- [ ] Mobile optimization

## 🎯 Success Metrics

- ✅ Database schema implemented
- ✅ Models with relationships created
- ✅ UI components integrated
- ✅ Performance optimized
- ⏳ API endpoints (next phase)
- ⏳ Real-time features (next phase)

---

**🚀 Hệ thống đã sẵn sàng cho việc tích hợp backend và testing!**
