<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

/**
 * Controller phục vụ Swagger UI để hiển thị API documentation
 */
class ApiDocumentationController extends Controller
{
    /**
     * Hiển thị Swagger UI interface
     */
    public function swaggerUi()
    {
        $swaggerHtml = $this->generateSwaggerHtml();

        return response($swaggerHtml)->header('Content-Type', 'text/html');
    }

    /**
     * Trả về OpenAPI JSON specification
     */
    public function openApiSpec()
    {
        $openApiPath = base_path('openapi.json');

        if (!File::exists($openApiPath)) {
            return response()->json([
                'error' => 'OpenAPI specification không tìm thấy'
            ], 404);
        }

        $openApiContent = File::get($openApiPath);

        return response($openApiContent)
            ->header('Content-Type', 'application/json')
            ->header('Access-Control-Allow-Origin', '*');
    }

    /**
     * Tạo interactive API explorer
     */
    public function apiExplorer()
    {
        $explorerHtml = $this->generateApiExplorerHtml();

        return response($explorerHtml)->header('Content-Type', 'text/html');
    }

    /**
     * Lấy danh sách tất cả endpoints
     */
    public function endpointsList()
    {
        $routes = Route::getRoutes();
        $apiRoutes = [];

        foreach ($routes as $route) {
            $uri = $route->uri();

            if (strpos($uri, 'api/') === 0) {
                $methods = $route->methods();
                $methods = array_filter($methods, function ($method) {
                    return !in_array($method, ['HEAD', 'OPTIONS']);
                });

                $apiRoutes[] = [
                    'uri' => $uri,
                    'methods' => array_values($methods),
                    'name' => $route->getName(),
                    'action' => $route->getActionName(),
                    'middleware' => $route->middleware(),
                ];
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'total_endpoints' => count($apiRoutes),
                'endpoints' => $apiRoutes,
            ]
        ]);
    }

    /**
     * Tạo markdown documentation
     */
    public function markdownDocs()
    {
        $markdownPath = base_path('API_DOCUMENTATION.md');

        if (!File::exists($markdownPath)) {
            return response()->json([
                'error' => 'API Documentation không tìm thấy'
            ], 404);
        }

        $markdownContent = File::get($markdownPath);

        // Chuyển đổi markdown thành HTML
        $htmlContent = $this->convertMarkdownToHtml($markdownContent);

        return response($htmlContent)->header('Content-Type', 'text/html');
    }

    /**
     * API schema validation endpoint
     */
    public function validateSchema(Request $request)
    {
        $endpoint = $request->get('endpoint');
        $method = $request->get('method', 'GET');
        $data = $request->get('data', []);

        // Simulation validation logic
        $validation = $this->performSchemaValidation($endpoint, $method, $data);

        return response()->json([
            'success' => true,
            'data' => [
                'endpoint' => $endpoint,
                'method' => $method,
                'validation_result' => $validation,
            ]
        ]);
    }

    // ===== PRIVATE HELPER METHODS =====

    private function generateSwaggerHtml()
    {
        $baseUrl = url('/');
        $openApiUrl = url('/api/v1/docs/openapi.json');

        return <<<HTML
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Documentation - Laravel Forum</title>
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/swagger-ui-dist@5.9.0/swagger-ui.css" />
    <link rel="icon" type="image/png" href="{$baseUrl}/favicon.ico" sizes="32x32" />
    <style>
        html {
            box-sizing: border-box;
            overflow: -moz-scrollbars-vertical;
            overflow-y: scroll;
        }
        *, *:before, *:after {
            box-sizing: inherit;
        }
        body {
            margin:0;
            background: #fafafa;
        }
        .swagger-ui .topbar {
            background-color: #2c3e50;
        }
        .swagger-ui .topbar .download-url-wrapper .select-label {
            color: #ffffff;
        }
        .swagger-ui .info .title {
            color: #2c3e50;
        }
    </style>
</head>
<body>
    <div id="swagger-ui"></div>

    <script src="https://unpkg.com/swagger-ui-dist@5.9.0/swagger-ui-bundle.js"></script>
    <script src="https://unpkg.com/swagger-ui-dist@5.9.0/swagger-ui-standalone-preset.js"></script>
    <script>
        window.onload = function() {
            const ui = SwaggerUIBundle({
                url: '{$openApiUrl}',
                dom_id: '#swagger-ui',
                deepLinking: true,
                presets: [
                    SwaggerUIBundle.presets.apis,
                    SwaggerUIStandalonePreset
                ],
                plugins: [
                    SwaggerUIBundle.plugins.DownloadUrl
                ],
                layout: "StandaloneLayout",
                validatorUrl: null,
                tryItOutEnabled: true,
                supportedSubmitMethods: ['get', 'post', 'put', 'delete', 'patch'],
                onComplete: function() {
                    console.log('Swagger UI loaded successfully');
                },
                onFailure: function(error) {
                    console.error('Failed to load Swagger UI:', error);
                }
            });
        };
    </script>
</body>
</html>
HTML;
    }

    private function generateApiExplorerHtml()
    {
        $baseUrl = url('/');

        return <<<HTML
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Explorer - Laravel Forum</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .endpoint-card {
            transition: all 0.3s ease;
            border-left: 4px solid #dee2e6;
        }
        .endpoint-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .method-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }
        .method-GET { background-color: #28a745; }
        .method-POST { background-color: #007bff; }
        .method-PUT { background-color: #ffc107; color: #000; }
        .method-DELETE { background-color: #dc3545; }
        .method-PATCH { background-color: #17a2b8; }

        .response-area {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            max-height: 400px;
            overflow-y: auto;
        }

        .header-title {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem 0;
        }
    </style>
</head>
<body>
    <div class="header-title text-center">
        <h1><i class="fas fa-code"></i> API Explorer</h1>
        <p class="mb-0">Interactive Laravel Forum API Testing Interface</p>
    </div>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-list"></i> API Endpoints</h5>
                    </div>
                    <div class="card-body p-0">
                        <div id="endpoints-list" class="list-group list-group-flush">
                            <div class="text-center p-3">
                                <div class="spinner-border" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-2 mb-0">Đang tải danh sách endpoints...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-play"></i> Test Endpoint</h5>
                    </div>
                    <div class="card-body">
                        <form id="api-test-form">
                            <div class="row">
                                <div class="col-md-3">
                                    <select class="form-select" id="method-select">
                                        <option value="GET">GET</option>
                                        <option value="POST">POST</option>
                                        <option value="PUT">PUT</option>
                                        <option value="DELETE">DELETE</option>
                                        <option value="PATCH">PATCH</option>
                                    </select>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" id="endpoint-input"
                                           placeholder="Nhập endpoint (ví dụ: /api/v1/threads)" value="/api/v1/threads">
                                </div>
                            </div>

                            <div class="mt-3">
                                <label class="form-label">Headers (JSON format):</label>
                                <textarea class="form-control" id="headers-input" rows="3"
                                          placeholder='{"Authorization": "Bearer your-token", "Content-Type": "application/json"}'></textarea>
                            </div>

                            <div class="mt-3">
                                <label class="form-label">Request Body (JSON format):</label>
                                <textarea class="form-control" id="body-input" rows="4"
                                          placeholder='{"title": "Test thread", "content": "Test content"}'></textarea>
                            </div>

                            <div class="mt-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane"></i> Gửi Request
                                </button>
                                <button type="button" class="btn btn-secondary" onclick="clearForm()">
                                    <i class="fas fa-eraser"></i> Clear
                                </button>
                            </div>
                        </form>

                        <div class="mt-4">
                            <h6>Response:</h6>
                            <div id="response-area" class="response-area p-3">
                                <em class="text-muted">Chưa có response. Thực hiện một API request để xem kết quả.</em>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-info-circle"></i> API Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Base URL:</h6>
                                <code>{$baseUrl}</code>

                                <h6 class="mt-3">Useful Links:</h6>
                                <ul class="list-unstyled">
                                    <li><a href="{$baseUrl}/api/v1/docs" target="_blank">
                                        <i class="fas fa-book"></i> Swagger Documentation
                                    </a></li>
                                    <li><a href="{$baseUrl}/api/v1/docs/markdown" target="_blank">
                                        <i class="fas fa-file-alt"></i> Markdown Documentation
                                    </a></li>
                                    <li><a href="{$baseUrl}/api/v1/monitoring/dashboard" target="_blank">
                                        <i class="fas fa-chart-line"></i> API Monitoring Dashboard
                                    </a></li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6>Quick Examples:</h6>
                                <div class="small">
                                    <strong>Get threads:</strong><br>
                                    <code>GET /api/v1/threads</code><br><br>

                                    <strong>Search:</strong><br>
                                    <code>GET /api/v1/search?query=laravel</code><br><br>

                                    <strong>User profile:</strong><br>
                                    <code>GET /api/v1/users/{username}</code>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Load endpoints list
        async function loadEndpoints() {
            try {
                const response = await fetch('/api/v1/docs/endpoints');
                const data = await response.json();

                const endpointsList = document.getElementById('endpoints-list');
                endpointsList.innerHTML = '';

                data.data.endpoints.forEach(endpoint => {
                    endpoint.methods.forEach(method => {
                        const item = document.createElement('div');
                        item.className = 'list-group-item list-group-item-action endpoint-card';
                        item.innerHTML = `
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="badge method-badge method-\${method}">\${method}</span>
                                    <span class="ms-2">\${endpoint.uri}</span>
                                </div>
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        `;
                        item.onclick = () => selectEndpoint(method, '/' + endpoint.uri);
                        endpointsList.appendChild(item);
                    });
                });
            } catch (error) {
                document.getElementById('endpoints-list').innerHTML =
                    '<div class="p-3 text-danger">Lỗi khi tải endpoints: ' + error.message + '</div>';
            }
        }

        function selectEndpoint(method, endpoint) {
            document.getElementById('method-select').value = method;
            document.getElementById('endpoint-input').value = endpoint;
        }

        // Handle form submission
        document.getElementById('api-test-form').addEventListener('submit', async function(e) {
            e.preventDefault();

            const method = document.getElementById('method-select').value;
            const endpoint = document.getElementById('endpoint-input').value;
            const headersText = document.getElementById('headers-input').value;
            const bodyText = document.getElementById('body-input').value;

            let headers = {};
            if (headersText.trim()) {
                try {
                    headers = JSON.parse(headersText);
                } catch (error) {
                    alert('Invalid JSON format in headers');
                    return;
                }
            }

            let body = null;
            if (bodyText.trim() && ['POST', 'PUT', 'PATCH'].includes(method)) {
                try {
                    body = JSON.stringify(JSON.parse(bodyText));
                    headers['Content-Type'] = 'application/json';
                } catch (error) {
                    alert('Invalid JSON format in body');
                    return;
                }
            }

            const responseArea = document.getElementById('response-area');
            responseArea.innerHTML = '<div class="text-center"><div class="spinner-border spinner-border-sm"></div> Sending request...</div>';

            try {
                const startTime = Date.now();
                const response = await fetch(endpoint, {
                    method: method,
                    headers: headers,
                    body: body
                });
                const duration = Date.now() - startTime;

                const responseData = await response.text();
                let formattedResponse;

                try {
                    formattedResponse = JSON.stringify(JSON.parse(responseData), null, 2);
                } catch {
                    formattedResponse = responseData;
                }

                const statusClass = response.ok ? 'text-success' : 'text-danger';
                responseArea.innerHTML = `
                    <div class="mb-2">
                        <strong>Status:</strong> <span class="\${statusClass}">\${response.status} \${response.statusText}</span>
                        <span class="ms-3"><strong>Duration:</strong> \${duration}ms</span>
                    </div>
                    <div class="mb-2"><strong>Response:</strong></div>
                    <pre class="mb-0" style="background: white; padding: 1rem; border-radius: 0.375rem;"><code>\${formattedResponse}</code></pre>
                `;
            } catch (error) {
                responseArea.innerHTML = `<div class="text-danger">Error: \${error.message}</div>`;
            }
        });

        function clearForm() {
            document.getElementById('headers-input').value = '';
            document.getElementById('body-input').value = '';
            document.getElementById('response-area').innerHTML =
                '<em class="text-muted">Chưa có response. Thực hiện một API request để xem kết quả.</em>';
        }

        // Load endpoints on page load
        loadEndpoints();
    </script>
</body>
</html>
HTML;
    }

    private function convertMarkdownToHtml($markdown)
    {
        // Simple markdown to HTML conversion
        $html = htmlspecialchars($markdown);

        // Convert headers
        $html = preg_replace('/^### (.*$)/m', '<h3>$1</h3>', $html);
        $html = preg_replace('/^## (.*$)/m', '<h2>$1</h2>', $html);
        $html = preg_replace('/^# (.*$)/m', '<h1>$1</h1>', $html);

        // Convert bold and italic
        $html = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $html);
        $html = preg_replace('/\*(.*?)\*/', '<em>$1</em>', $html);

        // Convert code blocks
        $html = preg_replace('/```(.*?)```/s', '<pre><code>$1</code></pre>', $html);
        $html = preg_replace('/`(.*?)`/', '<code>$1</code>', $html);

        // Convert links
        $html = preg_replace('/\[([^\]]+)\]\(([^)]+)\)/', '<a href="$2">$1</a>', $html);

        // Convert line breaks
        $html = nl2br($html);

        return <<<HTML
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Documentation - Laravel Forum</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding: 2rem 0; }
        pre { background: #f8f9fa; padding: 1rem; border-radius: 0.375rem; }
        code { background: #f8f9fa; padding: 0.25rem 0.5rem; border-radius: 0.25rem; }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="content">
                    {$html}
                </div>
            </div>
        </div>
    </div>
</body>
</html>
HTML;
    }

    private function performSchemaValidation($endpoint, $method, $data)
    {
        // Simulation validation - có thể integrate với actual validation logic
        return [
            'is_valid' => true,
            'errors' => [],
            'warnings' => [],
            'suggestions' => [
                'Consider adding rate limiting headers',
                'Include response time in headers',
            ]
        ];
    }
}
