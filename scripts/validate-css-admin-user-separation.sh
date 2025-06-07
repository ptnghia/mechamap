#!/bin/bash

# ========================================
# CSS Structure Validation Script
# Updated for Admin/User Separation
# ========================================

echo "🎨 CSS Structure Validation - Admin/User Separation"
echo "=================================================="

CSS_DIR="public/css"
ADMIN_DIR="$CSS_DIR/admin"
VIEWS_DIR="$CSS_DIR/views"

# Check main CSS files
echo "🔍 Kiểm tra Main CSS Files..."

main_files=("main-admin.css" "main-user.css" "dark-mode.css")
for file in "${main_files[@]}"; do
    if [ -f "$CSS_DIR/$file" ]; then
        echo "✅ $file - OK"
    else
        echo "❌ $file - MISSING"
    fi
done

# Check admin components
echo ""
echo "🔍 Kiểm tra Admin Components..."

admin_files=("admin-dashboard.css" "admin-forms.css" "admin-tables.css" "admin-sidebar.css" "admin-header.css" "admin-alerts.css" "admin-modals.css" "admin-buttons.css")
for file in "${admin_files[@]}"; do
    if [ -f "$ADMIN_DIR/$file" ]; then
        echo "✅ admin/$file - OK"
    else
        echo "❌ admin/$file - MISSING"
    fi
done

# Check view components
echo ""
echo "🔍 Kiểm tra View Components..."

view_files=("homepage.css" "threads.css" "profile.css" "auth.css" "search.css")
for file in "${view_files[@]}"; do
    if [ -f "$VIEWS_DIR/$file" ]; then
        echo "✅ views/$file - OK"
    else
        echo "❌ views/$file - MISSING"
    fi
done

# Check CSS imports in main files
echo ""
echo "🔍 Kiểm tra CSS Imports..."

if grep -q "@import.*admin/" "$CSS_DIR/main-admin.css" 2>/dev/null; then
    echo "✅ main-admin.css imports admin components"
else
    echo "❌ main-admin.css missing admin imports"
fi

if grep -q "@import.*views/" "$CSS_DIR/main-user.css" 2>/dev/null; then
    echo "✅ main-user.css imports view components"
else
    echo "❌ main-user.css missing view imports"
fi

# Check layout files
echo ""
echo "🔍 Kiểm tra Layout Integration..."

# Admin layouts
if grep -q "main-admin.css" "resources/views/admin/layouts/partials/styles.blade.php" 2>/dev/null; then
    echo "✅ Admin layout uses main-admin.css"
else
    echo "❌ Admin layout not updated"
fi

# User layouts
user_layouts=("resources/views/layouts/app.blade.php" "resources/views/layouts/guest.blade.php" "resources/views/layouts/auth.blade.php")
for layout in "${user_layouts[@]}"; do
    if grep -q "main-user.css" "$layout" 2>/dev/null; then
        echo "✅ $(basename $layout) uses main-user.css"
    else
        echo "❌ $(basename $layout) not updated"
    fi
done

# CSS Variables check
echo ""
echo "🔍 Kiểm tra CSS Variables..."

if grep -q -- "--admin-" "$CSS_DIR/main-admin.css" 2>/dev/null; then
    echo "✅ Admin CSS variables found"
else
    echo "❌ Admin CSS variables missing"
fi

if grep -q -- "--user-" "$CSS_DIR/main-user.css" 2>/dev/null; then
    echo "✅ User CSS variables found"
else
    echo "❌ User CSS variables missing"
fi

# Check for old main.css references
echo ""
echo "🔍 Kiểm tra Old CSS References..."

old_refs=$(grep -r "main\.css" resources/views/ 2>/dev/null | wc -l)
if [ "$old_refs" -eq 0 ]; then
    echo "✅ No old main.css references found"
else
    echo "⚠️  Found $old_refs references to old main.css:"
    grep -r "main\.css" resources/views/ 2>/dev/null
fi

echo ""
echo "=================================================="
echo "🎯 CSS Structure Validation Complete!"
echo "=================================================="
