# Báo Cáo Kiểm Tra Các Bảng Có Cột UUID

**Ngày tạo:** 2025-07-14  
**Database:** mechamap_backend  
**Tổng số bảng có UUID:** 16 bảng  

## 📊 Tổng Quan

- **Tổng số bảng có cột UUID:** 16 bảng
- **Tổng số cột UUID:** 16 cột
- **Các kiểu dữ liệu UUID được sử dụng:**
  - `uuid`: 15 cột (kiểu dữ liệu UUID native của MySQL/MariaDB)
  - `varchar(191)`: 1 cột (bảng failed_jobs)

## 📋 Danh Sách Chi Tiết Các Bảng

### 1. **cad_files**
- **Cột UUID:** `uuid`
- **Kiểu dữ liệu:** `uuid`
- **Số lượng record:** 0
- **Đặc điểm:** UNIQUE key, NOT NULL

### 2. **cart_items**
- **Cột UUID:** `uuid`
- **Kiểu dữ liệu:** `uuid`
- **Số lượng record:** 0
- **Đặc điểm:** UNIQUE key, NOT NULL

### 3. **engineering_standards**
- **Cột UUID:** `uuid`
- **Kiểu dữ liệu:** `uuid`
- **Số lượng record:** 0
- **Đặc điểm:** UNIQUE key, NOT NULL

### 4. **failed_jobs**
- **Cột UUID:** `uuid`
- **Kiểu dữ liệu:** `varchar(191)`
- **Số lượng record:** 0
- **Đặc điểm:** UNIQUE key, NOT NULL
- **Ghi chú:** Đây là bảng hệ thống của Laravel, sử dụng varchar thay vì kiểu UUID native

### 5. **manufacturing_processes**
- **Cột UUID:** `uuid`
- **Kiểu dữ liệu:** `uuid`
- **Số lượng record:** 0
- **Đặc điểm:** UNIQUE key, NOT NULL

### 6. **marketplace_cart_items** ⭐
- **Cột UUID:** `uuid`
- **Kiểu dữ liệu:** `uuid`
- **Số lượng record:** 6
- **Đặc điểm:** UNIQUE key, NOT NULL
- **Ví dụ UUID:** `a5c96325-0f3b-4d88-bf0e-48038bf8d631`

### 7. **marketplace_download_history** ⭐
- **Cột UUID:** `uuid`
- **Kiểu dữ liệu:** `uuid`
- **Số lượng record:** 1
- **Đặc điểm:** UNIQUE key, NOT NULL
- **Ví dụ UUID:** `f1ceac79-c985-401e-812a-d4b8d7a9f3ca`

### 8. **marketplace_orders** ⭐
- **Cột UUID:** `uuid`
- **Kiểu dữ liệu:** `uuid`
- **Số lượng record:** 137
- **Đặc điểm:** UNIQUE key, NOT NULL
- **Ví dụ UUID:** `17720239-180f-473a-b104-019005b7047b`

### 9. **marketplace_products** ⭐
- **Cột UUID:** `uuid`
- **Kiểu dữ liệu:** `uuid`
- **Số lượng record:** 95
- **Đặc điểm:** UNIQUE key, NOT NULL
- **Ví dụ UUID:** `7b2d5a0c-7778-4ae9-97f2-0382fb71a88c`

### 10. **marketplace_products_normalized** ⭐
- **Cột UUID:** `uuid`
- **Kiểu dữ liệu:** `uuid`
- **Số lượng record:** 94
- **Đặc điểm:** NOT NULL (không có UNIQUE key)
- **Ví dụ UUID:** `88897055-45b0-4741-b9a8-ed9d5de471e2`

### 11. **marketplace_sellers** ⭐
- **Cột UUID:** `uuid`
- **Kiểu dữ liệu:** `uuid`
- **Số lượng record:** 42
- **Đặc điểm:** UNIQUE key, NOT NULL
- **Ví dụ UUID:** `fdf85f20-4b4a-4925-a443-097a7668a5d3`

### 12. **marketplace_shopping_carts** ⭐
- **Cột UUID:** `uuid`
- **Kiểu dữ liệu:** `uuid`
- **Số lượng record:** 151
- **Đặc điểm:** UNIQUE key, NOT NULL
- **Ví dụ UUID:** `8bf2742b-4f4c-4955-a406-007f52fa3cab`

### 13. **materials** ⭐
- **Cột UUID:** `uuid`
- **Kiểu dữ liệu:** `uuid`
- **Số lượng record:** 10
- **Đặc điểm:** UNIQUE key, NOT NULL
- **Ví dụ UUID:** `2fdb9d49-f328-472b-af7e-29c41fb7b372`

### 14. **payment_disputes**
- **Cột UUID:** `uuid`
- **Kiểu dữ liệu:** `uuid`
- **Số lượng record:** 0
- **Đặc điểm:** UNIQUE key, NOT NULL

### 15. **payment_refunds**
- **Cột UUID:** `uuid`
- **Kiểu dữ liệu:** `uuid`
- **Số lượng record:** 0
- **Đặc điểm:** UNIQUE key, NOT NULL

### 16. **technical_drawings** ⭐
- **Cột UUID:** `uuid`
- **Kiểu dữ liệu:** `uuid`
- **Số lượng record:** 30
- **Đặc điểm:** UNIQUE key, NOT NULL
- **Ví dụ UUID:** `32877467-fdce-45f2-8288-02ece374bc33`

## 🔍 Phân Tích

### Bảng có dữ liệu (⭐):
1. **marketplace_shopping_carts** - 151 records
2. **marketplace_orders** - 137 records  
3. **marketplace_products** - 95 records
4. **marketplace_products_normalized** - 94 records
5. **marketplace_sellers** - 42 records
6. **technical_drawings** - 30 records
7. **materials** - 10 records
8. **marketplace_cart_items** - 6 records
9. **marketplace_download_history** - 1 record

### Bảng chưa có dữ liệu:
- cad_files, cart_items, engineering_standards, failed_jobs, manufacturing_processes, payment_disputes, payment_refunds

### Đặc điểm chung:
- Hầu hết các bảng sử dụng kiểu dữ liệu `uuid` native của MySQL/MariaDB
- Tất cả cột UUID đều có constraint NOT NULL
- Hầu hết có UNIQUE key (trừ marketplace_products_normalized)
- Chủ yếu tập trung trong hệ thống marketplace và technical drawings

## 💡 Khuyến Nghị

1. **Chuẩn hóa kiểu dữ liệu:** Xem xét chuyển đổi cột uuid trong bảng `failed_jobs` từ `varchar(191)` sang kiểu `uuid` để thống nhất
2. **Indexing:** Đảm bảo tất cả cột UUID đều có index để tối ưu hiệu suất truy vấn
3. **Validation:** Kiểm tra logic tạo UUID trong application để đảm bảo tính duy nhất
4. **Monitoring:** Theo dõi hiệu suất các truy vấn sử dụng UUID làm điều kiện WHERE
