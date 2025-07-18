{{-- WebSocket Configuration Component --}}
<script>
    // WebSocket Configuration from Laravel Backend
    window.websocketConfig = {!! $configJson !!};

    // Auto-initialize flag
    window.autoInitWebSocket = {{ $autoInit ? 'true' : 'false' }};

    // Environment information
    window.mechaMapEnv = {
        environment: '{{ app()->environment() }}',
        debug: {{ config('app.debug') ? 'true' : 'false' }},
        url: '{{ config('app.url') }}',
        websocket: {
            url: '{{ $serverUrl }}',
            host: '{{ $serverHost }}',
            port: {{ $serverPort }},
            secure: {{ $secure ? 'true' : 'false' }}
        }
    };

    // Console log for debugging
    @if(config('app.debug'))
    console.log('MechaMap WebSocket Config:', {
        config: window.websocketConfig,
        environment: window.mechaMapEnv,
        autoInit: window.autoInitWebSocket
    });
    @endif
</script>

{{-- Load Socket.IO client library --}}
@if($secure)
    <script src="https://cdn.socket.io/4.7.5/socket.io.min.js"></script>
@else
    <script src="https://cdn.socket.io/4.7.5/socket.io.min.js"></script>
@endif

{{-- Load WebSocket configuration helper --}}
<script src="{{ asset('js/websocket-config.js') }}?v={{ time() }}"></script>

{{-- Auto-initialization is handled by websocket-config.js to prevent duplicate connections --}}
{{-- Component only provides configuration, not initialization --}}
