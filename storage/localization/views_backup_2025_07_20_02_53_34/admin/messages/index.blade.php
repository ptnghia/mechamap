@extends('admin.layouts.dason')

@section('title', 'Quản lý Messages')

@push('styles')
<!-- Page specific CSS -->
@endpush

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Quản lý Messages</h1>

    <!-- Breadcrumbs -->
    @if(isset($breadcrumbs))
    <ol class="breadcrumb mb-4">
        @foreach($breadcrumbs as $breadcrumb)
        @if(isset($breadcrumb['url']))
        <li class="breadcrumb-item"><a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['title'] }}</a></li>
        @else
        <li class="breadcrumb-item active">{{ $breadcrumb['title'] }}</li>
        @endif
        @endforeach
    </ol>
    @endif

    <!-- Alerts -->
    @include('admin.layouts.partials.alerts')

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-cog me-1"></i>
            Cài đặt Messages
        </div>
        <div class="card-body">
            <form action="{{ route('admin.messages.settings.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="enable_private_messages"
                                name="enable_private_messages" value="1" {{ isset($settings['enable_private_messages'])
                                && $settings['enable_private_messages'] ? 'checked' : '' }}>
                            <label class="form-check-label" for="enable_private_messages">Cho phép messages cá
                                nhân</label>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="enable_message_encryption"
                                name="enable_message_encryption" value="1" {{
                                isset($settings['enable_message_encryption']) && $settings['enable_message_encryption']
                                ? 'checked' : '' }}>
                            <label class="form-check-label" for="enable_message_encryption">Mã hóa messages</label>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="enable_message_search"
                                name="enable_message_search" value="1" {{ isset($settings['enable_message_search']) &&
                                $settings['enable_message_search'] ? 'checked' : '' }}>
                            <label class="form-check-label" for="enable_message_search">Cho phép tìm kiếm
                                messages</label>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="enable_file_attachments"
                                name="enable_file_attachments" value="1" {{ isset($settings['enable_file_attachments'])
                                && $settings['enable_file_attachments'] ? 'checked' : '' }}>
                            <label class="form-check-label" for="enable_file_attachments">Cho phép đính kèm file</label>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="enable_message_reactions"
                                name="enable_message_reactions" value="1" {{
                                isset($settings['enable_message_reactions']) && $settings['enable_message_reactions']
                                ? 'checked' : '' }}>
                            <label class="form-check-label" for="enable_message_reactions">Cho phép reactions trong
                                messages</label>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="enable_message_editing"
                                name="enable_message_editing" value="1" {{ isset($settings['enable_message_editing']) &&
                                $settings['enable_message_editing'] ? 'checked' : '' }}>
                            <label class="form-check-label" for="enable_message_editing">Cho phép chỉnh sửa
                                message</label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="max_conversation_participants" class="form-label">Số người tham gia tối đa trong
                                một cuộc hội thoại</label>
                            <input type="number" class="form-control" id="max_conversation_participants"
                                name="max_conversation_participants" min="2" max="100"
                                value="{{ $settings['max_conversation_participants'] ?? 10 }}">
                        </div>

                        <div class="mb-3">
                            <label for="max_message_length" class="form-label">Độ dài tối đa của message (ký tự)</label>
                            <input type="number" class="form-control" id="max_message_length" name="max_message_length"
                                min="100" max="10000" value="{{ $settings['max_message_length'] ?? 1000 }}">
                        </div>

                        <div class="mb-3">
                            <label for="max_attachments_per_message" class="form-label">Số lượng file đính kèm tối
                                đa</label>
                            <input type="number" class="form-control" id="max_attachments_per_message"
                                name="max_attachments_per_message" min="0" max="10"
                                value="{{ $settings['max_attachments_per_message'] ?? 3 }}">
                        </div>

                        <div class="mb-3">
                            <label for="max_attachment_size_mb" class="form-label">Kích thước tối đa của file đính kèm
                                (MB)</label>
                            <input type="number" class="form-control" id="max_attachment_size_mb"
                                name="max_attachment_size_mb" min="1" max="100"
                                value="{{ $settings['max_attachment_size_mb'] ?? 10 }}">
                        </div>

                        <div class="mb-3">
                            <label for="message_retention_days" class="form-label">Thời gian lưu trữ messages (ngày, 0 =
                                vĩnh viễn)</label>
                            <input type="number" class="form-control" id="message_retention_days"
                                name="message_retention_days" min="0" max="3650"
                                value="{{ $settings['message_retention_days'] ?? 365 }}">
                        </div>

                        <div class="mb-3">
                            <label for="allowed_attachment_types" class="form-label">Loại file được phép đính
                                kèm</label>
                            <select class="form-select" id="allowed_attachment_types" name="allowed_attachment_types[]"
                                multiple>
                                @php
                                $allowedTypes = $settings['allowed_attachment_types'] ?? [];
                                if (is_string($allowedTypes)) {
                                $allowedTypes = json_decode($allowedTypes, true) ?? [];
                                }
                                @endphp
                                <option value="image" {{ in_array('image', $allowedTypes) ? 'selected' : '' }}>Hình ảnh
                                    (jpg, jpeg, png, gif)</option>
                                <option value="document" {{ in_array('document', $allowedTypes) ? 'selected' : '' }}>Tài
                                    liệu (doc, docx, pdf, txt)</option>
                                <option value="spreadsheet" {{ in_array('spreadsheet', $allowedTypes) ? 'selected' : ''
                                    }}>Bảng tính (xls, xlsx, csv)</option>
                                <option value="presentation" {{ in_array('presentation', $allowedTypes) ? 'selected'
                                    : '' }}>Trình chiếu (ppt, pptx)</option>
                                <option value="archive" {{ in_array('archive', $allowedTypes) ? 'selected' : '' }}>File
                                    nén (zip, rar)</option>
                                <option value="audio" {{ in_array('audio', $allowedTypes) ? 'selected' : '' }}>Âm thanh
                                    (mp3, wav)</option>
                                <option value="video" {{ in_array('video', $allowedTypes) ? 'selected' : '' }}>Video
                                    (mp4, avi)</option>
                            </select>
                            <small class="form-text text-muted">Giữ Ctrl để chọn nhiều loại</small>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Lưu cài đặt</button>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-chart-area me-1"></i>
                    Thống kê nhanh
                </div>
                <div class="card-body">
                    <p>Xem thống kê chi tiết về messages tại <a href="{{ route('admin.messages.statistics') }}">trang
                            thống kê</a>.</p>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="card bg-primary text-white h-100">
                                <div class="card-body d-flex align-items-center justify-content-between">
                                    <div>
                                        <h5 class="mb-0">Tổng số cuộc hội thoại</h5>
                                        <div class="display-4">{{ \App\Models\Conversation::count() }}</div>
                                    </div>
                                    <i class="fas fa-comments fa-2x"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card bg-info text-white h-100">
                                <div class="card-body d-flex align-items-center justify-content-between">
                                    <div>
                                        <h5 class="mb-0">Tổng số messages</h5>
                                        <div class="display-4">{{ \App\Models\Message::count() }}</div>
                                    </div>
                                    <i class="fas fa-envelope fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-tools me-1"></i>
                    Công cụ
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h5>Dọn dẹp messages cũ</h5>
                        <form action="{{ route('admin.messages.cleanup') }}" method="POST" class="row g-3">
                            @csrf
                            <div class="col-md-6">
                                <label for="days_old" class="form-label">Messages cũ hơn (ngày)</label>
                                <input type="number" class="form-control" id="days_old" name="days_old" min="1"
                                    value="30">
                            </div>
                            <div class="col-md-6">
                                <label for="delete_type" class="form-label">Loại xóa</label>
                                <select class="form-select" id="delete_type" name="delete_type">
                                    <option value="all">Tất cả messages</option>
                                    <option value="read">Chỉ messages đã đọc</option>
                                    <option value="inactive">Chỉ cuộc hội thoại không hoạt động</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-warning"
                                    onclick="return confirm('Bạn có chắc chắn muốn xóa messages cũ? Hành động này không thể hoàn tác.')">
                                    <i class="fas fa-trash"></i> Dọn dẹp
                                </button>
                            </div>
                        </form>
                    </div>
                    <hr>
                    <div class="mb-3">
                        <h5>Xuất thống kê</h5>
                        <a href="{{ route('admin.messages.export-statistics') }}" class="btn btn-success">
                            <i class="fas fa-file-export"></i> Xuất thống kê Messages (Excel)
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Enable Bootstrap Select for multiple select boxes
        $('#allowed_attachment_types').select2({
            placeholder: "Chọn loại file được phép",
            allowClear: true
        });

        // Toggle related fields based on checkboxes
        $('#enable_file_attachments').change(function() {
            const isEnabled = $(this).is(':checked');
            $('#max_attachments_per_message, #max_attachment_size_mb, #allowed_attachment_types')
                .prop('disabled', !isEnabled);
            if (!isEnabled) {
                $('#max_attachments_per_message').val('0');
            } else {
                $('#max_attachments_per_message').val('3');
            }
        });

        // Initial toggle
        $('#enable_file_attachments').trigger('change');
    });
</script>

@push('scripts')
<!-- Page specific JS -->
@endpush
@endsection