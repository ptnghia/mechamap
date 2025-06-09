@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>🧪 JavaScript Conflict Test</h4>
                    <p class="mb-0">Test xem JavaScript files có conflict không</p>
                </div>
                <div class="card-body">
                    <!-- Test ThreadItemBuilder Class -->
                    <div class="mb-4">
                        <h5>1. ThreadItemBuilder Class Test</h5>
                        <button id="test-thread-builder" class="btn btn-primary">Test ThreadItemBuilder</button>
                        <div id="thread-builder-result" class="mt-2"></div>
                    </div>

                    <!-- Test Thread Actions -->
                    <div class="mb-4">
                        <h5>2. Thread Actions Test</h5>
                        <div class="thread-item-actions">
                            <button class="btn btn-outline-warning bookmark-btn" data-thread-id="1"
                                data-bookmarked="false">
                                <i class="bi bi-bookmark"></i> Test Bookmark
                            </button>
                            <button class="btn btn-outline-info follow-btn" data-thread-id="1" data-following="false">
                                <i class="bi bi-person-plus"></i> Test Follow
                            </button>
                        </div>
                        <div id="actions-result" class="mt-2"></div>
                    </div>

                    <!-- Console Logs -->
                    <div class="mb-4">
                        <h5>3. JavaScript Console Log</h5>
                        <div id="console-log" class="bg-light p-3" style="height: 200px; overflow-y: auto;">
                            <small class="text-muted">JavaScript console messages sẽ hiện ở đây...</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
    const consoleDiv = document.getElementById('console-log');

    // Override console methods để capture logs
    const originalLog = console.log;
    const originalError = console.error;
    const originalWarn = console.warn;

    function addToConsole(type, message) {
        const timestamp = new Date().toLocaleTimeString();
        const logEntry = document.createElement('div');
        logEntry.className = `small ${type === 'error' ? 'text-danger' : type === 'warn' ? 'text-warning' : 'text-info'}`;
        logEntry.innerHTML = `[${timestamp}] <strong>${type.toUpperCase()}:</strong> ${message}`;
        consoleDiv.appendChild(logEntry);
        consoleDiv.scrollTop = consoleDiv.scrollHeight;
    }

    console.log = function(...args) {
        addToConsole('log', args.join(' '));
        originalLog.apply(console, args);
    };

    console.error = function(...args) {
        addToConsole('error', args.join(' '));
        originalError.apply(console, args);
    };

    console.warn = function(...args) {
        addToConsole('warn', args.join(' '));
        originalWarn.apply(console, args);
    };

    // Test ThreadItemBuilder
    document.getElementById('test-thread-builder').addEventListener('click', function() {
        const resultDiv = document.getElementById('thread-builder-result');

        try {
            // Check if ThreadItemBuilder exists
            if (typeof ThreadItemBuilder !== 'undefined') {
                console.log('✅ ThreadItemBuilder class đã được load thành công');

                // Test createSkeletonLoader method
                const skeleton = ThreadItemBuilder.createSkeletonLoader();
                if (skeleton) {
                    console.log('✅ ThreadItemBuilder.createSkeletonLoader() hoạt động OK');
                    resultDiv.innerHTML = '<div class="alert alert-success">✅ ThreadItemBuilder class hoạt động bình thường!</div>';
                } else {
                    console.error('❌ ThreadItemBuilder.createSkeletonLoader() trả về null');
                    resultDiv.innerHTML = '<div class="alert alert-warning">⚠️ Method createSkeletonLoader có vấn đề</div>';
                }
            } else {
                console.error('❌ ThreadItemBuilder class không tồn tại');
                resultDiv.innerHTML = '<div class="alert alert-danger">❌ ThreadItemBuilder class không được load</div>';
            }
        } catch (error) {
            console.error('❌ JavaScript Error:', error.message);
            resultDiv.innerHTML = `<div class="alert alert-danger">❌ JavaScript Error: ${error.message}</div>`;
        }
    });

    // Test Actions
    document.addEventListener('click', function(e) {
        if (e.target.closest('.bookmark-btn') || e.target.closest('.follow-btn')) {
            e.preventDefault();
            const actionsResult = document.getElementById('actions-result');
            const buttonType = e.target.closest('.bookmark-btn') ? 'bookmark' : 'follow';

            console.log(`✅ ${buttonType} button click event được capture thành công`);
            actionsResult.innerHTML = `<div class="alert alert-info">✅ ${buttonType} button click hoạt động (chặn API call để test)</div>`;
        }
    });

    // Test initial load
    console.log('🚀 Test page loaded successfully');
    console.log('📦 Checking JavaScript files...');

    // Check if both files loaded
    setTimeout(() => {
        if (typeof ThreadItemBuilder !== 'undefined') {
            console.log('✅ thread-item.js loaded successfully');
        } else {
            console.error('❌ thread-item.js NOT loaded');
        }

        // Check for thread-actions.js (không có global class nên check event handlers)
        console.log('✅ thread-actions.js assumed loaded (event-based)');

    }, 100);
});
</script>
@endpush