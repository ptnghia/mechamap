@extends('layouts.app')

@section('title', 'Test SEO Data')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">🧪 Test SEO Data Usage</h1>

            {{-- Test 1: Sử dụng biến $seoData trực tiếp --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h5>1. Sử dụng biến $seoData trực tiếp</h5>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <tr>
                            <td><strong>Title:</strong></td>
                            <td>{{ $seoData['title'] ?? 'Không có dữ liệu' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Description:</strong></td>
                            <td>{{ $seoData['description'] ?? 'Không có dữ liệu' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Keywords:</strong></td>
                            <td>{{ $seoData['keywords'] ?? 'Không có dữ liệu' }}</td>
                        </tr>
                        <tr>
                            <td><strong>OG Title:</strong></td>
                            <td>{{ $seoData['og_title'] ?? 'Không có dữ liệu' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Canonical URL:</strong></td>
                            <td>{{ $seoData['canonical_url'] ?? 'Không có dữ liệu' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- Test 2: Sử dụng biến được chuẩn bị sẵn --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h5>2. Sử dụng biến được chuẩn bị sẵn</h5>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <tr>
                            <td><strong>Current SEO Title:</strong></td>
                            <td>{{ $currentSeoTitle }}</td>
                        </tr>
                        <tr>
                            <td><strong>Current SEO Description:</strong></td>
                            <td>{{ $currentSeoDescription }}</td>
                        </tr>
                        <tr>
                            <td><strong>Current SEO Keywords:</strong></td>
                            <td>{{ $currentSeoKeywords }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- Test 3: Sử dụng Helper Functions --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h5>3. Sử dụng Helper Functions</h5>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <tr>
                            <td><strong>seo_title():</strong></td>
                            <td>{{ seo_title() }}</td>
                        </tr>
                        <tr>
                            <td><strong>seo_description():</strong></td>
                            <td>{{ seo_description() }}</td>
                        </tr>
                        <tr>
                            <td><strong>seo_value('keywords'):</strong></td>
                            <td>{{ seo_value('keywords', 'Default keywords') }}</td>
                        </tr>
                        <tr>
                            <td><strong>breadcrumb_title():</strong></td>
                            <td>{{ breadcrumb_title() }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- Test 4: Sử dụng Blade Directive --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h5>4. Sử dụng Blade Directive</h5>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <tr>
                            <td><strong>@seo('title'):</strong></td>
                            <td>@seo('title')</td>
                        </tr>
                        <tr>
                            <td><strong>@seo('description'):</strong></td>
                            <td>@seo('description')</td>
                        </tr>
                        <tr>
                            <td><strong>@seo('og_title'):</strong></td>
                            <td>@seo('og_title')</td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- Test 4.5: Test Custom Fallback Text --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h5>4.5. Test Custom Fallback Text cho seo_title_short()</h5>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <tr>
                            <td><strong>seo_title_short():</strong></td>
                            <td>{{ seo_title_short() }}</td>
                        </tr>
                        <tr>
                            <td><strong>seo_title_short('Custom Fallback'):</strong></td>
                            <td>{{ seo_title_short('Custom Fallback') }}</td>
                        </tr>
                        <tr>
                            <td><strong>seo_title_short('Trang Test', 'vi'):</strong></td>
                            <td>{{ seo_title_short('Trang Test', 'vi') }}</td>
                        </tr>
                        <tr>
                            <td><strong>seo_title_short('Test Page', 'en'):</strong></td>
                            <td>{{ seo_title_short('Test Page', 'en') }}</td>
                        </tr>
                        <tr>
                            <td><strong>@seo_short():</strong></td>
                            <td>@seo_short()</td>
                        </tr>
                        <tr>
                            <td><strong>@seo_short('Blade Directive Test'):</strong></td>
                            <td>@seo_short('Blade Directive Test')</td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- Test 5: Đa ngôn ngữ --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h5>5. Test Đa ngôn ngữ</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Tiếng Việt</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Title (vi):</strong></td>
                                    <td>{{ seo_title('vi') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Description (vi):</strong></td>
                                    <td>{{ seo_description('vi') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6>English</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Title (en):</strong></td>
                                    <td>{{ seo_title('en') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Description (en):</strong></td>
                                    <td>{{ seo_description('en') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Test 6: Debug toàn bộ $seoData --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h5>6. Debug toàn bộ $seoData</h5>
                </div>
                <div class="card-body">
                    <pre class="bg-light p-3"><code>{{ json_encode($seoData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                </div>
            </div>

            {{-- Test 7: Current Route Info --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h5>7. Thông tin Route hiện tại</h5>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <tr>
                            <td><strong>Route Name:</strong></td>
                            <td>{{ Route::currentRouteName() ?? 'Không có' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Current URL:</strong></td>
                            <td>{{ request()->url() }}</td>
                        </tr>
                        <tr>
                            <td><strong>Current Path:</strong></td>
                            <td>{{ request()->path() }}</td>
                        </tr>
                        <tr>
                            <td><strong>Current Locale:</strong></td>
                            <td>{{ app()->getLocale() }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="alert alert-info">
                <strong>💡 Lưu ý:</strong> Trang này được tạo để test việc sử dụng $seoData ở mọi view.
                Nếu tất cả các test trên hiển thị dữ liệu chính xác, nghĩa là cấu hình đã thành công!
            </div>
        </div>
    </div>
</div>
@endsection
