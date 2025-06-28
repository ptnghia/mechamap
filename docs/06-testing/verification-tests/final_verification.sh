#!/bin/bash

echo "🔧 MECHAMAP BACKEND - FINAL SYSTEM VERIFICATION"
echo "=============================================="
echo ""

cd /d/xampp/htdocs/laravel/mechamap_backend

echo "1️⃣ KIỂM TRA PHP VÀ LARAVEL..."
php --version | head -1
php artisan --version
echo ""

echo "2️⃣ KIỂM TRA DATABASE CONNECTION..."
php artisan db:show --counts 2>/dev/null || echo "❌ Database connection issue"
echo ""

echo "3️⃣ KIỂM TRA MEDIA DATA..."
php -r "
require_once 'vendor/autoload.php';
\$app = require_once 'bootstrap/app.php';
\$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
echo 'Media records: ' . App\Models\Media::count() . PHP_EOL;
echo 'Threads with media: ' . App\Models\Thread::whereHas('media')->count() . PHP_EOL;
"
echo ""

echo "4️⃣ KIỂM TRA STORAGE FILES..."
echo "Files in storage/app/public/thread-images: $(find storage/app/public/thread-images -name '*.jpg' | wc -l)"
echo "Files in public/storage/thread-images: $(find public/storage/thread-images -name '*.jpg' | wc -l)"
echo ""

echo "5️⃣ TEST SPECIFIC URLS FROM ERROR LOG..."
test_urls=(
    "thread-89-image-0.jpg"
    "thread-87-image-0.jpg"
    "thread-82-image-0.jpg"
    "thread-81-image-0.jpg"
    "thread-83-image-0.jpg"
)

for url in "${test_urls[@]}"; do
    if [[ -f "public/storage/thread-images/$url" ]]; then
        echo "✅ $url"
    else
        echo "❌ $url MISSING"
    fi
done
echo ""

echo "6️⃣ KIỂM TRA WEB ACCESS..."
echo "Starting PHP built-in server for 5 seconds..."
timeout 5s php -S localhost:8181 -t public >/dev/null 2>&1 &
sleep 2

# Test main page
if curl -s -o /dev/null -w "%{http_code}" http://localhost:8181/ | grep -q "200"; then
    echo "✅ Main page accessible"
else
    echo "❌ Main page not accessible"
fi

# Test image
if curl -s -o /dev/null -w "%{http_code}" http://localhost:8181/storage/thread-images/thread-89-image-0.jpg | grep -q "200"; then
    echo "✅ thread-89-image-0.jpg accessible"
else
    echo "❌ thread-89-image-0.jpg not accessible"
fi

# Kill server
pkill -f "php -S localhost:8181"
echo ""

echo "7️⃣ KIỂM TRA JAVASCRIPT DEPENDENCIES..."
if grep -q "jquery-3.7.1" resources/views/layouts/app.blade.php; then
    echo "✅ jQuery dependency"
else
    echo "❌ jQuery missing"
fi

if grep -q "lightbox2" resources/views/layouts/app.blade.php; then
    echo "✅ Lightbox dependency"
else
    echo "❌ Lightbox missing"
fi

if grep -q "bootstrap@5.3.2" resources/views/layouts/app.blade.php; then
    echo "✅ Bootstrap dependency"
else
    echo "❌ Bootstrap missing"
fi
echo ""

echo "8️⃣ SUMMARY..."
total_media=$(php -r "
require_once 'vendor/autoload.php';
\$app = require_once 'bootstrap/app.php';
\$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
echo App\Models\Media::count();
")

image_files=$(find public/storage/thread-images -name '*.jpg' 2>/dev/null | wc -l)

echo "📊 Final Statistics:"
echo "   - Media records: $total_media"
echo "   - Image files: $image_files"
echo "   - Status: $([ $total_media -gt 0 ] && [ $image_files -gt 0 ] && echo '✅ READY FOR PRODUCTION' || echo '❌ ISSUES FOUND')"
echo ""
echo "🎉 VERIFICATION COMPLETE!"
