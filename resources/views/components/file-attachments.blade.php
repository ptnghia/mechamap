@props(['attachments', 'showcase', 'allowDownloads' => true])

@php
use App\Services\FileAttachmentService;
$fileService = new FileAttachmentService();
@endphp

@if(!empty($attachments) && is_array($attachments))
<div class="file-attachments-section mt-4">
    <div class="section-header d-flex align-items-center justify-content-between mb-3">
        <h5 class="mb-0">
            <i class="fas fa-paperclip me-2 text-primary"></i>
            File Attachments
            <span class="badge bg-secondary ms-2">{{ count($attachments) }}</span>
        </h5>
        @if($allowDownloads)
            <small class="text-muted">
                <i class="fas fa-download me-1"></i>
                Click để tải xuống
            </small>
        @endif
    </div>

    <div class="files-grid">
        @foreach($attachments as $index => $file)
            @php
                $fileDisplay = $fileService->getFileDisplay($file['extension'] ?? '');
                $downloadUrl = $allowDownloads ? $fileService->getDownloadUrl($file, $showcase->id) : null;
            @endphp
            
            <div class="file-item {{ $allowDownloads ? 'downloadable' : '' }}" 
                 @if($downloadUrl) onclick="downloadFile('{{ $downloadUrl }}')" @endif>
                
                <!-- File Icon -->
                <div class="file-icon" style="background-color: {{ $fileDisplay['color'] }}">
                    <i class="{{ $fileDisplay['icon'] }}"></i>
                </div>

                <!-- File Info -->
                <div class="file-info">
                    <div class="file-name" title="{{ $file['name'] ?? 'Unknown' }}">
                        {{ $file['name'] ?? 'Unknown' }}
                    </div>
                    <div class="file-meta">
                        <span class="file-size">{{ $fileService->formatFileSize($file['size'] ?? 0) }}</span>
                        @if(isset($file['category']))
                            <span class="file-category badge bg-light text-dark ms-2">
                                {{ ucfirst($file['category']) }}
                            </span>
                        @endif
                    </div>
                    @if(isset($file['uploaded_at']))
                        <div class="file-date text-muted small">
                            Uploaded: {{ \Carbon\Carbon::parse($file['uploaded_at'])->format('d/m/Y H:i') }}
                        </div>
                    @endif
                </div>

                <!-- Download Action -->
                @if($allowDownloads && $downloadUrl)
                    <div class="file-actions">
                        <button type="button" class="btn btn-sm btn-outline-primary" title="Tải xuống">
                            <i class="fas fa-download"></i>
                        </button>
                        @if(isset($file['download_count']) && $file['download_count'] > 0)
                            <small class="text-muted d-block mt-1">
                                {{ $file['download_count'] }} lượt tải
                            </small>
                        @endif
                    </div>
                @else
                    <div class="file-actions">
                        <span class="text-muted small">
                            <i class="fas fa-lock"></i>
                            Không cho phép tải
                        </span>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    @if(!$allowDownloads)
        <div class="alert alert-info mt-3">
            <i class="fas fa-info-circle me-2"></i>
            Tác giả không cho phép tải xuống file attachments.
        </div>
    @endif
</div>

<style>
.file-attachments-section {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 0.5rem;
    padding: 1.5rem;
}

.files-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 1rem;
}

.file-item {
    background: #fff;
    border: 1px solid #dee2e6;
    border-radius: 0.5rem;
    padding: 1rem;
    display: flex;
    align-items: center;
    transition: all 0.3s ease;
}

.file-item.downloadable {
    cursor: pointer;
}

.file-item.downloadable:hover {
    border-color: #007bff;
    box-shadow: 0 2px 8px rgba(0,123,255,0.15);
    transform: translateY(-1px);
}

.file-icon {
    width: 48px;
    height: 48px;
    border-radius: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
    font-size: 1.5rem;
    color: #fff;
    flex-shrink: 0;
}

.file-info {
    flex: 1;
    min-width: 0;
}

.file-name {
    font-weight: 600;
    margin-bottom: 0.25rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    color: #495057;
}

.file-meta {
    display: flex;
    align-items: center;
    margin-bottom: 0.25rem;
}

.file-size {
    font-size: 0.875rem;
    color: #6c757d;
}

.file-category {
    font-size: 0.75rem;
}

.file-date {
    font-size: 0.75rem;
}

.file-actions {
    flex-shrink: 0;
    text-align: center;
}

.file-actions .btn {
    border-radius: 50%;
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
}

@media (max-width: 768px) {
    .files-grid {
        grid-template-columns: 1fr;
    }
    
    .file-item {
        padding: 0.75rem;
    }
    
    .file-icon {
        width: 40px;
        height: 40px;
        font-size: 1.25rem;
        margin-right: 0.75rem;
    }
}
</style>

<script>
function downloadFile(url) {
    // Create a temporary link and trigger download
    const link = document.createElement('a');
    link.href = url;
    link.style.display = 'none';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    // Optional: Show download notification
    if (typeof showNotification === 'function') {
        showNotification('Đang tải file...', 'info');
    }
}
</script>
@endif
