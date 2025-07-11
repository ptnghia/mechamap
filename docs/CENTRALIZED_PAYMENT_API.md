# 🔌 MechaMap Centralized Payment API Documentation

## Overview
API endpoints cho hệ thống thanh toán tập trung MechaMap. Tất cả payments từ customers sẽ được xử lý thông qua các endpoints này.

## 🔐 Authentication
Tất cả API endpoints yêu cầu authentication trừ webhooks và public endpoints.

```http
Authorization: Bearer {access_token}
Content-Type: application/json
Accept: application/json
```

## 💳 Payment Endpoints

### 1. Create Centralized Stripe Payment Intent

**Endpoint:** `POST /api/v1/payment/centralized/stripe/create-intent`

**Description:** Tạo Stripe Payment Intent cho centralized payment system.

**Request:**
```json
{
  "order_id": 123
}
```

**Response:**
```json
{
  "success": true,
  "message": "Centralized Stripe Payment Intent created successfully",
  "data": {
    "client_secret": "pi_xxx_secret_xxx",
    "payment_intent_id": "pi_xxx",
    "centralized_payment_id": 456,
    "order_id": 123,
    "amount": 500000,
    "currency": "VND"
  }
}
```

**Error Response:**
```json
{
  "success": false,
  "message": "Centralized Stripe payment không được cấu hình",
  "error": "Configuration error"
}
```

### 2. Create Centralized SePay Payment

**Endpoint:** `POST /api/v1/payment/centralized/sepay/create-payment`

**Description:** Tạo SePay payment cho centralized payment system.

**Request:**
```json
{
  "order_id": 123
}
```

**Response:**
```json
{
  "success": true,
  "message": "Centralized SePay payment created successfully",
  "data": {
    "payment_instructions": {
      "bank_name": "Ngân hàng TMCP Quân đội (MB Bank)",
      "account_number": "0903252427001",
      "account_name": "CONG TY CO PHAN CONG NGHE MECHAMAP",
      "amount": "500,000 VNĐ",
      "transaction_ref": "MECHAMAP-456-1234567890",
      "description": "MechaMap Order ORD-123 - Centralized Payment",
      "qr_code_url": "https://mechamap.com/api/payment/sepay/qr?ref=xxx"
    },
    "transaction_ref": "MECHAMAP-456-1234567890",
    "centralized_payment_id": 456,
    "order_id": 123,
    "amount": 500000,
    "currency": "VND"
  }
}
```

## 🔔 Webhook Endpoints

### 1. Centralized Stripe Webhook

**Endpoint:** `POST /api/v1/payment/centralized/webhook`

**Description:** Xử lý Stripe webhooks cho centralized payments.

**Headers:**
```http
Stripe-Signature: t=xxx,v1=xxx
Content-Type: application/json
```

**Webhook Events Handled:**
- `payment_intent.succeeded`
- `payment_intent.payment_failed`
- `payment_intent.canceled`

**Response:**
```json
{
  "received": true
}
```

### 2. Centralized SePay Webhook

**Endpoint:** `POST /api/v1/payment/centralized/sepay/webhook`

**Description:** Xử lý SePay webhooks cho centralized payments.

**Request:**
```json
{
  "transaction_ref": "MECHAMAP-456-1234567890",
  "amount": 500000,
  "status": "success",
  "bank_code": "MBBank",
  "account_number": "0903252427001",
  "timestamp": "2024-01-15T10:30:00Z"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Centralized SePay payment completed successfully"
}
```

## 🧪 Testing Endpoints

### 1. Test Configuration

**Endpoint:** `GET /api/v1/payment/test/centralized/configuration`

**Description:** Kiểm tra cấu hình centralized payment system.

**Response:**
```json
{
  "success": true,
  "message": "Centralized payment system configuration",
  "data": {
    "stripe_configured": true,
    "sepay_configured": true,
    "available_methods": {
      "stripe": {
        "name": "Stripe (International Cards)",
        "description": "Pay with international credit/debit cards",
        "currencies": ["VND"],
        "fees": "3.4% + 10,000 VNĐ per transaction"
      },
      "sepay": {
        "name": "SePay (Vietnam Banking)",
        "description": "Pay via Vietnamese bank transfer",
        "currencies": ["VND"],
        "fees": "Free for customers"
      }
    },
    "environment": "production",
    "timestamp": "2024-01-15T10:30:00Z"
  }
}
```

### 2. Create Test Order

**Endpoint:** `POST /api/v1/payment/test/centralized/create-order`

**Description:** Tạo test order cho testing centralized payments.

**Request:**
```json
{
  "customer_id": 1,
  "total_amount": 500000,
  "items": [
    {
      "product_id": 1,
      "quantity": 2,
      "price": 250000
    }
  ]
}
```

**Response:**
```json
{
  "success": true,
  "message": "Test order created successfully",
  "data": {
    "order_id": 123,
    "order_number": "TEST-1234567890",
    "total_amount": 500000,
    "customer_id": 1
  }
}
```

### 3. Test Stripe Payment

**Endpoint:** `POST /api/v1/payment/test/centralized/stripe`

**Description:** Test Stripe payment creation.

**Request:**
```json
{
  "order_id": 123
}
```

### 4. Test SePay Payment

**Endpoint:** `POST /api/v1/payment/test/centralized/sepay`

**Description:** Test SePay payment creation.

**Request:**
```json
{
  "order_id": 123
}
```

### 5. Simulate Webhooks

**Endpoint:** `POST /api/v1/payment/test/centralized/simulate-stripe-webhook`

**Description:** Simulate Stripe webhook events.

**Request:**
```json
{
  "centralized_payment_id": 456,
  "event_type": "payment_intent.succeeded"
}
```

**Endpoint:** `POST /api/v1/payment/test/centralized/simulate-sepay-webhook`

**Description:** Simulate SePay webhook events.

**Request:**
```json
{
  "centralized_payment_id": 456,
  "status": "success"
}
```

### 6. System Status

**Endpoint:** `GET /api/v1/payment/test/centralized/status`

**Description:** Lấy system status và statistics.

**Response:**
```json
{
  "success": true,
  "message": "Centralized payment system status",
  "data": {
    "total_centralized_payments": 1250,
    "completed_payments": 1180,
    "pending_payments": 45,
    "failed_payments": 25,
    "total_audit_logs": 5670,
    "recent_payments": [
      {
        "id": 456,
        "payment_reference": "CP-2024-001234",
        "status": "completed",
        "gross_amount": 500000,
        "created_at": "2024-01-15T10:30:00Z"
      }
    ]
  }
}
```

### 7. Cleanup Test Data

**Endpoint:** `DELETE /api/v1/payment/test/centralized/cleanup`

**Description:** Xóa test data (chỉ development environment).

**Response:**
```json
{
  "success": true,
  "message": "Test data cleaned up successfully",
  "data": {
    "deleted_orders": 5
  }
}
```

## 📊 Data Models

### CentralizedPayment Model
```json
{
  "id": 456,
  "payment_reference": "CP-2024-001234",
  "order_id": 123,
  "customer_id": 1,
  "customer_email": "customer@example.com",
  "payment_method": "stripe",
  "gross_amount": 500000,
  "gateway_fee": 27000,
  "net_received": 473000,
  "status": "completed",
  "gateway_payment_intent_id": "pi_xxx",
  "gateway_transaction_id": "pi_xxx",
  "paid_at": "2024-01-15T10:30:00Z",
  "confirmed_at": "2024-01-15T10:30:15Z",
  "created_at": "2024-01-15T10:29:45Z",
  "updated_at": "2024-01-15T10:30:15Z"
}
```

### Payment Status Values
- `pending` - Payment được tạo, chờ xử lý
- `processing` - Payment đang được xử lý bởi gateway
- `completed` - Payment thành công
- `failed` - Payment thất bại
- `cancelled` - Payment bị hủy
- `refunded` - Payment đã được hoàn tiền

## 🚨 Error Codes

| Code | Message | Description |
|------|---------|-------------|
| 400 | Bad Request | Invalid request parameters |
| 401 | Unauthorized | Missing or invalid authentication |
| 403 | Forbidden | Insufficient permissions |
| 404 | Not Found | Resource not found |
| 422 | Validation Error | Request validation failed |
| 500 | Internal Server Error | Server error |
| 503 | Service Unavailable | Payment gateway not configured |

## 🔄 Rate Limiting

- **Payment Creation**: 10 requests per minute per user
- **Webhook Endpoints**: 100 requests per minute per IP
- **Test Endpoints**: 50 requests per minute per user

## 📝 Changelog

### Version 1.0.0
- Initial centralized payment API
- Stripe and SePay integration
- Webhook handling
- Testing endpoints

---

**📞 Support**: Liên hệ tech@mechamap.com để được hỗ trợ API integration.
