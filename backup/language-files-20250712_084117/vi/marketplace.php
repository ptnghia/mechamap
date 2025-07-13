<?php

return [
    // Cart
    'cart' => [
        'title' => 'Giỏ hàng',
        'shopping_cart' => 'Giỏ hàng',
        'select_all' => 'Chọn tất cả',
        'clear_cart' => 'Xóa giỏ hàng',
        'remove_selected' => 'Xóa đã chọn',
        'items' => 'sản phẩm',
        'item' => 'sản phẩm',
        'qty' => 'SL',
        'quantity' => 'Số lượng',
        'available' => 'có sẵn',
        'remove' => 'Xóa',
        'save_for_later' => 'Lưu để mua sau',
        'product_no_longer_available' => 'Sản phẩm không còn khả dụng',
        'continue_shopping' => 'Tiếp tục mua sắm',
        'empty_cart' => 'Giỏ hàng trống',
        'empty_cart_message' => 'Giỏ hàng của bạn đang trống. Hãy thêm sản phẩm để tiếp tục.',
        
        // Order Summary
        'order_summary' => 'Tóm tắt đơn hàng',
        'subtotal' => 'Tạm tính',
        'shipping' => 'Phí vận chuyển',
        'tax' => 'Thuế',
        'total' => 'Tổng cộng',
        'free' => 'Miễn phí',
        'calculate_shipping' => 'Tính phí vận chuyển',
        'coupon_code' => 'Mã giảm giá',
        'apply' => 'Áp dụng',
        'proceed_to_checkout' => 'Tiến hành thanh toán',
        'secure_checkout' => 'Thanh toán an toàn',
        'ssl_encryption' => 'Thanh toán an toàn với mã hóa SSL',
        
        // Shipping Information
        'shipping_information' => 'Thông tin vận chuyển',
        'free_shipping_over' => 'Miễn phí vận chuyển cho đơn hàng trên $100',
        'standard_delivery' => 'Giao hàng tiêu chuẩn: 3-5 ngày làm việc',
        'express_delivery_available' => 'Có giao hàng nhanh',
        
        // Messages
        'clear_cart_confirm' => 'Bạn có chắc chắn muốn xóa toàn bộ giỏ hàng? Hành động này không thể hoàn tác.',
        'clear_cart_success' => 'Đã xóa giỏ hàng thành công',
        'clear_cart_failed' => 'Không thể xóa giỏ hàng',
        'remove_selected_confirm' => 'Bạn có chắc chắn muốn xóa :count sản phẩm đã chọn?',
        'remove_selected_failed' => 'Không thể xóa các sản phẩm đã chọn',
        'please_select_items' => 'Vui lòng chọn sản phẩm để xóa',
        'coupon_required' => 'Vui lòng nhập mã giảm giá',
        'coupon_apply_failed' => 'Không thể áp dụng mã giảm giá',
        'save_for_later_message' => 'Tính năng lưu để mua sau sẽ được triển khai trong các bản cập nhật tương lai',
    ],

    // Checkout
    'checkout' => [
        'title' => 'Thanh toán',
        'secure_checkout' => 'Thanh toán an toàn',
        'steps' => [
            'shipping' => 'Vận chuyển',
            'payment' => 'Thanh toán',
            'review' => 'Xem lại',
        ],
        
        // Shipping Information
        'shipping_information' => 'Thông tin vận chuyển',
        'first_name' => 'Tên',
        'last_name' => 'Họ',
        'email_address' => 'Địa chỉ email',
        'phone_number' => 'Số điện thoại',
        'address_line_1' => 'Địa chỉ dòng 1',
        'address_line_2' => 'Địa chỉ dòng 2',
        'city' => 'Thành phố',
        'state_province' => 'Tỉnh/Thành phố',
        'postal_code' => 'Mã bưu điện',
        'country' => 'Quốc gia',
        'select_country' => 'Chọn quốc gia',
        'billing_same_as_shipping' => 'Địa chỉ thanh toán giống địa chỉ giao hàng',
        'billing_information' => 'Thông tin thanh toán',
        'back_to_cart' => 'Quay lại giỏ hàng',
        'continue_to_payment' => 'Tiếp tục thanh toán',
        
        // Payment Information
        'payment_information' => 'Thông tin thanh toán',
        'payment_method' => 'Phương thức thanh toán',
        'credit_debit_card' => 'Thẻ tín dụng/Ghi nợ (Stripe)',
        'bank_transfer' => 'Chuyển khoản ngân hàng (SePay)',
        'stripe_redirect_message' => 'Bạn sẽ được chuyển đến Stripe để hoàn tất thanh toán một cách an toàn.',
        'sepay_redirect_message' => 'Bạn sẽ được chuyển đến trang thanh toán SePay với QR Code để chuyển khoản ngân hàng.',
        'back_to_shipping' => 'Quay lại vận chuyển',
        'review_order' => 'Xem lại đơn hàng',
        
        // Order Review
        'review_your_order' => 'Xem lại đơn hàng của bạn',
        'shipping_address' => 'Địa chỉ giao hàng',
        'payment_method_label' => 'Phương thức thanh toán',
        'back_to_payment' => 'Quay lại thanh toán',
        'complete_order' => 'Hoàn tất đơn hàng',
        'place_order' => 'Đặt hàng',
        
        // Order Summary in Checkout
        'calculated_at_next_step' => 'Tính toán ở bước tiếp theo',
        'payment_secure_encrypted' => 'Thông tin thanh toán của bạn được bảo mật và mã hóa',
        
        // Messages
        'failed_to_process_payment' => 'Không thể xử lý thông tin thanh toán',
        'failed_to_load_review' => 'Không thể tải thông tin xem lại đơn hàng',
    ],

    // Countries
    'countries' => [
        'vietnam' => 'Việt Nam',
        'united_states' => 'Hoa Kỳ',
        'canada' => 'Canada',
        'united_kingdom' => 'Vương quốc Anh',
        'australia' => 'Úc',
    ],

    // Common
    'required' => 'bắt buộc',
    'optional' => 'tùy chọn',
    'loading' => 'Đang tải...',
    'error' => 'Lỗi',
    'success' => 'Thành công',
    'warning' => 'Cảnh báo',
    'info' => 'Thông tin',
    'confirm' => 'Xác nhận',
    'cancel' => 'Hủy',
    'close' => 'Đóng',
    'save' => 'Lưu',
    'edit' => 'Sửa',
    'delete' => 'Xóa',
    'view' => 'Xem',
    'back' => 'Quay lại',
    'next' => 'Tiếp theo',
    'previous' => 'Trước',
    'continue' => 'Tiếp tục',
];
