# Marketplace Product Data Normalization

## Overview

The Marketplace Product Data Normalization system ensures data consistency and integrity across all marketplace products. It automatically detects and fixes common data inconsistencies, validates product information, and maintains data quality standards.

## Features

### 1. Digital Product Type Normalization
- **Auto-detection**: Automatically converts products to `digital` type when they have digital files
- **Consistency Check**: Converts products marked as `digital` but without digital files to `new_product`
- **File Validation**: Validates digital files through both JSON field and Media relationships

### 2. Pricing Normalization
- **Sale Logic**: Disables sales for products without sale prices
- **Price Validation**: Swaps prices when sale price is higher than regular price
- **Expired Sales**: Automatically disables expired sales based on `sale_ends_at`

### 3. Stock Status Normalization
- **Digital Products**: Ensures digital products are always in stock and don't manage stock
- **Physical Products**: Updates stock status based on quantity and management settings
- **Availability Logic**: Normalizes availability based on product type and stock settings

### 4. Data Integrity Validation
- **Required Fields**: Validates presence of critical fields (name, slug, price)
- **Array Fields**: Ensures JSON array fields contain valid data
- **Slug Generation**: Auto-generates unique slugs for products without them
- **SKU Generation**: Auto-generates unique SKUs when missing

## Usage

### Command Line Interface

#### Run Full Normalization
```bash
# Dry run to see what would be changed
php artisan marketplace:normalize-data --dry-run --all

# Apply all normalizations
php artisan marketplace:normalize-data --all
```

#### Specific Normalizations
```bash
# Fix only digital product inconsistencies
php artisan marketplace:normalize-data --fix-digital

# Fix only array field issues
php artisan marketplace:normalize-data --fix-arrays

# Fix only pricing issues
php artisan marketplace:normalize-data --fix-prices

# Regenerate missing slugs
php artisan marketplace:normalize-data --fix-slugs
```

### Programmatic Usage

#### Service Class
```php
use App\Services\MarketplaceDataNormalizationService;

$service = app(MarketplaceDataNormalizationService::class);

// Normalize a single product
$changes = $service->normalizeProduct($product);
if (!empty($changes)) {
    $product->update($changes);
}

// Batch normalize all products
$stats = $service->batchNormalize(100); // Process 100 products at a time

// Get normalization statistics
$stats = $service->getNormalizationStats();

// Validate product integrity
$issues = $service->validateProductIntegrity($product);
```

#### Model Methods
```php
// Normalize a product
$product->normalize();

// Validate product integrity
$issues = $product->validateIntegrity();

// Check if product is digital
$isDigital = $product->isDigitalProduct();

// Get effective price (considers sales)
$price = $product->getEffectivePrice();

// Get discount percentage
$discount = $product->getDiscountPercentage();

// Check availability
$available = $product->isAvailable();
```

### Automatic Normalization

The system includes an Observer that automatically normalizes data when products are created or updated:

```php
// This will automatically normalize the product data
$product = MarketplaceProduct::create([
    'name' => 'Test Product',
    'price' => 100.00,
    'product_type' => 'digital',
    'digital_files' => null, // Will be converted to new_product
    // ... other fields
]);
```

## Database Enhancements

### ~~Normalized View~~ (DEPRECATED)
~~A database view `marketplace_products_normalized` provides normalized data:~~

⚠️ **DEPRECATED as of 2025-08-24**: The `marketplace_products_normalized` view has been removed because it was not being used in the application. All normalization logic is now handled in the application layer through the `MarketplaceDataNormalizationService` and model methods.

For historical reference, the view definition is backed up at: `database/backups/marketplace_products_normalized_view_backup.sql`

### Performance Indexes
Added indexes for better query performance:
- `idx_product_type_active_status`
- `idx_seller_type_active`
- `idx_price_active`
- `idx_view_count`
- `idx_rating_average`

### Data Constraints
Added check constraints (MySQL 8.0+):
- Sale price must be ≤ regular price when on sale
- Stock quantity must be ≥ 0
- Price must be ≥ 0
- Rating average must be between 0 and 5

## Normalization Rules

### Digital Product Detection
1. Product has `product_type = 'digital'`
2. Product has non-empty `digital_files` JSON array
3. Product has related digital files through Media model

### Pricing Rules
1. Products on sale must have a valid sale price
2. Sale price must be less than regular price
3. Expired sales are automatically disabled

### Stock Rules
1. Digital products: Always in stock, no stock management
2. Physical products: Stock based on quantity and management settings
3. Out of stock when managed stock ≤ 0

### Data Quality Rules
1. All products must have names and slugs
2. Prices must be non-negative
3. Array fields must contain valid JSON
4. SKUs must be unique

## Monitoring and Logging

### Logging
All normalization actions are logged:
```php
Log::info("Product #{$product->id} converted to digital (has digital files)");
Log::warning("Invalid JSON in field tags, reset to empty array");
```

### Statistics
Get comprehensive statistics:
```php
$stats = $service->getNormalizationStats();
// Returns:
// - total_products
// - digital_products  
// - products_with_digital_files
// - products_on_sale
// - products_without_slugs
// - out_of_stock_products
// - products_with_issues
```

## Testing

Run the test suite:
```bash
php artisan test tests/Feature/MarketplaceDataNormalizationTest.php
```

The test suite covers:
- Digital product type normalization
- Pricing inconsistency fixes
- Stock status normalization
- Slug generation
- Array field normalization
- Data integrity validation
- Batch processing
- Observer functionality

## Best Practices

1. **Regular Maintenance**: Run normalization weekly or after bulk imports
2. **Dry Run First**: Always use `--dry-run` to preview changes
3. **Monitor Logs**: Check logs for normalization actions and warnings
4. **Validate After Changes**: Run integrity checks after bulk updates
5. **Backup Data**: Always backup before running normalization on production

## Troubleshooting

### Common Issues

**Digital products not detected**
- Check if `digital_files` field contains valid JSON array
- Verify Media relationships are properly set up

**Pricing issues persist**
- Ensure sale dates are properly set
- Check for custom pricing logic in application

**Performance issues**
- Use batch processing for large datasets
- Run during low-traffic periods
- Monitor database performance during normalization

### Error Recovery
If normalization fails:
1. Check error logs for specific issues
2. Run with `--dry-run` to identify problems
3. Fix data manually if needed
4. Re-run normalization

## Migration Notes

When upgrading:
1. Run the migration: `php artisan migrate`
2. Run full normalization: `php artisan marketplace:normalize-data --all`
3. Verify data integrity: Check statistics and run tests
4. Monitor application for any issues
