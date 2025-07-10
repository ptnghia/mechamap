<?php

return [
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
        'empty_cart' => 'Empty Cart',
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
