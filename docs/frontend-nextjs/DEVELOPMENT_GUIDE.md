# 🚀 MechaMap Frontend Development Guide

## ✅ Status: Port Convention Setup Completed

**🎉 Hoàn thành thiết lập port convention cho MechaMap!**
- ✅ Port 3000 đã được thiết lập thành công
- ✅ Automatic process management hoạt động tốt
- ✅ CORS và Sanctum đã được cấu hình
- ✅ Development server khởi động thành công

## 📋 Quy Ước Port & Development Server

### 🎯 Port Convention
- **Frontend Next.js**: Luôn chạy trên port `3000` ✅
- **Backend Laravel**: Chạy trên `https://mechamap.test` ✅

### 🔄 Development Scripts

#### 🚀 Khởi Động Development Server

**Cách 1: NPM Scripts (Recommended)**
```bash
# Kill process cũ và khởi động server
npm run dev

# Hoặc sử dụng lệnh force restart
npm run restart
```

**Cách 2: Script Files**
```bash
# Windows
./start-dev.bat

# Linux/Mac/Git Bash
./start-dev.sh
```

#### 📦 Available NPM Scripts

| Script | Mô tả |
|--------|--------|
| `npm run dev` | Kill port 3000 và khởi động dev server |
| `npm run dev:force` | Tương tự npm run dev (alias) |
| `npm run restart` | Force restart development server |
| `npm run kill-port` | Chỉ kill process trên port 3000 |
| `npm run start` | Khởi động production server trên port 3000 |
| `npm run start:force` | Kill port 3000 và khởi động production server |

### 🔧 Automatic Process Management

Hệ thống tự động:
1. **Kiểm tra port 3000** có process nào đang chạy không
2. **Kill process cũ** nếu có (force kill)
3. **Khởi động server mới** trên port 3000
4. **Hiển thị thông báo** trạng thái

### 🌐 Environment Configuration

#### Frontend (.env.local)
```bash
NEXT_PUBLIC_API_URL=https://mechamap.test/api/v1
NEXT_PUBLIC_APP_URL=http://localhost:3000
```

#### Backend (.env)
```bash
CORS_ALLOWED_ORIGINS=https://mechamap.com,https://www.mechamap.com,http://localhost:3000,https://mechamap.test
SANCTUM_STATEFUL_DOMAINS=mechamap.com,www.mechamap.com,localhost:3000,mechamap.test
```

### 🐛 Troubleshooting

#### ❌ Problem: Port 3000 đang được sử dụng
**Solution:**
```bash
npm run kill-port
npm run dev
```

#### ❌ Problem: CORS error từ Laravel
**Solution:**
```bash
# Clear Laravel cache
cd ../
php artisan config:clear
php artisan cache:clear
```

#### ❌ Problem: Next.js server không khởi động
**Solution:**
```bash
# Reinstall dependencies
npm install
npm run dev:force
```

### 📱 Cross-Platform Compatibility

| OS | Recommended Method |
|----|-------------------|
| Windows | `npm run dev` hoặc `start-dev.bat` |
| Linux/Mac | `npm run dev` hoặc `./start-dev.sh` |
| Git Bash (Windows) | `npm run dev` hoặc `./start-dev.sh` |

### 🔒 Security Notes

- Port 3000 chỉ dùng cho development
- Production sẽ chạy trên domain chính
- CORS đã được cấu hình cho localhost:3000
- Sanctum stateful domains bao gồm localhost:3000

### 🚀 Quick Start

```bash
# Clone project
git clone <repository>

# Install dependencies  
npm install

# Start development server (auto-kill existing process)
npm run dev
```

Server sẽ chạy tại: **http://localhost:3000**

---

## 📞 Support

Nếu gặp vấn đề, hãy kiểm tra:
1. Port 3000 có bị conflict không
2. Laravel backend có đang chạy không
3. Environment variables đã được cấu hình đúng chưa
4. Dependencies đã được install chưa
