# 🔧 Migration Consolidation Script Generator

**Auto-generate consolidated migration files for MechaMap**

---

## 🎯 Categories Table Consolidation Example

### **Original Files:**
1. `create_categories_table.php` (base structure)
2. `enhance_categories_for_mechanical_engineering.php` (15+ additional fields)

### **Consolidated Result:**
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Enhanced categories table for mechanical engineering forum - CONSOLIDATED
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            // === ORIGINAL BASE FIELDS ===
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->foreignId('parent_id')->nullable()->constrained('categories')->onDelete('set null');
            $table->integer('order')->default(0);
            
            // === MECHANICAL ENGINEERING ENHANCEMENTS ===
            // Visual & UI enhancements
            $table->string('icon', 500)->nullable()
                ->comment('URL hoặc class name của icon cho danh mục (material-symbols, ionicons, etc.)');

            $table->string('color_code', 7)->nullable()
                ->comment('Mã màu hex cho danh mục (#FF5722 cho Manufacturing, #2196F3 cho CAD/CAM)');

            $table->text('meta_description')->nullable()
                ->comment('Mô tả SEO cho danh mục');

            $table->text('meta_keywords')->nullable()
                ->comment('Keywords SEO cho danh mục kỹ thuật');

            // Mechanical Engineering specific fields
            $table->boolean('is_technical')->default(true)
                ->comment('Danh mục kỹ thuật yêu cầu expertise hay thảo luận chung');

            $table->enum('expertise_level', ['beginner', 'intermediate', 'advanced', 'expert'])->nullable()
                ->comment('Cấp độ chuyên môn được khuyến nghị cho danh mục');

            $table->boolean('requires_verification')->default(false)
                ->comment('Yêu cầu verification từ expert để post trong danh mục này');

            $table->json('allowed_file_types')->nullable()
                ->comment('Các loại file được phép upload: ["dwg","step","iges","pdf","doc","jpg"]');

            // Forum statistics và activity
            $table->integer('thread_count')->default(0)
                ->comment('Số lượng thread trong danh mục (cached)');

            $table->integer('post_count')->default(0)
                ->comment('Tổng số bài post trong danh mục (cached)');

            $table->timestamp('last_activity_at')->nullable()
                ->comment('Thời gian hoạt động cuối cùng trong danh mục');

            // Content organization
            $table->boolean('is_active')->default(true)
                ->comment('Danh mục có đang hoạt động không');

            $table->integer('sort_order')->default(0)
                ->comment('Thứ tự sắp xếp danh mục (thay thế cho order)');

            $table->timestamps();

            // === CONSOLIDATED INDEXES ===
            // Original indexes
            $table->index(['parent_id', 'order']);
            
            // Enhanced indexes for mechanical engineering
            $table->index(['is_active', 'sort_order'], 'categories_active_sort_index');
            $table->index(['is_technical', 'expertise_level'], 'categories_technical_level_index');
            $table->index(['parent_id', 'is_active', 'sort_order'], 'categories_hierarchy_index');
            $table->index(['thread_count', 'last_activity_at'], 'categories_activity_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
```

---

## 📊 Consolidation Impact Analysis

### **File Reduction:**
- **Before**: 2 files (create + enhance)
- **After**: 1 consolidated file
- **Reduction**: 50% fewer migration files

### **Field Integration:**
- **Original Fields**: 6 base fields
- **Enhanced Fields**: 13 additional fields  
- **Total Fields**: 19 comprehensive fields
- **Indexes**: 5 optimized indexes (vs 1 + 4 separate)

### **Benefits:**
✅ **Single Source of Truth**: All category fields in one place  
✅ **Improved Performance**: Consolidated indexes  
✅ **Better Documentation**: Complete field comments  
✅ **Easier Deployment**: One migration instead of two  
✅ **Cleaner Git History**: Reduced file count  

---

## 🚀 Auto-Generation Commands

### **Generate All Consolidated Migrations:**
```bash
# Create consolidated migration files
php artisan make:migration-consolidator \
  --source="create_categories_table.php,enhance_categories_for_mechanical_engineering.php" \
  --output="create_categories_table_consolidated.php"

php artisan make:migration-consolidator \
  --source="create_threads_table.php,optimize_threads_for_mechanical_forum.php" \
  --output="create_threads_table_consolidated.php"

php artisan make:migration-consolidator \
  --source="create_comments_table.php,enhance_comments_for_technical_discussion.php" \
  --output="create_comments_table_consolidated.php"
```

### **Backup & Replace Process:**
```bash
# 1. Backup original migrations
mkdir -p database/migrations/backup_original
cp database/migrations/*enhance*.php database/migrations/backup_original/
cp database/migrations/*optimize*.php database/migrations/backup_original/

# 2. Generate consolidated files
./scripts/generate-consolidated-migrations.sh

# 3. Test on fresh database
php artisan migrate:fresh --env=testing

# 4. Validate schema
php artisan schema:dump --prune
```

---

## 🔍 Quality Assurance Checklist

### **Pre-Consolidation:**
- [ ] Backup all original migration files
- [ ] Document field mappings and dependencies
- [ ] Identify potential conflicts or duplications
- [ ] Review index strategies for optimization

### **During Consolidation:**
- [ ] Maintain exact field definitions and constraints
- [ ] Preserve all comments and documentation
- [ ] Optimize index placement and naming
- [ ] Ensure proper foreign key relationships

### **Post-Consolidation:**
- [ ] Test fresh migration on clean database
- [ ] Validate all table structures match expectations
- [ ] Run performance benchmarks on consolidated tables
- [ ] Update model relationships and documentation
- [ ] Verify seeder compatibility with new structure

---

## 🎯 Next Steps

**Ready to proceed with automatic consolidation generation?**

1. **Review the consolidated example above**
2. **Confirm consolidation approach**  
3. **Generate consolidation scripts**
4. **Execute consolidation process**
5. **Test and validate results**

**This will streamline your migration structure from 42 files to ~25 files while maintaining all functionality!**
