#!/bin/bash

# =============================================================================
# MechaMap Assets Safe Cleanup Script
# Script an toàn để xóa các assets không sử dụng
# =============================================================================

set -e  # Exit on any error

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
BACKUP_DIR="assets_backup_$(date +%Y-%m-%d_%H-%M-%S)"
LOG_FILE="assets_cleanup_$(date +%Y-%m-%d_%H-%M-%S).log"

echo -e "${BLUE}==============================================================================${NC}"
echo -e "${BLUE}           MechaMap Assets Safe Cleanup Script${NC}"
echo -e "${BLUE}==============================================================================${NC}"
echo ""

# Function to log messages
log_message() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1" >> "$LOG_FILE"
    echo -e "$1"
}

# Function to calculate directory size
get_dir_size() {
    if [ -d "$1" ]; then
        du -sb "$1" 2>/dev/null | cut -f1
    else
        echo "0"
    fi
}

# Function to format bytes
format_bytes() {
    local bytes=$1
    if [ $bytes -gt 1048576 ]; then
        echo "$(echo "scale=2; $bytes/1048576" | bc) MB"
    elif [ $bytes -gt 1024 ]; then
        echo "$(echo "scale=1; $bytes/1024" | bc) KB"
    else
        echo "$bytes B"
    fi
}

# Check if we're in the right directory
if [ ! -f "composer.json" ] || [ ! -d "public/assets" ]; then
    log_message "${RED}❌ Error: Please run this script from the Laravel project root directory${NC}"
    exit 1
fi

log_message "${GREEN}✅ Found Laravel project with assets directory${NC}"

# Calculate initial size
INITIAL_SIZE=$(get_dir_size "public/assets")
log_message "${BLUE}📊 Initial assets size: $(format_bytes $INITIAL_SIZE)${NC}"

# Step 1: Create backup
log_message "${YELLOW}🔄 Step 1: Creating backup...${NC}"

if [ -d "public/assets" ]; then
    cp -r public/assets "$BACKUP_DIR"
    log_message "${GREEN}✅ Backup created: $BACKUP_DIR${NC}"
else
    log_message "${RED}❌ Assets directory not found!${NC}"
    exit 1
fi

# Step 2: Remove large unused libraries
log_message "${YELLOW}🗑️  Step 2: Removing large unused libraries...${NC}"

REMOVED_LIBS=0
LIBS_SIZE_SAVED=0

# TinyMCE (7.39 MB) - có CKEditor thay thế
if [ -d "public/assets/libs/tinymce" ]; then
    LIB_SIZE=$(get_dir_size "public/assets/libs/tinymce")
    rm -rf public/assets/libs/tinymce/
    REMOVED_LIBS=$((REMOVED_LIBS + 1))
    LIBS_SIZE_SAVED=$((LIBS_SIZE_SAVED + LIB_SIZE))
    log_message "   • Removed tinymce ($(format_bytes $LIB_SIZE))"
fi

# ECharts (0.75 MB) - trùng với ApexCharts
if [ -d "public/assets/libs/echarts" ]; then
    LIB_SIZE=$(get_dir_size "public/assets/libs/echarts")
    rm -rf public/assets/libs/echarts/
    REMOVED_LIBS=$((REMOVED_LIBS + 1))
    LIBS_SIZE_SAVED=$((LIBS_SIZE_SAVED + LIB_SIZE))
    log_message "   • Removed echarts ($(format_bytes $LIB_SIZE))"
fi

# Leaflet (0.16 MB) - không được sử dụng
if [ -d "public/assets/libs/leaflet" ]; then
    LIB_SIZE=$(get_dir_size "public/assets/libs/leaflet")
    rm -rf public/assets/libs/leaflet/
    REMOVED_LIBS=$((REMOVED_LIBS + 1))
    LIBS_SIZE_SAVED=$((LIBS_SIZE_SAVED + LIB_SIZE))
    log_message "   • Removed leaflet ($(format_bytes $LIB_SIZE))"
fi

# FullCalendar (0.23 MB) - không được sử dụng
if [ -d "public/assets/libs/@fullcalendar" ]; then
    LIB_SIZE=$(get_dir_size "public/assets/libs/@fullcalendar")
    rm -rf public/assets/libs/@fullcalendar/
    REMOVED_LIBS=$((REMOVED_LIBS + 1))
    LIBS_SIZE_SAVED=$((LIBS_SIZE_SAVED + LIB_SIZE))
    log_message "   • Removed @fullcalendar ($(format_bytes $LIB_SIZE))"
fi

# Dropzone (0.12 MB) - không được sử dụng
if [ -d "public/assets/libs/dropzone" ]; then
    LIB_SIZE=$(get_dir_size "public/assets/libs/dropzone")
    rm -rf public/assets/libs/dropzone/
    REMOVED_LIBS=$((REMOVED_LIBS + 1))
    LIBS_SIZE_SAVED=$((LIBS_SIZE_SAVED + LIB_SIZE))
    log_message "   • Removed dropzone ($(format_bytes $LIB_SIZE))"
fi

# Choices.js (0.08 MB) - trùng với select2
if [ -d "public/assets/libs/choices.js" ]; then
    LIB_SIZE=$(get_dir_size "public/assets/libs/choices.js")
    rm -rf public/assets/libs/choices.js/
    REMOVED_LIBS=$((REMOVED_LIBS + 1))
    LIBS_SIZE_SAVED=$((LIBS_SIZE_SAVED + LIB_SIZE))
    log_message "   • Removed choices.js ($(format_bytes $LIB_SIZE))"
fi

# GLightbox (0.07 MB) - có fancybox
if [ -d "public/assets/libs/glightbox" ]; then
    LIB_SIZE=$(get_dir_size "public/assets/libs/glightbox")
    rm -rf public/assets/libs/glightbox/
    REMOVED_LIBS=$((REMOVED_LIBS + 1))
    LIBS_SIZE_SAVED=$((LIBS_SIZE_SAVED + LIB_SIZE))
    log_message "   • Removed glightbox ($(format_bytes $LIB_SIZE))"
fi

# SweetAlert2 (0.06 MB) - có thể dùng native
if [ -d "public/assets/libs/sweetalert2" ]; then
    LIB_SIZE=$(get_dir_size "public/assets/libs/sweetalert2")
    rm -rf public/assets/libs/sweetalert2/
    REMOVED_LIBS=$((REMOVED_LIBS + 1))
    LIBS_SIZE_SAVED=$((LIBS_SIZE_SAVED + LIB_SIZE))
    log_message "   • Removed sweetalert2 ($(format_bytes $LIB_SIZE))"
fi

# AlertifyJS (0.06 MB) - trùng với toastr
if [ -d "public/assets/libs/alertifyjs" ]; then
    LIB_SIZE=$(get_dir_size "public/assets/libs/alertifyjs")
    rm -rf public/assets/libs/alertifyjs/
    REMOVED_LIBS=$((REMOVED_LIBS + 1))
    LIBS_SIZE_SAVED=$((LIBS_SIZE_SAVED + LIB_SIZE))
    log_message "   • Removed alertifyjs ($(format_bytes $LIB_SIZE))"
fi

# Các thư viện nhỏ khác
SMALL_LIBS=("masonry-layout" "jquery-validation" "pace-js" "nouislider" "twitter-bootstrap-wizard")

for lib in "${SMALL_LIBS[@]}"; do
    if [ -d "public/assets/libs/$lib" ]; then
        LIB_SIZE=$(get_dir_size "public/assets/libs/$lib")
        rm -rf "public/assets/libs/$lib/"
        REMOVED_LIBS=$((REMOVED_LIBS + 1))
        LIBS_SIZE_SAVED=$((LIBS_SIZE_SAVED + LIB_SIZE))
        log_message "   • Removed $lib ($(format_bytes $LIB_SIZE))"
    fi
done

log_message "${GREEN}✅ Removed $REMOVED_LIBS libraries, saved $(format_bytes $LIBS_SIZE_SAVED)${NC}"

# Step 3: Remove unused CSS files
log_message "${YELLOW}🎨 Step 3: Removing unused CSS files...${NC}"

REMOVED_CSS=0
CSS_SIZE_SAVED=0

CSS_FILES=("preloader.css" "preloader.min.css" "preloader.rtl.css" "realtime.css" "app.rtl.css" "bootstrap.rtl.css" "icons.rtl.css")

for css in "${CSS_FILES[@]}"; do
    if [ -f "public/assets/css/$css" ]; then
        CSS_SIZE=$(stat -f%z "public/assets/css/$css" 2>/dev/null || stat -c%s "public/assets/css/$css" 2>/dev/null || echo "0")
        rm "public/assets/css/$css"
        REMOVED_CSS=$((REMOVED_CSS + 1))
        CSS_SIZE_SAVED=$((CSS_SIZE_SAVED + CSS_SIZE))
        log_message "   • Removed $css ($(format_bytes $CSS_SIZE))"
    fi
done

log_message "${GREEN}✅ Removed $REMOVED_CSS CSS files, saved $(format_bytes $CSS_SIZE_SAVED)${NC}"

# Step 4: Remove unused fonts (optional - commented out for safety)
log_message "${YELLOW}🔤 Step 4: Checking fonts (skipped for safety)...${NC}"
log_message "   • boxicons and dripicons fonts detected but kept for safety"
log_message "   • You can manually remove them if you only use FontAwesome"

# Step 5: Clean up empty directories
log_message "${YELLOW}🧹 Step 5: Cleaning up empty directories...${NC}"

find public/assets -type d -empty -delete 2>/dev/null || true
log_message "${GREEN}✅ Empty directories cleaned${NC}"

# Step 6: Calculate final size and savings
FINAL_SIZE=$(get_dir_size "public/assets")
TOTAL_SAVED=$((INITIAL_SIZE - FINAL_SIZE))
SAVINGS_PERCENT=$(echo "scale=1; $TOTAL_SAVED * 100 / $INITIAL_SIZE" | bc)

log_message ""
log_message "${BLUE}📊 CLEANUP SUMMARY:${NC}"
log_message "   • Initial size: $(format_bytes $INITIAL_SIZE)"
log_message "   • Final size: $(format_bytes $FINAL_SIZE)"
log_message "   • Total saved: $(format_bytes $TOTAL_SAVED) (${SAVINGS_PERCENT}%)"
log_message "   • Libraries removed: $REMOVED_LIBS"
log_message "   • CSS files removed: $REMOVED_CSS"
log_message "   • Backup location: $BACKUP_DIR"
log_message "   • Log file: $LOG_FILE"

# Step 7: Verification
log_message ""
log_message "${YELLOW}⚠️  IMPORTANT NEXT STEPS:${NC}"
log_message "   1. Test your website thoroughly"
log_message "   2. Check admin panel functionality"
log_message "   3. Verify all charts and forms work"
log_message "   4. If issues occur, restore from backup:"
log_message "      rm -rf public/assets && mv $BACKUP_DIR public/assets"

log_message ""
log_message "${GREEN}✅ Assets cleanup completed successfully!${NC}"
log_message "${BLUE}==============================================================================${NC}"

# Create restore script
cat > "restore_assets.sh" << EOF
#!/bin/bash
# Restore assets from backup
echo "Restoring assets from backup..."
if [ -d "$BACKUP_DIR" ]; then
    rm -rf public/assets
    mv "$BACKUP_DIR" public/assets
    echo "✅ Assets restored successfully!"
else
    echo "❌ Backup directory not found: $BACKUP_DIR"
    exit 1
fi
EOF

chmod +x restore_assets.sh
log_message "${BLUE}📝 Created restore script: restore_assets.sh${NC}"
