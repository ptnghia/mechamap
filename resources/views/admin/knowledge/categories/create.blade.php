@extends('admin.layouts.dason')

@section('title', 'Thêm Danh Mục Mới')

@section('content')
<div class="container-fluid">
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Thêm Danh Mục Mới</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">MechaMap</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.knowledge.index') }}">Cơ Sở Tri Thức</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.knowledge.categories') }}">Danh Mục</a></li>
                        <li class="breadcrumb-item active">Thêm Mới</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <form action="{{ route('admin.knowledge.categories.store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Thông Tin Danh Mục</h4>
                    </div>
                    <div class="card-body">
                        <!-- Name -->
                        <div class="mb-3">
                            <label for="name" class="form-label">Tên Danh Mục <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Mô Tả</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="4" 
                                      placeholder="Mô tả chi tiết về danh mục...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Parent Category -->
                        <div class="mb-3">
                            <label for="parent_id" class="form-label">Danh Mục Cha</label>
                            <select class="form-select @error('parent_id') is-invalid @enderror" 
                                    id="parent_id" name="parent_id">
                                <option value="">Chọn danh mục cha (tùy chọn)</option>
                                @foreach($parent_categories as $parent)
                                    <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                                        {{ $parent->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">Để trống nếu đây là danh mục gốc</div>
                            @error('parent_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Icon and Color -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="icon" class="form-label">Icon (Font Awesome)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i id="iconPreview" class="fas fa-folder"></i>
                                        </span>
                                        <input type="text" class="form-control @error('icon') is-invalid @enderror" 
                                               id="icon" name="icon" value="{{ old('icon', 'fas fa-folder') }}" 
                                               placeholder="fas fa-folder">
                                    </div>
                                    <div class="form-text">Ví dụ: fas fa-cog, fas fa-tools, fas fa-cube</div>
                                    @error('icon')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="color" class="form-label">Màu Sắc</label>
                                    <div class="input-group">
                                        <input type="color" class="form-control form-control-color @error('color') is-invalid @enderror" 
                                               id="color" name="color" value="{{ old('color', '#007bff') }}">
                                        <input type="text" class="form-control" id="colorText" value="{{ old('color', '#007bff') }}" readonly>
                                    </div>
                                    @error('color')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Sort Order -->
                        <div class="mb-3">
                            <label for="sort_order" class="form-label">Thứ Tự Sắp Xếp</label>
                            <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                                   id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}" min="0">
                            <div class="form-text">Số nhỏ hơn sẽ hiển thị trước</div>
                            @error('sort_order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Settings -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Cài Đặt</h4>
                    </div>
                    <div class="card-body">
                        <!-- Active Status -->
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" 
                                       name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Kích hoạt danh mục
                                </label>
                            </div>
                            <div class="form-text">Danh mục không kích hoạt sẽ không hiển thị</div>
                        </div>
                    </div>
                </div>

                <!-- Preview -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Xem Trước</h4>
                    </div>
                    <div class="card-body">
                        <div id="categoryPreview" class="d-flex align-items-center p-3 border rounded">
                            <div class="avatar-sm me-3">
                                <span class="avatar-title rounded-circle" id="previewIcon" style="background-color: #007bff;">
                                    <i class="fas fa-folder text-white"></i>
                                </span>
                            </div>
                            <div>
                                <h6 class="mb-0" id="previewName">Tên danh mục</h6>
                                <p class="text-muted font-size-13 mb-0" id="previewDescription">Mô tả danh mục</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Common Icons -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Icon Phổ Biến</h4>
                    </div>
                    <div class="card-body">
                        <div class="row g-2">
                            <div class="col-3">
                                <button type="button" class="btn btn-outline-secondary btn-sm w-100 icon-btn" data-icon="fas fa-cog">
                                    <i class="fas fa-cog"></i>
                                </button>
                            </div>
                            <div class="col-3">
                                <button type="button" class="btn btn-outline-secondary btn-sm w-100 icon-btn" data-icon="fas fa-tools">
                                    <i class="fas fa-tools"></i>
                                </button>
                            </div>
                            <div class="col-3">
                                <button type="button" class="btn btn-outline-secondary btn-sm w-100 icon-btn" data-icon="fas fa-cube">
                                    <i class="fas fa-cube"></i>
                                </button>
                            </div>
                            <div class="col-3">
                                <button type="button" class="btn btn-outline-secondary btn-sm w-100 icon-btn" data-icon="fas fa-robot">
                                    <i class="fas fa-robot"></i>
                                </button>
                            </div>
                            <div class="col-3">
                                <button type="button" class="btn btn-outline-secondary btn-sm w-100 icon-btn" data-icon="fas fa-calculator">
                                    <i class="fas fa-calculator"></i>
                                </button>
                            </div>
                            <div class="col-3">
                                <button type="button" class="btn btn-outline-secondary btn-sm w-100 icon-btn" data-icon="fas fa-layer-group">
                                    <i class="fas fa-layer-group"></i>
                                </button>
                            </div>
                            <div class="col-3">
                                <button type="button" class="btn btn-outline-secondary btn-sm w-100 icon-btn" data-icon="fas fa-wrench">
                                    <i class="fas fa-wrench"></i>
                                </button>
                            </div>
                            <div class="col-3">
                                <button type="button" class="btn btn-outline-secondary btn-sm w-100 icon-btn" data-icon="fas fa-industry">
                                    <i class="fas fa-industry"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-info">
                                <i class="fas fa-save me-1"></i> Lưu Danh Mục
                            </button>
                            <a href="{{ route('admin.knowledge.categories') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i> Hủy
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Update preview when inputs change
    $('#name').on('keyup', function() {
        const name = $(this).val() || 'Tên danh mục';
        $('#previewName').text(name);
    });
    
    $('#description').on('keyup', function() {
        const description = $(this).val() || 'Mô tả danh mục';
        $('#previewDescription').text(description.substring(0, 50) + (description.length > 50 ? '...' : ''));
    });
    
    $('#icon').on('keyup', function() {
        const icon = $(this).val() || 'fas fa-folder';
        $('#iconPreview').attr('class', icon);
        $('#previewIcon i').attr('class', icon + ' text-white');
    });
    
    $('#color').on('change', function() {
        const color = $(this).val();
        $('#colorText').val(color);
        $('#previewIcon').css('background-color', color);
    });
    
    // Icon selection
    $('.icon-btn').on('click', function() {
        const icon = $(this).data('icon');
        $('#icon').val(icon);
        $('#iconPreview').attr('class', icon);
        $('#previewIcon i').attr('class', icon + ' text-white');
    });
    
    // Auto-generate slug from name
    $('#name').on('keyup', function() {
        // Add slug generation logic if needed
    });
});
</script>
@endsection
