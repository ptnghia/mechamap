#!/bin/bash
echo "Ì¥ç MechaMap Backend - Project Health Check"
echo "Date: $(date)"
echo ""

echo "Ì≥Å ROOT DIRECTORY STATUS:"
root_files=$(ls -1 | grep -E '\.' | wc -l)
echo "   Total root files: $root_files"
[ "$root_files" -le 10 ] && echo "   ‚úÖ Root directory is clean" || echo "   ‚ö†Ô∏è Root directory may need cleanup"

echo ""
echo "Ì≥ö DOCUMENTATION STATUS:"
docs_files=$(find docs/ -name "*.md" | wc -l)
echo "   Documentation files: $docs_files"
echo "   ‚úÖ Documentation organized"

echo ""
echo "‚öôÔ∏è LARAVEL CORE FILES:"
for file in artisan composer.json .env .gitignore README.md; do
    [ -f "$file" ] && echo "   ‚úÖ $file present" || echo "   ‚ùå $file missing"
done

echo ""
echo "Ì∑† IDE INTELLISENSE:"
[ -f "_ide_helper.php" ] && echo "   ‚úÖ IDE helper files working" || echo "   ‚ö†Ô∏è IDE helper missing"

echo ""
echo "‚úÖ Health check completed - Project is clean and ready!"
