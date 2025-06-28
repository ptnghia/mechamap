#!/bin/bash

# =============================================================================
# DASON TEMPLATE INTEGRATION SCRIPT FOR MECHAMAP
# =============================================================================
# This script automates the integration of Dason Laravel template into MechaMap
# Author: MechaMap Development Team
# Version: 1.0.0
# =============================================================================

set -e  # Exit on any error

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
DASON_PATH="./Dason-Laravel_v1.0.0/Admin"
MECHAMAP_PATH="."
BACKUP_DIR="./backups/dason_integration_$(date +%Y%m%d_%H%M%S)"
LOG_FILE="./logs/dason_integration.log"

# Create necessary directories
mkdir -p backups logs

# Logging function
log() {
    echo -e "${GREEN}[$(date '+%Y-%m-%d %H:%M:%S')]${NC} $1" | tee -a "$LOG_FILE"
}

error() {
    echo -e "${RED}[ERROR]${NC} $1" | tee -a "$LOG_FILE"
    exit 1
}

warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1" | tee -a "$LOG_FILE"
}

info() {
    echo -e "${BLUE}[INFO]${NC} $1" | tee -a "$LOG_FILE"
}

# =============================================================================
# PHASE 1: PREPARATION & BACKUP
# =============================================================================
phase1_backup() {
    log "🔄 PHASE 1: Starting backup and preparation..."
    
    # Create backup directory
    mkdir -p "$BACKUP_DIR"
    
    # Backup critical files
    info "Creating backup of critical files..."
    cp -r resources/ "$BACKUP_DIR/resources_backup/"
    cp -r public/ "$BACKUP_DIR/public_backup/"
    cp -r app/ "$BACKUP_DIR/app_backup/"
    cp -r routes/ "$BACKUP_DIR/routes_backup/"
    cp -r config/ "$BACKUP_DIR/config_backup/"
    cp package.json "$BACKUP_DIR/package.json.backup"
    cp composer.json "$BACKUP_DIR/composer.json.backup"
    cp webpack.mix.js "$BACKUP_DIR/webpack.mix.js.backup" 2>/dev/null || true
    
    # Database backup
    info "Creating database backup..."
    if command -v mysqldump &> /dev/null; then
        mysqldump mechamap_db > "$BACKUP_DIR/database_backup.sql" 2>/dev/null || warning "Database backup failed - please backup manually"
    else
        warning "mysqldump not found - please backup database manually"
    fi
    
    # Git backup
    info "Creating git backup..."
    git tag "pre-dason-integration-$(date +%Y%m%d_%H%M%S)" 2>/dev/null || warning "Git tag creation failed"
    
    log "✅ Phase 1 completed: Backup created at $BACKUP_DIR"
}

# =============================================================================
# PHASE 2: DEPENDENCIES UPDATE
# =============================================================================
phase2_dependencies() {
    log "🔄 PHASE 2: Updating dependencies..."
    
    # Check if Dason template exists
    if [ ! -d "$DASON_PATH" ]; then
        error "Dason template not found at $DASON_PATH"
    fi
    
    # Backup current package.json
    cp package.json "$BACKUP_DIR/package.json.original"
    
    # Merge package.json dependencies
    info "Merging package.json dependencies..."
    
    # Create merged package.json
    cat > package_merged.json << 'EOF'
{
    "private": true,
    "name": "MechaMap",
    "version": "2.0.0",
    "description": "MechaMap - Mechanical Engineering Community Platform",
    "scripts": {
        "dev": "npm run development",
        "development": "mix",
        "watch": "mix watch",
        "watch-poll": "mix watch -- --watch-options-poll=1000",
        "hot": "mix watch --hot",
        "prod": "npm run production",
        "production": "mix --production"
    },
    "devDependencies": {
        "axios": "^1.6.0",
        "browser-sync": "^2.27.7",
        "browser-sync-webpack-plugin": "^2.3.0",
        "laravel-mix": "^6.0.49",
        "lodash": "^4.17.21",
        "postcss": "^8.4.31",
        "resolve-url-loader": "^5.0.0",
        "rtlcss": "^4.1.1",
        "sass": "^1.69.5",
        "sass-loader": "^13.3.2"
    },
    "dependencies": {
        "@ckeditor/ckeditor5-build-classic": "^40.0.0",
        "@curiosityx/bootstrap-session-timeout": "^1.0.0",
        "@fullcalendar/bootstrap": "^6.1.9",
        "@fullcalendar/core": "^6.1.9",
        "@fullcalendar/daygrid": "^6.1.9",
        "@fullcalendar/interaction": "^6.1.9",
        "@fullcalendar/timegrid": "^6.1.9",
        "@simonwep/pickr": "^1.8.2",
        "alertifyjs": "^1.13.1",
        "apexcharts": "^3.44.0",
        "bootstrap": "^5.3.2",
        "bootstrap-datepicker": "^1.10.0",
        "bootstrap-touchspin": "^4.3.0",
        "chart.js": "^4.4.0",
        "choices.js": "^10.2.0",
        "datatables.net": "^1.13.6",
        "datatables.net-bs5": "^1.13.6",
        "datatables.net-buttons": "^2.4.2",
        "datatables.net-buttons-bs5": "^2.4.2",
        "datatables.net-responsive": "^2.5.0",
        "datatables.net-responsive-bs5": "^2.5.0",
        "dropzone": "^6.0.0",
        "echarts": "^5.4.3",
        "feather-icons": "^4.29.1",
        "flatpickr": "^4.6.13",
        "glightbox": "^3.2.0",
        "jquery": "^3.7.1",
        "jquery-validation": "^1.19.5",
        "masonry-layout": "^4.2.2",
        "metismenu": "^3.0.7",
        "nouislider": "^15.7.1",
        "select2": "^4.1.0",
        "simplebar": "^6.2.5",
        "sweetalert2": "^11.7.32",
        "swiper": "^10.3.1",
        "tinymce": "^6.7.2"
    }
}
EOF
    
    # Replace package.json
    mv package_merged.json package.json
    
    # Install dependencies
    info "Installing npm dependencies..."
    npm install || error "npm install failed"
    
    log "✅ Phase 2 completed: Dependencies updated"
}

# =============================================================================
# PHASE 3: ASSETS MIGRATION
# =============================================================================
phase3_assets() {
    log "🔄 PHASE 3: Migrating assets..."
    
    # Create assets backup
    cp -r public/ "$BACKUP_DIR/public_pre_assets/"
    
    # Copy Dason assets
    info "Copying Dason assets..."
    
    # Create assets directory structure
    mkdir -p public/assets/{css,js,images,fonts,libs}
    
    # Copy CSS files
    cp -r "$DASON_PATH/public/assets/css/"* public/assets/css/ 2>/dev/null || warning "CSS copy failed"
    
    # Copy JS files
    cp -r "$DASON_PATH/public/assets/js/"* public/assets/js/ 2>/dev/null || warning "JS copy failed"
    
    # Copy images
    cp -r "$DASON_PATH/public/assets/images/"* public/assets/images/ 2>/dev/null || warning "Images copy failed"
    
    # Copy fonts
    cp -r "$DASON_PATH/public/assets/fonts/"* public/assets/fonts/ 2>/dev/null || warning "Fonts copy failed"
    
    # Copy libraries
    cp -r "$DASON_PATH/public/assets/libs/"* public/assets/libs/ 2>/dev/null || warning "Libraries copy failed"
    
    # Update webpack.mix.js
    info "Updating webpack.mix.js..."
    cat > webpack.mix.js << 'EOF'
const mix = require('laravel-mix');

// Compile main application assets
mix.js('resources/js/app.js', 'public/js')
   .sass('resources/sass/app.scss', 'public/css')
   .options({
       processCssUrls: false
   });

// Copy Dason assets
mix.copy('public/assets/css/app.min.css', 'public/css/dason-admin.css')
   .copy('public/assets/js/app.min.js', 'public/js/dason-admin.js');

// Compile custom admin styles
mix.sass('resources/sass/admin.scss', 'public/css/admin.css');

// Version files for cache busting
if (mix.inProduction()) {
    mix.version();
}
EOF
    
    log "✅ Phase 3 completed: Assets migrated"
}

# =============================================================================
# PHASE 4: LAYOUT INTEGRATION
# =============================================================================
phase4_layouts() {
    log "🔄 PHASE 4: Integrating layouts..."
    
    # Backup current views
    cp -r resources/views/ "$BACKUP_DIR/views_pre_layouts/"
    
    # Create new admin layout structure
    info "Creating new admin layout structure..."
    
    mkdir -p resources/views/admin/layouts/dason
    mkdir -p resources/views/admin/components
    
    # Copy and adapt Dason layouts
    cp -r "$DASON_PATH/resources/views/layouts/"* resources/views/admin/layouts/dason/ 2>/dev/null || warning "Layout copy failed"
    
    # Create MechaMap admin master layout
    cat > resources/views/admin/layouts/master.blade.php << 'EOF'
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <title>@yield('title', 'Admin Dashboard') | MechaMap - Mechanical Engineering Platform</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="MechaMap Admin Dashboard" name="description" />
    <meta content="MechaMap Team" name="author" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('images/favicon.svg') }}">
    
    @include('admin.layouts.partials.head-css')
    @stack('styles')
</head>

<body data-layout="vertical" data-topbar="light" data-sidebar="dark">
    <!-- Begin page -->
    <div id="layout-wrapper">
        @include('admin.layouts.partials.topbar')
        @include('admin.layouts.partials.sidebar')
        
        <!-- Start right Content here -->
        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    @yield('content')
                </div>
            </div>
            @include('admin.layouts.partials.footer')
        </div>
        <!-- end main content-->
    </div>
    <!-- END layout-wrapper -->

    @include('admin.layouts.partials.right-sidebar')
    @include('admin.layouts.partials.vendor-scripts')
    @stack('scripts')
</body>
</html>
EOF
    
    log "✅ Phase 4 completed: Layouts integrated"
}

# =============================================================================
# MAIN EXECUTION
# =============================================================================
main() {
    log "🚀 Starting Dason Template Integration for MechaMap..."
    log "📁 Dason Path: $DASON_PATH"
    log "📁 MechaMap Path: $MECHAMAP_PATH"
    log "📁 Backup Directory: $BACKUP_DIR"
    
    # Check prerequisites
    if [ ! -d "$DASON_PATH" ]; then
        error "Dason template directory not found: $DASON_PATH"
    fi
    
    if [ ! -f "composer.json" ]; then
        error "Not in a Laravel project directory"
    fi
    
    # Execute phases
    phase1_backup
    phase2_dependencies
    phase3_assets
    phase4_layouts
    
    log "🎉 Dason integration completed successfully!"
    log "📋 Next steps:"
    log "   1. Run: npm run dev"
    log "   2. Test admin panel: /admin"
    log "   3. Customize branding and colors"
    log "   4. Update navigation menus"
    log "   5. Test all admin functionality"
    log ""
    log "📁 Backup location: $BACKUP_DIR"
    log "📄 Log file: $LOG_FILE"
}

# Execute main function
main "$@"
