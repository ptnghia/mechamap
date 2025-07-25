@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show admin-alert admin-alert-success" role="alert">
        <i class="fas fa-check-circle-fill me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show admin-alert admin-alert-danger" role="alert">
        <i class="fas fa-exclamation-triangle-fill me-2"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('warning'))
    <div class="alert alert-warning alert-dismissible fade show admin-alert admin-alert-warning" role="alert">
        <i class="fas fa-exclamation-circle-fill me-2"></i>
        {{ session('warning') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('info'))
    <div class="alert alert-info alert-dismissible fade show admin-alert admin-alert-info" role="alert">
        <i class="fas fa-info-circle-fill me-2"></i>
        {{ session('info') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show admin-alert admin-alert-danger" role="alert">
        <i class="fas fa-exclamation-triangle-fill me-2"></i>
        <strong>{{ __('Error!') }}</strong> {{ __('Please check the form for errors.') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
