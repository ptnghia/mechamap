<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Thread Actions</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Thread Item CSS -->
    <link href="{{ asset('css/thread-item.css') }}" rel="stylesheet">

    <style>
        .test-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .test-section {
            background: #f8f9fa;
            padding: 1.5rem;
            margin-bottom: 2rem;
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }

        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            margin: 0.25rem;
            border-radius: 0.25rem;
            font-size: 0.875rem;
        }

        .status-success {
            background-color: #d1edff;
            color: #0c4a6e;
        }

        .status-warning {
            background-color: #fef3c7;
            color: #92400e;
        }

        .status-error {
            background-color: #fecaca;
            color: #991b1b;
        }
    </style>
</head>

<body>
    <div class="test-container">
        <h1>🧪 Thread Actions Integration Test</h1>
        <p class="text-muted">Kiểm tra bookmark và follow functionality trên trang thực tế</p>

        <!-- Authentication Status -->
        <div class="test-section">
            <h3>🔐 Authentication Status</h3>
            @auth
            <div class="status-badge status-success">
                ✅ Đã đăng nhập: {{ Auth::user()->name }} ({{ Auth::user()->email }})
            </div>
            @else
            <div class="status-badge status-error">
                ❌ Chưa đăng nhập
            </div>
            <p><a href="{{ route('login') }}" class="btn btn-primary">Đăng nhập để test</a></p>
            @endauth
        </div>

        @auth
        <!-- Test Thread Item Component -->
        <div class="test-section">
            <h3>🎯 Test Thread Item Component</h3>
            <p>Component với bookmark và follow buttons:</p>

            @php
            $testThread = App\Models\Thread::find(11);
            @endphp

            @if($testThread)
            <!-- Default variant -->
            <h5>Default Variant:</h5>
            @include('partials.thread-item', [
            'thread' => $testThread,
            'variant' => 'default',
            'showBookmark' => true,
            'showFollow' => true
            ])

            <hr class="my-4">

            <!-- Forum variant -->
            <h5>Forum Variant:</h5>
            @include('partials.thread-item', [
            'thread' => $testThread,
            'variant' => 'forum',
            'showBookmark' => true,
            'showFollow' => true
            ])

            <hr class="my-4">

            <!-- Custom Actions variant -->
            <h5>Custom Actions Variant:</h5>
            @include('partials.thread-item', [
            'thread' => $testThread,
            'variant' => 'whats-new',
            'showBookmark' => true,
            'showFollow' => true,
            'customActions' => [
            [
            'url' => route('threads.show', $testThread),
            'label' => 'Xem chi tiết',
            'icon' => 'bi-eye',
            'class' => 'btn btn-sm btn-info'
            ]
            ]
            ])
            @else
            <div class="status-badge status-error">
                ❌ Không tìm thấy test thread (ID: 11)
            </div>
            @endif
        </div>

        <!-- API Endpoints Test -->
        <div class="test-section">
            <h3>🔗 API Endpoints</h3>
            <p>Test các API endpoints trực tiếp:</p>

            <div class="row">
                <div class="col-md-6">
                    <button class="btn btn-outline-primary" onclick="testBookmarkAPI()">
                        <i class="bi bi-bookmark"></i> Test Bookmark API
                    </button>
                    <div id="bookmark-result" class="mt-2"></div>
                </div>
                <div class="col-md-6">
                    <button class="btn btn-outline-success" onclick="testFollowAPI()">
                        <i class="bi bi-bell"></i> Test Follow API
                    </button>
                    <div id="follow-result" class="mt-2"></div>
                </div>
            </div>
        </div>

        <!-- Console Log Area -->
        <div class="test-section">
            <h3>📋 Console Log</h3>
            <div id="console-log" class="bg-dark text-light p-3 rounded"
                style="height: 200px; overflow-y: auto; font-family: monospace; font-size: 0.875rem;">
                <div class="text-success">✅ Test page loaded successfully</div>
                <div class="text-info">ℹ️ Thread Actions JavaScript: {{ file_exists(public_path('js/thread-actions.js'))
                    ? 'Loaded' : 'Not Found' }}</div>
                <div class="text-info">ℹ️ Thread Item CSS: {{ file_exists(public_path('css/thread-item.css')) ? 'Loaded'
                    : 'Not Found' }}</div>
            </div>
        </div>
        @endauth
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Thread Actions JS -->
    <script src="{{ asset('js/thread-actions.js') }}"></script>

    <script>
        // Console logging helper
        function logToConsole(message, type = 'info') {
            const consoleLog = document.getElementById('console-log');
            const timestamp = new Date().toLocaleTimeString();
            const colors = {
                'info': 'text-info',
                'success': 'text-success',
                'error': 'text-danger',
                'warning': 'text-warning'
            };

            const logEntry = document.createElement('div');
            logEntry.className = colors[type] || 'text-light';
            logEntry.innerHTML = `[${timestamp}] ${message}`;

            consoleLog.appendChild(logEntry);
            consoleLog.scrollTop = consoleLog.scrollHeight;
        }

        // Test API functions
        async function testBookmarkAPI() {
            logToConsole('🔖 Testing Bookmark API...', 'info');

            try {
                const response = await fetch('/api/v1/threads/11/bookmark', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    credentials: 'same-origin'
                });

                const data = await response.json();

                if (response.ok) {
                    logToConsole('✅ Bookmark API success: ' + JSON.stringify(data), 'success');
                    document.getElementById('bookmark-result').innerHTML =
                        '<div class="status-badge status-success">✅ API Success</div>';
                } else {
                    logToConsole('❌ Bookmark API error: ' + JSON.stringify(data), 'error');
                    document.getElementById('bookmark-result').innerHTML =
                        '<div class="status-badge status-error">❌ API Error</div>';
                }
            } catch (error) {
                logToConsole('💥 Bookmark API exception: ' + error.message, 'error');
                document.getElementById('bookmark-result').innerHTML =
                    '<div class="status-badge status-error">💥 Exception</div>';
            }
        }

        async function testFollowAPI() {
            logToConsole('👥 Testing Follow API...', 'info');

            try {
                const response = await fetch('/api/v1/threads/11/follow', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    credentials: 'same-origin'
                });

                const data = await response.json();

                if (response.ok) {
                    logToConsole('✅ Follow API success: ' + JSON.stringify(data), 'success');
                    document.getElementById('follow-result').innerHTML =
                        '<div class="status-badge status-success">✅ API Success</div>';
                } else {
                    logToConsole('❌ Follow API error: ' + JSON.stringify(data), 'error');
                    document.getElementById('follow-result').innerHTML =
                        '<div class="status-badge status-error">❌ API Error</div>';
                }
            } catch (error) {
                logToConsole('💥 Follow API exception: ' + error.message, 'error');
                document.getElementById('follow-result').innerHTML =
                    '<div class="status-badge status-error">💥 Exception</div>';
            }
        }

        // Log any JavaScript errors
        window.addEventListener('error', function(e) {
            logToConsole('💥 JavaScript Error: ' + e.message + ' (Line: ' + e.lineno + ')', 'error');
        });

        // Log when thread actions are triggered
        document.addEventListener('click', function(e) {
            if (e.target.closest('.bookmark-btn') || e.target.closest('.follow-btn')) {
                const actionType = e.target.closest('.bookmark-btn') ? 'bookmark' : 'follow';
                logToConsole(`🎯 ${actionType} button clicked`, 'info');
            }
        });
    </script>
</body>

</html>