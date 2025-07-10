# 🔌 MechaMap API Documentation

> **Complete REST API reference for MechaMap platform**

## 📋 API Guides

- [Authentication](./authentication.md) - API authentication methods
- [Endpoints](./endpoints.md) - All API endpoints with examples
- [Rate Limiting](./rate-limiting.md) - Usage limits and policies
- [Examples](./examples.md) - Code examples and SDKs

## 🚀 Quick Start

```bash
# Get API token
curl -X POST https://mechamap.com/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"user@example.com","password":"password"}'

# Use API
curl -X GET https://mechamap.com/api/v1/products \
  -H "Authorization: Bearer YOUR_TOKEN"
```

## 📊 API Overview

- **Base URL**: `https://mechamap.com/api/v1`
- **Authentication**: Bearer Token
- **Rate Limit**: 1000 requests/hour
- **Response Format**: JSON

## 🔗 Related Docs

- [Authentication Guide](../../user-guides/getting-started.md)
- [Testing API](../testing/api-tests.md)
- [Error Handling](./error-handling.md)
