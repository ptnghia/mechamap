@extends('layouts.app')

@section('title', 'Test Comment Image Upload Component')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0">Test Comment Image Upload Component</h3>
                    <p class="text-muted mb-0">Testing the new comment image upload component with drag & drop functionality</p>
                </div>
                <div class="card-body">
                    
                    <!-- Test Form 1: Regular Size -->
                    <div class="mb-5">
                        <h5>Regular Size Component</h5>
                        <form id="test-form-1" class="border p-3 rounded">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Comment Content</label>
                                <textarea class="form-control" rows="3" placeholder="Enter your comment..."></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Upload Images</label>
                                <x-comment-image-upload
                                    :max-files="5"
                                    max-size="5MB"
                                    context="comment"
                                    upload-text="Thêm hình ảnh cho bình luận"
                                    accept-description="Tối đa 5 file • 5MB mỗi file • JPG, JPEG, PNG, GIF, WEBP"
                                    :show-preview="true"
                                    :compact="false"
                                />
                            </div>
                            
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-1"></i>
                                Submit Comment
                            </button>
                        </form>
                    </div>

                    <!-- Test Form 2: Compact Size -->
                    <div class="mb-5">
                        <h5>Compact Size Component</h5>
                        <form id="test-form-2" class="border p-3 rounded bg-light">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Reply Content</label>
                                <textarea class="form-control" rows="2" placeholder="Enter your reply..."></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Upload Images</label>
                                <x-comment-image-upload
                                    :max-files="3"
                                    max-size="5MB"
                                    context="reply"
                                    upload-text="Thêm hình ảnh cho phản hồi"
                                    accept-description="Tối đa 3 file • 5MB mỗi file • JPG, JPEG, PNG, GIF, WEBP"
                                    :show-preview="true"
                                    :compact="true"
                                />
                            </div>
                            
                            <button type="submit" class="btn btn-success btn-sm">
                                <i class="fas fa-reply me-1"></i>
                                Submit Reply
                            </button>
                        </form>
                    </div>

                    <!-- Test Results -->
                    <div class="mb-4">
                        <h5>Test Results</h5>
                        <div id="test-results" class="alert alert-info">
                            <strong>Ready for testing!</strong> Try uploading some images using the components above.
                        </div>
                    </div>

                    <!-- API Test Buttons -->
                    <div class="mb-4">
                        <h5>API Test Functions</h5>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-primary" onclick="testUpload(1)">
                                Test Upload Form 1
                            </button>
                            <button type="button" class="btn btn-outline-success" onclick="testUpload(2)">
                                Test Upload Form 2
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="clearAllUploads()">
                                Clear All Uploads
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const resultsDiv = document.getElementById('test-results');
    
    // Handle form submissions
    document.getElementById('test-form-1').addEventListener('submit', async function(e) {
        e.preventDefault();
        await handleFormSubmit(this, 1);
    });
    
    document.getElementById('test-form-2').addEventListener('submit', async function(e) {
        e.preventDefault();
        await handleFormSubmit(this, 2);
    });
    
    async function handleFormSubmit(form, formNumber) {
        const uploadComponent = form.querySelector('.comment-image-upload');
        const textarea = form.querySelector('textarea');
        
        if (!uploadComponent || !uploadComponent.commentImageUpload) {
            showResult('Error: Upload component not found', 'danger');
            return;
        }
        
        const hasFiles = uploadComponent.commentImageUpload.hasFiles();
        const content = textarea.value.trim();
        
        if (!content && !hasFiles) {
            showResult('Please enter some content or upload images', 'warning');
            return;
        }
        
        showResult(`Form ${formNumber}: Processing...`, 'info');
        
        try {
            let uploadedImages = [];
            
            if (hasFiles) {
                showResult(`Form ${formNumber}: Uploading images...`, 'info');
                uploadedImages = await uploadComponent.commentImageUpload.uploadFiles();
            }
            
            // Simulate comment submission
            const result = {
                content: content,
                images: uploadedImages,
                form: formNumber,
                timestamp: new Date().toLocaleString()
            };
            
            showResult(`Form ${formNumber}: Success! Uploaded ${uploadedImages.length} images`, 'success');
            console.log('Form submission result:', result);
            
            // Clear form
            textarea.value = '';
            uploadComponent.commentImageUpload.clearFiles();
            
        } catch (error) {
            showResult(`Form ${formNumber}: Error - ${error.message}`, 'danger');
            console.error('Form submission error:', error);
        }
    }
    
    function showResult(message, type) {
        resultsDiv.className = `alert alert-${type}`;
        resultsDiv.innerHTML = `<strong>${new Date().toLocaleTimeString()}:</strong> ${message}`;
    }
});

// Global test functions
window.testUpload = async function(formNumber) {
    const form = document.getElementById(`test-form-${formNumber}`);
    const uploadComponent = form.querySelector('.comment-image-upload');
    
    if (!uploadComponent || !uploadComponent.commentImageUpload) {
        alert('Upload component not found');
        return;
    }
    
    const hasFiles = uploadComponent.commentImageUpload.hasFiles();
    
    if (!hasFiles) {
        alert(`Form ${formNumber}: No files selected. Please select some images first.`);
        return;
    }
    
    try {
        const uploadedImages = await uploadComponent.commentImageUpload.uploadFiles();
        alert(`Form ${formNumber}: Successfully uploaded ${uploadedImages.length} images!`);
        console.log('Uploaded images:', uploadedImages);
    } catch (error) {
        alert(`Form ${formNumber}: Upload failed - ${error.message}`);
        console.error('Upload error:', error);
    }
};

window.clearAllUploads = function() {
    const uploadComponents = document.querySelectorAll('.comment-image-upload');
    uploadComponents.forEach(component => {
        if (component.commentImageUpload) {
            component.commentImageUpload.clearFiles();
        }
    });
    
    document.getElementById('test-results').className = 'alert alert-info';
    document.getElementById('test-results').innerHTML = '<strong>Cleared!</strong> All uploads have been cleared.';
};
</script>
@endsection
