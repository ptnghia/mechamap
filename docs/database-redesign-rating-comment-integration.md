# Database Redesign: Rating & Comment Integration

## Phân tích cấu trúc hiện tại

### 1. **Bảng `showcase_ratings`** ✅ **Đã tốt**
```sql
- id (PK)
- showcase_id (FK)
- user_id (FK)
- technical_quality (1-5)
- innovation (1-5)
- usefulness (1-5)
- documentation (1-5)
- overall_rating (calculated)
- review (text, nullable) ← Đã có sẵn cho nhận xét
- created_at, updated_at
```

### 2. **Bảng `showcase_comments`** ❌ **Cần điều chỉnh**
```sql
- id (PK)
- showcase_id (FK)
- user_id (FK)
- parent_id (FK, nullable)
- comment (text)
- has_media (boolean)
- images (json, nullable)
- like_count (integer)
- created_at, updated_at
```

## 🎯 **Đề xuất thiết kế mới**

### **Phương án 1: Mở rộng bảng `showcase_ratings` (Khuyến nghị)**

#### **Ưu điểm:**
- ✅ Tận dụng cấu trúc có sẵn
- ✅ Đơn giản, ít thay đổi
- ✅ Dữ liệu tập trung
- ✅ Performance tốt

#### **Thay đổi cần thiết:**

1. **Thêm cột hỗ trợ media vào `showcase_ratings`:**
```sql
ALTER TABLE showcase_ratings ADD COLUMN has_media BOOLEAN DEFAULT FALSE;
ALTER TABLE showcase_ratings ADD COLUMN images JSON NULL;
ALTER TABLE showcase_ratings ADD COLUMN like_count INTEGER DEFAULT 0;
```

2. **Tạo bảng `showcase_rating_replies` mới:**
```sql
CREATE TABLE showcase_rating_replies (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    rating_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    parent_id BIGINT UNSIGNED NULL,
    content TEXT NOT NULL,
    has_media BOOLEAN DEFAULT FALSE,
    images JSON NULL,
    like_count INTEGER DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (rating_id) REFERENCES showcase_ratings(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (parent_id) REFERENCES showcase_rating_replies(id) ON DELETE CASCADE,
    
    INDEX idx_rating_replies_rating_id (rating_id),
    INDEX idx_rating_replies_user_id (user_id),
    INDEX idx_rating_replies_parent_id (parent_id)
);
```

3. **Tạo bảng `showcase_rating_likes`:**
```sql
CREATE TABLE showcase_rating_likes (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    rating_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (rating_id) REFERENCES showcase_ratings(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    
    UNIQUE KEY unique_rating_like (rating_id, user_id),
    INDEX idx_rating_likes_user_id (user_id)
);
```

4. **Tạo bảng `showcase_rating_reply_likes`:**
```sql
CREATE TABLE showcase_rating_reply_likes (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    reply_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (reply_id) REFERENCES showcase_rating_replies(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    
    UNIQUE KEY unique_reply_like (reply_id, user_id),
    INDEX idx_reply_likes_user_id (user_id)
);
```

### **Phương án 2: Unified Comment System**

#### **Ưu điểm:**
- ✅ Hệ thống thống nhất
- ✅ Flexible cho tương lai
- ✅ Dễ mở rộng

#### **Nhược điểm:**
- ❌ Phức tạp hơn
- ❌ Cần migration lớn
- ❌ Performance có thể chậm hơn

#### **Cấu trúc:**
```sql
CREATE TABLE showcase_interactions (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    showcase_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    parent_id BIGINT UNSIGNED NULL,
    type ENUM('rating', 'comment', 'reply') NOT NULL,
    
    -- Rating fields (chỉ dùng khi type = 'rating')
    technical_quality TINYINT UNSIGNED NULL,
    innovation TINYINT UNSIGNED NULL,
    usefulness TINYINT UNSIGNED NULL,
    documentation TINYINT UNSIGNED NULL,
    overall_rating DECIMAL(3,2) NULL,
    
    -- Content fields
    content TEXT NULL,
    has_media BOOLEAN DEFAULT FALSE,
    images JSON NULL,
    like_count INTEGER DEFAULT 0,
    
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (showcase_id) REFERENCES showcases(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (parent_id) REFERENCES showcase_interactions(id) ON DELETE CASCADE,
    
    UNIQUE KEY unique_user_rating (showcase_id, user_id, type) WHERE type = 'rating',
    INDEX idx_interactions_showcase_type (showcase_id, type),
    INDEX idx_interactions_user_id (user_id),
    INDEX idx_interactions_parent_id (parent_id)
);
```

## 🚀 **Khuyến nghị: Phương án 1**

### **Lý do chọn Phương án 1:**

1. **Tối thiểu thay đổi:** Tận dụng cấu trúc có sẵn
2. **Performance tốt:** Queries đơn giản, indexes hiệu quả
3. **Dễ implement:** Ít risk, dễ rollback
4. **Tương thích:** Không ảnh hưởng code hiện tại

### **Migration Plan:**

#### **Step 1: Tạo migration mở rộng `showcase_ratings`**
```php
// database/migrations/2025_01_22_000001_extend_showcase_ratings_for_media.php
public function up(): void
{
    Schema::table('showcase_ratings', function (Blueprint $table) {
        $table->boolean('has_media')->default(false)->after('review');
        $table->json('images')->nullable()->after('has_media');
        $table->unsignedInteger('like_count')->default(0)->after('images');
        
        $table->index(['showcase_id', 'like_count']);
        $table->index(['like_count', 'created_at']);
    });
}
```

#### **Step 2: Tạo bảng replies**
```php
// database/migrations/2025_01_22_000002_create_showcase_rating_replies_table.php
public function up(): void
{
    Schema::create('showcase_rating_replies', function (Blueprint $table) {
        $table->id();
        $table->foreignId('rating_id')->constrained('showcase_ratings')->onDelete('cascade');
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->foreignId('parent_id')->nullable()->constrained('showcase_rating_replies')->onDelete('cascade');
        $table->text('content');
        $table->boolean('has_media')->default(false);
        $table->json('images')->nullable();
        $table->unsignedInteger('like_count')->default(0);
        $table->timestamps();
        
        $table->index(['rating_id', 'created_at']);
        $table->index(['user_id', 'created_at']);
        $table->index(['parent_id', 'created_at']);
    });
}
```

#### **Step 3: Tạo bảng likes**
```php
// database/migrations/2025_01_22_000003_create_showcase_rating_likes_table.php
public function up(): void
{
    Schema::create('showcase_rating_likes', function (Blueprint $table) {
        $table->id();
        $table->foreignId('rating_id')->constrained('showcase_ratings')->onDelete('cascade');
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->timestamps();
        
        $table->unique(['rating_id', 'user_id']);
        $table->index('user_id');
    });
}

// Tương tự cho showcase_rating_reply_likes
```

### **Model Updates:**

#### **ShowcaseRating Model:**
```php
class ShowcaseRating extends Model
{
    protected $fillable = [
        // ... existing fields
        'has_media',
        'images',
        'like_count',
    ];
    
    protected $casts = [
        // ... existing casts
        'has_media' => 'boolean',
        'images' => 'array',
        'like_count' => 'integer',
    ];
    
    // New relationships
    public function replies(): HasMany
    {
        return $this->hasMany(ShowcaseRatingReply::class, 'rating_id');
    }
    
    public function likes(): HasMany
    {
        return $this->hasMany(ShowcaseRatingLike::class, 'rating_id');
    }
    
    public function isLikedBy(User $user): bool
    {
        return $this->likes()->where('user_id', $user->id)->exists();
    }
}
```

#### **Tạo Model mới:**
```php
// app/Models/ShowcaseRatingReply.php
class ShowcaseRatingReply extends Model
{
    protected $fillable = [
        'rating_id', 'user_id', 'parent_id', 'content', 
        'has_media', 'images', 'like_count'
    ];
    
    protected $casts = [
        'has_media' => 'boolean',
        'images' => 'array',
        'like_count' => 'integer',
    ];
    
    public function rating(): BelongsTo
    {
        return $this->belongsTo(ShowcaseRating::class, 'rating_id');
    }
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    public function parent(): BelongsTo
    {
        return $this->belongsTo(ShowcaseRatingReply::class, 'parent_id');
    }
    
    public function replies(): HasMany
    {
        return $this->hasMany(ShowcaseRatingReply::class, 'parent_id');
    }
    
    public function likes(): HasMany
    {
        return $this->hasMany(ShowcaseRatingReplyLike::class, 'reply_id');
    }
}
```

## 📊 **Impact Assessment**

### **Dữ liệu hiện tại:**
- ✅ `showcase_ratings` table: Giữ nguyên, chỉ thêm cột
- ❓ `showcase_comments` table: Có thể giữ lại hoặc migrate

### **Code changes:**
- ✅ Minimal: Chỉ cần update relationships
- ✅ Backward compatible: Code cũ vẫn hoạt động
- ✅ Progressive enhancement: Thêm tính năng mới

### **Performance:**
- ✅ Tốt: Indexes được tối ưu
- ✅ Scalable: Có thể handle lượng data lớn
- ✅ Query efficient: Ít joins phức tạp

## 🎯 **Kết luận**

**Khuyến nghị sử dụng Phương án 1** với các lý do:

1. **Risk thấp:** Ít thay đổi, dễ rollback
2. **Performance tốt:** Cấu trúc tối ưu
3. **Implementation nhanh:** 1-2 ngày
4. **Tương thích:** Không ảnh hưởng tính năng hiện tại
5. **Scalable:** Dễ mở rộng trong tương lai

**Timeline thực hiện:**
- **Day 1:** Tạo migrations và chạy
- **Day 2:** Update models và test
- **Day 3:** Update UI components và test integration

Bạn có muốn tôi tạo các migration files này không?
