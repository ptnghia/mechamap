#!/bin/bash

# CSS Structure Validation Script
# Kiểm tra và validate cấu trúc CSS sau khi loại bỏ Vite

echo "🔍 MechaMap CSS Structure Validation"
echo "======================================"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Base directory
BASE_DIR="public/css"
VIEWS_DIR="$BASE_DIR/views"

echo -e "${BLUE}📁 Checking CSS directory structure...${NC}"

# Check if main CSS files exist
FILES_TO_CHECK=(
    "$BASE_DIR/main.css"
    "$BASE_DIR/buttons.css"
    "$BASE_DIR/forms.css"
    "$BASE_DIR/alerts.css"
    "$BASE_DIR/avatar.css"
    "$BASE_DIR/custom-header.css"
    "$BASE_DIR/sidebar.css"
    "$BASE_DIR/mobile-nav.css"
    "$BASE_DIR/auth.css"
    "$BASE_DIR/auth-modal.css"
    "$BASE_DIR/admin-pagination.css"
    "$BASE_DIR/dark-mode.css"
    "$BASE_DIR/compact-theme.css"
)

# Check view-specific CSS files
VIEW_FILES=(
    "$VIEWS_DIR/homepage.css"
    "$VIEWS_DIR/threads.css"
    "$VIEWS_DIR/profile.css"
    "$VIEWS_DIR/admin.css"
    "$VIEWS_DIR/auth.css"
    "$VIEWS_DIR/search.css"
)

# Function to check file existence
check_file() {
    if [ -f "$1" ]; then
        echo -e "  ✅ $1"
        return 0
    else
        echo -e "  ❌ $1 ${RED}(missing)${NC}"
        return 1
    fi
}

# Check main CSS files
echo -e "${YELLOW}📄 Main CSS files:${NC}"
missing_main=0
for file in "${FILES_TO_CHECK[@]}"; do
    if ! check_file "$file"; then
        ((missing_main++))
    fi
done

# Check views directory and files
echo -e "${YELLOW}📁 Views CSS files:${NC}"
if [ ! -d "$VIEWS_DIR" ]; then
    echo -e "  ❌ ${RED}Views directory missing: $VIEWS_DIR${NC}"
    mkdir -p "$VIEWS_DIR"
    echo -e "  ✅ ${GREEN}Created views directory${NC}"
fi

missing_views=0
for file in "${VIEW_FILES[@]}"; do
    if ! check_file "$file"; then
        ((missing_views++))
    fi
done

# Check main.css imports
echo -e "${YELLOW}🔗 Checking main.css imports:${NC}"
if [ -f "$BASE_DIR/main.css" ]; then
    # Check for @import statements
    imports=$(grep -c "@import" "$BASE_DIR/main.css")
    echo -e "  📊 Found $imports @import statements"

    # Check specific imports
    EXPECTED_IMPORTS=(
        "buttons.css"
        "forms.css"
        "alerts.css"
        "avatar.css"
        "custom-header.css"
        "sidebar.css"
        "mobile-nav.css"
        "auth.css"
        "auth-modal.css"
        "admin-pagination.css"
        "dark-mode.css"
        "compact-theme.css"
        "views/homepage.css"
        "views/threads.css"
        "views/profile.css"
        "views/admin.css"
        "views/auth.css"
        "views/search.css"
    )

    missing_imports=0
    for import in "${EXPECTED_IMPORTS[@]}"; do
        if grep -q "$import" "$BASE_DIR/main.css"; then
            echo -e "  ✅ Import: $import"
        else
            echo -e "  ❌ ${RED}Missing import: $import${NC}"
            ((missing_imports++))
        fi
    done
else
    echo -e "  ❌ ${RED}main.css file not found${NC}"
fi

# Check for old Vite files (should be removed)
echo -e "${YELLOW}🗑️  Checking for removed Vite files:${NC}"
OLD_FILES=(
    "vite.config.js"
    "tailwind.config.js"
    "postcss.config.js"
    "resources/css"
    "resources/js"
)

vite_files_found=0
for file in "${OLD_FILES[@]}"; do
    if [ -e "$file" ]; then
        echo -e "  ⚠️  ${YELLOW}Old Vite file still exists: $file${NC}"
        ((vite_files_found++))
    else
        echo -e "  ✅ Removed: $file"
    fi
done

# Check blade layouts for asset() usage
echo -e "${YELLOW}🔍 Checking blade layouts for correct asset() usage:${NC}"
LAYOUT_FILES=(
    "resources/views/layouts/app.blade.php"
    "resources/views/layouts/guest.blade.php"
    "resources/views/layouts/auth.blade.php"
    "resources/views/admin/layouts/partials/meta.blade.php"
)

blade_issues=0
for layout in "${LAYOUT_FILES[@]}"; do
    if [ -f "$layout" ]; then
        if grep -q "asset('css/main.css')" "$layout" || grep -q "asset('css/app.css')" "$layout"; then
            echo -e "  ✅ $layout uses asset() correctly"
        else
            echo -e "  ❌ ${RED}$layout may not use asset() correctly${NC}"
            ((blade_issues++))
        fi

        # Check for old @vite usage
        if grep -q "@vite" "$layout"; then
            echo -e "  ⚠️  ${YELLOW}$layout still contains @vite directive${NC}"
            ((blade_issues++))
        fi
    else
        echo -e "  ❌ ${RED}Layout file not found: $layout${NC}"
        ((blade_issues++))
    fi
done

# Summary
echo ""
echo -e "${BLUE}📊 VALIDATION SUMMARY${NC}"
echo "======================================"

total_issues=$((missing_main + missing_views + missing_imports + vite_files_found + blade_issues))

if [ $total_issues -eq 0 ]; then
    echo -e "${GREEN}✅ All checks passed! CSS structure is properly configured.${NC}"
else
    echo -e "${RED}⚠️  Found $total_issues issues:${NC}"
    [ $missing_main -gt 0 ] && echo -e "  - $missing_main missing main CSS files"
    [ $missing_views -gt 0 ] && echo -e "  - $missing_views missing view CSS files"
    [ $missing_imports -gt 0 ] && echo -e "  - $missing_imports missing imports in main.css"
    [ $vite_files_found -gt 0 ] && echo -e "  - $vite_files_found old Vite files still present"
    [ $blade_issues -gt 0 ] && echo -e "  - $blade_issues blade layout issues"
fi

# File sizes
echo ""
echo -e "${BLUE}📏 CSS File Sizes:${NC}"
if [ -f "$BASE_DIR/main.css" ]; then
    size=$(wc -c < "$BASE_DIR/main.css")
    echo -e "  main.css: ${size} bytes"
fi

# Quick performance check
echo ""
echo -e "${BLUE}🚀 Performance Notes:${NC}"
echo -e "  • CSS files are loaded directly (no build step)"
echo -e "  • Bootstrap loaded via CDN"
echo -e "  • Multiple @import statements (consider concatenation for production)"
echo -e "  • CSS variables used for consistency"

echo ""
echo -e "${GREEN}✅ Validation complete!${NC}"
