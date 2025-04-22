@extends('admin.layouts.app')

@section('title', 'Bản quyền')
@section('header', 'Bản quyền')

@section('content')
    <div class="row">
        <div class="col-md-3 mb-4">
            @include('admin.settings.partials.sidebar')
        </div>
        
        <div class="col-md-9">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Thông tin bản quyền') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.update-copyright') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="copyright_text" class="form-label">{{ __('Nội dung bản quyền') }}</label>
                            <input type="text" class="form-control @error('copyright_text') is-invalid @enderror" id="copyright_text" name="copyright_text" value="{{ old('copyright_text', $settings['copyright_text'] ?? '© ' . date('Y') . ' ' . config('app.name') . '. All rights reserved.') }}">
                            <div class="form-text">{{ __('Nội dung bản quyền hiển thị ở footer của trang web.') }}</div>
                            @error('copyright_text')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="copyright_owner" class="form-label">{{ __('Chủ sở hữu bản quyền') }}</label>
                                <input type="text" class="form-control @error('copyright_owner') is-invalid @enderror" id="copyright_owner" name="copyright_owner" value="{{ old('copyright_owner', $settings['copyright_owner'] ?? config('app.name')) }}">
                                <div class="form-text">{{ __('Tên của chủ sở hữu bản quyền.') }}</div>
                                @error('copyright_owner')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="copyright_year" class="form-label">{{ __('Năm bản quyền') }}</label>
                                <input type="text" class="form-control @error('copyright_year') is-invalid @enderror" id="copyright_year" name="copyright_year" value="{{ old('copyright_year', $settings['copyright_year'] ?? date('Y')) }}">
                                <div class="form-text">{{ __('Năm bản quyền. Có thể là một năm cụ thể hoặc một khoảng thời gian (ví dụ: 2020-2023).') }}</div>
                                @error('copyright_year')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i> {{ __('Lưu cấu hình') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Xem trước') }}</h5>
                </div>
                <div class="card-body">
                    <div class="border p-3 rounded bg-light text-center">
                        <p class="mb-0" id="copyright-preview">
                            {{ $settings['copyright_text'] ?? '© ' . date('Y') . ' ' . config('app.name') . '. All rights reserved.' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Live preview
    document.getElementById('copyright_text').addEventListener('input', function() {
        document.getElementById('copyright-preview').textContent = this.value;
    });
    
    // Auto-generate copyright text
    document.getElementById('copyright_owner').addEventListener('input', updateCopyrightText);
    document.getElementById('copyright_year').addEventListener('input', updateCopyrightText);
    
    function updateCopyrightText() {
        const owner = document.getElementById('copyright_owner').value || '{{ config('app.name') }}';
        const year = document.getElementById('copyright_year').value || '{{ date('Y') }}';
        const text = `© ${year} ${owner}. All rights reserved.`;
        
        // Only update if the current value is the default or empty
        const currentText = document.getElementById('copyright_text').value;
        if (!currentText || currentText === document.getElementById('copyright-preview').textContent) {
            document.getElementById('copyright_text').value = text;
            document.getElementById('copyright-preview').textContent = text;
        }
    }
</script>
@endpush
