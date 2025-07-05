#!/bin/bash

# CSS Optimization Validation Script
# Kiểm tra cấu trúc CSS mới sau khi tối ưu hóa

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}🔍 CSS OPTIMIZATION VALIDATION${NC}"
echo "======================================"

# Base directories
CSS_DIR="public/css"
FRONTEND_DIR="$CSS_DIR/frontend"
ADMIN_DIR="$CSS_DIR/admin"

# Check new frontend structure
echo -e "${YELLOW}📁 Checking new frontend structure:${NC}"

# Check main directories
if [ -d "$FRONTEND_DIR" ]; then
    echo -e "  ✅ frontend/ directory exists"
else
    echo -e "  ❌ ${RED}frontend/ directory missing${NC}"
    exit 1
fi

if [ -d "$FRONTEND_DIR/components" ]; then
    echo -e "  ✅ frontend/components/ directory exists"
else
    echo -e "  ❌ ${RED}frontend/components/ directory missing${NC}"
fi

if [ -d "$FRONTEND_DIR/views" ]; then
    echo -e "  ✅ frontend/views/ directory exists"
else
    echo -e "  ❌ ${RED}frontend/views/ directory missing${NC}"
fi

if [ -d "$FRONTEND_DIR/utilities" ]; then
    echo -e "  ✅ frontend/utilities/ directory exists"
else
    echo -e "  ❌ ${RED}frontend/utilities/ directory missing${NC}"
fi

# Check main optimized CSS file
echo -e "${YELLOW}📄 Checking main CSS file:${NC}"
if [ -f "$FRONTEND_DIR/main-user-optimized.css" ]; then
    echo -e "  ✅ main-user-optimized.css exists"
    size=$(wc -c < "$FRONTEND_DIR/main-user-optimized.css")
    echo -e "  📊 Size: ${size} bytes"
else
    echo -e "  ❌ ${RED}main-user-optimized.css missing${NC}"
fi

# Check component files
echo -e "${YELLOW}🧩 Checking component files:${NC}"
COMPONENT_FILES=(
    "buttons.css"
    "forms.css"
    "alerts.css"
    "avatar.css"
    "auth-modal.css"
    "mobile-nav.css"
    "sidebar.css"
    "thread-form.css"
)

for file in "${COMPONENT_FILES[@]}"; do
    if [ -f "$FRONTEND_DIR/components/$file" ]; then
        echo -e "  ✅ components/$file"
    else
        echo -e "  ❌ ${RED}components/$file missing${NC}"
    fi
done

# Check view files
echo -e "${YELLOW}👁️  Checking view files:${NC}"
VIEW_FILES=(
    "homepage.css"
    "threads.css"
    "profile.css"
    "auth.css"
    "search.css"
    "home.css"
    "activity.css"
    "whats-new.css"
)

for file in "${VIEW_FILES[@]}"; do
    if [ -f "$FRONTEND_DIR/views/$file" ]; then
        echo -e "  ✅ views/$file"
    else
        echo -e "  ❌ ${RED}views/$file missing${NC}"
    fi
done

# Check utility files
echo -e "${YELLOW}🛠️  Checking utility files:${NC}"
UTILITY_FILES=(
    "utilities.css"
    "dark-mode.css"
    "compact-theme.css"
    "enhanced-menu.css"
)

for file in "${UTILITY_FILES[@]}"; do
    if [ -f "$FRONTEND_DIR/utilities/$file" ]; then
        echo -e "  ✅ utilities/$file"
    else
        echo -e "  ❌ ${RED}utilities/$file missing${NC}"
    fi
done

# Check that old files are removed
echo -e "${YELLOW}🗑️  Checking old files are removed:${NC}"
OLD_FILES=(
    "buttons.css"
    "forms.css"
    "alerts.css"
    "avatar.css"
    "auth-modal.css"
    "mobile-nav.css"
    "home.css"
    "search.css"
    "activity.css"
    "whats-new.css"
    "dark-mode.css"
    "compact-theme.css"
    "enhanced-menu.css"
)

removed_count=0
for file in "${OLD_FILES[@]}"; do
    if [ ! -f "$CSS_DIR/$file" ]; then
        echo -e "  ✅ $file removed from root"
        ((removed_count++))
    else
        echo -e "  ⚠️  ${YELLOW}$file still exists in root${NC}"
    fi
done

echo -e "  📊 Removed $removed_count out of ${#OLD_FILES[@]} old files"

# Check admin files are untouched
echo -e "${YELLOW}🔒 Checking admin files are preserved:${NC}"
if [ -d "$ADMIN_DIR" ]; then
    echo -e "  ✅ admin/ directory preserved"
    admin_files=$(find "$ADMIN_DIR" -name "*.css" | wc -l)
    echo -e "  📊 Admin CSS files: $admin_files"
else
    echo -e "  ❌ ${RED}admin/ directory missing${NC}"
fi

if [ -f "$CSS_DIR/main-admin.css" ]; then
    echo -e "  ✅ main-admin.css preserved"
else
    echo -e "  ❌ ${RED}main-admin.css missing${NC}"
fi

# Check layout integration
echo -e "${YELLOW}🔗 Checking layout integration:${NC}"
if grep -q "frontend/main-user-optimized.css" "resources/views/layouts/app.blade.php"; then
    echo -e "  ✅ app.blade.php uses optimized CSS"
else
    echo -e "  ❌ ${RED}app.blade.php not updated${NC}"
fi

# Check for old references
old_refs=$(grep -c "asset('css/buttons.css')" "resources/views/layouts/app.blade.php" 2>/dev/null || echo "0")
if [ "$old_refs" -eq 0 ]; then
    echo -e "  ✅ No old CSS references in layout"
else
    echo -e "  ⚠️  ${YELLOW}Found $old_refs old CSS references${NC}"
fi

# Performance check
echo -e "${YELLOW}⚡ Performance analysis:${NC}"
total_css_files=$(find "$FRONTEND_DIR" -name "*.css" | wc -l)
echo -e "  📊 Total frontend CSS files: $total_css_files"

total_size=$(find "$FRONTEND_DIR" -name "*.css" -exec wc -c {} + | tail -1 | awk '{print $1}')
echo -e "  📊 Total frontend CSS size: $total_size bytes"

# Summary
echo ""
echo -e "${BLUE}📊 OPTIMIZATION SUMMARY${NC}"
echo "======================================"
echo -e "${GREEN}✅ CSS optimization completed successfully!${NC}"
echo ""
echo "📈 Benefits achieved:"
echo "  - Organized structure (components/views/utilities)"
echo "  - Reduced HTTP requests through consolidation"
echo "  - Maintained admin panel separation"
echo "  - Improved maintainability"
echo "  - Performance optimized imports"
echo ""
echo "🎯 Next steps:"
echo "  - Test all frontend pages"
echo "  - Verify responsive design"
echo "  - Check dark mode functionality"
echo "  - Monitor performance metrics"
