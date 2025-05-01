# API Settings

API Settings cung cấp các endpoint để lấy thông tin cài đặt của hệ thống.

## Danh sách Endpoint

| Endpoint                   | Method | Mô tả                 |
| -------------------------- | ------ | --------------------- |
| `/api/v1/settings`         | GET    | Lấy tất cả cài đặt    |
| `/api/v1/settings/{group}` | GET    | Lấy cài đặt theo nhóm |
| `/api/v1/favicon`          | GET    | Lấy favicon URL       |

## Chi tiết Endpoint

### 1. Lấy tất cả cài đặt

```
GET /api/v1/settings
```

#### Mô tả

Lấy tất cả cài đặt của hệ thống, được nhóm theo group.

#### Tham số

Không có tham số.

#### Response

```json
{
    "success": true,
    "data": {
        "general": {
            "site_name": "MechaMap",
            "site_slogan": "Diễn đàn cộng đồng chia sẻ kiến thức",
            "site_logo": "/storage/settings/logo.png",
            "site_favicon": "/storage/settings/favicon.png",
            "site_theme": "default",
            "site_language": "vi",
            "site_timezone": "Asia/Ho_Chi_Minh",
            "site_date_format": "d/m/Y",
            "site_time_format": "H:i",
            "site_status": "online",
            "site_maintenance_message": "Trang web đang được bảo trì. Vui lòng quay lại sau.",
            "site_domain": "mechamap.com",
            "site_tagline": "Một nền tảng định vị hoặc liệt kê các loại robot, máy móc, thiết bị cơ khí theo khu vực, loại, ứng dụng...",
            "site_maintenance_mode": "0",
            "site_banner": "/storage/settings/banner.jpg"
        },
        "company": {
            "company_name": "MechaMap JSC",
            "company_address": "Hà Nội, Việt Nam",
            "company_phone": "+84 123 456 789",
            "company_email": "info@mechamap.com",
            "company_tax_code": "0123456789",
            "company_website": "https://mechamap.com",
            "company_founded": "2025"
        },
        "contact": {
            "contact_email": "contact@mechamap.com",
            "contact_phone": "+84 123 456 789",
            "contact_address": "Hà Nội, Việt Nam",
            "contact_map": "https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3724.0964841656167!2d105.84052531493254!3d21.028856985998!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3135ab9bd9861ca1%3A0xe7887f7b72ca17a9!2zSMOgIE7hu5lpLCBWaeG7h3QgTmFt!5e0!3m2!1svi!2s!4v1620120000000!5m2!1svi!2s",
            "contact_form_recipients": "contact@mechamap.com,support@mechamap.com"
        },
        "social": {
            "social_facebook": "https://facebook.com/mechamap",
            "social_twitter": "https://twitter.com/mechamap",
            "social_instagram": "https://instagram.com/mechamap",
            "social_youtube": "https://youtube.com/mechamap",
            "social_linkedin": "https://linkedin.com/company/mechamap",
            "social_tiktok": "https://tiktok.com/@mechamap"
        },
        "api": {
            "google_analytics_id": "UA-XXXXXXXXX-X",
            "google_maps_api_key": "YOUR_GOOGLE_MAPS_API_KEY",
            "recaptcha_site_key": "YOUR_RECAPTCHA_SITE_KEY",
            "recaptcha_secret_key": "YOUR_RECAPTCHA_SECRET_KEY",
            "facebook_app_id": "YOUR_FACEBOOK_APP_ID",
            "facebook_app_secret": "YOUR_FACEBOOK_APP_SECRET",
            "google_client_id": "YOUR_GOOGLE_CLIENT_ID",
            "google_client_secret": "YOUR_GOOGLE_CLIENT_SECRET"
        },
        "copyright": {
            "copyright_text": "© 2025 MechaMap. Tất cả các quyền được bảo lưu.",
            "copyright_owner": "MechaMap JSC",
            "copyright_year": "2025",
            "terms_url": "/terms",
            "privacy_url": "/privacy",
            "cookie_url": "/cookie-policy"
        }
    },
    "message": "Lấy cài đặt thành công"
}
```

### 2. Lấy cài đặt theo nhóm

```
GET /api/v1/settings/{group}
```

#### Mô tả

Lấy cài đặt của hệ thống theo nhóm cụ thể.

#### Tham số

| Tham số | Kiểu   | Mô tả                                                            |
| ------- | ------ | ---------------------------------------------------------------- |
| `group` | string | Nhóm cài đặt (general, company, contact, social, api, copyright) |

#### Response

```json
{
    "success": true,
    "data": {
        "site_name": "MechaMap",
        "site_slogan": "Diễn đàn cộng đồng chia sẻ kiến thức",
        "site_logo": "/storage/settings/NXFgYl2tJTMz7FWxiOAwvWxOrmQKpGWQTGiJGptS.png",
        "site_favicon": "/storage/settings/keSfCE9MAvsO2neFBCBoLr2DC2eHttdAycBtKMVT.png",
        "site_theme": "default",
        "site_language": "vi",
        "site_timezone": "Asia/Ho_Chi_Minh",
        "site_date_format": "d/m/Y",
        "site_time_format": "H:i",
        "site_status": "online",
        "site_maintenance_message": "Trang web đang được bảo trì. Vui lòng quay lại sau.",
        "site_domain": "mechamap.com",
        "site_tagline": "Một nền tảng định vị hoặc liệt kê các loại robot, máy móc, thiết bị cơ khí theo khu vực, loại, ứng dụng...",
        "site_maintenance_mode": "0",
        "site_banner": "/storage/settings/c3A7sC8cU7YjS11Qo9UGsTi279jM6DMICPRurSln.jpg"
    },
    "message": "Lấy cài đặt thành công"
}
```

### 3. Lấy favicon URL

```
GET /api/v1/favicon
```

#### Mô tả

Lấy URL đầy đủ của favicon. API này sẽ tự động chuyển đổi đường dẫn tương đối thành đường dẫn tuyệt đối.

#### Tham số

Không có tham số.

#### Response

```json
{
    "success": true,
    "data": {
        "favicon": "https://mechamap.test/storage/settings/keSfCE9MAvsO2neFBCBoLr2DC2eHttdAycBtKMVT.png"
    },
    "message": "Lấy favicon thành công"
}
```

#### Xử lý lỗi

Nếu API gặp lỗi, frontend sẽ sử dụng favicon mặc định từ thư mục public. Đường dẫn favicon mặc định sẽ được chuyển đổi thành đường dẫn tuyệt đối dựa trên `NEXT_PUBLIC_SITE_URL`.

## Lưu ý

1. Các đường dẫn như `/storage/settings/favicon.png` là đường dẫn tương đối. Khi sử dụng trong frontend, bạn cần chuyển đổi chúng thành đường dẫn tuyệt đối bằng cách thêm domain vào trước, ví dụ: `https://mechamap.test/storage/settings/favicon.png`.

2. Endpoint `/api/v1/favicon` đã xử lý việc chuyển đổi đường dẫn tương đối thành đường dẫn tuyệt đối, vì vậy bạn có thể sử dụng URL trả về trực tiếp.

3. Các nhóm cài đặt hiện có:
    - `general`: Cài đặt chung (site_name, site_logo, site_favicon, ...)
    - `company`: Thông tin công ty (company_name, company_address, ...)
    - `contact`: Thông tin liên hệ (contact_email, contact_phone, ...)
    - `social`: Mạng xã hội (social_facebook, social_twitter, ...)
    - `api`: Cài đặt API (google_analytics_id, google_maps_api_key, ...)
    - `copyright`: Bản quyền (copyright_text, copyright_owner, ...)
