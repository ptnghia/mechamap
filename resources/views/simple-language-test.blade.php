<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Simple Language Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <h4>Simple Language Test</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Current Status</h5>
                        <ul class="list-group">
                            <li class="list-group-item">
                                <strong>App Locale:</strong> {{ app()->getLocale() }}
                            </li>
                            <li class="list-group-item">
                                <strong>Session Locale:</strong> {{ session('locale', 'not set') }}
                            </li>
                            <li class="list-group-item">
                                <strong>Welcome Message:</strong> {{ __('messages.welcome') }}
                            </li>
                            <li class="list-group-item">
                                <strong>Switch Language:</strong> {{ __('messages.language.switch_language') }}
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h5>Language Switch Links</h5>
                        <div class="d-grid gap-2">
                            <a href="{{ route('language.switch', 'vi') }}" class="btn btn-primary">
                                Switch to Vietnamese (vi)
                            </a>
                            <a href="{{ route('language.switch', 'en') }}" class="btn btn-success">
                                Switch to English (en)
                            </a>
                            <button onclick="switchWithAjax('vi')" class="btn btn-outline-primary">
                                AJAX Switch to Vietnamese
                            </button>
                            <button onclick="switchWithAjax('en')" class="btn btn-outline-success">
                                AJAX Switch to English
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4">
                    <h5>Debug Info</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <button onclick="showDebugInfo()" class="btn btn-info">Show Debug Info</button>
                            <div id="debugInfo" class="mt-3" style="display: none;">
                                <pre id="debugData" class="bg-light p-3"></pre>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div id="ajaxResult" class="alert alert-info" style="display: none;">
                                <h6>AJAX Result:</h6>
                                <pre id="ajaxData"></pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function switchWithAjax(locale) {
            fetch(`/language/switch/${locale}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('ajaxData').textContent = JSON.stringify(data, null, 2);
                document.getElementById('ajaxResult').style.display = 'block';
                
                if (data.success) {
                    setTimeout(() => window.location.reload(), 1000);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('ajaxData').textContent = 'Error: ' + error.message;
                document.getElementById('ajaxResult').style.display = 'block';
            });
        }
        
        function showDebugInfo() {
            fetch('/debug-language')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('debugData').textContent = JSON.stringify(data, null, 2);
                    document.getElementById('debugInfo').style.display = 'block';
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('debugData').textContent = 'Error: ' + error.message;
                    document.getElementById('debugInfo').style.display = 'block';
                });
        }
    </script>
</body>
</html>
