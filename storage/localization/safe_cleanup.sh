#!/bin/bash

# Safe Cleanup Script for Old Localization Files
# Run this after confirming new system works correctly

echo "🧹 Starting safe cleanup..."

# Move old files to archive (don't delete)
if [ -d "resources/lang_old" ]; then
    echo "📦 Archiving old lang files..."
    mv resources/lang_old storage/localization/archive_lang_old_$(date +%Y%m%d)
fi

# Archive old scripts (keep for reference)
if [ -d "scripts/localization_old" ]; then
    echo "📦 Archiving old scripts..."
    mv scripts/localization_old storage/localization/archive_scripts_$(date +%Y%m%d)
fi

echo "✅ Cleanup completed safely"
echo "📁 Old files archived in storage/localization/"
