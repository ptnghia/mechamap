<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('messages.language.switch_language') }} - MechaMap</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">{{ __('messages.language.switch_language') }} Test</h4>
                        @include('partials.language-switcher')
                    </div>
                    <div class="card-body">
                        <h5>{{ __('messages.welcome') }}</h5>
                        
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <h6>{{ __('messages.language.select_language') }}</h6>
                                <p><strong>Current Locale:</strong> {{ app()->getLocale() }}</p>
                                <p><strong>Current Language:</strong> {{ $currentLanguage['name'] }}</p>
                                <p><strong>Direction:</strong> {{ $currentLanguage['direction'] }}</p>
                            </div>
                            <div class="col-md-6">
                                <h6>Available Languages</h6>
                                <ul class="list-group">
                                    @foreach($supportedLanguages as $locale => $language)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="flag-icon flag-icon-{{ $language['flag'] }} me-2"></i>
                                            {{ $language['name'] }}
                                        </span>
                                        @if($locale === $currentLocale)
                                            <span class="badge bg-primary">Current</span>
                                        @else
                                            <a href="{{ route('language.switch', $locale) }}" class="btn btn-sm btn-outline-primary">
                                                Switch
                                            </a>
                                        @endif
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <h6>Sample Translations</h6>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Key</th>
                                            <th>Translation</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>messages.welcome</td>
                                            <td>{{ __('messages.welcome') }}</td>
                                        </tr>
                                        <tr>
                                            <td>messages.language.switch_language</td>
                                            <td>{{ __('messages.language.switch_language') }}</td>
                                        </tr>
                                        <tr>
                                            <td>messages.language.select_language</td>
                                            <td>{{ __('messages.language.select_language') }}</td>
                                        </tr>
                                        <tr>
                                            <td>messages.buttons.save</td>
                                            <td>{{ __('messages.buttons.save') }}</td>
                                        </tr>
                                        <tr>
                                            <td>messages.buttons.cancel</td>
                                            <td>{{ __('messages.buttons.cancel') }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <h6>Test Actions</h6>
                            <div class="d-flex gap-2">
                                <button class="btn btn-primary" onclick="testLanguageAPI()">
                                    Test Language API
                                </button>
                                <button class="btn btn-secondary" onclick="autoDetectLanguage()">
                                    {{ __('messages.language.auto_detect') }}
                                </button>
                                <a href="{{ route('home') }}" class="btn btn-outline-primary">
                                    Back to Home
                                </a>
                            </div>
                        </div>
                        
                        <div id="apiResult" class="mt-3" style="display: none;">
                            <div class="alert alert-info">
                                <h6>API Response:</h6>
                                <pre id="apiData"></pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    function testLanguageAPI() {
        fetch('/language/current')
            .then(response => response.json())
            .then(data => {
                document.getElementById('apiData').textContent = JSON.stringify(data, null, 2);
                document.getElementById('apiResult').style.display = 'block';
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error testing API');
            });
    }
    
    function autoDetectLanguage() {
        fetch('/language/auto-detect', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            if (data.success) {
                setTimeout(() => window.location.reload(), 1000);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error auto-detecting language');
        });
    }
    </script>
</body>
</html>
