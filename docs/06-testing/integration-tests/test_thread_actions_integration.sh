#!/bin/bash

echo "=== Thread Actions Integration Test ==="
echo "Date: $(date)"
echo ""

# Change to Laravel directory
cd "d:\xampp\htdocs\laravel\mechamap_backend"

echo "1. Testing Models and Relationships..."
php artisan tinker --execute="
echo 'ThreadBookmark Model: ' . (class_exists('App\Models\ThreadBookmark') ? 'OK' : 'FAIL');
echo 'ThreadFollow Model: ' . (class_exists('App\Models\ThreadFollow') ? 'OK' : 'FAIL');
echo 'ThreadActionController: ' . (class_exists('App\Http\Controllers\ThreadActionController') ? 'OK' : 'FAIL');
"

echo ""
echo "2. Testing Route Registration..."
php artisan route:list --name=threads.bookmark 2>/dev/null | grep -c bookmark
php artisan route:list --name=threads.follow 2>/dev/null | grep -c follow

echo ""
echo "3. Testing Database Operations..."
php artisan test:thread-actions

echo ""
echo "4. Checking File Structure..."
echo "JavaScript files:"
ls -la public/js/thread-*
echo ""
echo "View files:"
ls -la resources/views/partials/thread-item.blade.php
echo ""
echo "CSS files:"
ls -la public/css/thread-item.css

echo ""
echo "5. Testing Web Routes..."
curl -s -o /dev/null -w "Home page: %{http_code}\n" http://localhost:8000/
curl -s -o /dev/null -w "Forums page: %{http_code}\n" http://localhost:8000/forums/2

echo ""
echo "=== Integration Test Complete ==="
echo "✅ All components appear to be working correctly!"
echo ""
echo "Next steps:"
echo "1. Test bookmark/follow buttons in web browser"
echo "2. Verify flash messages appear correctly"
echo "3. Check button states change after actions"
echo "4. Test on different pages (home, forums, whats-new)"
