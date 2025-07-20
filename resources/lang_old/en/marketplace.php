<?php

return [
    // Marketplace Main
    'marketplace' => [
        'title' => 'MechaMap Marketplace',
        'subtitle' => 'Discover thousands of high-quality technical products from trusted suppliers',
        'search_placeholder' => 'Search products, categories, suppliers...',
        'products_available' => 'Products Available',
        'verified_sellers' => 'Verified Sellers',
        'items' => 'items',
        'no_categories_available' => 'No categories available',
        'no_featured_products_available' => 'No featured products available',
        'check_back_later' => 'Please check back later',
        'home' => 'Home',
        'marketplace' => 'Marketplace',
        'sold_by' => 'Sold by',
        'subcategories' => 'Subcategories',
        'commission' => 'Commission',
        'view' => 'View',
        'no_products_found' => 'No products found',
        'try_adjusting_filters' => 'Try adjusting your filters',
        'view_all_products' => 'View All Products',
        'browse_categories' => 'Browse Categories',
        'featured_products' => 'Featured Products',
        'view_all' => 'View All',
        'discover_products' => 'Discover thousands of high-quality technical products',
        'advanced_search' => 'Advanced Search',
        'sort' => 'Sort',
        'relevance' => 'Relevance',
        'latest' => 'Latest',
        'price_low_to_high' => 'Price: Low to High',
        'price_high_to_low' => 'Price: High to Low',
        'highest_rated' => 'Highest Rated',
        'most_popular' => 'Most Popular',
        'name_a_z' => 'Name: A-Z',
        'in_stock' => 'In Stock',
        'add_to_cart' => 'Add to Cart',
        'add_to_wishlist' => 'Add to Wishlist',
        'product_description' => 'Product Description',
        'related_products' => 'Related Products',
        'seller_not_available' => 'Seller not available',
    ],

    // Products
    'products' => [
        'title' => 'Products',
        'all' => 'All Products',
        'featured' => 'Featured Products',
        'popular' => 'Popular Products',
        'add_to_cart' => 'Add to Cart',
        'buy_now' => 'Buy Now',
        'verified' => 'Verified',
        'service' => 'Service',
        'manufacturer' => 'Manufacturer',
    ],

    // Categories
    'categories' => [
        'title' => 'Categories',
        'all' => 'All Categories',
        'subcategories' => 'Subcategories',
    ],

    // Suppliers
    'suppliers' => [
        'title' => 'Suppliers',
    ],

    // Cart
    'cart' => [
        'title' => 'Shopping Cart',
        'shopping_cart' => 'Shopping Cart',
        'select_all' => 'Select All',
        'clear_cart' => 'Clear Cart',
        'remove_selected' => 'Remove Selected',
        'items' => 'items',
        'item' => 'item',
        'qty' => 'Qty',
        'quantity' => 'Quantity',
        'available' => 'available',
        'remove' => 'Remove',
        'save_for_later' => 'Save for Later',
        'product_no_longer_available' => 'Product no longer available',
        'continue_shopping' => 'Continue Shopping',
        'empty' => 'Cart is empty',
        'empty_cart' => 'Empty Cart',
        'cart_empty' => 'Cart is empty',
        'add_products' => 'Add Products',
        'empty_cart_message' => 'Your cart is empty. Add products to continue.',

        // Order Summary
        'order_summary' => 'Order Summary',
        'subtotal' => 'Subtotal',
        'shipping' => 'Shipping',
        'tax' => 'Tax',
        'total' => 'Total',
        'free' => 'Free',
        'calculate_shipping' => 'Calculate shipping',
        'coupon_code' => 'Coupon code',
        'apply' => 'Apply',
        'proceed_to_checkout' => 'Proceed to Checkout',
        'checkout' => 'Checkout',
        'secure_checkout' => 'Secure Checkout',
        'ssl_encryption' => 'Secure checkout with SSL encryption',

        // Shipping Information
        'shipping_information' => 'Shipping Information',
        'free_shipping_over' => 'Free shipping on orders over $100',
        'standard_delivery' => 'Standard delivery: 3-5 business days',
        'express_delivery_available' => 'Express delivery available',

        // Messages
        'clear_cart_confirm' => 'Are you sure you want to clear your entire cart? This action cannot be undone.',
        'clear_cart_success' => 'Cart cleared successfully',
        'clear_cart_failed' => 'Failed to clear cart',
        'remove_selected_confirm' => 'Are you sure you want to remove :count selected item(s)?',
        'remove_selected_failed' => 'Failed to remove selected items',
        'please_select_items' => 'Please select items to remove',
        'coupon_required' => 'Please enter a coupon code',
        'coupon_apply_failed' => 'Failed to apply coupon',
        'save_for_later_message' => 'Save for later functionality will be implemented in future updates',
        'recently_viewed_products' => 'Recently Viewed Products',
        'no_recently_viewed' => 'No recently viewed products',
    ],

    // Checkout
    'checkout' => [
        'title' => 'Checkout',
        'secure_checkout' => 'Secure Checkout',
        'steps' => [
            'shipping' => 'Shipping',
            'payment' => 'Payment',
            'review' => 'Review',
        ],

        // Shipping Information
        'shipping_information' => 'Shipping Information',
        'first_name' => 'First Name',
        'last_name' => 'Last Name',
        'email_address' => 'Email Address',
        'phone_number' => 'Phone Number',
        'address_line_1' => 'Address Line 1',
        'address_line_2' => 'Address Line 2',
        'city' => 'City',
        'state_province' => 'State/Province',
        'postal_code' => 'Postal Code',
        'country' => 'Country',
        'select_country' => 'Select Country',
        'billing_same_as_shipping' => 'Billing address is the same as shipping address',
        'billing_information' => 'Billing Information',
        'back_to_cart' => 'Back to Cart',
        'continue_to_payment' => 'Continue to Payment',

        // Payment Information
        'payment_information' => 'Payment Information',
        'payment_method' => 'Payment Method',
        'credit_debit_card' => 'Credit/Debit Card (Stripe)',
        'bank_transfer' => 'Bank Transfer (SePay)',
        'stripe_redirect_message' => 'You will be redirected to Stripe to complete your payment securely.',
        'sepay_redirect_message' => 'You will be redirected to SePay payment page with QR Code for bank transfer.',
        'back_to_shipping' => 'Back to Shipping',
        'review_order' => 'Review Order',

        // Order Review
        'review_your_order' => 'Review Your Order',
        'shipping_address' => 'Shipping Address',
        'payment_method_label' => 'Payment Method',
        'back_to_payment' => 'Back to Payment',
        'complete_order' => 'Complete Order',
        'place_order' => 'Place Order',

        // Order Summary in Checkout
        'calculated_at_next_step' => 'Calculated at next step',
        'payment_secure_encrypted' => 'Your payment information is secure and encrypted',

        // Messages
        'failed_to_process_payment' => 'Failed to process payment information',
        'failed_to_load_review' => 'Failed to load order review',
    ],

    // Countries
    'countries' => [
        'vietnam' => 'Vietnam',
        'united_states' => 'United States',
        'canada' => 'Canada',
        'united_kingdom' => 'United Kingdom',
        'australia' => 'Australia',
    ],

    // Shop
    'shop' => 'Shop',
    'business_tools' => 'Business Tools',
    'become_seller' => 'Become a Seller',
    'my_account' => 'My Account',
    'my_orders' => 'My Orders',
    'downloads' => 'Downloads',
    'bulk_orders' => 'Bulk Orders',

    // RFQ
    'rfq' => [
        'title' => 'Request for Quote',
    ],

    // Product Management
    'product_management' => [
        // Page Titles
        'create_product' => 'Add New Product',
        'edit_product' => 'Edit Product',
        'manage_products' => 'Manage Products',
        'product_list' => 'Product List',

        // Subtitles & Descriptions
        'create_physical_product' => 'Create new physical product for MechaMap Marketplace',
        'create_technical_product' => 'Create new CAD file or technical service for MechaMap Marketplace',
        'manage_physical_products' => 'Manage your physical products on MechaMap Marketplace',

        // Form Sections
        'basic_information' => 'Basic Information',
        'pricing_inventory' => 'Pricing & Inventory',
        'technical_specifications' => 'Technical Specifications',
        'product_images' => 'Product Images',
        'actions' => 'Actions',
        'help_guide' => 'Help Guide',

        // Form Fields
        'product_name' => 'Product Name',
        'category' => 'Category',
        'select_category' => 'Select Category',
        'material' => 'Material',
        'material_placeholder' => 'e.g., Stainless Steel 304, Aluminum 6061...',
        'short_description' => 'Short Description',
        'short_description_placeholder' => 'Brief product description (max 500 characters)',
        'detailed_description' => 'Detailed Description',
        'detailed_description_placeholder' => 'Detailed product description, features, applications...',

        // Pricing Fields
        'selling_price' => 'Selling Price',
        'sale_price' => 'Sale Price',
        'stock_quantity' => 'Stock Quantity',
        'inventory_management' => 'Inventory Management',
        'auto_manage_stock' => 'Auto Manage Stock',
        'auto_manage_stock_help' => 'System will automatically reduce stock when orders are placed',
        'currency_vnd' => 'VND',

        // Technical Specifications
        'manufacturing_process' => 'Manufacturing Process',
        'manufacturing_process_placeholder' => 'e.g., CNC Machining, 3D Printing...',
        'tags' => 'Tags',
        'tags_placeholder' => 'e.g., mechanical, machining, precision (comma separated)',
        'detailed_technical_specs' => 'Detailed Technical Specifications',
        'spec_name_placeholder' => 'Specification Name',
        'spec_value_placeholder' => 'Value',
        'spec_unit_placeholder' => 'Unit',
        'add_specification' => 'Add Specification',

        // Image Upload
        'upload_images' => 'Upload Images',
        'image_upload_help' => 'Select multiple images (JPG, PNG, GIF - max 2MB each). First image will be the featured image.',

        // Action Buttons
        'create_product_btn' => 'Create Product',
        'save_draft' => 'Save Draft',
        'cancel' => 'Cancel',
        'back' => 'Back',
        'add_product' => 'Add Product',

        // Help Guide
        'help_complete_info' => 'Fill in complete product information',
        'help_quality_images' => 'Upload high-quality images',
        'help_detailed_description' => 'Describe features and applications in detail',
        'help_approval_time' => 'Product will be reviewed within 24-48h',

        // Validation Messages
        'price_validation_error' => 'Sale price must be lower than regular selling price!',
        'required_field' => 'This field is required',

        // Status
        'draft' => 'Draft',
        'published' => 'Published',
        'pending' => 'Pending',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
    ],

    // Common
    'required' => 'required',
    'optional' => 'optional',
    'loading' => 'Loading...',
    'error' => 'Error',
    'success' => 'Success',
    'warning' => 'Warning',
    'info' => 'Info',
    'confirm' => 'Confirm',
    'cancel' => 'Cancel',
    'close' => 'Close',
    'save' => 'Save',
    'edit' => 'Edit',
    'delete' => 'Delete',
    'view' => 'View',
    'back' => 'Back',
    'next' => 'Next',
    'previous' => 'Previous',
    'continue' => 'Continue',
];
