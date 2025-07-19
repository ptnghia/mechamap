/**
 * Test WebSocket Connection Script
 * Kiểm tra kết nối WebSocket từ client đến realtime server
 */

const io = require('socket.io-client');

// Configuration
const config = {
    serverUrl: 'http://localhost:3000',
    timeout: 10000,
    reconnection: false,
    forceNew: true
};

console.log('🔍 Testing WebSocket Connection to MechaMap Realtime Server');
console.log('=======================================================');
console.log(`Server URL: ${config.serverUrl}`);
console.log(`Timeout: ${config.timeout}ms`);
console.log('');

// Create socket connection
const socket = io(config.serverUrl, config);

// Connection events
socket.on('connect', () => {
    console.log('✅ Connected to realtime server');
    console.log(`Socket ID: ${socket.id}`);
    console.log('');
    
    // Test authentication (if needed)
    console.log('🔐 Testing authentication...');
    socket.emit('authenticate', {
        token: 'test_token',
        user_id: 1
    });
});

socket.on('connect_error', (error) => {
    console.log('❌ Connection failed');
    console.log(`Error: ${error.message}`);
    console.log('');
    process.exit(1);
});

socket.on('disconnect', (reason) => {
    console.log('🔌 Disconnected from server');
    console.log(`Reason: ${reason}`);
    console.log('');
});

// Authentication events
socket.on('authenticated', (data) => {
    console.log('✅ Authentication successful');
    console.log('User data:', data);
    console.log('');
    
    // Test sending a message
    console.log('📤 Testing message sending...');
    socket.emit('test_message', {
        type: 'test',
        message: 'Hello from test script',
        timestamp: new Date().toISOString()
    });
});

socket.on('authentication_error', (error) => {
    console.log('❌ Authentication failed');
    console.log('Error:', error);
    console.log('');
});

// Message events
socket.on('message_received', (data) => {
    console.log('📥 Message received from server');
    console.log('Data:', data);
    console.log('');
});

socket.on('notification', (data) => {
    console.log('🔔 Notification received');
    console.log('Data:', data);
    console.log('');
});

// Error handling
socket.on('error', (error) => {
    console.log('❌ Socket error');
    console.log('Error:', error);
    console.log('');
});

// Test timeout
setTimeout(() => {
    console.log('⏰ Test completed');
    console.log('');
    
    if (socket.connected) {
        console.log('✅ Connection test successful');
        socket.disconnect();
    } else {
        console.log('❌ Connection test failed');
    }
    
    process.exit(0);
}, config.timeout);

// Handle process termination
process.on('SIGINT', () => {
    console.log('\n🛑 Test interrupted by user');
    if (socket.connected) {
        socket.disconnect();
    }
    process.exit(0);
});

process.on('SIGTERM', () => {
    console.log('\n🛑 Test terminated');
    if (socket.connected) {
        socket.disconnect();
    }
    process.exit(0);
});
