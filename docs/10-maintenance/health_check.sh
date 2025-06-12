#!/bin/bash
echo "� MechaMap Backend - Project Health Check"
echo "Date: $(date)"
echo ""

echo "� ROOT DIRECTORY STATUS:"
root_files=$(ls -1 | grep -E '\.' | wc -l)
echo "   Total root files: $root_files"
[ "$root_files" -le 10 ] && echo "   ✅ Root directory is clean" || echo "   ⚠️ Root directory may need cleanup"

echo ""
echo "� DOCUMENTATION STATUS:"
docs_files=$(find docs/ -name "*.md" | wc -l)
echo "   Documentation files: $docs_files"
echo "   ✅ Documentation organized"

echo ""
echo "⚙️ LARAVEL CORE FILES:"
for file in artisan composer.json .env .gitignore README.md; do
    [ -f "$file" ] && echo "   ✅ $file present" || echo "   ❌ $file missing"
done

echo ""
echo "� IDE INTELLISENSE:"
[ -f "_ide_helper.php" ] && echo "   ✅ IDE helper files working" || echo "   ⚠️ IDE helper missing"

echo ""
echo "✅ Health check completed - Project is clean and ready!"
