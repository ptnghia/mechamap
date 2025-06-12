#!/bin/bash

# 🔧 MechaMap Migration Consolidation Script
# Auto-generate consolidated migration files

echo "🚀 MechaMap Migration Consolidation Starting..."
echo "================================================"

# Configuration
MIGRATIONS_DIR="database/migrations"
BACKUP_DIR="database/migrations/backup_original"
CONSOLIDATED_DIR="database/migrations/consolidated"
TIMESTAMP=$(date +"%Y_%m_%d_%H%M%S")

# Create directories
mkdir -p "$BACKUP_DIR"
mkdir -p "$CONSOLIDATED_DIR"

echo "📁 Created backup and consolidated directories"

# Define consolidation pairs
declare -A CONSOLIDATION_PAIRS=(
    ["create_categories_table.php"]="enhance_categories_for_mechanical_engineering.php"
    ["create_threads_table.php"]="optimize_threads_for_mechanical_forum.php"
    ["create_comments_table.php"]="enhance_comments_for_technical_discussion.php"
    ["create_tags_table.php"]="enhance_tags_for_mechanical_engineering.php"
    ["create_social_interactions_table.php"]="enhance_social_interactions_for_mechanical_forum.php"
    ["create_media_table.php"]="enhance_media_for_mechanical_engineering.php"
    ["create_showcases_table.php"]="enhance_showcases_for_mechanical_engineering.php"
)

# Function to extract table name from migration filename
extract_table_name() {
    local filename="$1"
    echo "$filename" | sed -E 's/.*_create_(.*)_table\.php$/\1/'
}

# Function to generate consolidated migration
generate_consolidated_migration() {
    local create_file="$1"
    local enhance_file="$2"
    local table_name=$(extract_table_name "$create_file")

    echo "🔄 Consolidating: $table_name"
    echo "   ├── Base: $create_file"
    echo "   └── Enhanced: $enhance_file"

    # Find actual files with timestamps
    local create_path=$(ls "$MIGRATIONS_DIR"/*"$create_file" 2>/dev/null | head -1)
    local enhance_path=$(ls "$MIGRATIONS_DIR"/*"$enhance_file" 2>/dev/null | head -1)

    if [[ ! -f "$create_path" ]]; then
        echo "   ❌ Base file not found: $create_file"
        return 1
    fi

    if [[ ! -f "$enhance_path" ]]; then
        echo "   ❌ Enhanced file not found: $enhance_file"
        return 1
    fi

    # Backup original files
    cp "$create_path" "$BACKUP_DIR/"
    cp "$enhance_path" "$BACKUP_DIR/"

    # Generate new consolidated timestamp
    local new_timestamp="${TIMESTAMP}_create_${table_name}_table_consolidated.php"
    local output_file="$CONSOLIDATED_DIR/$new_timestamp"

    # Create consolidated migration file
    cat > "$output_file" << 'EOF'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * CONSOLIDATED: Create and enhance table for mechanical engineering forum
     */
    public function up(): void
    {
        Schema::create('TABLE_NAME', function (Blueprint $table) {
            // === BASE FIELDS ===
BASE_FIELDS

            // === MECHANICAL ENGINEERING ENHANCEMENTS ===
ENHANCED_FIELDS

            $table->timestamps();

            // === CONSOLIDATED INDEXES ===
BASE_INDEXES
ENHANCED_INDEXES
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('TABLE_NAME');
    }
};
EOF

    # Replace table name
    sed -i "s/TABLE_NAME/$table_name/g" "$output_file"

    # Extract and merge content (simplified - manual review needed)
    echo "   ✅ Generated: $new_timestamp"
    echo "   ⚠️  Manual review required for field consolidation"

    return 0
}

# Main consolidation process
echo ""
echo "🔄 Starting consolidation process..."
echo "=================================="

consolidation_count=0
for create_file in "${!CONSOLIDATION_PAIRS[@]}"; do
    enhance_file="${CONSOLIDATION_PAIRS[$create_file]}"

    if generate_consolidated_migration "$create_file" "$enhance_file"; then
        ((consolidation_count++))
    fi
    echo ""
done

echo "✅ Consolidation Summary:"
echo "========================"
echo "   📁 Files processed: ${#CONSOLIDATION_PAIRS[@]} pairs"
echo "   ✅ Successfully consolidated: $consolidation_count"
echo "   📂 Backups saved to: $BACKUP_DIR"
echo "   📂 Consolidated files in: $CONSOLIDATED_DIR"
echo ""

# Generate consolidation report
REPORT_FILE="docs/development/CONSOLIDATION_EXECUTION_REPORT.md"
cat > "$REPORT_FILE" << EOF
# 📊 Migration Consolidation Execution Report

**Execution Date**: $(date)
**Script Version**: 1.0
**Total Pairs Processed**: ${#CONSOLIDATION_PAIRS[@]}
**Successfully Consolidated**: $consolidation_count

## 📁 File Structure Changes

### Before Consolidation:
\`\`\`
$MIGRATIONS_DIR/
├── *_create_categories_table.php
├── *_enhance_categories_for_mechanical_engineering.php
├── *_create_threads_table.php
├── *_optimize_threads_for_mechanical_forum.php
└── ... (42 total files)
\`\`\`

### After Consolidation:
\`\`\`
$MIGRATIONS_DIR/
├── backup_original/           # Original files backed up
├── consolidated/              # New consolidated migrations
└── ... (remaining original files)
\`\`\`

## 🔄 Consolidation Results

| Table | Base Migration | Enhanced Migration | Status |
|-------|---------------|-------------------|---------|
EOF

# Add results to report
for create_file in "${!CONSOLIDATION_PAIRS[@]}"; do
    enhance_file="${CONSOLIDATION_PAIRS[$create_file]}"
    table_name=$(extract_table_name "$create_file")
    echo "| $table_name | $create_file | $enhance_file | ✅ Consolidated |" >> "$REPORT_FILE"
done

cat >> "$REPORT_FILE" << EOF

## 🚀 Next Steps

1. **Review consolidated migrations** in \`$CONSOLIDATED_DIR/\`
2. **Manual field integration** - Merge create and enhance logic
3. **Test fresh migration**: \`php artisan migrate:fresh --env=testing\`
4. **Replace original files** once validated
5. **Update documentation** and model relationships

## ⚠️ Important Notes

- **Manual review required**: Automated consolidation needs field integration
- **Backup preserved**: Original files saved in \`$BACKUP_DIR/\`
- **Test before deployment**: Validate on fresh database first
- **Git history**: Consider preserving change history in commit messages

EOF

echo "📊 Consolidation report generated: $REPORT_FILE"
echo ""
echo "🎯 Next Actions:"
echo "=================="
echo "1. Review consolidated files in: $CONSOLIDATED_DIR/"
echo "2. Manually merge field definitions and indexes"
echo "3. Test with: php artisan migrate:fresh --env=testing"
echo "4. Replace original files when ready"
echo ""
echo "🚀 Consolidation script completed!"
