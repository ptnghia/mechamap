#!/bin/bash

# Setup Script for Showcase Rating System
# Chạy migrations và seeders cho hệ thống rating & comment tích hợp

echo "🚀 Setting up Showcase Rating System..."
echo "======================================"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check if we're in Laravel root directory
if [ ! -f "artisan" ]; then
    print_error "Please run this script from Laravel root directory"
    exit 1
fi

# Step 1: Run migrations
print_status "Running database migrations..."
php artisan migrate --path=database/migrations/2025_01_22_000001_extend_showcase_ratings_for_media.php
if [ $? -eq 0 ]; then
    print_success "Extended showcase_ratings table"
else
    print_error "Failed to extend showcase_ratings table"
    exit 1
fi

php artisan migrate --path=database/migrations/2025_01_22_000002_create_showcase_rating_replies_table.php
if [ $? -eq 0 ]; then
    print_success "Created showcase_rating_replies table"
else
    print_error "Failed to create showcase_rating_replies table"
    exit 1
fi

php artisan migrate --path=database/migrations/2025_01_22_000003_create_showcase_rating_likes_table.php
if [ $? -eq 0 ]; then
    print_success "Created showcase_rating_likes table"
else
    print_error "Failed to create showcase_rating_likes table"
    exit 1
fi

php artisan migrate --path=database/migrations/2025_01_22_000004_create_showcase_rating_reply_likes_table.php
if [ $? -eq 0 ]; then
    print_success "Created showcase_rating_reply_likes table"
else
    print_error "Failed to create showcase_rating_reply_likes table"
    exit 1
fi

php artisan migrate --path=database/migrations/2025_01_22_000005_create_rating_system_indexes.php
if [ $? -eq 0 ]; then
    print_success "Created performance indexes"
else
    print_error "Failed to create performance indexes"
    exit 1
fi

# Step 2: Clear caches
print_status "Clearing application caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

print_success "Caches cleared"

# Step 3: Run seeders (optional)
read -p "Do you want to run test seeders? (y/n): " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    print_status "Running test seeders..."
    php artisan db:seed --class=ShowcaseRatingSystemSeeder
    if [ $? -eq 0 ]; then
        print_success "Test data seeded successfully"
    else
        print_warning "Seeder failed - this is optional"
    fi
fi

# Step 4: Verify setup
print_status "Verifying database setup..."

# Check if tables exist
tables=("showcase_ratings" "showcase_rating_replies" "showcase_rating_likes" "showcase_rating_reply_likes")
for table in "${tables[@]}"; do
    result=$(php artisan tinker --execute="echo Schema::hasTable('$table') ? 'EXISTS' : 'MISSING';")
    if [[ $result == *"EXISTS"* ]]; then
        print_success "Table $table exists"
    else
        print_error "Table $table is missing"
    fi
done

# Step 5: Generate IDE helper (if available)
if [ -f "vendor/bin/ide-helper" ]; then
    print_status "Generating IDE helper files..."
    php artisan ide-helper:models --write
    print_success "IDE helper files generated"
fi

echo ""
echo "======================================"
print_success "Showcase Rating System setup completed!"
echo ""
echo "📋 Summary:"
echo "  ✅ Extended showcase_ratings table with media & likes support"
echo "  ✅ Created showcase_rating_replies table for threaded discussions"
echo "  ✅ Created like system tables for ratings and replies"
echo "  ✅ Added performance indexes"
echo "  ✅ Updated model relationships"
echo ""
echo "🎯 Next steps:"
echo "  1. Update your controllers to handle new functionality"
echo "  2. Test the new rating & comment system"
echo "  3. Update API endpoints if needed"
echo "  4. Run tests to ensure everything works"
echo ""
echo "📚 Documentation:"
echo "  - Database schema: docs/database-redesign-rating-comment-integration.md"
echo "  - UI changes: docs/showcase-rating-comment-redesign.md"
echo ""
print_success "Happy coding! 🚀"
