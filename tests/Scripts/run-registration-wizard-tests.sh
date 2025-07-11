#!/bin/bash

# 🧪 Registration Wizard Test Runner
# Comprehensive testing script for multi-step registration wizard

set -e

echo "🧙‍♂️ MechaMap Registration Wizard Test Suite"
echo "=============================================="

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Test configuration
TEST_ENV="testing"
DB_CONNECTION="sqlite"
DB_DATABASE=":memory:"

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

# Function to run tests with error handling
run_test() {
    local test_name="$1"
    local test_command="$2"
    
    print_status "Running $test_name..."
    
    if eval "$test_command"; then
        print_success "$test_name completed successfully"
        return 0
    else
        print_error "$test_name failed"
        return 1
    fi
}

# Setup test environment
setup_test_environment() {
    print_status "Setting up test environment..."
    
    # Set environment variables
    export APP_ENV="$TEST_ENV"
    export DB_CONNECTION="$DB_CONNECTION"
    export DB_DATABASE="$DB_DATABASE"
    
    # Clear cache
    php artisan config:clear
    php artisan cache:clear
    php artisan view:clear
    
    # Run migrations
    php artisan migrate:fresh --env="$TEST_ENV"
    
    print_success "Test environment setup complete"
}

# Cleanup test environment
cleanup_test_environment() {
    print_status "Cleaning up test environment..."
    
    # Clear test data
    php artisan cache:clear
    php artisan config:clear
    
    print_success "Test environment cleanup complete"
}

# Run unit tests
run_unit_tests() {
    print_status "Running Unit Tests..."
    
    run_test "Registration Wizard Service Tests" \
        "php artisan test tests/Unit/RegistrationWizardLogicTest.php --env=$TEST_ENV"
    
    run_test "Form Request Validation Tests" \
        "php artisan test tests/Unit/BasicRegistrationRequestTest.php --env=$TEST_ENV"
    
    run_test "Business Registration Request Tests" \
        "php artisan test tests/Unit/BusinessRegistrationRequestTest.php --env=$TEST_ENV"
}

# Run feature tests
run_feature_tests() {
    print_status "Running Feature Tests..."
    
    run_test "Registration Wizard Integration Tests" \
        "php artisan test tests/Feature/RegistrationWizardIntegrationTest.php --env=$TEST_ENV"
    
    run_test "Registration Controller Tests" \
        "php artisan test tests/Feature/RegisterWizardControllerTest.php --env=$TEST_ENV"
}

# Run browser tests
run_browser_tests() {
    print_status "Running Browser Tests..."
    
    # Check if Dusk is available
    if ! php artisan dusk:install --help > /dev/null 2>&1; then
        print_warning "Laravel Dusk not installed. Skipping browser tests."
        return 0
    fi
    
    # Start Chrome driver
    print_status "Starting Chrome driver..."
    php artisan dusk:chrome-driver --detect
    
    run_test "Registration Wizard Browser Tests" \
        "php artisan dusk tests/Browser/RegistrationWizardBrowserTest.php --env=$TEST_ENV"
}

# Run API tests
run_api_tests() {
    print_status "Running API Tests..."
    
    run_test "Username Availability API Tests" \
        "php artisan test --filter=test_username_availability_check --env=$TEST_ENV"
    
    run_test "Field Validation API Tests" \
        "php artisan test --filter=test_field_validation_endpoint --env=$TEST_ENV"
    
    run_test "Auto-save API Tests" \
        "php artisan test --filter=test_auto_save_functionality --env=$TEST_ENV"
}

# Run performance tests
run_performance_tests() {
    print_status "Running Performance Tests..."
    
    run_test "Rate Limiting Tests" \
        "php artisan test --filter=test_rate_limiting --env=$TEST_ENV"
    
    run_test "Session Management Performance" \
        "php artisan test --filter=test_session_management --env=$TEST_ENV"
}

# Run security tests
run_security_tests() {
    print_status "Running Security Tests..."
    
    run_test "CSRF Protection Tests" \
        "php artisan test --filter=csrf --env=$TEST_ENV"
    
    run_test "Input Validation Security Tests" \
        "php artisan test --filter=validation --env=$TEST_ENV"
    
    run_test "File Upload Security Tests" \
        "php artisan test --filter=file_upload --env=$TEST_ENV"
}

# Generate test report
generate_test_report() {
    print_status "Generating test report..."
    
    local report_file="tests/Reports/registration-wizard-test-report-$(date +%Y%m%d-%H%M%S).html"
    
    # Create reports directory if it doesn't exist
    mkdir -p tests/Reports
    
    # Run tests with coverage
    php artisan test tests/Feature/RegistrationWizardIntegrationTest.php \
        --coverage-html tests/Reports/coverage \
        --env="$TEST_ENV"
    
    print_success "Test report generated: $report_file"
}

# Main test execution
main() {
    local test_type="${1:-all}"
    local failed_tests=0
    
    echo "Starting Registration Wizard Test Suite..."
    echo "Test Type: $test_type"
    echo "Environment: $TEST_ENV"
    echo ""
    
    # Setup
    setup_test_environment || exit 1
    
    # Run tests based on type
    case "$test_type" in
        "unit")
            run_unit_tests || ((failed_tests++))
            ;;
        "feature")
            run_feature_tests || ((failed_tests++))
            ;;
        "browser")
            run_browser_tests || ((failed_tests++))
            ;;
        "api")
            run_api_tests || ((failed_tests++))
            ;;
        "performance")
            run_performance_tests || ((failed_tests++))
            ;;
        "security")
            run_security_tests || ((failed_tests++))
            ;;
        "all")
            run_unit_tests || ((failed_tests++))
            run_feature_tests || ((failed_tests++))
            run_api_tests || ((failed_tests++))
            run_performance_tests || ((failed_tests++))
            run_security_tests || ((failed_tests++))
            
            # Browser tests last (they take longest)
            if command -v google-chrome > /dev/null 2>&1; then
                run_browser_tests || ((failed_tests++))
            else
                print_warning "Chrome not found. Skipping browser tests."
            fi
            ;;
        *)
            print_error "Invalid test type: $test_type"
            echo "Valid types: unit, feature, browser, api, performance, security, all"
            exit 1
            ;;
    esac
    
    # Generate report for comprehensive tests
    if [[ "$test_type" == "all" ]]; then
        generate_test_report
    fi
    
    # Cleanup
    cleanup_test_environment
    
    # Summary
    echo ""
    echo "=============================================="
    if [[ $failed_tests -eq 0 ]]; then
        print_success "All tests passed! ✅"
        echo ""
        echo "🎉 Registration Wizard is ready for production!"
    else
        print_error "$failed_tests test suite(s) failed ❌"
        echo ""
        echo "🔧 Please fix the failing tests before deployment."
        exit 1
    fi
}

# Help function
show_help() {
    echo "Registration Wizard Test Runner"
    echo ""
    echo "Usage: $0 [test_type]"
    echo ""
    echo "Test Types:"
    echo "  unit        - Run unit tests only"
    echo "  feature     - Run feature tests only"
    echo "  browser     - Run browser tests only"
    echo "  api         - Run API tests only"
    echo "  performance - Run performance tests only"
    echo "  security    - Run security tests only"
    echo "  all         - Run all tests (default)"
    echo ""
    echo "Examples:"
    echo "  $0              # Run all tests"
    echo "  $0 unit         # Run unit tests only"
    echo "  $0 browser      # Run browser tests only"
    echo ""
}

# Check for help flag
if [[ "$1" == "-h" || "$1" == "--help" ]]; then
    show_help
    exit 0
fi

# Run main function
main "$@"
