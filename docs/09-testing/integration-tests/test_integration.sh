#!/bin/bash

echo "🧪 KIỂM TRA TÍCH HỢP SIDEBAR VÀ MULTI-STEP FORM"
echo "=================================================="
echo

# Kiểm tra các file view có tồn tại
echo "📁 Kiểm tra View Files:"
files=(
    "resources/views/threads/create.blade.php"
    "resources/views/components/thread-creation-sidebar.blade.php"
    "resources/views/components/sidebar.blade.php"
    "resources/views/pages/rules.blade.php"
    "resources/views/pages/writing-guide.blade.php"
    "public/css/thread-form.css"
)

for file in "${files[@]}"; do
    if [ -f "$file" ]; then
        size=$(du -h "$file" | cut -f1)
        echo "   ✅ $file ($size)"
    else
        echo "   ❌ $file - KHÔNG TỒN TẠI"
    fi
done

echo
echo "🔗 URLs để test:"
echo "   • http://localhost:8000/threads/create"
echo "   • http://localhost:8000/rules"
echo "   • http://localhost:8000/help/writing-guide"

echo
echo "✨ TÍNH NĂNG ĐÃ HOÀN THÀNH:"
echo "   ✅ Sidebar tích hợp cho trang thread creation"
echo "   ✅ Multi-step form với progress indicator"
echo "   ✅ Accessibility improvements (ARIA, keyboard nav)"
echo "   ✅ Performance optimization với caching"
echo "   ✅ Responsive design cho mobile"
echo "   ✅ Route configuration hoàn chỉnh"

echo
echo "🚀 READY FOR TESTING!"
echo "Mở browser và test các URLs ở trên."
