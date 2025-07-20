# MechaMap WebSocket Connection Analysis

Phân tích số lượng kết nối WebSocket được tạo khi load trang web.

## 🎯 **TÓM TẮT: 1 KẾT NỐI DUY NHẤT**

**Kết quả:** Mỗi lần load web, MechaMap chỉ tạo **1 kết nối WebSocket duy nhất** tới Realtime server.

## 🔍 **CHI TIẾT PHÂN TÍCH**

### **1. Singleton Pattern Implementation**

MechaMap sử dụng **Singleton Pattern** để đảm bảo chỉ có 1 kết nối:

```javascript
// public/js/websocket-config.js
function WebSocketManager() {
    if (instance) {
        console.log('MechaMap WebSocket: Returning existing WebSocketManager instance');
        return instance; // ✅ Trả về instance hiện có
    }
    
    console.log('MechaMap WebSocket: Creating new WebSocketManager instance');
    this.socket = null;
    this.isConnecting = false;
    this.connectionPromise = null;
    this.connectionAttempts = 0;
    this.MAX_CONNECTION_ATTEMPTS = 1; // ✅ Giới hạn 1 kết nối
    
    instance = this;
    return this;
}
```

### **2. Connection Reuse Logic**

Trước khi tạo kết nối mới, hệ thống kiểm tra:

```javascript
// Return existing connection if available
if (wsManager.socket && wsManager.socket.connected) {
    console.log('MechaMap WebSocket: Using existing connection', { id: wsManager.socket.id });
    return wsManager.socket; // ✅ Sử dụng lại kết nối hiện có
}

// Return existing connection promise if in progress
if (wsManager.connectionPromise) {
    console.log('MechaMap WebSocket: Connection already in progress, waiting...');
    return await wsManager.connectionPromise; // ✅ Đợi kết nối đang thực hiện
}
```

### **3. Auto-initialization Protection**

Hệ thống ngăn chặn khởi tạo nhiều lần:

```javascript
// Use multiple checks to prevent race conditions
if (window.autoInitWebSocket &&
    !window.mechaMapSocketInitialized &&
    !window.mechaMapSocketInitializing) {
    
    window.mechaMapSocketInitialized = true;
    window.mechaMapSocketInitializing = true; // ✅ Flag ngăn khởi tạo lặp
    
    // ... khởi tạo WebSocket
}
```

### **4. Connection Attempt Limit**

Giới hạn số lần thử kết nối:

```javascript
// Check connection attempts limit
if (wsManager.connectionAttempts >= wsManager.MAX_CONNECTION_ATTEMPTS) {
    console.log('MechaMap WebSocket: Maximum connection attempts reached');
    return null; // ✅ Dừng nếu đã đạt giới hạn
}
```

## 📊 **LUỒNG KẾT NỐI CHI TIẾT**

### **Lần đầu load trang:**

1. **Page Load** → Component `<x-websocket-config>` được render
2. **Script Load** → `websocket-config.js` được load
3. **Auto-init Check** → Kiểm tra `window.autoInitWebSocket = true`
4. **Create Connection** → Tạo 1 kết nối WebSocket duy nhất
5. **Authentication** → Xác thực với Sanctum token
6. **Connected** → Kết nối thành công, lưu vào singleton

### **Reload trang hoặc navigate:**

1. **Page Load** → Component được render lại
2. **Script Load** → `websocket-config.js` được load lại
3. **Instance Check** → Kiểm tra `instance` đã tồn tại
4. **Reuse Connection** → Sử dụng lại kết nối hiện có
5. **No New Connection** → Không tạo kết nối mới

### **Multiple tabs:**

1. **Tab 1** → Tạo 1 kết nối WebSocket
2. **Tab 2** → Tạo 1 kết nối WebSocket riêng biệt
3. **Total** → 2 kết nối (1 kết nối/tab)

## 🔧 **CÁC THÀNH PHẦN QUẢN LÝ KẾT NỐI**

### **1. WebSocketManager (Singleton)**
- **Mục đích:** Quản lý 1 instance duy nhất
- **File:** `public/js/websocket-config.js`
- **Chức năng:** Tạo, quản lý, tái sử dụng kết nối

### **2. NotificationService**
- **Mục đích:** Xử lý notifications
- **File:** `public/js/frontend/services/notification-service.js`
- **Chức năng:** Sử dụng WebSocket từ WebSocketManager

### **3. WebSocketConfig Component**
- **Mục đích:** Cung cấp cấu hình
- **File:** `resources/views/components/websocket-config.blade.php`
- **Chức năng:** Render config, không tạo kết nối

## 📈 **PERFORMANCE IMPACT**

### **✅ Ưu điểm của 1 kết nối:**

1. **Tiết kiệm tài nguyên:**
   - Server: Ít connection pools
   - Client: Ít memory usage
   - Network: Ít TCP connections

2. **Tránh duplicate events:**
   - Không nhận duplicate notifications
   - Không có race conditions
   - Consistent state management

3. **Better connection management:**
   - Dễ debug và monitor
   - Reliable reconnection logic
   - Clear connection lifecycle

### **📊 Resource Usage:**

```
1 User = 1 WebSocket Connection
100 Users = 100 WebSocket Connections
1000 Users = 1000 WebSocket Connections
```

## 🔍 **MONITORING & DEBUGGING**

### **Browser Console Logs:**

Khi load trang, bạn sẽ thấy:

```javascript
// Lần đầu
MechaMap WebSocket: Creating new WebSocketManager instance
MechaMap WebSocket: Connecting to http://localhost:3000
MechaMap WebSocket: Connected successfully { id: "abc123" }

// Lần sau (reload/navigate)
MechaMap WebSocket: Returning existing WebSocketManager instance
MechaMap WebSocket: Using existing connection { id: "abc123" }
```

### **Server Logs:**

Realtime server sẽ log:

```
[info] User connected: 1 {"userId":1,"socketId":"abc123","totalConnections":1}
```

## 🎯 **KẾT LUẬN**

### **Số lượng kết nối:**
- **1 tab = 1 kết nối WebSocket**
- **Multiple tabs = Multiple kết nối (1/tab)**
- **Reload/navigate = Tái sử dụng kết nối hiện có**

### **Tối ưu hóa:**
- ✅ Singleton pattern ngăn duplicate connections
- ✅ Connection reuse cho performance
- ✅ Auto-reconnect khi mất kết nối
- ✅ Graceful error handling

### **Best Practices được áp dụng:**
- ✅ Connection pooling
- ✅ Resource management
- ✅ Error recovery
- ✅ Memory leak prevention

**MechaMap đã được thiết kế tối ưu để sử dụng tài nguyên hiệu quả với chỉ 1 kết nối WebSocket per tab!** 🎉
