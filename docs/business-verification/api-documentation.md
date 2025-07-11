# 🔌 MechaMap Business Verification API Documentation

**RESTful API Reference for Business Verification Platform**

[![API Version](https://img.shields.io/badge/API%20Version-2.0-blue.svg)](CHANGELOG.md)
[![Authentication](https://img.shields.io/badge/Auth-Laravel%20Sanctum-green.svg)](../security-guide.md)
[![Rate Limit](https://img.shields.io/badge/Rate%20Limit-60%2Fmin-orange.svg)](#rate-limiting)

---

## 🎯 **API Overview**

MechaMap Business Verification API cung cấp endpoints để quản lý business registration, document verification, và marketplace permissions. API được thiết kế theo RESTful principles với authentication bằng Laravel Sanctum.

### **🔗 Base URL**
```
Production: https://mechapap.com/api/v2
Development: https://mechapap.test/api/v2
```

### **📋 API Features**
- **Business Registration**: Multi-step registration workflow
- **Document Management**: Upload, verify, và manage documents
- **Permission System**: Dynamic marketplace permissions
- **Security Monitoring**: Real-time threat detection
- **Compliance Reporting**: GDPR/CCPA compliant data export

## 🔐 **Authentication**

### **🎫 Laravel Sanctum Tokens**

All API endpoints require authentication using Bearer tokens:

```http
Authorization: Bearer {your-api-token}
Content-Type: application/json
Accept: application/json
```

### **🔑 Getting API Token**

```http
POST /api/auth/login
Content-Type: application/json

{
  "email": "user@example.com",
  "password": "password",
  "device_name": "api-client"
}
```

**Response:**
```json
{
  "success": true,
  "token": "1|abc123...",
  "user": {
    "id": 1,
    "name": "John Doe",
    "role": "manufacturer"
  }
}
```

## 🏢 **Business Registration Endpoints**

### **📝 Start Registration**

```http
POST /api/business/registration/start
```

**Request Body:**
```json
{
  "role": "manufacturer",
  "business_name": "Tech Manufacturing Corp",
  "business_type": "manufacturing",
  "industry": "electronics",
  "country": "VN"
}
```

**Response:**
```json
{
  "success": true,
  "application_id": "app_123456789",
  "status": "draft",
  "next_step": "business_details",
  "expires_at": "2025-07-19T10:00:00Z"
}
```

### **📋 Submit Business Details**

```http
PUT /api/business/registration/{application_id}/details
```

**Request Body:**
```json
{
  "business_name": "Tech Manufacturing Corp",
  "registration_number": "0123456789",
  "tax_id": "TAX123456789",
  "business_address": "123 Tech Street, Ho Chi Minh City",
  "business_phone": "+84123456789",
  "business_email": "contact@techmanufacturing.com",
  "contact_person_name": "Nguyen Van A",
  "contact_person_position": "CEO",
  "contact_person_phone": "+84987654321",
  "contact_person_email": "ceo@techmanufacturing.com",
  "website": "https://techmanufacturing.com",
  "employee_count": "50-100",
  "annual_revenue": "1-5M",
  "business_description": "Electronics manufacturing company"
}
```

**Response:**
```json
{
  "success": true,
  "application_id": "app_123456789",
  "status": "details_completed",
  "next_step": "document_upload",
  "validation_errors": []
}
```

### **📄 Upload Documents**

```http
POST /api/business/registration/{application_id}/documents
Content-Type: multipart/form-data
```

**Request (Multipart Form):**
```
document_type: business_license
file: [binary file data]
description: Business license issued by HCMC Department
```

**Response:**
```json
{
  "success": true,
  "document_id": "doc_987654321",
  "document_type": "business_license",
  "file_size": 2048576,
  "mime_type": "application/pdf",
  "security_scan": {
    "safe": true,
    "threats": [],
    "scan_id": "scan_123"
  },
  "verification_status": "pending"
}
```

### **✅ Submit Application**

```http
POST /api/business/registration/{application_id}/submit
```

**Request Body:**
```json
{
  "terms_accepted": true,
  "privacy_policy_accepted": true,
  "marketing_consent": false,
  "additional_notes": "Please prioritize review for urgent business needs"
}
```

**Response:**
```json
{
  "success": true,
  "application_id": "app_123456789",
  "status": "submitted",
  "submitted_at": "2025-07-12T10:30:00Z",
  "estimated_review_time": "3-5 business days",
  "tracking_number": "BV2025071200123"
}
```

## 📊 **Application Management Endpoints**

### **📋 Get Application Status**

```http
GET /api/business/applications/{application_id}
```

**Response:**
```json
{
  "success": true,
  "application": {
    "id": "app_123456789",
    "tracking_number": "BV2025071200123",
    "status": "under_review",
    "business_name": "Tech Manufacturing Corp",
    "application_type": "manufacturer",
    "submitted_at": "2025-07-12T10:30:00Z",
    "last_updated": "2025-07-13T14:20:00Z",
    "estimated_completion": "2025-07-17T17:00:00Z",
    "progress": {
      "registration": "completed",
      "document_upload": "completed", 
      "verification": "in_progress",
      "approval": "pending"
    },
    "documents": [
      {
        "id": "doc_987654321",
        "type": "business_license",
        "status": "verified",
        "verified_at": "2025-07-13T14:20:00Z",
        "verified_by": "Admin User"
      }
    ],
    "timeline": [
      {
        "action": "application_submitted",
        "timestamp": "2025-07-12T10:30:00Z",
        "description": "Application submitted for review"
      },
      {
        "action": "document_verified",
        "timestamp": "2025-07-13T14:20:00Z", 
        "description": "Business license verified successfully"
      }
    ]
  }
}
```

### **📄 Get Document Details**

```http
GET /api/business/documents/{document_id}
```

**Response:**
```json
{
  "success": true,
  "document": {
    "id": "doc_987654321",
    "application_id": "app_123456789",
    "document_type": "business_license",
    "original_filename": "business_license.pdf",
    "file_size": 2048576,
    "mime_type": "application/pdf",
    "uploaded_at": "2025-07-12T10:25:00Z",
    "verification_status": "verified",
    "verified_at": "2025-07-13T14:20:00Z",
    "verified_by": "Admin User",
    "verification_notes": "Document is clear and valid",
    "security_scan": {
      "safe": true,
      "scan_date": "2025-07-12T10:25:30Z",
      "threats_detected": []
    },
    "download_url": "/api/business/documents/doc_987654321/download?token=abc123"
  }
}
```

## 🛒 **Marketplace Permission Endpoints**

### **🔍 Check User Permissions**

```http
GET /api/marketplace/permissions
```

**Response:**
```json
{
  "success": true,
  "user": {
    "id": 123,
    "role": "manufacturer",
    "verification_status": "verified"
  },
  "permissions": {
    "can_buy_digital": true,
    "can_buy_new": true,
    "can_buy_used": false,
    "can_sell_digital": true,
    "can_sell_new": true,
    "can_sell_used": false,
    "can_view_marketplace": true,
    "can_create_products": true
  },
  "commission_rate": 5.0,
  "verification_required_for": [
    "selling_new_products",
    "reduced_commission_rate"
  ]
}
```

### **💰 Get Commission Rates**

```http
GET /api/marketplace/commission-rates
```

**Response:**
```json
{
  "success": true,
  "commission_rates": {
    "manufacturer": {
      "unverified": 10.0,
      "verified": 5.0
    },
    "supplier": {
      "unverified": 10.0,
      "verified": 3.0
    },
    "verified_partner": {
      "unverified": 10.0,
      "verified": 2.0
    },
    "brand": {
      "any": 0.0
    }
  },
  "user_rate": 5.0,
  "savings_from_verification": 5.0
}
```

## 🔒 **Security & Compliance Endpoints**

### **🛡️ Security Monitoring**

```http
GET /api/security/incidents
Authorization: Bearer {admin-token}
```

**Response:**
```json
{
  "success": true,
  "incidents": [
    {
      "id": "inc_123456",
      "type": "suspicious_upload",
      "threat_level": "medium",
      "detected_at": "2025-07-12T15:30:00Z",
      "user_id": 456,
      "details": {
        "filename": "suspicious_file.exe",
        "threats": ["Suspicious file extension"]
      },
      "status": "resolved"
    }
  ],
  "summary": {
    "total_incidents": 5,
    "critical": 0,
    "high": 1,
    "medium": 2,
    "low": 2
  }
}
```

### **📋 Compliance Report**

```http
POST /api/compliance/audit-report
Authorization: Bearer {admin-token}

{
  "date_from": "2025-07-01",
  "date_to": "2025-07-12",
  "format": "json"
}
```

**Response:**
```json
{
  "success": true,
  "report": {
    "total_activities": 1250,
    "activities_by_action": {
      "application_submitted": 45,
      "document_verified": 38,
      "application_approved": 32
    },
    "security_incidents": 5,
    "compliance_score": 94.5,
    "recommendations": [
      "Continue current security practices",
      "Regular audit trail reviews"
    ]
  }
}
```

## ⚠️ **Error Handling**

### **📋 Standard Error Response**

```json
{
  "success": false,
  "error": {
    "code": "VALIDATION_ERROR",
    "message": "The given data was invalid.",
    "details": {
      "business_name": ["The business name field is required."],
      "tax_id": ["The tax ID format is invalid."]
    }
  },
  "timestamp": "2025-07-12T10:30:00Z",
  "request_id": "req_123456789"
}
```

### **🔢 HTTP Status Codes**

| Code | Description | Usage |
|------|-------------|-------|
| `200` | OK | Successful request |
| `201` | Created | Resource created successfully |
| `400` | Bad Request | Invalid request data |
| `401` | Unauthorized | Authentication required |
| `403` | Forbidden | Insufficient permissions |
| `404` | Not Found | Resource not found |
| `422` | Unprocessable Entity | Validation errors |
| `429` | Too Many Requests | Rate limit exceeded |
| `500` | Internal Server Error | Server error |

## 🚦 **Rate Limiting**

### **📊 Rate Limits**

| Endpoint Type | Limit | Window |
|---------------|-------|--------|
| **Authentication** | 5 requests | 1 minute |
| **Registration** | 10 requests | 1 hour |
| **Document Upload** | 20 files | 1 hour |
| **General API** | 60 requests | 1 minute |
| **Admin API** | 120 requests | 1 minute |

### **📋 Rate Limit Headers**

```http
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 45
X-RateLimit-Reset: 1625097600
```

---

**© 2025 MechaMap. Business Verification API Documentation.**
