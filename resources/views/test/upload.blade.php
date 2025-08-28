@extends('layouts.app')

@section('title', 'Test Unified Upload Service')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold mb-8">🧪 Test Unified Upload Service</h1>

        <!-- User Stats -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-xl font-semibold mb-4">📊 User Upload Statistics</h2>
            <div id="user-stats" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="text-center p-4 bg-blue-50 rounded">
                    <div class="text-2xl font-bold text-blue-600" id="total-files">-</div>
                    <div class="text-sm text-gray-600">Total Files</div>
                </div>
                <div class="text-center p-4 bg-green-50 rounded">
                    <div class="text-2xl font-bold text-green-600" id="total-size">-</div>
                    <div class="text-sm text-gray-600">Total Size</div>
                </div>
                <div class="text-center p-4 bg-purple-50 rounded">
                    <div class="text-2xl font-bold text-purple-600" id="categories">-</div>
                    <div class="text-sm text-gray-600">Categories</div>
                </div>
            </div>
        </div>

        <!-- Test Forms -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            
            <!-- Single File Upload Test -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-4">📁 Single File Upload Test</h2>
                <form id="single-upload-form" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">File</label>
                        <input type="file" name="file" class="w-full border border-gray-300 rounded-md p-2" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                        <select name="category" class="w-full border border-gray-300 rounded-md p-2">
                            <option value="test">Test</option>
                            <option value="comments">Comments</option>
                            <option value="gallery">Gallery</option>
                            <option value="threads">Threads</option>
                            <option value="showcases">Showcases</option>
                            <option value="avatars">Avatars</option>
                        </select>
                    </div>
                    <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700">
                        Upload Single File
                    </button>
                </form>
                <div id="single-upload-result" class="mt-4"></div>
            </div>

            <!-- Multiple Files Upload Test -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-4">📁📁 Multiple Files Upload Test</h2>
                <form id="multiple-upload-form" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Files</label>
                        <input type="file" name="files[]" multiple class="w-full border border-gray-300 rounded-md p-2" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                        <select name="category" class="w-full border border-gray-300 rounded-md p-2">
                            <option value="test">Test</option>
                            <option value="gallery">Gallery</option>
                            <option value="documents">Documents</option>
                        </select>
                    </div>
                    <button type="submit" class="w-full bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-700">
                        Upload Multiple Files
                    </button>
                </form>
                <div id="multiple-upload-result" class="mt-4"></div>
            </div>

            <!-- Validation Test -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-4">🔒 Validation Test</h2>
                <p class="text-sm text-gray-600 mb-4">Test with strict validation: Max 1MB, only JPG/PNG</p>
                <form id="validation-test-form" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">File</label>
                        <input type="file" name="file" class="w-full border border-gray-300 rounded-md p-2" required>
                    </div>
                    <button type="submit" class="w-full bg-yellow-600 text-white py-2 px-4 rounded-md hover:bg-yellow-700">
                        Test Validation
                    </button>
                </form>
                <div id="validation-test-result" class="mt-4"></div>
            </div>

            <!-- File List -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-4">📋 Your Files</h2>
                <div class="mb-4">
                    <select id="category-filter" class="border border-gray-300 rounded-md p-2">
                        <option value="">All Categories</option>
                        <option value="test">Test</option>
                        <option value="comments">Comments</option>
                        <option value="gallery">Gallery</option>
                        <option value="threads">Threads</option>
                        <option value="showcases">Showcases</option>
                        <option value="avatars">Avatars</option>
                    </select>
                    <button id="load-files" class="ml-2 bg-gray-600 text-white py-2 px-4 rounded-md hover:bg-gray-700">
                        Load Files
                    </button>
                </div>
                <div id="files-list" class="space-y-2 max-h-96 overflow-y-auto"></div>
            </div>
        </div>

        <!-- Actions -->
        <div class="mt-8 bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">🛠️ Actions</h2>
            <div class="flex space-x-4">
                <button id="refresh-stats" class="bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700">
                    Refresh Stats
                </button>
                <button id="cleanup-dirs" class="bg-red-600 text-white py-2 px-4 rounded-md hover:bg-red-700">
                    Cleanup Empty Directories
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Load initial stats
    loadUserStats();

    // Single file upload
    document.getElementById('single-upload-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        fetch('/test-upload/single', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            displayResult('single-upload-result', data);
            if (data.success) {
                loadUserStats();
                this.reset();
            }
        })
        .catch(error => {
            displayResult('single-upload-result', {success: false, message: 'Network error: ' + error.message});
        });
    });

    // Multiple files upload
    document.getElementById('multiple-upload-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        fetch('/test-upload/multiple', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            displayResult('multiple-upload-result', data);
            if (data.success) {
                loadUserStats();
                this.reset();
            }
        })
        .catch(error => {
            displayResult('multiple-upload-result', {success: false, message: 'Network error: ' + error.message});
        });
    });

    // Validation test
    document.getElementById('validation-test-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        fetch('/test-upload/validation', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            displayResult('validation-test-result', data);
            if (data.success) {
                loadUserStats();
                this.reset();
            }
        })
        .catch(error => {
            displayResult('validation-test-result', {success: false, message: 'Network error: ' + error.message});
        });
    });

    // Load files
    document.getElementById('load-files').addEventListener('click', loadFiles);
    
    // Refresh stats
    document.getElementById('refresh-stats').addEventListener('click', loadUserStats);
    
    // Cleanup directories
    document.getElementById('cleanup-dirs').addEventListener('click', function() {
        fetch('/test-upload/cleanup', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
        });
    });

    function displayResult(elementId, data) {
        const element = document.getElementById(elementId);
        const alertClass = data.success ? 'bg-green-100 border-green-400 text-green-700' : 'bg-red-100 border-red-400 text-red-700';
        
        let html = `<div class="border ${alertClass} px-4 py-3 rounded">
            <p><strong>${data.success ? 'Success' : 'Error'}:</strong> ${data.message}</p>`;
        
        if (data.data) {
            if (Array.isArray(data.data)) {
                html += '<ul class="mt-2 list-disc list-inside">';
                data.data.forEach(item => {
                    html += `<li>${item.name} (${item.size_human}) - <a href="${item.url}" target="_blank" class="text-blue-600 underline">View</a></li>`;
                });
                html += '</ul>';
            } else {
                html += `<p class="mt-2">File: ${data.data.name} (${data.data.size_human}) - <a href="${data.data.url}" target="_blank" class="text-blue-600 underline">View</a></p>`;
            }
        }
        
        html += '</div>';
        element.innerHTML = html;
    }

    function loadUserStats() {
        fetch('/test-upload/stats')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('total-files').textContent = data.data.total_files;
                document.getElementById('total-size').textContent = data.data.total_size_human;
                document.getElementById('categories').textContent = Object.keys(data.data.by_category).length;
            }
        });
    }

    function loadFiles() {
        const category = document.getElementById('category-filter').value;
        const url = '/test-upload/files' + (category ? '?category=' + category : '');
        
        fetch(url)
        .then(response => response.json())
        .then(data => {
            const filesList = document.getElementById('files-list');
            if (data.success && data.data.length > 0) {
                let html = '';
                data.data.forEach(file => {
                    html += `<div class="flex justify-between items-center p-2 border rounded">
                        <div>
                            <strong>${file.name}</strong> (${file.size_human})
                            <br><small class="text-gray-600">${file.category} - ${file.created_at}</small>
                        </div>
                        <div class="space-x-2">
                            <a href="${file.url}" target="_blank" class="text-blue-600 underline">View</a>
                            <button onclick="deleteFile(${file.id})" class="text-red-600 underline">Delete</button>
                        </div>
                    </div>`;
                });
                filesList.innerHTML = html;
            } else {
                filesList.innerHTML = '<p class="text-gray-600">No files found</p>';
            }
        });
    }

    window.deleteFile = function(mediaId) {
        if (confirm('Are you sure you want to delete this file?')) {
            fetch('/test-upload/delete', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({media_id: mediaId})
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                if (data.success) {
                    loadFiles();
                    loadUserStats();
                }
            });
        }
    };
});
</script>
@endsection
