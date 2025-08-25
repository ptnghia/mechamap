@extends('layouts.app')

@section('title', __('showcase.create.title'))

@push('styles')
<style>
    .showcase-create-form {
        max-width: 800px;
        margin: 0 auto;
    }

    .form-section {
        background: #f8f9fa;
        padding: 1.5rem;
        border-radius: 0.5rem;
        margin-bottom: 1.5rem;
    }

    .form-section h5 {
        color: #495057;
        margin-bottom: 1rem;
        font-weight: 600;
    }

    .file-upload-area {
        border: 2px dashed #dee2e6;
        border-radius: 0.5rem;
        padding: 3rem 2rem;
        text-align: center;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .file-upload-area:hover {
        border-color: #007bff;
        background-color: #f8f9ff;
    }

    .file-upload-area.dragover {
        border-color: #007bff;
        background-color: #e3f2fd;
    }

    .image-preview {
        max-width: 100%;
        max-height: 300px;
        border-radius: 0.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .form-help {
        background: #e3f2fd;
        padding: 1rem;
        border-radius: 0.5rem;
        border-left: 4px solid #2196f3;
    }

    /* Multi-select styling */
    .multi-select-container {
        position: relative;
    }

    .multi-select-container select[multiple] {
        min-height: 120px;
        background-image: none;
    }

    .multi-select-container select[multiple] option {
        padding: 8px 12px;
        border-radius: 4px;
        margin: 2px 0;
    }

    .multi-select-container select[multiple] option:checked {
        background: #007bff;
        color: white;
    }

    /* Form check improvements */
    .form-check {
        padding: 0.5rem;
        border-radius: 0.375rem;
        transition: background-color 0.15s ease-in-out;
    }

    .form-check:hover {
        background-color: #f8f9fa;
    }

    .form-check-input:checked {
        background-color: #007bff;
        border-color: #007bff;
    }

    /* Switch styling */
    .form-switch .form-check-input {
        width: 2em;
        margin-left: -2.5em;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='rgba%280,0,0,.25%29'/%3e%3c/svg%3e");
        background-position: left center;
        border-radius: 2em;
        transition: background-position .15s ease-in-out;
    }

    .form-switch .form-check-input:checked {
        background-position: right center;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='%23fff'/%3e%3c/svg%3e");
    }

    /* Materials input styling */
    #materials {
        position: relative;
    }

    #materials:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    /* Materials suggestions styling */
    #materials-suggestions option {
        padding: 8px 12px;
        font-size: 14px;
    }

    /* Materials tags styling */
    .materials-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-top: 0.5rem;
    }

    .material-tag {
        background: #e3f2fd;
        color: #1976d2;
        padding: 0.25rem 0.5rem;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }

    .material-tag .remove-tag {
        cursor: pointer;
        color: #d32f2f;
        font-weight: bold;
    }

    .material-tag .remove-tag:hover {
        color: #b71c1c;
    }

    /* Manufacturing process select styling */
    #manufacturing_process {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
        background-position: right 0.5rem center;
        background-repeat: no-repeat;
        background-size: 1.5em 1.5em;
        padding-right: 2.5rem;
    }

    #manufacturing_process optgroup {
        font-weight: bold;
        color: #374151;
        background-color: #f9fafb;
        padding: 0.5rem 0;
    }

    #manufacturing_process option {
        font-weight: normal;
        color: #1f2937;
        padding: 0.5rem 1rem;
    }

    #manufacturing_process option:hover {
        background-color: #e5e7eb;
    }

    /* Manufacturing process icon styling */
    .manufacturing-process-container {
        position: relative;
    }

    .manufacturing-process-container .form-label i {
        color: #059669;
    }

    /* Complexity Level Styling */
    .complexity-level-container .form-label i {
        color: #7c3aed;
    }

    .complexity-level-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1rem;
        margin-top: 0.75rem;
    }

    .complexity-option {
        position: relative;
        border: 2px solid #e5e7eb;
        border-radius: 0.75rem;
        padding: 1rem;
        transition: all 0.3s ease;
        cursor: pointer;
        background: #ffffff;
    }

    .complexity-option:hover {
        border-color: #7c3aed;
        box-shadow: 0 4px 12px rgba(124, 58, 237, 0.15);
        transform: translateY(-2px);
    }

    .complexity-option input[type="radio"] {
        position: absolute;
        top: 0.75rem;
        right: 0.75rem;
        margin: 0;
    }

    .complexity-option input[type="radio"]:checked + .complexity-label {
        color: #7c3aed;
    }

    .complexity-option:has(input:checked) {
        border-color: #7c3aed;
        background: linear-gradient(135deg, #f3f4f6 0%, #e0e7ff 100%);
        box-shadow: 0 4px 12px rgba(124, 58, 237, 0.2);
    }

    .complexity-label {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        cursor: pointer;
        margin: 0;
        padding-right: 2rem;
    }

    .complexity-icon {
        flex-shrink: 0;
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.125rem;
        transition: all 0.3s ease;
    }

    .complexity-content {
        flex: 1;
    }

    .complexity-title {
        font-weight: 600;
        font-size: 1rem;
        margin-bottom: 0.25rem;
        color: #1f2937;
    }

    .complexity-desc {
        font-size: 0.875rem;
        color: #6b7280;
        line-height: 1.4;
    }

    /* Level-specific colors */
    .complexity-option[data-level="beginner"] .complexity-icon {
        background: linear-gradient(135deg, #dcfce7, #bbf7d0);
        color: #16a34a;
    }

    .complexity-option[data-level="intermediate"] .complexity-icon {
        background: linear-gradient(135deg, #fef3c7, #fde68a);
        color: #d97706;
    }

    .complexity-option[data-level="advanced"] .complexity-icon {
        background: linear-gradient(135deg, #dbeafe, #bfdbfe);
        color: #2563eb;
    }

    .complexity-option[data-level="expert"] .complexity-icon {
        background: linear-gradient(135deg, #fce7f3, #fbcfe8);
        color: #be185d;
    }

    /* Checked state colors */
    .complexity-option[data-level="beginner"]:has(input:checked) {
        border-color: #16a34a;
        background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
    }

    .complexity-option[data-level="intermediate"]:has(input:checked) {
        border-color: #d97706;
        background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
    }

    .complexity-option[data-level="advanced"]:has(input:checked) {
        border-color: #2563eb;
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
    }

    .complexity-option[data-level="expert"]:has(input:checked) {
        border-color: #be185d;
        background: linear-gradient(135deg, #fdf2f8 0%, #fce7f3 100%);
    }

    /* Mobile responsive */
    @media (max-width: 768px) {
        .complexity-level-grid {
            grid-template-columns: 1fr;
            gap: 0.75rem;
        }

        .complexity-option {
            padding: 0.75rem;
        }

        .complexity-icon {
            width: 2rem;
            height: 2rem;
            font-size: 1rem;
        }
    }

    /* Boolean Fields Styling */
    .project-features-section h5 i {
        color: #f59e0b;
    }

    .sharing-settings-section h5 i {
        color: #10b981;
    }

    /* Feature Checkboxes */
    .feature-checkbox-container {
        height: 100%;
    }

    .feature-check {
        position: relative;
        border: 2px solid #e5e7eb;
        border-radius: 0.75rem;
        padding: 1rem;
        transition: all 0.3s ease;
        cursor: pointer;
        background: #ffffff;
        height: 100%;
        margin: 0;
    }

    .feature-check:hover {
        border-color: #f59e0b;
        box-shadow: 0 4px 12px rgba(245, 158, 11, 0.15);
        transform: translateY(-2px);
    }

    .feature-check input[type="checkbox"] {
        position: absolute;
        top: 0.75rem;
        right: 0.75rem;
        margin: 0;
        transform: scale(1.2);
    }

    .feature-check:has(input:checked) {
        border-color: #f59e0b;
        background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
        box-shadow: 0 4px 12px rgba(245, 158, 11, 0.2);
    }

    .feature-label {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        cursor: pointer;
        margin: 0;
        padding-right: 2rem;
        width: 100%;
    }

    .feature-icon {
        flex-shrink: 0;
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.125rem;
        transition: all 0.3s ease;
    }

    .feature-content {
        flex: 1;
    }

    .feature-title {
        font-weight: 600;
        font-size: 1rem;
        margin-bottom: 0.25rem;
        color: #1f2937;
    }

    .feature-desc {
        font-size: 0.875rem;
        color: #6b7280;
        line-height: 1.4;
    }

    /* Feature-specific colors */
    .tutorial-icon {
        background: linear-gradient(135deg, #dbeafe, #bfdbfe);
        color: #2563eb;
    }

    .calculations-icon {
        background: linear-gradient(135deg, #fce7f3, #fbcfe8);
        color: #be185d;
    }

    .cad-icon {
        background: linear-gradient(135deg, #dcfce7, #bbf7d0);
        color: #16a34a;
    }

    /* Setting Checkboxes */
    .setting-checkbox-container {
        height: 100%;
    }

    .setting-check {
        position: relative;
        border: 2px solid #e5e7eb;
        border-radius: 0.75rem;
        padding: 1rem;
        transition: all 0.3s ease;
        cursor: pointer;
        background: #ffffff;
        height: 100%;
        margin: 0;
    }

    .setting-check:hover {
        border-color: #10b981;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.15);
        transform: translateY(-2px);
    }

    .setting-check input[type="checkbox"] {
        position: absolute;
        top: 0.75rem;
        right: 0.75rem;
        margin: 0;
        transform: scale(1.2);
    }

    .setting-check:has(input:checked) {
        border-color: #10b981;
        background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
    }

    .setting-label {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        cursor: pointer;
        margin: 0;
        padding-right: 2rem;
        width: 100%;
    }

    .setting-icon {
        flex-shrink: 0;
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.125rem;
        transition: all 0.3s ease;
    }

    .setting-content {
        flex: 1;
    }

    .setting-title {
        font-weight: 600;
        font-size: 1rem;
        margin-bottom: 0.25rem;
        color: #1f2937;
    }

    .setting-desc {
        font-size: 0.875rem;
        color: #6b7280;
        line-height: 1.4;
    }

    /* Setting-specific colors */
    .public-icon {
        background: linear-gradient(135deg, #dbeafe, #bfdbfe);
        color: #2563eb;
    }

    .download-icon {
        background: linear-gradient(135deg, #fef3c7, #fde68a);
        color: #d97706;
    }

    .comment-icon {
        background: linear-gradient(135deg, #dcfce7, #bbf7d0);
        color: #16a34a;
    }

    /* Mobile responsive for boolean fields */
    @media (max-width: 768px) {
        .feature-check, .setting-check {
            padding: 0.75rem;
        }

        .feature-icon, .setting-icon {
            width: 2rem;
            height: 2rem;
            font-size: 1rem;
        }

        .feature-title, .setting-title {
            font-size: 0.9rem;
        }

        .feature-desc, .setting-desc {
            font-size: 0.8rem;
        }
    }

    /* Multiple Images Upload Styling */
    .multiple-images-upload-area {
        border: 2px dashed #e5e7eb;
        border-radius: 0.75rem;
        background: #f9fafb;
        transition: all 0.3s ease;
    }

    .upload-zone {
        padding: 2rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .upload-zone:hover {
        border-color: #3b82f6;
        background: #eff6ff;
    }

    .upload-zone.dragover {
        border-color: #2563eb;
        background: #dbeafe;
        transform: scale(1.02);
    }

    .multiple-images-upload-area.has-files {
        border-color: #10b981;
        background: #ecfdf5;
    }

    /* Images Gallery Styling */
    .images-gallery {
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        background: #ffffff;
        overflow: hidden;
    }

    .gallery-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem;
        background: #f8fafc;
        border-bottom: 1px solid #e5e7eb;
    }

    .gallery-header h6 {
        margin: 0;
        color: #374151;
    }

    .images-list {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 1rem;
        padding: 1rem;
        max-height: 400px;
        overflow-y: auto;
    }

    .image-item {
        position: relative;
        border: 2px solid #e5e7eb;
        border-radius: 0.5rem;
        overflow: hidden;
        background: #ffffff;
        transition: all 0.3s ease;
        cursor: move;
    }

    .image-item:hover {
        border-color: #3b82f6;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
        transform: translateY(-2px);
    }

    .image-item.dragging {
        opacity: 0.5;
        transform: rotate(5deg);
    }

    .image-preview-thumb {
        width: 100%;
        height: 120px;
        object-fit: cover;
        display: block;
    }

    .image-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.7);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .image-item:hover .image-overlay {
        opacity: 1;
    }

    .image-actions {
        display: flex;
        gap: 0.5rem;
    }

    .image-action-btn {
        width: 2rem;
        height: 2rem;
        border: none;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.875rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .image-action-btn.view {
        background: #3b82f6;
        color: white;
    }

    .image-action-btn.view:hover {
        background: #2563eb;
        transform: scale(1.1);
    }

    .image-action-btn.delete {
        background: #ef4444;
        color: white;
    }

    .image-action-btn.delete:hover {
        background: #dc2626;
        transform: scale(1.1);
    }

    .image-info {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(transparent, rgba(0, 0, 0, 0.8));
        color: white;
        padding: 0.5rem;
        font-size: 0.75rem;
    }

    .image-name {
        font-weight: 500;
        margin-bottom: 0.25rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .image-size {
        opacity: 0.8;
    }

    .gallery-footer {
        padding: 0.75rem 1rem;
        background: #f8fafc;
        border-top: 1px solid #e5e7eb;
        text-align: center;
    }

    /* Drag and Drop States */
    .upload-zone.drag-enter {
        border-color: #10b981;
        background: #ecfdf5;
    }

    .upload-zone.drag-over {
        border-color: #059669;
        background: #d1fae5;
        transform: scale(1.02);
    }

    /* Loading State */
    .image-item.loading {
        opacity: 0.8;
        pointer-events: none;
    }

    .loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.9);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10;
    }

    .loading-overlay i {
        font-size: 1.5rem;
        color: #3b82f6;
    }

    .processing-text {
        font-size: 0.7rem;
        color: #3b82f6;
        font-weight: 500;
        margin-top: 0.25rem;
        text-align: center;
    }

    /* Compression Info Styling */
    .compressed-size {
        color: #10b981;
        font-weight: 600;
    }

    .original-size {
        color: #6b7280;
        font-size: 0.65rem;
        display: block;
        margin-top: 0.125rem;
    }

    .compression-badge {
        display: inline-block;
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        font-size: 0.6rem;
        padding: 0.125rem 0.25rem;
        border-radius: 0.25rem;
        margin-top: 0.25rem;
        font-weight: 500;
    }

    .compression-badge i {
        margin-right: 0.125rem;
        font-size: 0.5rem;
    }

    /* Disabled button state */
    .image-action-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        pointer-events: none;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    /* Mobile responsive for multiple images */
    @media (max-width: 768px) {
        .images-list {
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 0.75rem;
            padding: 0.75rem;
        }

        .image-preview-thumb {
            height: 100px;
        }

        .upload-zone {
            padding: 1.5rem 1rem;
        }

        .gallery-header {
            padding: 0.75rem;
        }

        .image-action-btn {
            width: 1.75rem;
            height: 1.75rem;
            font-size: 0.75rem;
        }
    }

    /* File Attachments Upload Area Styling */
    .file-attachments-upload-area {
        border: 2px dashed #dee2e6;
        border-radius: 0.5rem;
        padding: 2rem;
        text-align: center;
        transition: all 0.3s ease;
        cursor: pointer;
        background: #fafbfc;
    }

    .file-attachments-upload-area:hover {
        border-color: #007bff;
        background-color: #f8f9ff;
    }

    .file-attachments-upload-area.dragover {
        border-color: #007bff;
        background-color: #e3f2fd;
        transform: scale(1.01);
    }

    .file-attachments-upload-area.has-files {
        border-color: #28a745;
        background: #f0f9f0;
    }

    /* Files Grid Styling */
    .files-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
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

    .file-item:hover {
        border-color: #007bff;
        box-shadow: 0 2px 8px rgba(0,123,255,0.1);
    }

    .file-icon {
        width: 40px;
        height: 40px;
        border-radius: 0.375rem;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 0.75rem;
        font-size: 1.25rem;
        color: #fff;
    }

    .file-icon.pdf { background: #dc3545; }
    .file-icon.doc { background: #0d6efd; }
    .file-icon.xls { background: #198754; }
    .file-icon.cad { background: #fd7e14; }
    .file-icon.img { background: #6f42c1; }
    .file-icon.zip { background: #6c757d; }
    .file-icon.default { background: #adb5bd; }

    .file-info {
        flex: 1;
        min-width: 0;
    }

    .file-name {
        font-weight: 500;
        margin-bottom: 0.25rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .file-size {
        font-size: 0.875rem;
        color: #6c757d;
    }

    .file-remove {
        background: none;
        border: none;
        color: #dc3545;
        font-size: 1.25rem;
        cursor: pointer;
        padding: 0.25rem;
        border-radius: 0.25rem;
        transition: all 0.2s ease;
    }

    .file-remove:hover {
        background: #f8d7da;
        color: #721c24;
    }

    /* Enhanced Validation Styling */
    .form-control.is-invalid,
    .form-select.is-invalid {
        border-color: #dc3545;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
    }

    .invalid-feedback {
        display: block;
        width: 100%;
        margin-top: 0.25rem;
        font-size: 0.875rem;
        color: #dc3545;
        background: #f8d7da;
        border: 1px solid #f5c6cb;
        border-radius: 0.375rem;
        padding: 0.5rem 0.75rem;
    }

    .invalid-feedback i {
        margin-right: 0.25rem;
    }

    .form-control.is-valid,
    .form-select.is-valid {
        border-color: #198754;
        box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25);
    }

    .valid-feedback {
        display: block;
        width: 100%;
        margin-top: 0.25rem;
        font-size: 0.875rem;
        color: #198754;
        background: #d1e7dd;
        border: 1px solid #badbcc;
        border-radius: 0.375rem;
        padding: 0.5rem 0.75rem;
    }

    /* Character counter styling */
    .char-counter {
        font-size: 0.75rem;
        color: #6c757d;
        text-align: right;
        margin-top: 0.25rem;
    }

    .char-counter.warning {
        color: #fd7e14;
    }

    .char-counter.danger {
        color: #dc3545;
    }

    /* Field validation indicators */
    .field-validation-indicator {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 1rem;
    }

    .field-validation-indicator.valid {
        color: #198754;
    }

    .field-validation-indicator.invalid {
        color: #dc3545;
    }

    /* Wizard Progress Indicator Styling */
    .wizard-progress {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        padding: 1.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .progress-steps {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        position: relative;
    }

    .progress-steps::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 0;
        right: 0;
        height: 2px;
        background: #e5e7eb;
        z-index: 1;
        transform: translateY(-50%);
    }

    .step {
        display: flex;
        flex-direction: column;
        align-items: center;
        position: relative;
        z-index: 2;
        background: #fff;
        padding: 0 0.5rem;
        transition: all 0.3s ease;
    }

    .step-number {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #e5e7eb;
        color: #6b7280;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 1rem;
        margin-bottom: 0.5rem;
        transition: all 0.3s ease;
    }

    .step-label {
        font-size: 0.875rem;
        color: #6b7280;
        text-align: center;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .step.active .step-number {
        background: #3b82f6;
        color: #fff;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.2);
    }

    .step.active .step-label {
        color: #3b82f6;
        font-weight: 600;
    }

    .step.completed .step-number {
        background: #10b981;
        color: #fff;
    }

    .step.completed .step-label {
        color: #10b981;
        font-weight: 600;
    }

    .step.completed .step-number::before {
        content: '✓';
        font-size: 0.875rem;
    }

    .progress-bar {
        height: 6px;
        background: #e5e7eb;
        border-radius: 3px;
        overflow: hidden;
    }

    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #3b82f6 0%, #1d4ed8 100%);
        border-radius: 3px;
        transition: width 0.5s ease;
    }

    /* Wizard Step Content */
    .wizard-step {
        display: none;
    }

    .wizard-step.active {
        display: block;
        animation: fadeInUp 0.3s ease;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Wizard Navigation */
    .wizard-navigation {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 2rem;
        padding-top: 1.5rem;
        border-top: 1px solid #e5e7eb;
    }

    .wizard-nav-btn {
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
        font-weight: 500;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
    }

    .wizard-nav-btn.btn-prev {
        background: #f3f4f6;
        color: #374151;
    }

    .wizard-nav-btn.btn-prev:hover {
        background: #e5e7eb;
    }

    .wizard-nav-btn.btn-next {
        background: #3b82f6;
        color: #fff;
    }

    .wizard-nav-btn.btn-next:hover {
        background: #2563eb;
    }

    .wizard-nav-btn.btn-submit {
        background: #10b981;
        color: #fff;
    }

    .wizard-nav-btn.btn-submit:hover {
        background: #059669;
    }

    .wizard-nav-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .progress-steps {
            flex-wrap: wrap;
            gap: 1rem;
        }

        .step {
            flex: 1;
            min-width: calc(50% - 0.5rem);
        }

        .step-label {
            font-size: 0.75rem;
        }

        .step-number {
            width: 32px;
            height: 32px;
            font-size: 0.875rem;
        }

        .wizard-navigation {
            flex-direction: column;
            gap: 1rem;
        }

        .wizard-nav-btn {
            width: 100%;
        }
    }

    /* Preview Modal Styling */
    .preview-container {
        background: #f8f9fa;
        min-height: 500px;
    }

    .preview-showcase {
        background: #fff;
        margin: 1rem;
        border-radius: 0.75rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .preview-header {
        padding: 2rem;
        border-bottom: 1px solid #e5e7eb;
    }

    .preview-title {
        font-size: 2rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 1rem;
    }

    .preview-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .preview-meta-item {
        display: flex;
        align-items: center;
        color: #6b7280;
        font-size: 0.875rem;
    }

    .preview-meta-item i {
        margin-right: 0.5rem;
        color: #3b82f6;
    }

    .preview-cover-image {
        width: 100%;
        height: 300px;
        object-fit: cover;
        border-radius: 0.5rem;
        margin-bottom: 1.5rem;
    }

    .preview-content {
        padding: 2rem;
    }

    .preview-section {
        margin-bottom: 2rem;
    }

    .preview-section-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
    }

    .preview-section-title i {
        margin-right: 0.5rem;
        color: #3b82f6;
    }

    .preview-description {
        color: #374151;
        line-height: 1.6;
        margin-bottom: 1.5rem;
    }

    .preview-technical-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .preview-technical-item {
        background: #f9fafb;
        padding: 1rem;
        border-radius: 0.5rem;
        border-left: 4px solid #3b82f6;
    }

    .preview-technical-label {
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.5rem;
    }

    .preview-technical-value {
        color: #6b7280;
    }

    .preview-features {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-bottom: 1.5rem;
    }

    .preview-feature-badge {
        background: #dbeafe;
        color: #1e40af;
        padding: 0.5rem 1rem;
        border-radius: 1rem;
        font-size: 0.875rem;
        font-weight: 500;
        display: flex;
        align-items: center;
    }

    .preview-feature-badge i {
        margin-right: 0.5rem;
    }

    .preview-gallery {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .preview-gallery-item {
        position: relative;
        border-radius: 0.5rem;
        overflow: hidden;
        aspect-ratio: 4/3;
    }

    .preview-gallery-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .preview-gallery-item:hover .preview-gallery-image {
        transform: scale(1.05);
    }

    .preview-attachments {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1rem;
    }

    .preview-attachment-item {
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        padding: 1rem;
        display: flex;
        align-items: center;
    }

    .preview-attachment-icon {
        width: 40px;
        height: 40px;
        background: #3b82f6;
        color: #fff;
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
    }

    .preview-attachment-info {
        flex: 1;
    }

    .preview-attachment-name {
        font-weight: 500;
        color: #374151;
        margin-bottom: 0.25rem;
    }

    .preview-attachment-size {
        font-size: 0.875rem;
        color: #6b7280;
    }

    .preview-sharing-settings {
        background: #f0f9ff;
        border: 1px solid #bae6fd;
        border-radius: 0.5rem;
        padding: 1rem;
    }

    .preview-sharing-item {
        display: flex;
        align-items: center;
        margin-bottom: 0.5rem;
    }

    .preview-sharing-item:last-child {
        margin-bottom: 0;
    }

    .preview-sharing-icon {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        margin-right: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
    }

    .preview-sharing-icon.enabled {
        background: #10b981;
        color: #fff;
    }

    .preview-sharing-icon.disabled {
        background: #ef4444;
        color: #fff;
    }

    .preview-empty-state {
        text-align: center;
        padding: 3rem;
        color: #6b7280;
    }

    .preview-empty-state i {
        font-size: 3rem;
        margin-bottom: 1rem;
        color: #d1d5db;
    }

    /* Responsive Preview */
    @media (max-width: 768px) {
        .preview-showcase {
            margin: 0.5rem;
        }

        .preview-header,
        .preview-content {
            padding: 1rem;
        }

        .preview-title {
            font-size: 1.5rem;
        }

        .preview-technical-grid {
            grid-template-columns: 1fr;
        }

        .preview-gallery {
            grid-template-columns: repeat(2, 1fr);
        }

        .preview-attachments {
            grid-template-columns: 1fr;
        }
    }

    /* ========================================
       MOBILE RESPONSIVE OPTIMIZATIONS
       ======================================== */

    /* Mobile-First Approach - Base Mobile Styles */
    @media (max-width: 576px) {
        /* Container and Layout */
        .container {
            padding-left: 0.75rem;
            padding-right: 0.75rem;
        }

        .card {
            border-radius: 0;
            border-left: none;
            border-right: none;
            margin-left: -0.75rem;
            margin-right: -0.75rem;
        }

        .card-body {
            padding: 1rem 0.75rem;
        }

        /* Wizard Progress - Mobile Optimized */
        .wizard-progress {
            margin: 0 -0.75rem 1rem;
            border-radius: 0;
            border-left: none;
            border-right: none;
            padding: 1rem 0.75rem;
        }

        .progress-steps {
            flex-direction: column;
            gap: 0.5rem;
            align-items: stretch;
        }

        .progress-steps::before {
            display: none; /* Hide connecting line on mobile */
        }

        .step {
            flex-direction: row;
            justify-content: flex-start;
            align-items: center;
            background: #f8f9fa;
            border-radius: 8px;
            padding: 0.75rem;
            margin: 0;
            border: 2px solid transparent;
            transition: all 0.3s ease;
        }

        .step.active {
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            border-color: #2196f3;
            transform: scale(1.02);
        }

        .step.completed {
            background: linear-gradient(135deg, #e8f5e8 0%, #c8e6c9 100%);
            border-color: #4caf50;
        }

        .step-number {
            margin-right: 0.75rem;
            margin-bottom: 0;
            width: 32px;
            height: 32px;
            line-height: 28px;
            font-size: 0.875rem;
            flex-shrink: 0;
        }

        .step-label {
            font-size: 0.875rem;
            font-weight: 500;
            text-align: left;
            flex-grow: 1;
        }

        /* Progress Bar - Mobile */
        .progress-bar {
            height: 4px;
            margin-top: 1rem;
        }

        /* Wizard Navigation - Mobile */
        .wizard-navigation {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            border-top: 1px solid #e5e7eb;
            padding: 1rem 0.75rem;
            z-index: 1000;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
        }

        .wizard-nav-btn {
            padding: 0.875rem 1.25rem;
            font-size: 0.875rem;
            border-radius: 8px;
            min-height: 44px; /* Touch-friendly minimum */
            font-weight: 600;
        }

        .wizard-step-info {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
        }

        .wizard-step-info .current-step {
            font-size: 0.75rem;
            color: #6b7280;
        }

        #previewBtn {
            padding: 0.5rem 0.75rem;
            font-size: 0.75rem;
            min-height: auto;
        }

        /* Form Sections - Mobile */
        .form-section {
            margin-bottom: 1.5rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid #e5e7eb;
        }

        .form-section:last-child {
            border-bottom: none;
            margin-bottom: 5rem; /* Space for fixed navigation */
        }

        .form-section h5 {
            font-size: 1.125rem;
            margin-bottom: 1rem;
            padding: 0.75rem;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 8px;
            border-left: 4px solid #007bff;
            margin-left: -0.75rem;
            margin-right: -0.75rem;
        }

        /* Form Controls - Touch Optimized */
        .form-control, .form-select {
            min-height: 44px;
            font-size: 16px; /* Prevent zoom on iOS */
            border-radius: 8px;
            padding: 0.75rem;
        }

        .form-control:focus, .form-select:focus {
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.25);
        }

        .form-label {
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        /* Checkbox and Radio - Touch Friendly */
        .form-check-input {
            width: 1.25rem;
            height: 1.25rem;
            margin-top: 0.125rem;
        }

        .form-check-label {
            font-size: 0.875rem;
            padding-left: 0.5rem;
        }

        /* Buttons - Touch Optimized */
        .btn {
            min-height: 44px;
            padding: 0.75rem 1.25rem;
            font-size: 0.875rem;
            border-radius: 8px;
            font-weight: 500;
        }

        .btn-sm {
            min-height: 36px;
            padding: 0.5rem 0.875rem;
            font-size: 0.75rem;
        }
    }

    /* Tablet Optimizations */
    @media (min-width: 577px) and (max-width: 991px) {
        .wizard-navigation {
            padding: 1.25rem 1rem;
        }

        .wizard-nav-btn {
            padding: 0.875rem 1.5rem;
            font-size: 0.9rem;
        }

        .progress-steps {
            gap: 1.5rem;
        }

        .step-label {
            font-size: 0.875rem;
        }

        .form-control, .form-select {
            min-height: 42px;
            font-size: 15px;
        }
    }

    /* Advanced Technical Fields Styling */
    .technical-spec-row, .learning-objective-row {
        transition: all 0.3s ease;
    }

    .technical-spec-row:hover, .learning-objective-row:hover {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 8px;
        margin: -8px;
    }

    .spec-suggestion, .objective-suggestion {
        transition: all 0.2s ease;
        border: 1px solid #dee2e6;
    }

    .spec-suggestion:hover, .objective-suggestion:hover {
        background-color: #007bff;
        color: white;
        border-color: #007bff;
        transform: translateY(-1px);
    }

    .remove-spec-btn, .remove-objective-btn {
        transition: all 0.2s ease;
    }

    .remove-spec-btn:hover, .remove-objective-btn:hover {
        background-color: #dc3545;
        border-color: #dc3545;
        color: white;
    }

    /* Preview Learning Objectives Styling */
    .preview-learning-objectives {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 12px;
        padding: 20px;
        border-left: 4px solid #28a745;
    }

    .preview-subsection-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #495057;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
    }

    .preview-subsection-title i {
        margin-right: 8px;
        color: #28a745;
    }

    .preview-objectives-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .preview-objective-item {
        display: flex;
        align-items: flex-start;
        padding: 8px 0;
        font-size: 0.95rem;
        line-height: 1.5;
        color: #495057;
    }

    .preview-objective-item:not(:last-child) {
        border-bottom: 1px solid #e9ecef;
    }

    .preview-objective-item i {
        margin-top: 2px;
        flex-shrink: 0;
    }

    /* Enhanced Industry Application Styling */
    #industry_application_enhanced {
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        border: 2px solid #dee2e6;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    #industry_application_enhanced:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        background: white;
    }

    /* Technical Specs Grid Enhancement */
    .preview-technical-grid .preview-technical-item {
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 12px;
        margin-bottom: 8px;
        transition: all 0.2s ease;
    }

    .preview-technical-grid .preview-technical-item:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    /* ========================================
       MOBILE IMAGE & FILE UPLOAD OPTIMIZATION
       ======================================== */

    @media (max-width: 576px) {
        /* Cover Image Upload - Mobile */
        .cover-image-upload-area {
            margin: 0 -0.75rem;
            border-radius: 0;
            border-left: none;
            border-right: none;
        }

        .upload-zone {
            padding: 2rem 1rem !important;
            min-height: 120px;
        }

        .upload-zone i {
            font-size: 2rem !important;
        }

        .upload-zone h6 {
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
        }

        .upload-zone p {
            font-size: 0.75rem;
            margin-bottom: 0.25rem;
        }

        /* Multiple Images - Mobile Grid */
        .multiple-images-upload-area {
            margin: 0 -0.75rem;
            border-radius: 0;
            border-left: none;
            border-right: none;
        }

        .images-list {
            grid-template-columns: repeat(2, 1fr);
            gap: 0.5rem;
            padding: 0.75rem;
        }

        .image-item {
            border-radius: 8px;
            overflow: hidden;
        }

        .image-item img {
            aspect-ratio: 1;
            object-fit: cover;
        }

        .image-actions {
            padding: 0.5rem;
            gap: 0.25rem;
        }

        .image-actions .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            min-height: auto;
        }

        /* File Attachments - Mobile */
        .file-attachments-upload-area {
            margin: 0 -0.75rem;
            border-radius: 0;
            border-left: none;
            border-right: none;
        }

        .attachments-list {
            padding: 0.75rem;
        }

        .attachment-item {
            padding: 0.75rem;
            border-radius: 8px;
            margin-bottom: 0.5rem;
        }

        .attachment-info {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
        }

        .attachment-name {
            font-size: 0.875rem;
            word-break: break-all;
        }

        .attachment-size {
            font-size: 0.75rem;
        }

        .attachment-actions {
            margin-top: 0.5rem;
            width: 100%;
            justify-content: space-between;
        }

        .attachment-actions .btn {
            padding: 0.375rem 0.75rem;
            font-size: 0.75rem;
            min-height: auto;
        }

        /* Technical Specs - Mobile */
        .technical-spec-row .row {
            flex-direction: column;
            gap: 0.5rem;
        }

        .technical-spec-row .col-md-4,
        .technical-spec-row .col-md-3,
        .technical-spec-row .col-md-1 {
            width: 100%;
            max-width: 100%;
            flex: none;
        }

        .technical-spec-row .col-md-1 {
            display: flex;
            justify-content: center;
            margin-top: 0.5rem;
        }

        .remove-spec-btn, .remove-objective-btn {
            min-height: 36px;
            padding: 0.5rem 0.75rem;
        }

        /* Learning Objectives - Mobile */
        .learning-objective-row .input-group {
            flex-wrap: wrap;
        }

        .learning-objective-row .input-group-text {
            border-bottom-right-radius: 0;
            border-top-right-radius: 0.375rem;
        }

        .learning-objective-row .form-control {
            border-top-left-radius: 0;
            border-bottom-left-radius: 0.375rem;
            border-bottom-right-radius: 0;
            border-top-right-radius: 0;
        }

        .learning-objective-row .remove-objective-btn {
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
            border-top-right-radius: 0.375rem;
            border-bottom-right-radius: 0.375rem;
            width: 100%;
            margin-top: 0.5rem;
        }

        /* Suggestion Buttons - Mobile */
        .spec-suggestion, .objective-suggestion {
            font-size: 0.75rem;
            padding: 0.5rem 0.75rem;
            margin-bottom: 0.5rem;
            width: 100%;
            text-align: left;
        }

        /* Complexity Level - Mobile */
        .complexity-level-grid {
            grid-template-columns: 1fr;
            gap: 0.5rem;
        }

        .complexity-option {
            padding: 0.875rem;
        }

        /* Software Used - Mobile */
        .software-grid {
            grid-template-columns: 1fr;
            gap: 0.5rem;
        }

        .software-item {
            padding: 0.75rem;
        }

        /* Boolean Fields - Mobile */
        .feature-check, .setting-check {
            padding: 0.875rem;
            margin-bottom: 0.5rem;
        }

        .feature-icon, .setting-icon {
            width: 2rem;
            height: 2rem;
            font-size: 0.875rem;
        }

        .feature-label, .setting-label {
            font-size: 0.875rem;
        }

        .feature-description, .setting-description {
            font-size: 0.75rem;
            margin-top: 0.25rem;
        }
    }

    /* Tablet Specific Optimizations */
    @media (min-width: 577px) and (max-width: 991px) {
        .images-list {
            grid-template-columns: repeat(3, 1fr);
            gap: 0.75rem;
        }

        .technical-spec-row .row {
            gap: 0.5rem;
        }

        .complexity-level-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .software-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    /* ========================================
       MOBILE PREVIEW MODAL OPTIMIZATION
       ======================================== */

    @media (max-width: 576px) {
        /* Preview Modal - Full Screen on Mobile */
        .modal-dialog {
            margin: 0;
            max-width: 100%;
            height: 100vh;
        }

        .modal-content {
            height: 100vh;
            border-radius: 0;
            border: none;
        }

        .modal-header {
            padding: 1rem 0.75rem;
            border-bottom: 1px solid #e5e7eb;
            position: sticky;
            top: 0;
            background: white;
            z-index: 1;
        }

        .modal-title {
            font-size: 1rem;
            font-weight: 600;
        }

        .btn-close {
            padding: 0.5rem;
            margin: -0.5rem -0.25rem -0.5rem auto;
        }

        .modal-body {
            padding: 0;
            overflow-y: auto;
            flex: 1;
        }

        .modal-footer {
            padding: 1rem 0.75rem;
            border-top: 1px solid #e5e7eb;
            position: sticky;
            bottom: 0;
            background: white;
            z-index: 1;
        }

        .modal-footer .btn {
            flex: 1;
            margin: 0 0.25rem;
        }

        /* Preview Content - Mobile */
        .preview-showcase {
            margin: 0;
            border-radius: 0;
            border: none;
            box-shadow: none;
        }

        .preview-header {
            padding: 1rem 0.75rem;
            border-bottom: 1px solid #e5e7eb;
        }

        .preview-title {
            font-size: 1.25rem;
            line-height: 1.4;
            margin-bottom: 0.5rem;
        }

        .preview-meta {
            font-size: 0.75rem;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .preview-content {
            padding: 1rem 0.75rem;
        }

        .preview-section {
            margin-bottom: 1.5rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid #f3f4f6;
        }

        .preview-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }

        .preview-section-title {
            font-size: 1rem;
            margin-bottom: 0.75rem;
            padding: 0.5rem 0.75rem;
            background: #f8f9fa;
            border-radius: 6px;
            margin-left: -0.75rem;
            margin-right: -0.75rem;
        }

        .preview-description {
            font-size: 0.875rem;
            line-height: 1.6;
        }

        /* Preview Technical Grid - Mobile */
        .preview-technical-grid {
            grid-template-columns: 1fr;
            gap: 0.5rem;
        }

        .preview-technical-item {
            padding: 0.75rem;
            border-radius: 6px;
        }

        .preview-technical-label {
            font-size: 0.75rem;
            margin-bottom: 0.25rem;
        }

        .preview-technical-value {
            font-size: 0.875rem;
        }

        /* Preview Gallery - Mobile */
        .preview-gallery {
            grid-template-columns: repeat(2, 1fr);
            gap: 0.5rem;
        }

        .preview-gallery img {
            border-radius: 6px;
            aspect-ratio: 1;
            object-fit: cover;
        }

        /* Preview Attachments - Mobile */
        .preview-attachments {
            grid-template-columns: 1fr;
            gap: 0.5rem;
        }

        .preview-attachment-item {
            padding: 0.75rem;
            border-radius: 6px;
            font-size: 0.875rem;
        }

        /* Preview Features - Mobile */
        .preview-features {
            gap: 0.5rem;
        }

        .preview-feature-badge {
            padding: 0.375rem 0.75rem;
            font-size: 0.75rem;
            border-radius: 6px;
        }

        /* Preview Learning Objectives - Mobile */
        .preview-learning-objectives {
            padding: 0.75rem;
            border-radius: 8px;
            margin-left: -0.75rem;
            margin-right: -0.75rem;
        }

        .preview-subsection-title {
            font-size: 0.875rem;
            margin-bottom: 0.75rem;
        }

        .preview-objective-item {
            font-size: 0.875rem;
            padding: 0.5rem 0;
        }

        /* Preview Sharing Settings - Mobile */
        .preview-sharing-grid {
            grid-template-columns: 1fr;
            gap: 0.5rem;
        }

        .preview-setting-item {
            padding: 0.75rem;
            border-radius: 6px;
        }

        .preview-setting-label {
            font-size: 0.75rem;
        }

        .preview-setting-value {
            font-size: 0.875rem;
        }
    }

    /* Tablet Preview Optimizations */
    @media (min-width: 577px) and (max-width: 991px) {
        .modal-dialog {
            margin: 1rem;
            max-width: calc(100% - 2rem);
        }

        .preview-gallery {
            grid-template-columns: repeat(3, 1fr);
        }

        .preview-technical-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .preview-sharing-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    /* ========================================
       TOUCH INTERACTION ENHANCEMENTS
       ======================================== */

    /* Touch feedback for interactive elements */
    .touch-optimized {
        transition: all 0.2s ease;
        -webkit-tap-highlight-color: transparent;
    }

    .touch-optimized.touch-active {
        transform: scale(0.98);
        opacity: 0.8;
    }

    /* Touch-friendly upload zones */
    .touch-upload-zone {
        transition: all 0.3s ease;
    }

    .touch-upload-zone:active {
        transform: scale(0.99);
        background-color: #f8f9fa;
    }

    /* Mobile device specific styles */
    .mobile-device .btn {
        min-height: 44px;
        padding: 0.75rem 1.25rem;
    }

    .mobile-device .form-control,
    .mobile-device .form-select {
        min-height: 44px;
        font-size: 16px; /* Prevent zoom on iOS */
    }

    .mobile-device .form-check-input {
        width: 1.25rem;
        height: 1.25rem;
    }

    /* Tablet device specific styles */
    .tablet-device .btn {
        min-height: 42px;
        padding: 0.75rem 1.125rem;
    }

    .tablet-device .form-control,
    .tablet-device .form-select {
        min-height: 42px;
        font-size: 15px;
    }

    /* Improved touch targets */
    @media (max-width: 576px) {
        .step {
            min-height: 60px;
            cursor: pointer;
        }

        .spec-suggestion,
        .objective-suggestion {
            min-height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .remove-spec-btn,
        .remove-objective-btn {
            min-width: 44px;
            min-height: 44px;
        }

        /* Improved file upload touch targets */
        .attachment-actions .btn {
            min-height: 36px;
            min-width: 60px;
        }

        .image-actions .btn {
            min-height: 32px;
            min-width: 50px;
        }

        /* Better spacing for touch */
        .form-group {
            margin-bottom: 1.5rem;
        }

        .mb-3 {
            margin-bottom: 1.25rem !important;
        }

        .mb-4 {
            margin-bottom: 1.75rem !important;
        }
    }

    /* Accessibility improvements for touch devices */
    @media (hover: none) and (pointer: coarse) {
        /* Remove hover effects on touch devices */
        .btn:hover,
        .form-control:hover,
        .step:hover,
        .spec-suggestion:hover,
        .objective-suggestion:hover {
            transform: none;
            box-shadow: none;
        }

        /* Focus styles for touch navigation */
        .btn:focus,
        .form-control:focus,
        .form-select:focus {
            outline: 2px solid #007bff;
            outline-offset: 2px;
        }
    }

    /* High contrast mode support */
    @media (prefers-contrast: high) {
        .step {
            border-width: 2px;
        }

        .step.active {
            border-width: 3px;
        }

        .btn {
            border-width: 2px;
        }

        .form-control,
        .form-select {
            border-width: 2px;
        }
    }

    /* Reduced motion support */
    @media (prefers-reduced-motion: reduce) {
        .touch-optimized,
        .touch-upload-zone,
        .step,
        .wizard-nav-btn,
        .btn {
            transition: none;
        }

        .touch-optimized.touch-active {
            transform: none;
        }

        .wizard-step.active {
            animation: none;
        }
    }

    /* Dark mode support for mobile */
    @media (prefers-color-scheme: dark) and (max-width: 576px) {
        .wizard-progress {
            background: #1f2937;
            border-color: #374151;
        }

        .step {
            background: #374151;
            color: #f9fafb;
        }

        .step.active {
            background: linear-gradient(135deg, #1e3a8a 0%, #1d4ed8 100%);
        }

        .step.completed {
            background: linear-gradient(135deg, #166534 0%, #16a34a 100%);
        }

        .wizard-navigation {
            background: #1f2937;
            border-color: #374151;
        }

        .modal-content {
            background: #1f2937;
            color: #f9fafb;
        }

        .modal-header,
        .modal-footer {
            border-color: #374151;
        }

        .preview-showcase {
            background: #1f2937;
        }

        .preview-section-title {
            background: #374151;
            color: #f9fafb;
        }
    }

    /* ========================================
       ENHANCED UI COMPONENTS STYLES
       ======================================== */

    /* Enhanced Multi-Select Dropdown */
    .enhanced-multiselect-container {
        position: relative;
        width: 100%;
    }

    .multiselect-input-wrapper {
        position: relative;
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
        background: white;
        min-height: 44px;
        display: flex;
        align-items: flex-start;
        padding: 0.375rem 2.5rem 0.375rem 0.75rem;
        cursor: text;
        transition: all 0.2s ease;
    }

    .multiselect-input-wrapper:focus-within {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    .selected-items {
        display: flex;
        flex-wrap: wrap;
        gap: 0.25rem;
        flex: 1;
        min-height: 1.5rem;
    }

    .selected-item {
        display: inline-flex;
        align-items: center;
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        color: white;
        padding: 0.25rem 0.5rem;
        border-radius: 1rem;
        font-size: 0.875rem;
        font-weight: 500;
        gap: 0.375rem;
        animation: slideIn 0.2s ease;
    }

    .selected-item-remove {
        background: rgba(255, 255, 255, 0.2);
        border: none;
        color: white;
        border-radius: 50%;
        width: 1.25rem;
        height: 1.25rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .selected-item-remove:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: scale(1.1);
    }

    .multiselect-search {
        border: none;
        outline: none;
        background: transparent;
        flex: 1;
        min-width: 120px;
        font-size: 0.875rem;
        padding: 0.25rem 0;
    }

    .multiselect-search::placeholder {
        color: #6c757d;
    }

    .multiselect-toggle {
        position: absolute;
        right: 0.75rem;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #6c757d;
        cursor: pointer;
        padding: 0.25rem;
        transition: all 0.2s ease;
    }

    .multiselect-toggle:hover {
        color: #007bff;
        transform: translateY(-50%) scale(1.1);
    }

    .multiselect-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid #ced4da;
        border-top: none;
        border-radius: 0 0 0.375rem 0.375rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        z-index: 1000;
        max-height: 300px;
        overflow-y: auto;
        display: none;
        animation: slideDown 0.2s ease;
    }

    .multiselect-dropdown.show {
        display: block;
    }

    .multiselect-options {
        padding: 0.5rem 0;
    }

    .multiselect-option {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.75rem 1rem;
        cursor: pointer;
        transition: all 0.2s ease;
        border-left: 3px solid transparent;
    }

    .multiselect-option:hover {
        background: #f8f9fa;
        border-left-color: #007bff;
        transform: translateX(2px);
    }

    .multiselect-option.selected {
        background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
        border-left-color: #007bff;
    }

    .option-content {
        display: flex;
        align-items: center;
        flex: 1;
    }

    .option-text {
        font-weight: 500;
        color: #212529;
    }

    .option-desc {
        color: #6c757d;
        font-size: 0.75rem;
        margin-left: 0.5rem;
    }

    .option-checkbox {
        width: 1.5rem;
        height: 1.5rem;
        border: 2px solid #ced4da;
        border-radius: 0.25rem;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }

    .multiselect-option.selected .option-checkbox {
        background: #007bff;
        border-color: #007bff;
        color: white;
    }

    .option-checkbox i {
        font-size: 0.75rem;
        opacity: 0;
        transition: opacity 0.2s ease;
    }

    .multiselect-option.selected .option-checkbox i {
        opacity: 1;
    }

    /* Enhanced Tag Input */
    .enhanced-tag-input-container {
        position: relative;
        width: 100%;
    }

    .tag-input-wrapper {
        position: relative;
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
        background: white;
        min-height: 44px;
        display: flex;
        align-items: flex-start;
        padding: 0.375rem 2.5rem 0.375rem 0.75rem;
        cursor: text;
        transition: all 0.2s ease;
    }

    .tag-input-wrapper:focus-within {
        border-color: #28a745;
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
    }

    .selected-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 0.25rem;
        flex: 1;
        min-height: 1.5rem;
    }

    .tag-item {
        display: inline-flex;
        align-items: center;
        background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
        color: white;
        padding: 0.25rem 0.5rem;
        border-radius: 1rem;
        font-size: 0.875rem;
        font-weight: 500;
        gap: 0.375rem;
        animation: slideIn 0.2s ease;
    }

    .tag-item-remove {
        background: rgba(255, 255, 255, 0.2);
        border: none;
        color: white;
        border-radius: 50%;
        width: 1.25rem;
        height: 1.25rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .tag-item-remove:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: scale(1.1);
    }

    .tag-input-field {
        border: none;
        outline: none;
        background: transparent;
        flex: 1;
        min-width: 120px;
        font-size: 0.875rem;
        padding: 0.25rem 0;
    }

    .tag-input-field::placeholder {
        color: #6c757d;
    }

    .tag-suggestions-toggle {
        position: absolute;
        right: 0.75rem;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #ffc107;
        cursor: pointer;
        padding: 0.25rem;
        transition: all 0.2s ease;
    }

    .tag-suggestions-toggle:hover {
        color: #e0a800;
        transform: translateY(-50%) scale(1.1);
    }

    .tag-suggestions-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid #ced4da;
        border-top: none;
        border-radius: 0 0 0.375rem 0.375rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        z-index: 1000;
        max-height: 400px;
        overflow-y: auto;
        display: none;
        animation: slideDown 0.2s ease;
    }

    .tag-suggestions-dropdown.show {
        display: block;
    }

    .suggestions-header {
        padding: 0.75rem 1rem;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-bottom: 1px solid #dee2e6;
        font-weight: 600;
        color: #495057;
        display: flex;
        align-items: center;
    }

    .suggestions-grid {
        padding: 0.5rem;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 0.5rem;
    }

    .suggestion-category {
        background: #f8f9fa;
        border-radius: 0.375rem;
        overflow: hidden;
    }

    .category-header {
        padding: 0.5rem 0.75rem;
        background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
        color: white;
        font-weight: 600;
        font-size: 0.875rem;
        display: flex;
        align-items: center;
    }

    .suggestion-item {
        padding: 0.5rem 0.75rem;
        cursor: pointer;
        transition: all 0.2s ease;
        border-left: 3px solid transparent;
    }

    .suggestion-item:hover {
        background: white;
        border-left-color: #28a745;
        transform: translateX(2px);
    }

    .suggestion-text {
        font-weight: 500;
        color: #212529;
        display: block;
    }

    .suggestion-desc {
        color: #6c757d;
        font-size: 0.75rem;
        margin-top: 0.125rem;
    }

    /* Animations */
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: scale(0.8);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
@endpush

@section('content')
<div class="py-5">
    <div class="container">
        <div class="showcase-create-form">

            <!-- Header -->
            <div class="text-center mb-5">
                <h2><i class="stars me-2"></i>{{ __('showcase.create.heading') }}</h2>
                <p class="text-muted">{{ __('showcase.create.description') }}</p>
            </div>

            <!-- Help Information -->
            <div class="form-help mb-4">
                <h6><i class="info-circle me-2"></i>Về Showcase</h6>
                <p class="mb-2">Showcase là nơi bạn có thể trình bày các dự án cơ khí, sản phẩm kỹ thuật, hoặc giải pháp
                    công nghệ của mình. Đây là cơ hội để:</p>
                <ul class="mb-0">
                    <li>Giới thiệu dự án và ý tưởng sáng tạo</li>
                    <li>Chia sẻ kinh nghiệm và kỹ năng chuyên môn</li>
                    <li>Kết nối với cộng đồng kỹ sư và nhà phát triển</li>
                    <li>Nhận phản hồi và góp ý từ chuyên gia</li>
                </ul>
            </div>

            <!-- Wizard Progress Indicator -->
            <div class="wizard-progress mb-4">
                <div class="progress-steps">
                    <div class="step active" data-step="1">
                        <div class="step-number">1</div>
                        <div class="step-label">Thông Tin Cơ Bản</div>
                    </div>
                    <div class="step" data-step="2">
                        <div class="step-number">2</div>
                        <div class="step-label">Media Upload</div>
                    </div>
                    <div class="step" data-step="3">
                        <div class="step-number">3</div>
                        <div class="step-label">Thông Tin Kỹ Thuật</div>
                    </div>
                    <div class="step" data-step="4">
                        <div class="step-number">4</div>
                        <div class="step-label">Tính Năng & Cài Đặt</div>
                    </div>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: 25%"></div>
                </div>
            </div>

            <!-- Create Form -->
            <form method="POST" action="{{ route('showcase.store') }}" enctype="multipart/form-data" id="showcaseForm">
                @csrf

                <!-- Step 1: Basic Information -->
                <div class="wizard-step active" data-step="1">
                    <!-- Basic Information Section -->
                    <div class="form-section">
                    <h5><i class="fas fa-edit-square me-2"></i>Thông Tin Cơ Bản</h5>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="title" class="form-label">{{ __('showcase.create.showcase_title') }} <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title"
                                name="title" value="{{ old('title') }}"
                                placeholder="VD: Robot hàn tự động cho ngành sản xuất ô tô" required>
                            @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Tên dự án hoặc sản phẩm bạn muốn showcase</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="location" class="form-label">Địa điểm</label>
                            <input type="text" class="form-control @error('location') is-invalid @enderror"
                                id="location" name="location" value="{{ old('location') }}"
                                placeholder="VD: Nhà máy ABC, Bình Dương">
                            @error('location')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="usage" class="form-label">Lĩnh vực ứng dụng</label>
                            <input type="text" class="form-control @error('usage') is-invalid @enderror" id="usage"
                                name="usage" value="{{ old('usage') }}"
                                placeholder="VD: Sản xuất ô tô, Cơ khí chính xác">
                            @error('usage')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">{{ __('showcase.create.detailed_description') }} <span
                                class="text-danger">*</span></label>

                        {{-- TinyMCE Rich Text Editor for Showcase Description --}}
                        <x-tinymce-editor
                            name="description"
                            id="description"
                            :value="old('description')"
                            placeholder="{{ __('showcase.create.description_placeholder') }}"
                            context="showcase"
                            :height="300"
                            :required="true"
                        />

                        @error('description')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            <i class="fas fa-info-circle me-1"></i>
                            Cung cấp thông tin chi tiết về dự án, công nghệ và kết quả. Hỗ trợ định dạng văn bản, hình ảnh và liên kết.
                        </div>
                    </div>
                </div>
                </div>

                <!-- Step 2: Media Upload -->
                <div class="wizard-step" data-step="2">
                    <!-- Cover Image Section -->
                    <div class="form-section">
                    <h5><i class="fas fa-image me-2"></i>{{ __('showcase.create.cover_image_section') }}</h5>

                    <div class="mb-3">
                        <label for="cover_image" class="form-label">{{ __('showcase.create.upload_cover_image') }} <span
                                class="text-danger">*</span></label>
                        <div class="file-upload-area" id="fileUploadArea">
                            <i class="fas fa-cloud-upload-alt fs-1 text-muted mb-3"></i>
                            <h6>Kéo thả file hoặc click để chọn</h6>
                            <p class="text-muted mb-0">JPG, PNG, WebP (tối đa 5MB)</p>
                            <input type="file" class="d-none @error('cover_image') is-invalid @enderror"
                                id="cover_image" name="cover_image" accept="image/*" required>
                        </div>

                        <!-- Image Preview -->
                        <div id="imagePreview" class="mt-3 text-center" style="display: none;">
                            <img id="previewImg" class="image-preview" alt="Preview">
                            <div class="mt-2">
                                <button type="button" class="btn btn-sm btn-outline-danger" id="removeImage">
                                    <i class="fas fa-trash"></i> Xóa ảnh
                                </button>
                            </div>
                        </div>

                        @error('cover_image')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <div class="form-text">{{ __('showcase.create.cover_image_help') }}</div>
                    </div>
                </div>

                <!-- Multiple Images Section -->
                <div class="form-section">
                    <h5><i class="fas fa-images me-2"></i>Thư viện hình ảnh</h5>
                    <p class="text-muted mb-3">Thêm nhiều hình ảnh để showcase dự án của bạn một cách chi tiết</p>

                    <div class="mb-3">
                        <label class="form-label">Upload nhiều hình ảnh (tùy chọn)</label>

                        <!-- Multiple Images Upload Area -->
                        <div class="multiple-images-upload-area" id="multipleImagesUploadArea">
                            <div class="upload-zone" id="multipleUploadZone">
                                <i class="fas fa-images fs-1 text-muted mb-3"></i>
                                <h6>Kéo thả nhiều file hoặc click để chọn</h6>
                                <p class="text-muted mb-0">JPG, PNG, WebP (tối đa 10MB mỗi file, tối đa 10 files)</p>
                                <small class="text-success">
                                    <i class="fas fa-compress-alt me-1"></i>
                                    Hình ảnh sẽ được tự động nén để tối ưu tốc độ tải
                                </small>
                                <input type="file" class="d-none" id="multiple_images" name="multiple_images[]"
                                    accept="image/*" multiple>
                            </div>
                        </div>

                        <!-- Images Preview Gallery -->
                        <div id="imagesGallery" class="images-gallery mt-3" style="display: none;">
                            <div class="gallery-header">
                                <h6><i class="fas fa-th me-1"></i>Hình ảnh đã chọn</h6>
                                <button type="button" class="btn btn-sm btn-outline-danger" id="clearAllImages">
                                    <i class="fas fa-trash me-1"></i>Xóa tất cả
                                </button>
                            </div>
                            <div id="imagesList" class="images-list">
                                <!-- Images will be dynamically added here -->
                            </div>
                            <div class="gallery-footer">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Kéo thả để sắp xếp lại thứ tự hình ảnh
                                </small>
                            </div>
                        </div>

                        <div class="form-text">
                            <i class="fas fa-lightbulb me-1"></i>
                            Thêm nhiều góc nhìn, chi tiết kỹ thuật, và quá trình thực hiện dự án.
                            <br>
                            <i class="fas fa-info-circle me-1"></i>
                            Hình ảnh sẽ được tự động nén xuống 1920x1080px để tối ưu hiệu suất mà vẫn giữ chất lượng tốt.
                        </div>
                    </div>
                </div>

                <!-- File Attachments Section -->
                <div class="form-section">
                    <h5><i class="fas fa-paperclip me-2"></i>File Attachments</h5>
                    <p class="text-muted mb-4">Thêm các file đính kèm như CAD files, bản vẽ kỹ thuật, tài liệu tính toán, báo cáo</p>

                    <div class="mb-3">
                        <label for="file_attachments" class="form-label">Upload file đính kèm (tùy chọn)</label>

                        {{-- File Upload Area --}}
                        <div class="file-attachments-upload-area" id="fileAttachmentsUploadArea">
                            <div class="upload-zone text-center py-4" id="attachmentUploadZone">
                                <i class="fas fa-cloud-upload-alt fs-1 text-muted mb-3"></i>
                                <h6>Kéo thả file hoặc click để chọn</h6>
                                <p class="text-muted mb-2">
                                    CAD files (DWG, STEP, IGES), Documents (PDF, DOC), Spreadsheets (XLS), Images (JPG, PNG)
                                </p>
                                <p class="text-muted small mb-0">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Tối đa 10 files, mỗi file tối đa 50MB
                                </p>
                                <input type="file" class="d-none" id="file_attachments" name="file_attachments[]"
                                       multiple accept=".dwg,.step,.stp,.iges,.igs,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.gif,.zip,.rar,.7z">
                            </div>
                        </div>

                        {{-- Files List --}}
                        <div class="attachments-list mt-3" id="attachmentsList" style="display: none;">
                            <h6 class="mb-3">
                                <i class="fas fa-list me-2"></i>
                                File đã chọn (<span id="attachmentsCount">0</span>)
                            </h6>
                            <div class="files-grid" id="filesGrid">
                                <!-- Files will be displayed here -->
                            </div>
                        </div>

                        <div class="form-text">
                            <i class="fas fa-lightbulb me-1"></i>
                            Hỗ trợ các định dạng: CAD files (DWG, STEP, IGES), Documents (PDF, DOC, XLS), Images, Archives (ZIP, RAR)
                        </div>
                    </div>
                </div>
                </div>

                <!-- Step 3: Technical Information -->
                <div class="wizard-step" data-step="3">
                    <!-- Technical Details Section -->
                    <div class="form-section">
                    <h5><i class="fas fa-cog me-2"></i>Thông Tin Kỹ Thuật</h5>

                    <div class="row">
                        <!-- Enhanced Software Used Multi-Select -->
                        <div class="col-md-12 mb-3">
                            <label for="software_used" class="form-label">
                                <i class="fas fa-laptop-code me-2"></i>
                                Phần mềm sử dụng
                            </label>
                            <div class="enhanced-multiselect-container" id="softwareMultiSelect">
                                <div class="multiselect-input-wrapper">
                                    <div class="selected-items" id="selectedSoftware"></div>
                                    <input type="text"
                                           class="multiselect-search"
                                           id="softwareSearch"
                                           placeholder="Tìm kiếm và chọn phần mềm..."
                                           autocomplete="off">
                                    <button type="button" class="multiselect-toggle" id="softwareToggle">
                                        <i class="fas fa-chevron-down"></i>
                                    </button>
                                </div>
                                <div class="multiselect-dropdown" id="softwareDropdown">
                                    <div class="multiselect-options">
                                        <div class="multiselect-option" data-value="SolidWorks">
                                            <div class="option-content">
                                                <i class="fas fa-cube text-primary me-2"></i>
                                                <span class="option-text">SolidWorks</span>
                                                <small class="option-desc">3D CAD Design</small>
                                            </div>
                                            <div class="option-checkbox">
                                                <i class="fas fa-check"></i>
                                            </div>
                                        </div>
                                        <div class="multiselect-option" data-value="AutoCAD">
                                            <div class="option-content">
                                                <i class="fas fa-drafting-compass text-warning me-2"></i>
                                                <span class="option-text">AutoCAD</span>
                                                <small class="option-desc">2D/3D CAD</small>
                                            </div>
                                            <div class="option-checkbox">
                                                <i class="fas fa-check"></i>
                                            </div>
                                        </div>
                                        <div class="multiselect-option" data-value="ANSYS">
                                            <div class="option-content">
                                                <i class="fas fa-calculator text-success me-2"></i>
                                                <span class="option-text">ANSYS</span>
                                                <small class="option-desc">Simulation & Analysis</small>
                                            </div>
                                            <div class="option-checkbox">
                                                <i class="fas fa-check"></i>
                                            </div>
                                        </div>
                                        <div class="multiselect-option" data-value="MATLAB">
                                            <div class="option-content">
                                                <i class="fas fa-chart-line text-info me-2"></i>
                                                <span class="option-text">MATLAB</span>
                                                <small class="option-desc">Mathematical Computing</small>
                                            </div>
                                            <div class="option-checkbox">
                                                <i class="fas fa-check"></i>
                                            </div>
                                        </div>
                                        <div class="multiselect-option" data-value="CATIA">
                                            <div class="option-content">
                                                <i class="fas fa-cogs text-secondary me-2"></i>
                                                <span class="option-text">CATIA</span>
                                                <small class="option-desc">Advanced CAD/CAM</small>
                                            </div>
                                            <div class="option-checkbox">
                                                <i class="fas fa-check"></i>
                                            </div>
                                        </div>
                                        <div class="multiselect-option" data-value="Fusion360">
                                            <div class="option-content">
                                                <i class="fas fa-atom text-danger me-2"></i>
                                                <span class="option-text">Fusion 360</span>
                                                <small class="option-desc">Cloud-based CAD</small>
                                            </div>
                                            <div class="option-checkbox">
                                                <i class="fas fa-check"></i>
                                            </div>
                                        </div>
                                        <div class="multiselect-option" data-value="Inventor">
                                            <div class="option-content">
                                                <i class="fas fa-tools text-dark me-2"></i>
                                                <span class="option-text">Autodesk Inventor</span>
                                                <small class="option-desc">3D Mechanical Design</small>
                                            </div>
                                            <div class="option-checkbox">
                                                <i class="fas fa-check"></i>
                                            </div>
                                        </div>
                                        <div class="multiselect-option" data-value="NX">
                                            <div class="option-content">
                                                <i class="fas fa-microchip text-primary me-2"></i>
                                                <span class="option-text">Siemens NX</span>
                                                <small class="option-desc">Advanced CAD/CAM/CAE</small>
                                            </div>
                                            <div class="option-checkbox">
                                                <i class="fas fa-check"></i>
                                            </div>
                                        </div>
                                        <div class="multiselect-option" data-value="Creo">
                                            <div class="option-content">
                                                <i class="fas fa-project-diagram text-warning me-2"></i>
                                                <span class="option-text">PTC Creo</span>
                                                <small class="option-desc">Parametric Design</small>
                                            </div>
                                            <div class="option-checkbox">
                                                <i class="fas fa-check"></i>
                                            </div>
                                        </div>
                                        <div class="multiselect-option" data-value="Other">
                                            <div class="option-content">
                                                <i class="fas fa-plus-circle text-muted me-2"></i>
                                                <span class="option-text">Khác</span>
                                                <small class="option-desc">Phần mềm khác</small>
                                            </div>
                                            <div class="option-checkbox">
                                                <i class="fas fa-check"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Hidden inputs for form submission -->
                                <div id="softwareHiddenInputs"></div>
                            </div>
                            @error('software_used')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Tìm kiếm và chọn các phần mềm đã sử dụng trong dự án
                            </div>
                        </div>

                        <!-- Enhanced Materials Tag Input -->
                        <div class="col-md-6 mb-3">
                            <label for="materials" class="form-label">
                                <i class="fas fa-cubes me-1"></i>Vật liệu chính
                            </label>
                            <div class="enhanced-tag-input-container" id="materialsTagInput">
                                <div class="tag-input-wrapper">
                                    <div class="selected-tags" id="materialsTags"></div>
                                    <input type="text"
                                           class="tag-input-field"
                                           id="materialsInputField"
                                           placeholder="Nhập vật liệu và nhấn Enter..."
                                           autocomplete="off">
                                    <button type="button" class="tag-suggestions-toggle" id="materialsToggle">
                                        <i class="fas fa-lightbulb"></i>
                                    </button>
                                </div>
                                <div class="tag-suggestions-dropdown" id="materialsSuggestions">
                                    <div class="suggestions-header">
                                        <i class="fas fa-star text-warning me-2"></i>
                                        <span>Vật liệu phổ biến</span>
                                    </div>
                                    <div class="suggestions-grid">
                                        <!-- Metals Category -->
                                        <div class="suggestion-category">
                                            <div class="category-header">
                                                <i class="fas fa-hammer text-secondary me-2"></i>
                                                <span>Kim loại</span>
                                            </div>
                                            <div class="suggestion-item" data-value="Thép carbon S235">
                                                <span class="suggestion-text">Thép carbon S235</span>
                                                <small class="suggestion-desc">Thép kết cấu phổ biến</small>
                                            </div>
                                            <div class="suggestion-item" data-value="Thép carbon S355">
                                                <span class="suggestion-text">Thép carbon S355</span>
                                                <small class="suggestion-desc">Thép kết cấu chất lượng cao</small>
                                            </div>
                                            <div class="suggestion-item" data-value="Thép không gỉ 304">
                                                <span class="suggestion-text">Thép không gỉ 304</span>
                                                <small class="suggestion-desc">Chống ăn mòn tốt</small>
                                            </div>
                                            <div class="suggestion-item" data-value="Nhôm 6061">
                                                <span class="suggestion-text">Nhôm 6061</span>
                                                <small class="suggestion-desc">Nhôm kết cấu phổ biến</small>
                                            </div>
                                            <div class="suggestion-item" data-value="Nhôm 7075">
                                                <span class="suggestion-text">Nhôm 7075</span>
                                                <small class="suggestion-desc">Nhôm hàng không</small>
                                            </div>
                                        </div>
                                        <!-- Plastics Category -->
                                        <div class="suggestion-category">
                                            <div class="category-header">
                                                <i class="fas fa-cube text-info me-2"></i>
                                                <span>Nhựa kỹ thuật</span>
                                            </div>
                                            <div class="suggestion-item" data-value="ABS">
                                                <span class="suggestion-text">ABS</span>
                                                <small class="suggestion-desc">Nhựa kỹ thuật phổ biến</small>
                                            </div>
                                            <div class="suggestion-item" data-value="PLA">
                                                <span class="suggestion-text">PLA</span>
                                                <small class="suggestion-desc">Nhựa sinh học, 3D printing</small>
                                            </div>
                                            <div class="suggestion-item" data-value="PETG">
                                                <span class="suggestion-text">PETG</span>
                                                <small class="suggestion-desc">Nhựa trong suốt, bền</small>
                                            </div>
                                            <div class="suggestion-item" data-value="Nylon">
                                                <span class="suggestion-text">Nylon</span>
                                                <small class="suggestion-desc">Nhựa kỹ thuật cao cấp</small>
                                            </div>
                                        </div>
                                        <!-- Composites Category -->
                                        <div class="suggestion-category">
                                            <div class="category-header">
                                                <i class="fas fa-layer-group text-success me-2"></i>
                                                <span>Vật liệu composite</span>
                                            </div>
                                            <div class="suggestion-item" data-value="Carbon Fiber">
                                                <span class="suggestion-text">Carbon Fiber</span>
                                                <small class="suggestion-desc">Nhẹ, cứng, đắt</small>
                                            </div>
                                            <div class="suggestion-item" data-value="Fiberglass">
                                                <span class="suggestion-text">Fiberglass</span>
                                                <small class="suggestion-desc">Composite phổ biến</small>
                                            </div>
                                            <div class="suggestion-item" data-value="Kevlar">
                                                <span class="suggestion-text">Kevlar</span>
                                                <small class="suggestion-desc">Chống va đập</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Hidden input for form submission -->
                                <input type="hidden" id="materials" name="materials" value="{{ old('materials') }}">
                            </div>
                            @error('materials')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Nhập vật liệu và nhấn Enter để thêm tag, hoặc chọn từ gợi ý
                            </div>
                        </div>

                        <!-- Manufacturing Process -->
                        <div class="col-md-6 mb-3 manufacturing-process-container">
                            <label for="manufacturing_process" class="form-label">
                                <i class="fas fa-industry me-1"></i>Quy trình sản xuất
                            </label>
                            <select class="form-select @error('manufacturing_process') is-invalid @enderror"
                                id="manufacturing_process" name="manufacturing_process">
                                <option value="">Chọn quy trình sản xuất chính</option>

                                <!-- Subtractive Manufacturing -->
                                <optgroup label="🔧 Gia công cắt gọt">
                                    <option value="CNC Machining" {{ old('manufacturing_process')=='CNC Machining' ? 'selected' : '' }}>
                                        CNC Machining - Gia công cắt gọt tự động
                                    </option>
                                    <option value="Manual Machining" {{ old('manufacturing_process')=='Manual Machining' ? 'selected' : '' }}>
                                        Manual Machining - Gia công thủ công
                                    </option>
                                    <option value="Grinding" {{ old('manufacturing_process')=='Grinding' ? 'selected' : '' }}>
                                        Grinding - Mài, đánh bóng
                                    </option>
                                    <option value="EDM" {{ old('manufacturing_process')=='EDM' ? 'selected' : '' }}>
                                        EDM - Gia công tia lửa điện
                                    </option>
                                </optgroup>

                                <!-- Additive Manufacturing -->
                                <optgroup label="🖨️ Sản xuất cộng dồn">
                                    <option value="3D Printing" {{ old('manufacturing_process')=='3D Printing' ? 'selected' : '' }}>
                                        3D Printing - In 3D (FDM, SLA, SLS)
                                    </option>
                                    <option value="Metal 3D Printing" {{ old('manufacturing_process')=='Metal 3D Printing' ? 'selected' : '' }}>
                                        Metal 3D Printing - In 3D kim loại
                                    </option>
                                </optgroup>

                                <!-- Forming Processes -->
                                <optgroup label="🔨 Gia công tạo hình">
                                    <option value="Casting" {{ old('manufacturing_process')=='Casting' ? 'selected' : '' }}>
                                        Casting - Đúc kim loại
                                    </option>
                                    <option value="Forging" {{ old('manufacturing_process')=='Forging' ? 'selected' : '' }}>
                                        Forging - Rèn, dập
                                    </option>
                                    <option value="Sheet Metal" {{ old('manufacturing_process')=='Sheet Metal' ? 'selected' : '' }}>
                                        Sheet Metal - Gia công tôn
                                    </option>
                                    <option value="Stamping" {{ old('manufacturing_process')=='Stamping' ? 'selected' : '' }}>
                                        Stamping - Dập tấm
                                    </option>
                                    <option value="Deep Drawing" {{ old('manufacturing_process')=='Deep Drawing' ? 'selected' : '' }}>
                                        Deep Drawing - Dập sâu
                                    </option>
                                </optgroup>

                                <!-- Joining Processes -->
                                <optgroup label="🔗 Gia công nối">
                                    <option value="Welding" {{ old('manufacturing_process')=='Welding' ? 'selected' : '' }}>
                                        Welding - Hàn (TIG, MIG, Arc)
                                    </option>
                                    <option value="Brazing" {{ old('manufacturing_process')=='Brazing' ? 'selected' : '' }}>
                                        Brazing - Hàn đồng
                                    </option>
                                    <option value="Soldering" {{ old('manufacturing_process')=='Soldering' ? 'selected' : '' }}>
                                        Soldering - Hàn thiếc
                                    </option>
                                    <option value="Riveting" {{ old('manufacturing_process')=='Riveting' ? 'selected' : '' }}>
                                        Riveting - Tán đinh
                                    </option>
                                </optgroup>

                                <!-- Molding Processes -->
                                <optgroup label="🏭 Gia công đúc ép">
                                    <option value="Injection Molding" {{ old('manufacturing_process')=='Injection Molding' ? 'selected' : '' }}>
                                        Injection Molding - Ép phun nhựa
                                    </option>
                                    <option value="Blow Molding" {{ old('manufacturing_process')=='Blow Molding' ? 'selected' : '' }}>
                                        Blow Molding - Thổi phồng
                                    </option>
                                    <option value="Compression Molding" {{ old('manufacturing_process')=='Compression Molding' ? 'selected' : '' }}>
                                        Compression Molding - Ép nén
                                    </option>
                                </optgroup>

                                <!-- Assembly & Finishing -->
                                <optgroup label="🔧 Lắp ráp & hoàn thiện">
                                    <option value="Assembly" {{ old('manufacturing_process')=='Assembly' ? 'selected' : '' }}>
                                        Assembly - Lắp ráp cơ khí
                                    </option>
                                    <option value="Surface Treatment" {{ old('manufacturing_process')=='Surface Treatment' ? 'selected' : '' }}>
                                        Surface Treatment - Xử lý bề mặt
                                    </option>
                                    <option value="Heat Treatment" {{ old('manufacturing_process')=='Heat Treatment' ? 'selected' : '' }}>
                                        Heat Treatment - Nhiệt luyện
                                    </option>
                                </optgroup>

                                <!-- Others -->
                                <optgroup label="🔄 Khác">
                                    <option value="Hybrid Process" {{ old('manufacturing_process')=='Hybrid Process' ? 'selected' : '' }}>
                                        Hybrid Process - Quy trình kết hợp
                                    </option>
                                    <option value="Other" {{ old('manufacturing_process')=='Other' ? 'selected' : '' }}>
                                        Other - Quy trình khác
                                    </option>
                                </optgroup>
                            </select>
                            @error('manufacturing_process')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Chọn quy trình sản xuất chính được sử dụng trong dự án
                            </div>
                        </div>

                        <!-- Complexity Level -->
                        <div class="col-md-6 mb-3 complexity-level-container">
                            <label class="form-label">
                                <i class="fas fa-chart-line me-1"></i>Mức độ phức tạp
                            </label>
                            <div class="complexity-level-grid mt-2">
                                <div class="complexity-option" data-level="beginner">
                                    <input class="form-check-input" type="radio" name="complexity_level" id="complexity_beginner"
                                        value="Beginner" {{ old('complexity_level')=='Beginner' ? 'checked' : '' }}>
                                    <label class="complexity-label" for="complexity_beginner">
                                        <div class="complexity-icon">
                                            <i class="fas fa-seedling"></i>
                                        </div>
                                        <div class="complexity-content">
                                            <div class="complexity-title">Cơ bản</div>
                                            <div class="complexity-desc">Dự án đơn giản, phù hợp người mới bắt đầu</div>
                                        </div>
                                    </label>
                                </div>

                                <div class="complexity-option" data-level="intermediate">
                                    <input class="form-check-input" type="radio" name="complexity_level" id="complexity_intermediate"
                                        value="Intermediate" {{ old('complexity_level')=='Intermediate' ? 'checked' : '' }}>
                                    <label class="complexity-label" for="complexity_intermediate">
                                        <div class="complexity-icon">
                                            <i class="fas fa-cogs"></i>
                                        </div>
                                        <div class="complexity-content">
                                            <div class="complexity-title">Trung bình</div>
                                            <div class="complexity-desc">Yêu cầu kiến thức cơ bản về cơ khí</div>
                                        </div>
                                    </label>
                                </div>

                                <div class="complexity-option" data-level="advanced">
                                    <input class="form-check-input" type="radio" name="complexity_level" id="complexity_advanced"
                                        value="Advanced" {{ old('complexity_level')=='Advanced' ? 'checked' : '' }}>
                                    <label class="complexity-label" for="complexity_advanced">
                                        <div class="complexity-icon">
                                            <i class="fas fa-rocket"></i>
                                        </div>
                                        <div class="complexity-content">
                                            <div class="complexity-title">Nâng cao</div>
                                            <div class="complexity-desc">Cần kinh nghiệm và kỹ năng chuyên sâu</div>
                                        </div>
                                    </label>
                                </div>

                                <div class="complexity-option" data-level="expert">
                                    <input class="form-check-input" type="radio" name="complexity_level" id="complexity_expert"
                                        value="Expert" {{ old('complexity_level')=='Expert' ? 'checked' : '' }}>
                                    <label class="complexity-label" for="complexity_expert">
                                        <div class="complexity-icon">
                                            <i class="fas fa-crown"></i>
                                        </div>
                                        <div class="complexity-content">
                                            <div class="complexity-title">Chuyên gia</div>
                                            <div class="complexity-desc">Dành cho chuyên gia có kinh nghiệm cao</div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            @error('complexity_level')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Chọn mức độ phức tạp phù hợp với dự án của bạn
                            </div>
                        </div>

                        <!-- Industry Application -->
                        <div class="col-md-6 mb-3">
                            <label for="industry_application" class="form-label">Ứng dụng ngành</label>
                            <input type="text" class="form-control @error('industry_application') is-invalid @enderror"
                                id="industry_application" name="industry_application" value="{{ old('industry_application') }}"
                                placeholder="VD: Ô tô, Hàng không, Năng lượng">
                            @error('industry_application')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Ngành công nghiệp ứng dụng dự án</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="floors" class="form-label">Quy mô/Cấp độ</label>
                            <select class="form-select @error('floors') is-invalid @enderror" id="floors" name="floors">
                                <option value="">Chọn quy mô dự án</option>
                                <option value="1" {{ old('floors')=='1' ? 'selected' : '' }}>Prototype/Demo</option>
                                <option value="2" {{ old('floors')=='2' ? 'selected' : '' }}>Pilot/Thử nghiệm</option>
                                <option value="3" {{ old('floors')=='3' ? 'selected' : '' }}>Sản xuất nhỏ</option>
                                <option value="4" {{ old('floors')=='4' ? 'selected' : '' }}>Sản xuất hàng loạt</option>
                                <option value="5" {{ old('floors')=='5' ? 'selected' : '' }}>Quy mô công nghiệp</option>
                            </select>
                            @error('floors')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="category" class="form-label">{{ __('showcase.create.technical_category') }}</label>
                            <select class="form-select" id="category" name="category">
                                <option value="">{{ __('showcase.create.choose_category') }}</option>
                                <option value="mechanical-design">Thiết kế cơ khí</option>
                                <option value="automation">Tự động hóa</option>
                                <option value="robotics">Robot học</option>
                                <option value="manufacturing">Sản xuất chế tạo</option>
                                <option value="cad-cam">CAD/CAM</option>
                                <option value="plc-scada">PLC/SCADA</option>
                                <option value="materials">Vật liệu kỹ thuật</option>
                                <option value="other">Khác</option>
                            </select>
                        </div>
                    </div>

                    <!-- Advanced Technical Fields -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-cogs me-2"></i>Thông số kỹ thuật chi tiết
                            </h6>
                        </div>

                        <!-- Technical Specifications -->
                        <div class="col-12 mb-4">
                            <label class="form-label">
                                <i class="fas fa-clipboard-list me-2"></i>Thông số kỹ thuật
                            </label>
                            <p class="text-muted small mb-3">Thêm các thông số kỹ thuật quan trọng của dự án (kích thước, công suất, tốc độ, v.v.)</p>

                            <div id="technical-specs-container">
                                <div class="technical-spec-row mb-2" data-index="0">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <input type="text"
                                                   class="form-control"
                                                   name="technical_specs[0][name]"
                                                   placeholder="Tên thông số (VD: Kích thước)"
                                                   value="{{ old('technical_specs.0.name') }}">
                                        </div>
                                        <div class="col-md-4">
                                            <input type="text"
                                                   class="form-control"
                                                   name="technical_specs[0][value]"
                                                   placeholder="Giá trị (VD: 1000x500x300)"
                                                   value="{{ old('technical_specs.0.value') }}">
                                        </div>
                                        <div class="col-md-3">
                                            <input type="text"
                                                   class="form-control"
                                                   name="technical_specs[0][unit]"
                                                   placeholder="Đơn vị (VD: mm)"
                                                   value="{{ old('technical_specs.0.unit') }}">
                                        </div>
                                        <div class="col-md-1">
                                            <button type="button" class="btn btn-outline-danger btn-sm remove-spec-btn" style="display: none;">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="add-spec-btn">
                                <i class="fas fa-plus me-2"></i>Thêm thông số
                            </button>

                            <!-- Common Specs Suggestions -->
                            <div class="mt-3">
                                <small class="text-muted">Gợi ý thông số phổ biến:</small>
                                <div class="mt-2">
                                    <button type="button" class="btn btn-outline-secondary btn-sm me-2 spec-suggestion"
                                            data-name="Kích thước" data-unit="mm">Kích thước</button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm me-2 spec-suggestion"
                                            data-name="Trọng lượng" data-unit="kg">Trọng lượng</button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm me-2 spec-suggestion"
                                            data-name="Công suất" data-unit="kW">Công suất</button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm me-2 spec-suggestion"
                                            data-name="Tốc độ" data-unit="rpm">Tốc độ</button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm me-2 spec-suggestion"
                                            data-name="Áp suất" data-unit="bar">Áp suất</button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm me-2 spec-suggestion"
                                            data-name="Nhiệt độ" data-unit="°C">Nhiệt độ</button>
                                </div>
                            </div>
                        </div>

                        <!-- Learning Objectives -->
                        <div class="col-12 mb-4">
                            <label class="form-label">
                                <i class="fas fa-graduation-cap me-2"></i>Mục tiêu học tập
                            </label>
                            <p class="text-muted small mb-3">Người xem sẽ học được gì từ dự án này?</p>

                            <div id="learning-objectives-container">
                                <div class="learning-objective-row mb-2" data-index="0">
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-check-circle text-success"></i>
                                        </span>
                                        <input type="text"
                                               class="form-control"
                                               name="learning_objectives[0]"
                                               placeholder="VD: Hiểu nguyên lý thiết kế cầu trục"
                                               value="{{ old('learning_objectives.0') }}">
                                        <button type="button" class="btn btn-outline-danger remove-objective-btn" style="display: none;">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="add-objective-btn">
                                <i class="fas fa-plus me-2"></i>Thêm mục tiêu
                            </button>

                            <!-- Learning Objectives Suggestions -->
                            <div class="mt-3">
                                <small class="text-muted">Gợi ý mục tiêu học tập:</small>
                                <div class="mt-2">
                                    <button type="button" class="btn btn-outline-secondary btn-sm me-2 mb-1 objective-suggestion">
                                        Hiểu nguyên lý thiết kế cơ bản
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm me-2 mb-1 objective-suggestion">
                                        Nắm vững quy trình sản xuất
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm me-2 mb-1 objective-suggestion">
                                        Áp dụng phần mềm CAD/CAM
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm me-2 mb-1 objective-suggestion">
                                        Tính toán kết cấu và độ bền
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm me-2 mb-1 objective-suggestion">
                                        Lựa chọn vật liệu phù hợp
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Enhanced Industry Application -->
                        <div class="col-12 mb-3">
                            <label for="industry_application_enhanced" class="form-label">
                                <i class="fas fa-industry me-2"></i>Ứng dụng ngành công nghiệp
                            </label>
                            <p class="text-muted small mb-3">Dự án này có thể ứng dụng trong ngành nào?</p>

                            <select class="form-select" id="industry_application_enhanced" name="industry_application">
                                <option value="">Chọn ngành ứng dụng chính</option>

                                <!-- Manufacturing Industries -->
                                <optgroup label="🏭 Sản xuất chế tạo">
                                    <option value="automotive" {{ old('industry_application')=='automotive' ? 'selected' : '' }}>
                                        Ô tô - Automotive
                                    </option>
                                    <option value="aerospace" {{ old('industry_application')=='aerospace' ? 'selected' : '' }}>
                                        Hàng không vũ trụ - Aerospace
                                    </option>
                                    <option value="shipbuilding" {{ old('industry_application')=='shipbuilding' ? 'selected' : '' }}>
                                        Đóng tàu - Shipbuilding
                                    </option>
                                    <option value="machinery" {{ old('industry_application')=='machinery' ? 'selected' : '' }}>
                                        Máy móc công nghiệp - Industrial Machinery
                                    </option>
                                    <option value="electronics" {{ old('industry_application')=='electronics' ? 'selected' : '' }}>
                                        Điện tử - Electronics Manufacturing
                                    </option>
                                </optgroup>

                                <!-- Energy & Infrastructure -->
                                <optgroup label="⚡ Năng lượng & Hạ tầng">
                                    <option value="oil_gas" {{ old('industry_application')=='oil_gas' ? 'selected' : '' }}>
                                        Dầu khí - Oil & Gas
                                    </option>
                                    <option value="renewable_energy" {{ old('industry_application')=='renewable_energy' ? 'selected' : '' }}>
                                        Năng lượng tái tạo - Renewable Energy
                                    </option>
                                    <option value="power_generation" {{ old('industry_application')=='power_generation' ? 'selected' : '' }}>
                                        Phát điện - Power Generation
                                    </option>
                                    <option value="construction" {{ old('industry_application')=='construction' ? 'selected' : '' }}>
                                        Xây dựng - Construction
                                    </option>
                                </optgroup>

                                <!-- Process Industries -->
                                <optgroup label="🏗️ Công nghiệp quy trình">
                                    <option value="chemical" {{ old('industry_application')=='chemical' ? 'selected' : '' }}>
                                        Hóa chất - Chemical Processing
                                    </option>
                                    <option value="pharmaceutical" {{ old('industry_application')=='pharmaceutical' ? 'selected' : '' }}>
                                        Dược phẩm - Pharmaceutical
                                    </option>
                                    <option value="food_beverage" {{ old('industry_application')=='food_beverage' ? 'selected' : '' }}>
                                        Thực phẩm đồ uống - Food & Beverage
                                    </option>
                                    <option value="textile" {{ old('industry_application')=='textile' ? 'selected' : '' }}>
                                        Dệt may - Textile
                                    </option>
                                </optgroup>

                                <!-- Technology & Innovation -->
                                <optgroup label="💡 Công nghệ & Đổi mới">
                                    <option value="robotics" {{ old('industry_application')=='robotics' ? 'selected' : '' }}>
                                        Robot học - Robotics
                                    </option>
                                    <option value="automation" {{ old('industry_application')=='automation' ? 'selected' : '' }}>
                                        Tự động hóa - Industrial Automation
                                    </option>
                                    <option value="iot_industry4" {{ old('industry_application')=='iot_industry4' ? 'selected' : '' }}>
                                        IoT & Industry 4.0
                                    </option>
                                    <option value="research_development" {{ old('industry_application')=='research_development' ? 'selected' : '' }}>
                                        Nghiên cứu phát triển - R&D
                                    </option>
                                </optgroup>

                                <!-- Others -->
                                <optgroup label="🔧 Khác">
                                    <option value="general_manufacturing" {{ old('industry_application')=='general_manufacturing' ? 'selected' : '' }}>
                                        Sản xuất tổng quát - General Manufacturing
                                    </option>
                                    <option value="maintenance_repair" {{ old('industry_application')=='maintenance_repair' ? 'selected' : '' }}>
                                        Bảo trì sửa chữa - Maintenance & Repair
                                    </option>
                                    <option value="education_training" {{ old('industry_application')=='education_training' ? 'selected' : '' }}>
                                        Giáo dục đào tạo - Education & Training
                                    </option>
                                    <option value="other" {{ old('industry_application')=='other' ? 'selected' : '' }}>
                                        Khác - Other
                                    </option>
                                </optgroup>
                            </select>
                            @error('industry_application')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                </div>

                <!-- Step 4: Features & Settings -->
                <div class="wizard-step" data-step="4">
                    <!-- Project Features Section -->
                    <div class="form-section project-features-section">
                    <h5><i class="fas fa-star me-2"></i>Tính năng dự án</h5>
                    <p class="text-muted mb-3">Chọn các tính năng có trong dự án của bạn</p>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="feature-checkbox-container">
                                <div class="form-check feature-check">
                                    <input class="form-check-input" type="checkbox" name="has_tutorial" id="has_tutorial"
                                        value="1" {{ old('has_tutorial') ? 'checked' : '' }}>
                                    <label class="form-check-label feature-label" for="has_tutorial">
                                        <div class="feature-icon tutorial-icon">
                                            <i class="fas fa-list-ol"></i>
                                        </div>
                                        <div class="feature-content">
                                            <div class="feature-title">Hướng dẫn step-by-step</div>
                                            <div class="feature-desc">Dự án có kèm hướng dẫn chi tiết từng bước</div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="feature-checkbox-container">
                                <div class="form-check feature-check">
                                    <input class="form-check-input" type="checkbox" name="has_calculations" id="has_calculations"
                                        value="1" {{ old('has_calculations') ? 'checked' : '' }}>
                                    <label class="form-check-label feature-label" for="has_calculations">
                                        <div class="feature-icon calculations-icon">
                                            <i class="fas fa-calculator"></i>
                                        </div>
                                        <div class="feature-content">
                                            <div class="feature-title">Tính toán kỹ thuật</div>
                                            <div class="feature-desc">Bao gồm các tính toán và phân tích chi tiết</div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="feature-checkbox-container">
                                <div class="form-check feature-check">
                                    <input class="form-check-input" type="checkbox" name="has_cad_files" id="has_cad_files"
                                        value="1" {{ old('has_cad_files') ? 'checked' : '' }}>
                                    <label class="form-check-label feature-label" for="has_cad_files">
                                        <div class="feature-icon cad-icon">
                                            <i class="fas fa-cube"></i>
                                        </div>
                                        <div class="feature-content">
                                            <div class="feature-title">File CAD đính kèm</div>
                                            <div class="feature-desc">File 3D, bản vẽ kỹ thuật có thể tải xuống</div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Settings Section -->
                <div class="form-section sharing-settings-section">
                    <h5><i class="fas fa-share-alt me-2"></i>Cài đặt chia sẻ</h5>
                    <p class="text-muted mb-3">Thiết lập quyền truy cập và tương tác với dự án</p>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="setting-checkbox-container">
                                <div class="form-check setting-check">
                                    <input class="form-check-input" type="checkbox" name="is_public" id="is_public"
                                        value="1" {{ old('is_public', true) ? 'checked' : '' }}>
                                    <label class="form-check-label setting-label" for="is_public">
                                        <div class="setting-icon public-icon">
                                            <i class="fas fa-globe"></i>
                                        </div>
                                        <div class="setting-content">
                                            <div class="setting-title">Công khai</div>
                                            <div class="setting-desc">Cho phép mọi người xem và tìm kiếm dự án</div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="setting-checkbox-container">
                                <div class="form-check setting-check">
                                    <input class="form-check-input" type="checkbox" name="allow_downloads" id="allow_downloads"
                                        value="1" {{ old('allow_downloads') ? 'checked' : '' }}>
                                    <label class="form-check-label setting-label" for="allow_downloads">
                                        <div class="setting-icon download-icon">
                                            <i class="fas fa-download"></i>
                                        </div>
                                        <div class="setting-content">
                                            <div class="setting-title">Cho phép tải xuống</div>
                                            <div class="setting-desc">Người dùng có thể tải file đính kèm</div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="setting-checkbox-container">
                                <div class="form-check setting-check">
                                    <input class="form-check-input" type="checkbox" name="allow_comments" id="allow_comments"
                                        value="1" {{ old('allow_comments', true) ? 'checked' : '' }}>
                                    <label class="form-check-label setting-label" for="allow_comments">
                                        <div class="setting-icon comment-icon">
                                            <i class="fas fa-comments"></i>
                                        </div>
                                        <div class="setting-content">
                                            <div class="setting-title">Cho phép bình luận</div>
                                            <div class="setting-desc">Người dùng có thể bình luận và thảo luận</div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </div>

                <!-- Wizard Navigation -->
                <div class="wizard-navigation">
                    <button type="button" class="wizard-nav-btn btn-prev" id="prevBtn" style="display: none;">
                        <i class="fas fa-arrow-left me-2"></i>Quay lại
                    </button>

                    <div class="wizard-step-info">
                        <span class="current-step">Bước 1</span> / <span class="total-steps">4</span>
                        <button type="button" class="btn btn-outline-primary btn-sm ms-3" id="previewBtn">
                            <i class="fas fa-eye me-2"></i>Xem trước
                        </button>
                    </div>

                    <button type="button" class="wizard-nav-btn btn-next" id="nextBtn">
                        Tiếp theo <i class="fas fa-arrow-right ms-2"></i>
                    </button>

                    <button type="submit" class="wizard-nav-btn btn-submit" id="submitBtn" style="display: none;">
                        <i class="fas fa-check-circle me-2"></i>Tạo Showcase
                    </button>
                </div>

                <!-- Legacy Action Buttons (Hidden) -->
                <div class="d-flex justify-content-between" style="display: none !important;">
                    <a href="{{ route('showcase.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Quay lại
                    </a>

                    <div>
                        <button type="button" class="btn btn-outline-primary me-2" id="previewBtn">
                            <i class="fas fa-eye me-2"></i>Xem trước
                        </button>
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="fas fa-check-circle me-2"></i>{{ __('showcase.create.create_button') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewModalLabel">
                    <i class="fas fa-eye me-2"></i>Xem trước Showcase
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="preview-container" id="previewContainer">
                    <!-- Preview content will be generated here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Đóng
                </button>
                <button type="button" class="btn btn-primary" id="editFromPreview">
                    <i class="fas fa-edit me-2"></i>Chỉnh sửa
                </button>
            </div>
        </div>
    </div>
</div>


@endsection

@push('scripts')
{{-- TinyMCE Scripts for Rich Text Editor --}}
<script src="{{ asset('js/tinymce/tinymce.min.js') }}"></script>
<script src="{{ asset('js/tinymce-config.js') }}"></script>
<script src="{{ asset('js/tinymce-uploader.js') }}"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
    const fileUploadArea = document.getElementById('fileUploadArea');
    const fileInput = document.getElementById('cover_image');
    const imagePreview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');
    const removeImageBtn = document.getElementById('removeImage');
    // Initialize multi-select for software_used
    const softwareSelect = document.getElementById('software_used');
    if (softwareSelect) {
        // Add container class for styling
        softwareSelect.parentElement.classList.add('multi-select-container');

        // Add keyboard support for multi-select
        softwareSelect.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const option = this.options[this.selectedIndex];
                if (option) {
                    option.selected = !option.selected;
                }
            }
        });
    }

    // Initialize materials input with tag system
    const materialsInput = document.getElementById('materials');
    let materialsArray = [];

    if (materialsInput) {
        // Create tags container
        const tagsContainer = document.createElement('div');
        tagsContainer.className = 'materials-tags';
        materialsInput.parentNode.insertBefore(tagsContainer, materialsInput.nextSibling);

        // Handle input events
        materialsInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ',') {
                e.preventDefault();
                addMaterial(this.value.trim());
                this.value = '';
            }
        });

        materialsInput.addEventListener('blur', function() {
            if (this.value.trim()) {
                addMaterial(this.value.trim());
                this.value = '';
            }
        });

        // Function to add material tag
        function addMaterial(material) {
            if (material && !materialsArray.includes(material)) {
                materialsArray.push(material);
                updateMaterialsDisplay();
                updateMaterialsInput();
            }
        }

        // Function to remove material tag
        function removeMaterial(material) {
            materialsArray = materialsArray.filter(m => m !== material);
            updateMaterialsDisplay();
            updateMaterialsInput();
        }

        // Function to update materials display
        function updateMaterialsDisplay() {
            const tagsContainer = materialsInput.parentNode.querySelector('.materials-tags');
            tagsContainer.innerHTML = '';

            materialsArray.forEach(material => {
                const tag = document.createElement('span');
                tag.className = 'material-tag';
                tag.innerHTML = `
                    <span>${material}</span>
                    <span class="remove-tag" onclick="removeMaterialTag('${material}')">&times;</span>
                `;
                tagsContainer.appendChild(tag);
            });
        }

        // Function to update hidden input value
        function updateMaterialsInput() {
            materialsInput.value = materialsArray.join(', ');
        }

        // Global function for removing tags (accessible from onclick)
        window.removeMaterialTag = function(material) {
            removeMaterial(material);
        };

        // Initialize with existing value
        if (materialsInput.value) {
            const existingMaterials = materialsInput.value.split(',').map(m => m.trim()).filter(m => m);
            materialsArray = existingMaterials;
            updateMaterialsDisplay();
        }
    }

    // File upload area click
    fileUploadArea.addEventListener('click', () => {
        fileInput.click();
    });

    // Drag and drop functionality
    fileUploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        fileUploadArea.classList.add('dragover');
    });

    fileUploadArea.addEventListener('dragleave', () => {
        fileUploadArea.classList.remove('dragover');
    });

    fileUploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        fileUploadArea.classList.remove('dragover');

        const files = e.dataTransfer.files;
        if (files.length > 0) {
            fileInput.files = files;
            handleFileSelect(files[0]);
        }
    });

    // File input change
    fileInput.addEventListener('change', (e) => {
        if (e.target.files.length > 0) {
            handleFileSelect(e.target.files[0]);
        }
    });

    // Handle file selection
    function handleFileSelect(file) {
        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = (e) => {
                previewImg.src = e.target.result;
                imagePreview.style.display = 'block';
                fileUploadArea.style.display = 'none';
            };
            reader.readAsDataURL(file);
        }
    }

    // Remove image
    removeImageBtn.addEventListener('click', () => {
        fileInput.value = '';
        imagePreview.style.display = 'none';
        fileUploadArea.style.display = 'block';
    });



    // Multiple Images Upload Functionality
    const multipleUploadZone = document.getElementById('multipleUploadZone');
    const multipleImagesInput = document.getElementById('multiple_images');
    const imagesGallery = document.getElementById('imagesGallery');
    const imagesList = document.getElementById('imagesList');
    const clearAllImagesBtn = document.getElementById('clearAllImages');

    let selectedImages = [];
    let imageCounter = 0;

    // Multiple images upload zone click
    multipleUploadZone.addEventListener('click', () => {
        multipleImagesInput.click();
    });

    // Multiple images drag and drop
    multipleUploadZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        multipleUploadZone.classList.add('drag-over');
    });

    multipleUploadZone.addEventListener('dragleave', (e) => {
        e.preventDefault();
        multipleUploadZone.classList.remove('drag-over');
    });

    multipleUploadZone.addEventListener('drop', (e) => {
        e.preventDefault();
        multipleUploadZone.classList.remove('drag-over');

        const files = Array.from(e.dataTransfer.files);
        handleMultipleFiles(files);
    });

    // Multiple images input change
    multipleImagesInput.addEventListener('change', (e) => {
        const files = Array.from(e.target.files);
        handleMultipleFiles(files);
    });

    // Clear all images
    clearAllImagesBtn.addEventListener('click', () => {
        if (confirm('Bạn có chắc muốn xóa tất cả hình ảnh?')) {
            selectedImages = [];
            updateImagesGallery();
            multipleImagesInput.value = '';
        }
    });

    async function handleMultipleFiles(files) {
        const validFiles = files.filter(file => {
            // Check file type
            if (!file.type.startsWith('image/')) {
                alert(`File ${file.name} không phải là hình ảnh`);
                return false;
            }

            // Check file size (10MB before compression)
            if (file.size > 10 * 1024 * 1024) {
                alert(`File ${file.name} quá lớn. Tối đa 10MB`);
                return false;
            }

            return true;
        });

        // Check total files limit (10 files)
        if (selectedImages.length + validFiles.length > 10) {
            alert('Tối đa 10 hình ảnh. Vui lòng chọn ít hơn.');
            return;
        }

        // Process files with compression
        for (const file of validFiles) {
            try {
                // Show loading state
                const loadingImageData = {
                    id: ++imageCounter,
                    file: file,
                    name: file.name,
                    size: formatFileSize(file.size),
                    url: URL.createObjectURL(file),
                    loading: true
                };

                selectedImages.push(loadingImageData);
                updateImagesGallery();

                // Compress image
                const compressedFile = await compressImage(file);

                // Update with compressed file
                const imageIndex = selectedImages.findIndex(img => img.id === loadingImageData.id);
                if (imageIndex !== -1) {
                    selectedImages[imageIndex] = {
                        id: loadingImageData.id,
                        file: compressedFile,
                        name: file.name,
                        size: formatFileSize(compressedFile.size),
                        originalSize: formatFileSize(file.size),
                        url: URL.createObjectURL(compressedFile),
                        compressed: true,
                        loading: false
                    };

                    updateImagesGallery();
                }
            } catch (error) {
                console.error('Error compressing image:', error);
                // Remove loading item on error
                selectedImages = selectedImages.filter(img => img.id !== imageCounter);
                alert(`Lỗi khi xử lý file ${file.name}`);
            }
        }

        updateMultipleImagesInput();
    }

    function updateImagesGallery() {
        if (selectedImages.length === 0) {
            imagesGallery.style.display = 'none';
            document.querySelector('.multiple-images-upload-area').classList.remove('has-files');
            return;
        }

        imagesGallery.style.display = 'block';
        document.querySelector('.multiple-images-upload-area').classList.add('has-files');

        imagesList.innerHTML = '';

        selectedImages.forEach((imageData, index) => {
            const imageItem = document.createElement('div');
            imageItem.className = 'image-item';
            imageItem.draggable = true;
            imageItem.dataset.index = index;

            // Add loading class if needed
            if (imageData.loading) {
                imageItem.classList.add('loading');
            }

            imageItem.innerHTML = `
                <img src="${imageData.url}" alt="${imageData.name}" class="image-preview-thumb">
                ${imageData.loading ? '<div class="loading-overlay"><i class="fas fa-spinner fa-spin"></i></div>' : ''}
                <div class="image-overlay">
                    <div class="image-actions">
                        <button type="button" class="image-action-btn view" onclick="viewImage('${imageData.url}', '${imageData.name}')" ${imageData.loading ? 'disabled' : ''}>
                            <i class="fas fa-eye"></i>
                        </button>
                        <button type="button" class="image-action-btn delete" onclick="removeImage(${imageData.id})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                <div class="image-info">
                    <div class="image-name">${imageData.name}</div>
                    <div class="image-size">
                        ${imageData.compressed && imageData.originalSize ?
                            `<span class="compressed-size">${imageData.size}</span>
                             <span class="original-size">từ ${imageData.originalSize}</span>
                             <span class="compression-badge"><i class="fas fa-compress-alt"></i> Nén</span>`
                            : imageData.size}
                    </div>
                    ${imageData.loading ? '<div class="processing-text">Đang xử lý...</div>' : ''}
                </div>
            `;

            // Add drag and drop for reordering
            imageItem.addEventListener('dragstart', handleDragStart);
            imageItem.addEventListener('dragover', handleDragOver);
            imageItem.addEventListener('drop', handleDrop);
            imageItem.addEventListener('dragend', handleDragEnd);

            imagesList.appendChild(imageItem);
        });
    }

    function updateMultipleImagesInput() {
        // Create a new FileList with selected files
        const dt = new DataTransfer();
        selectedImages.forEach(imageData => {
            dt.items.add(imageData.file);
        });
        multipleImagesInput.files = dt.files;
    }

    // Global functions for onclick handlers
    window.viewImage = function(url, name) {
        const modal = document.createElement('div');
        modal.className = 'modal fade';
        modal.innerHTML = `
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">${name}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center">
                        <img src="${url}" alt="${name}" class="img-fluid">
                    </div>
                </div>
            </div>
        `;

        document.body.appendChild(modal);
        const bsModal = new bootstrap.Modal(modal);
        bsModal.show();

        modal.addEventListener('hidden.bs.modal', () => {
            document.body.removeChild(modal);
        });
    };

    window.removeImage = function(imageId) {
        selectedImages = selectedImages.filter(img => img.id !== imageId);
        updateImagesGallery();
        updateMultipleImagesInput();
    };

    // Drag and drop for reordering
    let draggedIndex = null;

    function handleDragStart(e) {
        draggedIndex = parseInt(e.target.dataset.index);
        e.target.classList.add('dragging');
    }

    function handleDragOver(e) {
        e.preventDefault();
    }

    function handleDrop(e) {
        e.preventDefault();
        const dropIndex = parseInt(e.target.closest('.image-item').dataset.index);

        if (draggedIndex !== null && draggedIndex !== dropIndex) {
            // Reorder images
            const draggedImage = selectedImages[draggedIndex];
            selectedImages.splice(draggedIndex, 1);
            selectedImages.splice(dropIndex, 0, draggedImage);

            updateImagesGallery();
            updateMultipleImagesInput();
        }
    }

    function handleDragEnd(e) {
        e.target.classList.remove('dragging');
        draggedIndex = null;
    }

    // Image compression function
    function compressImage(file, maxWidth = 1920, maxHeight = 1080, quality = 0.8) {
        return new Promise((resolve, reject) => {
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');
            const img = new Image();

            img.onload = function() {
                // Calculate new dimensions
                let { width, height } = calculateDimensions(img.width, img.height, maxWidth, maxHeight);

                // Set canvas dimensions
                canvas.width = width;
                canvas.height = height;

                // Draw and compress
                ctx.drawImage(img, 0, 0, width, height);

                // Convert to blob
                canvas.toBlob((blob) => {
                    if (blob) {
                        // Create new file with compressed data
                        const compressedFile = new File([blob], file.name, {
                            type: file.type,
                            lastModified: Date.now()
                        });
                        resolve(compressedFile);
                    } else {
                        reject(new Error('Canvas to Blob conversion failed'));
                    }
                }, file.type, quality);
            };

            img.onerror = () => reject(new Error('Image load failed'));
            img.src = URL.createObjectURL(file);
        });
    }

    // Calculate optimal dimensions while maintaining aspect ratio
    function calculateDimensions(originalWidth, originalHeight, maxWidth, maxHeight) {
        let width = originalWidth;
        let height = originalHeight;

        // If image is smaller than max dimensions, don't upscale
        if (width <= maxWidth && height <= maxHeight) {
            return { width, height };
        }

        // Calculate scaling factor
        const widthRatio = maxWidth / width;
        const heightRatio = maxHeight / height;
        const ratio = Math.min(widthRatio, heightRatio);

        return {
            width: Math.round(width * ratio),
            height: Math.round(height * ratio)
        };
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // File Attachments Management
    let selectedAttachments = [];
    const maxAttachments = 10;
    const maxFileSize = 50 * 1024 * 1024; // 50MB

    const fileAttachmentsUploadArea = document.getElementById('fileAttachmentsUploadArea');
    const fileAttachmentsInput = document.getElementById('file_attachments');
    const attachmentsList = document.getElementById('attachmentsList');
    const filesGrid = document.getElementById('filesGrid');
    const attachmentsCount = document.getElementById('attachmentsCount');

    // File attachments upload area click
    fileAttachmentsUploadArea.addEventListener('click', () => {
        fileAttachmentsInput.click();
    });

    // File attachments drag and drop
    fileAttachmentsUploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        fileAttachmentsUploadArea.classList.add('dragover');
    });

    fileAttachmentsUploadArea.addEventListener('dragleave', () => {
        fileAttachmentsUploadArea.classList.remove('dragover');
    });

    fileAttachmentsUploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        fileAttachmentsUploadArea.classList.remove('dragover');
        const files = Array.from(e.dataTransfer.files);
        handleAttachmentFiles(files);
    });

    // File input change
    fileAttachmentsInput.addEventListener('change', (e) => {
        const files = Array.from(e.target.files);
        handleAttachmentFiles(files);
    });

    function handleAttachmentFiles(files) {
        for (const file of files) {
            if (selectedAttachments.length >= maxAttachments) {
                alert(`Tối đa ${maxAttachments} files được phép upload`);
                break;
            }

            if (file.size > maxFileSize) {
                alert(`File "${file.name}" quá lớn. Tối đa 50MB.`);
                continue;
            }

            // Check if file already exists
            if (selectedAttachments.some(f => f.name === file.name && f.size === file.size)) {
                alert(`File "${file.name}" đã được chọn.`);
                continue;
            }

            selectedAttachments.push(file);
        }

        updateAttachmentsDisplay();
    }

    function updateAttachmentsDisplay() {
        if (selectedAttachments.length === 0) {
            attachmentsList.style.display = 'none';
            fileAttachmentsUploadArea.classList.remove('has-files');
            return;
        }

        attachmentsList.style.display = 'block';
        fileAttachmentsUploadArea.classList.add('has-files');
        attachmentsCount.textContent = selectedAttachments.length;

        filesGrid.innerHTML = '';

        selectedAttachments.forEach((file, index) => {
            const fileItem = document.createElement('div');
            fileItem.className = 'file-item';

            const fileIcon = getFileIcon(file.name);
            const fileSize = formatFileSize(file.size);

            fileItem.innerHTML = `
                <div class="file-icon ${fileIcon.class}">
                    <i class="${fileIcon.icon}"></i>
                </div>
                <div class="file-info">
                    <div class="file-name" title="${file.name}">${file.name}</div>
                    <div class="file-size">${fileSize}</div>
                </div>
                <button type="button" class="file-remove" onclick="removeAttachment(${index})" title="Xóa file">
                    <i class="fas fa-times"></i>
                </button>
            `;

            filesGrid.appendChild(fileItem);
        });
    }

    function getFileIcon(filename) {
        const ext = filename.split('.').pop().toLowerCase();

        if (['pdf'].includes(ext)) {
            return { class: 'pdf', icon: 'fas fa-file-pdf' };
        } else if (['doc', 'docx'].includes(ext)) {
            return { class: 'doc', icon: 'fas fa-file-word' };
        } else if (['xls', 'xlsx'].includes(ext)) {
            return { class: 'xls', icon: 'fas fa-file-excel' };
        } else if (['ppt', 'pptx'].includes(ext)) {
            return { class: 'doc', icon: 'fas fa-file-powerpoint' };
        } else if (['dwg', 'step', 'stp', 'iges', 'igs'].includes(ext)) {
            return { class: 'cad', icon: 'fas fa-cube' };
        } else if (['jpg', 'jpeg', 'png', 'gif'].includes(ext)) {
            return { class: 'img', icon: 'fas fa-file-image' };
        } else if (['zip', 'rar', '7z'].includes(ext)) {
            return { class: 'zip', icon: 'fas fa-file-archive' };
        } else {
            return { class: 'default', icon: 'fas fa-file' };
        }
    }

    function removeAttachment(index) {
        selectedAttachments.splice(index, 1);
        updateAttachmentsDisplay();
    }

    // Wizard Management
    let currentStep = 1;
    const totalSteps = 4;

    const wizardSteps = document.querySelectorAll('.wizard-step');
    const progressSteps = document.querySelectorAll('.progress-steps .step');
    const progressFill = document.querySelector('.progress-fill');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const submitBtn = document.getElementById('submitBtn');
    const currentStepSpan = document.querySelector('.current-step');

    function updateWizardUI() {
        // Update step visibility
        wizardSteps.forEach((step, index) => {
            step.classList.toggle('active', index + 1 === currentStep);
        });

        // Update progress indicator
        progressSteps.forEach((step, index) => {
            const stepNumber = index + 1;
            step.classList.toggle('active', stepNumber === currentStep);
            step.classList.toggle('completed', stepNumber < currentStep);
        });

        // Update progress bar
        const progressPercentage = (currentStep / totalSteps) * 100;
        progressFill.style.width = progressPercentage + '%';

        // Update navigation buttons
        prevBtn.style.display = currentStep > 1 ? 'block' : 'none';
        nextBtn.style.display = currentStep < totalSteps ? 'block' : 'none';
        submitBtn.style.display = currentStep === totalSteps ? 'block' : 'none';

        // Update step info
        currentStepSpan.textContent = `Bước ${currentStep}`;

        // Scroll to top of form
        document.querySelector('.wizard-progress').scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }

    function validateCurrentStep() {
        const currentStepElement = document.querySelector(`.wizard-step[data-step="${currentStep}"]`);
        const requiredFields = currentStepElement.querySelectorAll('[required]');
        let isValid = true;

        // Clear previous validation states
        currentStepElement.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        currentStepElement.querySelectorAll('.invalid-feedback').forEach(el => el.remove());

        // Validate required fields in current step
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                showFieldError(field, 'Trường này là bắt buộc.');
                isValid = false;
            }
        });

        // Step-specific validation
        if (currentStep === 1) {
            // Validate title
            const title = document.getElementById('title');
            if (title.value.trim().length < 5) {
                showFieldError(title, 'Tiêu đề phải có ít nhất 5 ký tự.');
                isValid = false;
            }

            // Validate description
            const description = document.getElementById('description');
            const descriptionText = description.value.replace(/<[^>]*>/g, '').trim();
            if (descriptionText.length < 50) {
                showFieldError(description, 'Mô tả phải có ít nhất 50 ký tự.');
                isValid = false;
            }
        } else if (currentStep === 2) {
            // Validate cover image
            const coverImage = document.getElementById('cover_image');
            if (!coverImage.files.length) {
                showFieldError(coverImage, 'Hình ảnh đại diện là bắt buộc.');
                isValid = false;
            }
        } else if (currentStep === 3) {
            // Validate technical information
            const softwareUsed = document.querySelectorAll('input[name="software_used[]"]:checked');
            const materials = document.getElementById('materials');
            const manufacturingProcess = document.getElementById('manufacturing_process');

            if (softwareUsed.length === 0 && !materials.value.trim() && !manufacturingProcess.value) {
                const technicalSection = currentStepElement.querySelector('.form-section h5');
                if (technicalSection) {
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'alert alert-danger mt-2';
                    errorDiv.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i>Vui lòng điền ít nhất một thông tin kỹ thuật.';
                    technicalSection.parentNode.appendChild(errorDiv);
                }
                isValid = false;
            }
        }

        return isValid;
    }

    // Wizard navigation event listeners
    nextBtn.addEventListener('click', () => {
        if (validateCurrentStep() && currentStep < totalSteps) {
            currentStep++;
            updateWizardUI();
        }
    });

    prevBtn.addEventListener('click', () => {
        if (currentStep > 1) {
            currentStep--;
            updateWizardUI();
        }
    });

    // Progress step click navigation
    progressSteps.forEach((step, index) => {
        step.addEventListener('click', () => {
            const targetStep = index + 1;
            if (targetStep < currentStep || (targetStep === currentStep + 1 && validateCurrentStep())) {
                currentStep = targetStep;
                updateWizardUI();
            }
        });
    });

    // Initialize wizard
    updateWizardUI();

    // Preview Functionality
    const previewBtn = document.getElementById('previewBtn');
    const previewModal = new bootstrap.Modal(document.getElementById('previewModal'));
    const previewContainer = document.getElementById('previewContainer');
    const editFromPreview = document.getElementById('editFromPreview');

    function generatePreviewContent() {
        // Collect form data
        const formData = {
            title: document.getElementById('title')?.value || '',
            description: tinymce.get('description')?.getContent() || '',
            location: document.getElementById('location')?.value || '',
            usage: document.getElementById('usage')?.value || '',
            coverImage: document.getElementById('cover_image')?.files[0],
            multipleImages: selectedImages || [],
            fileAttachments: selectedAttachments || [],
            softwareUsed: Array.from(document.querySelectorAll('input[name="software_used[]"]:checked')).map(cb => cb.value),
            materials: document.getElementById('materials')?.value || '',
            manufacturingProcess: document.getElementById('manufacturing_process')?.value || '',
            complexityLevel: document.querySelector('input[name="complexity_level"]:checked')?.value || '',
            industryApplication: document.getElementById('industry_application_enhanced')?.value || document.getElementById('industry_application')?.value || '',
            projectScale: document.getElementById('floors')?.value || '',
            category: document.getElementById('category')?.value || '',
            hasTutorial: document.getElementById('has_tutorial')?.checked || false,
            hasCalculations: document.getElementById('has_calculations')?.checked || false,
            hasCadFiles: document.getElementById('has_cad_files')?.checked || false,
            isPublic: document.getElementById('is_public')?.checked || false,
            allowDownloads: document.getElementById('allow_downloads')?.checked || false,
            allowComments: document.getElementById('allow_comments')?.checked || false,
            // Advanced technical fields
            technicalSpecs: collectTechnicalSpecs(),
            learningObjectives: collectLearningObjectives()
        };

        return generatePreviewHTML(formData);
    }

    function collectTechnicalSpecs() {
        const specs = [];
        const specRows = document.querySelectorAll('.technical-spec-row');

        specRows.forEach(row => {
            const nameInput = row.querySelector('input[name*="[name]"]');
            const valueInput = row.querySelector('input[name*="[value]"]');
            const unitInput = row.querySelector('input[name*="[unit]"]');

            if (nameInput && valueInput && nameInput.value.trim() && valueInput.value.trim()) {
                specs.push({
                    name: nameInput.value.trim(),
                    value: valueInput.value.trim(),
                    unit: unitInput ? unitInput.value.trim() : ''
                });
            }
        });

        return specs;
    }

    function collectLearningObjectives() {
        const objectives = [];
        const objectiveRows = document.querySelectorAll('.learning-objective-row');

        objectiveRows.forEach(row => {
            const input = row.querySelector('input[name*="learning_objectives"]');
            if (input && input.value.trim()) {
                objectives.push(input.value.trim());
            }
        });

        return objectives;
    }

    function generatePreviewHTML(data) {
        const coverImageUrl = data.coverImage ? URL.createObjectURL(data.coverImage) : null;

        return `
            <div class="preview-showcase">
                <!-- Header Section -->
                <div class="preview-header">
                    <h1 class="preview-title">${data.title || 'Tiêu đề showcase'}</h1>

                    <div class="preview-meta">
                        ${data.location ? `
                            <div class="preview-meta-item">
                                <i class="fas fa-map-marker-alt"></i>
                                ${data.location}
                            </div>
                        ` : ''}
                        ${data.usage ? `
                            <div class="preview-meta-item">
                                <i class="fas fa-tools"></i>
                                ${data.usage}
                            </div>
                        ` : ''}
                        ${data.complexityLevel ? `
                            <div class="preview-meta-item">
                                <i class="fas fa-layer-group"></i>
                                ${getComplexityLevelText(data.complexityLevel)}
                            </div>
                        ` : ''}
                        ${data.projectScale ? `
                            <div class="preview-meta-item">
                                <i class="fas fa-building"></i>
                                ${getProjectScaleText(data.projectScale)}
                            </div>
                        ` : ''}
                    </div>

                    ${coverImageUrl ? `
                        <img src="${coverImageUrl}" alt="Cover Image" class="preview-cover-image">
                    ` : ''}
                </div>

                <!-- Content Section -->
                <div class="preview-content">
                    ${data.description ? `
                        <div class="preview-section">
                            <h3 class="preview-section-title">
                                <i class="fas fa-align-left"></i>
                                Mô tả dự án
                            </h3>
                            <div class="preview-description">${data.description}</div>
                        </div>
                    ` : ''}

                    ${generateTechnicalInfoSection(data)}
                    ${generateProjectFeaturesSection(data)}
                    ${generateMediaGallerySection(data)}
                    ${generateFileAttachmentsSection(data)}
                    ${generateSharingSettingsSection(data)}
                </div>
            </div>
        `;
    }

    function generateTechnicalInfoSection(data) {
        const technicalItems = [];

        if (data.softwareUsed.length > 0) {
            technicalItems.push({
                label: 'Phần mềm sử dụng',
                value: data.softwareUsed.join(', ')
            });
        }

        if (data.materials) {
            technicalItems.push({
                label: 'Vật liệu chính',
                value: data.materials
            });
        }

        if (data.manufacturingProcess) {
            technicalItems.push({
                label: 'Quy trình sản xuất',
                value: getManufacturingProcessText(data.manufacturingProcess)
            });
        }

        if (data.industryApplication) {
            technicalItems.push({
                label: 'Ứng dụng ngành',
                value: getIndustryApplicationText(data.industryApplication)
            });
        }

        // Add technical specifications
        if (data.technicalSpecs && data.technicalSpecs.length > 0) {
            const validSpecs = data.technicalSpecs.filter(spec => spec.name && spec.value);
            if (validSpecs.length > 0) {
                technicalItems.push({
                    label: 'Thông số kỹ thuật',
                    value: validSpecs.map(spec =>
                        `${spec.name}: ${spec.value}${spec.unit ? ' ' + spec.unit : ''}`
                    ).join(', ')
                });
            }
        }

        if (technicalItems.length === 0 && (!data.learningObjectives || data.learningObjectives.length === 0)) return '';

        let technicalSection = '';
        if (technicalItems.length > 0) {
            technicalSection = `
                <div class="preview-technical-grid">
                    ${technicalItems.map(item => `
                        <div class="preview-technical-item">
                            <div class="preview-technical-label">${item.label}</div>
                            <div class="preview-technical-value">${item.value}</div>
                        </div>
                    `).join('')}
                </div>
            `;
        }

        // Add learning objectives section
        let learningSection = '';
        if (data.learningObjectives && data.learningObjectives.length > 0) {
            const validObjectives = data.learningObjectives.filter(obj => obj && obj.trim());
            if (validObjectives.length > 0) {
                learningSection = `
                    <div class="preview-learning-objectives mt-3">
                        <h4 class="preview-subsection-title">
                            <i class="fas fa-graduation-cap"></i>
                            Mục tiêu học tập
                        </h4>
                        <ul class="preview-objectives-list">
                            ${validObjectives.map(objective => `
                                <li class="preview-objective-item">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    ${objective}
                                </li>
                            `).join('')}
                        </ul>
                    </div>
                `;
            }
        }

        return `
            <div class="preview-section">
                <h3 class="preview-section-title">
                    <i class="fas fa-cogs"></i>
                    Thông tin kỹ thuật
                </h3>
                ${technicalSection}
                ${learningSection}
            </div>
        `;
    }

    function generateProjectFeaturesSection(data) {
        const features = [];

        if (data.hasTutorial) {
            features.push({ icon: 'fas fa-graduation-cap', text: 'Hướng dẫn step-by-step' });
        }

        if (data.hasCalculations) {
            features.push({ icon: 'fas fa-calculator', text: 'Tính toán kỹ thuật' });
        }

        if (data.hasCadFiles) {
            features.push({ icon: 'fas fa-cube', text: 'File CAD đính kèm' });
        }

        if (features.length === 0) return '';

        return `
            <div class="preview-section">
                <h3 class="preview-section-title">
                    <i class="fas fa-star"></i>
                    Tính năng dự án
                </h3>
                <div class="preview-features">
                    ${features.map(feature => `
                        <div class="preview-feature-badge">
                            <i class="${feature.icon}"></i>
                            ${feature.text}
                        </div>
                    `).join('')}
                </div>
            </div>
        `;
    }

    function generateMediaGallerySection(data) {
        if (data.multipleImages.length === 0) return '';

        return `
            <div class="preview-section">
                <h3 class="preview-section-title">
                    <i class="fas fa-images"></i>
                    Thư viện hình ảnh (${data.multipleImages.length} ảnh)
                </h3>
                <div class="preview-gallery">
                    ${data.multipleImages.map(image => `
                        <div class="preview-gallery-item">
                            <img src="${URL.createObjectURL(image)}" alt="Gallery Image" class="preview-gallery-image">
                        </div>
                    `).join('')}
                </div>
            </div>
        `;
    }

    function generateFileAttachmentsSection(data) {
        if (data.fileAttachments.length === 0) return '';

        return `
            <div class="preview-section">
                <h3 class="preview-section-title">
                    <i class="fas fa-paperclip"></i>
                    File đính kèm (${data.fileAttachments.length} files)
                </h3>
                <div class="preview-attachments">
                    ${data.fileAttachments.map(file => `
                        <div class="preview-attachment-item">
                            <div class="preview-attachment-icon">
                                <i class="${getFileIcon(file.name)}"></i>
                            </div>
                            <div class="preview-attachment-info">
                                <div class="preview-attachment-name">${file.name}</div>
                                <div class="preview-attachment-size">${formatFileSize(file.size)}</div>
                            </div>
                        </div>
                    `).join('')}
                </div>
            </div>
        `;
    }

    function generateSharingSettingsSection(data) {
        return `
            <div class="preview-section">
                <h3 class="preview-section-title">
                    <i class="fas fa-share-alt"></i>
                    Cài đặt chia sẻ
                </h3>
                <div class="preview-sharing-settings">
                    <div class="preview-sharing-item">
                        <div class="preview-sharing-icon ${data.isPublic ? 'enabled' : 'disabled'}">
                            <i class="fas ${data.isPublic ? 'fa-check' : 'fa-times'}"></i>
                        </div>
                        <span>Công khai: ${data.isPublic ? 'Có' : 'Không'}</span>
                    </div>
                    <div class="preview-sharing-item">
                        <div class="preview-sharing-icon ${data.allowDownloads ? 'enabled' : 'disabled'}">
                            <i class="fas ${data.allowDownloads ? 'fa-check' : 'fa-times'}"></i>
                        </div>
                        <span>Cho phép tải xuống: ${data.allowDownloads ? 'Có' : 'Không'}</span>
                    </div>
                    <div class="preview-sharing-item">
                        <div class="preview-sharing-icon ${data.allowComments ? 'enabled' : 'disabled'}">
                            <i class="fas ${data.allowComments ? 'fa-check' : 'fa-times'}"></i>
                        </div>
                        <span>Cho phép bình luận: ${data.allowComments ? 'Có' : 'Không'}</span>
                    </div>
                </div>
            </div>
        `;
    }

    // Helper functions
    function getComplexityLevelText(level) {
        const levels = {
            'beginner': 'Người mới bắt đầu',
            'intermediate': 'Trung cấp',
            'advanced': 'Nâng cao',
            'expert': 'Chuyên gia'
        };
        return levels[level] || level;
    }

    function getProjectScaleText(scale) {
        const scales = {
            '1': 'Cá nhân',
            '2': 'Nhóm nhỏ',
            '3': 'Dự án vừa',
            '4': 'Dự án lớn',
            '5': 'Dự án khổng lồ'
        };
        return scales[scale] || scale;
    }

    function getManufacturingProcessText(process) {
        const processes = {
            'CNC Machining': 'CNC Machining',
            'Manual Machining': 'Gia công thủ công',
            'Grinding': 'Mài, đánh bóng',
            'EDM': 'Gia công tia lửa điện',
            '3D Printing': 'In 3D',
            'Metal 3D Printing': 'In 3D kim loại',
            'Casting': 'Đúc kim loại',
            'Forging': 'Rèn, dập',
            'Sheet Metal': 'Gia công tôn',
            'Stamping': 'Dập tấm',
            'Deep Drawing': 'Dập sâu',
            'Welding': 'Hàn',
            'Brazing': 'Hàn đồng',
            'Soldering': 'Hàn thiếc',
            'Riveting': 'Tán đinh',
            'Injection Molding': 'Ép phun nhựa',
            'Blow Molding': 'Thổi phồng',
            'Compression Molding': 'Ép nén',
            'Assembly': 'Lắp ráp cơ khí',
            'Surface Treatment': 'Xử lý bề mặt',
            'Heat Treatment': 'Nhiệt luyện',
            'Hybrid Process': 'Quy trình kết hợp',
            'Other': 'Quy trình khác'
        };
        return processes[process] || process;
    }

    function getIndustryApplicationText(industry) {
        const industryMap = {
            'automotive': 'Ô tô - Automotive',
            'aerospace': 'Hàng không vũ trụ - Aerospace',
            'shipbuilding': 'Đóng tàu - Shipbuilding',
            'machinery': 'Máy móc công nghiệp - Industrial Machinery',
            'electronics': 'Điện tử - Electronics Manufacturing',
            'oil_gas': 'Dầu khí - Oil & Gas',
            'renewable_energy': 'Năng lượng tái tạo - Renewable Energy',
            'power_generation': 'Phát điện - Power Generation',
            'construction': 'Xây dựng - Construction',
            'chemical': 'Hóa chất - Chemical Processing',
            'pharmaceutical': 'Dược phẩm - Pharmaceutical',
            'food_beverage': 'Thực phẩm đồ uống - Food & Beverage',
            'textile': 'Dệt may - Textile',
            'robotics': 'Robot học - Robotics',
            'automation': 'Tự động hóa - Industrial Automation',
            'iot_industry4': 'IoT & Industry 4.0',
            'research_development': 'Nghiên cứu phát triển - R&D',
            'general_manufacturing': 'Sản xuất tổng quát - General Manufacturing',
            'maintenance_repair': 'Bảo trì sửa chữa - Maintenance & Repair',
            'education_training': 'Giáo dục đào tạo - Education & Training',
            'other': 'Khác - Other'
        };
        return industryMap[industry] || industry;
    }

    function getFileIcon(filename) {
        const extension = filename.split('.').pop().toLowerCase();
        const iconMap = {
            'pdf': 'fas fa-file-pdf',
            'doc': 'fas fa-file-word',
            'docx': 'fas fa-file-word',
            'xls': 'fas fa-file-excel',
            'xlsx': 'fas fa-file-excel',
            'ppt': 'fas fa-file-powerpoint',
            'pptx': 'fas fa-file-powerpoint',
            'dwg': 'fas fa-drafting-compass',
            'step': 'fas fa-cube',
            'stp': 'fas fa-cube',
            'iges': 'fas fa-cube',
            'igs': 'fas fa-cube',
            'zip': 'fas fa-file-archive',
            'rar': 'fas fa-file-archive',
            '7z': 'fas fa-file-archive'
        };
        return iconMap[extension] || 'fas fa-file';
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // Preview Event Handlers
    previewBtn.addEventListener('click', () => {
        const previewContent = generatePreviewContent();
        previewContainer.innerHTML = previewContent;
        previewModal.show();
    });

    editFromPreview.addEventListener('click', () => {
        previewModal.hide();
        // Focus on the first field that needs attention
        const firstEmptyField = document.querySelector('.wizard-step.active input:not([value]), .wizard-step.active textarea:not([value])');
        if (firstEmptyField) {
            firstEmptyField.focus();
        }
    });

    // Clean up object URLs when modal is hidden
    document.getElementById('previewModal').addEventListener('hidden.bs.modal', () => {
        // Clean up any object URLs to prevent memory leaks
        const images = previewContainer.querySelectorAll('img[src^="blob:"]');
        images.forEach(img => {
            URL.revokeObjectURL(img.src);
        });
    });

    // Advanced Technical Fields Management
    let specIndex = 1;
    let objectiveIndex = 1;

    // Technical Specifications Management
    const addSpecBtn = document.getElementById('add-spec-btn');
    const specsContainer = document.getElementById('technical-specs-container');

    addSpecBtn.addEventListener('click', () => {
        const newSpecRow = createSpecRow(specIndex);
        specsContainer.appendChild(newSpecRow);
        specIndex++;
        updateSpecRemoveButtons();
    });

    function createSpecRow(index) {
        const div = document.createElement('div');
        div.className = 'technical-spec-row mb-2';
        div.setAttribute('data-index', index);

        div.innerHTML = `
            <div class="row">
                <div class="col-md-4">
                    <input type="text"
                           class="form-control"
                           name="technical_specs[${index}][name]"
                           placeholder="Tên thông số (VD: Kích thước)">
                </div>
                <div class="col-md-4">
                    <input type="text"
                           class="form-control"
                           name="technical_specs[${index}][value]"
                           placeholder="Giá trị (VD: 1000x500x300)">
                </div>
                <div class="col-md-3">
                    <input type="text"
                           class="form-control"
                           name="technical_specs[${index}][unit]"
                           placeholder="Đơn vị (VD: mm)">
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-outline-danger btn-sm remove-spec-btn">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        `;

        // Add remove event listener
        const removeBtn = div.querySelector('.remove-spec-btn');
        removeBtn.addEventListener('click', () => {
            div.remove();
            updateSpecRemoveButtons();
        });

        return div;
    }

    function updateSpecRemoveButtons() {
        const specRows = specsContainer.querySelectorAll('.technical-spec-row');
        specRows.forEach((row, index) => {
            const removeBtn = row.querySelector('.remove-spec-btn');
            if (specRows.length > 1) {
                removeBtn.style.display = 'block';
            } else {
                removeBtn.style.display = 'none';
            }
        });
    }

    // Spec suggestions
    document.querySelectorAll('.spec-suggestion').forEach(btn => {
        btn.addEventListener('click', () => {
            const name = btn.getAttribute('data-name');
            const unit = btn.getAttribute('data-unit');

            // Find the last spec row and fill it, or create a new one
            const lastRow = specsContainer.querySelector('.technical-spec-row:last-child');
            const nameInput = lastRow.querySelector('input[name*="[name]"]');
            const unitInput = lastRow.querySelector('input[name*="[unit]"]');

            if (nameInput.value === '') {
                nameInput.value = name;
                unitInput.value = unit;
                nameInput.focus();
            } else {
                // Create new row
                const newRow = createSpecRow(specIndex);
                specsContainer.appendChild(newRow);
                specIndex++;

                const newNameInput = newRow.querySelector('input[name*="[name]"]');
                const newUnitInput = newRow.querySelector('input[name*="[unit]"]');
                newNameInput.value = name;
                newUnitInput.value = unit;

                updateSpecRemoveButtons();
            }
        });
    });

    // Learning Objectives Management
    const addObjectiveBtn = document.getElementById('add-objective-btn');
    const objectivesContainer = document.getElementById('learning-objectives-container');

    addObjectiveBtn.addEventListener('click', () => {
        const newObjectiveRow = createObjectiveRow(objectiveIndex);
        objectivesContainer.appendChild(newObjectiveRow);
        objectiveIndex++;
        updateObjectiveRemoveButtons();
    });

    function createObjectiveRow(index) {
        const div = document.createElement('div');
        div.className = 'learning-objective-row mb-2';
        div.setAttribute('data-index', index);

        div.innerHTML = `
            <div class="input-group">
                <span class="input-group-text">
                    <i class="fas fa-check-circle text-success"></i>
                </span>
                <input type="text"
                       class="form-control"
                       name="learning_objectives[${index}]"
                       placeholder="VD: Hiểu nguyên lý thiết kế cầu trục">
                <button type="button" class="btn btn-outline-danger remove-objective-btn">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;

        // Add remove event listener
        const removeBtn = div.querySelector('.remove-objective-btn');
        removeBtn.addEventListener('click', () => {
            div.remove();
            updateObjectiveRemoveButtons();
        });

        return div;
    }

    function updateObjectiveRemoveButtons() {
        const objectiveRows = objectivesContainer.querySelectorAll('.learning-objective-row');
        objectiveRows.forEach((row, index) => {
            const removeBtn = row.querySelector('.remove-objective-btn');
            if (objectiveRows.length > 1) {
                removeBtn.style.display = 'block';
            } else {
                removeBtn.style.display = 'none';
            }
        });
    }

    // Objective suggestions
    document.querySelectorAll('.objective-suggestion').forEach(btn => {
        btn.addEventListener('click', () => {
            const objective = btn.textContent.trim();

            // Find the last objective row and fill it, or create a new one
            const lastRow = objectivesContainer.querySelector('.learning-objective-row:last-child');
            const input = lastRow.querySelector('input[name*="learning_objectives"]');

            if (input.value === '') {
                input.value = objective;
                input.focus();
            } else {
                // Create new row
                const newRow = createObjectiveRow(objectiveIndex);
                objectivesContainer.appendChild(newRow);
                objectiveIndex++;

                const newInput = newRow.querySelector('input[name*="learning_objectives"]');
                newInput.value = objective;

                updateObjectiveRemoveButtons();
            }
        });
    });

    // Initialize remove buttons visibility
    updateSpecRemoveButtons();
    updateObjectiveRemoveButtons();

    // ========================================
    // MOBILE TOUCH OPTIMIZATIONS
    // ========================================

    // Mobile-specific enhancements
    function initializeMobileOptimizations() {
        // Check if device is mobile
        const isMobile = window.innerWidth <= 576;
        const isTablet = window.innerWidth > 576 && window.innerWidth <= 991;

        if (isMobile || isTablet) {
            // Add mobile class to body
            document.body.classList.add(isMobile ? 'mobile-device' : 'tablet-device');

            // Optimize touch interactions
            optimizeTouchInteractions();

            // Add mobile-specific event listeners
            addMobileEventListeners();

            // Optimize form scrolling
            optimizeFormScrolling();

            // Add haptic feedback for supported devices
            addHapticFeedback();
        }
    }

    function optimizeTouchInteractions() {
        // Add touch-friendly classes to interactive elements
        const interactiveElements = document.querySelectorAll(
            '.btn, .form-control, .form-select, .form-check-input, .step, .spec-suggestion, .objective-suggestion'
        );

        interactiveElements.forEach(element => {
            element.classList.add('touch-optimized');

            // Add touch feedback
            element.addEventListener('touchstart', function() {
                this.classList.add('touch-active');
            });

            element.addEventListener('touchend', function() {
                setTimeout(() => {
                    this.classList.remove('touch-active');
                }, 150);
            });
        });

        // Optimize drag and drop for touch
        const uploadAreas = document.querySelectorAll('.upload-zone');
        uploadAreas.forEach(area => {
            // Add touch-friendly upload area styling
            area.classList.add('touch-upload-zone');

            // Improve touch target size
            area.style.minHeight = '120px';
            area.style.padding = '2rem 1rem';
        });
    }

    function addMobileEventListeners() {
        // Prevent zoom on input focus (iOS)
        const inputs = document.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                // Temporarily set font-size to 16px to prevent zoom
                if (this.style.fontSize !== '16px') {
                    this.dataset.originalFontSize = this.style.fontSize;
                    this.style.fontSize = '16px';
                }
            });

            input.addEventListener('blur', function() {
                // Restore original font-size
                if (this.dataset.originalFontSize) {
                    this.style.fontSize = this.dataset.originalFontSize;
                    delete this.dataset.originalFontSize;
                }
            });
        });

        // Add swipe navigation for wizard steps
        let touchStartX = 0;
        let touchEndX = 0;

        const wizardContainer = document.querySelector('.wizard-step.active');
        if (wizardContainer) {
            wizardContainer.addEventListener('touchstart', function(e) {
                touchStartX = e.changedTouches[0].screenX;
            });

            wizardContainer.addEventListener('touchend', function(e) {
                touchEndX = e.changedTouches[0].screenX;
                handleSwipeGesture();
            });
        }

        function handleSwipeGesture() {
            const swipeThreshold = 50;
            const swipeDistance = touchEndX - touchStartX;

            if (Math.abs(swipeDistance) > swipeThreshold) {
                if (swipeDistance > 0 && currentStep > 1) {
                    // Swipe right - go to previous step
                    prevBtn.click();
                } else if (swipeDistance < 0 && currentStep < totalSteps) {
                    // Swipe left - go to next step
                    if (validateCurrentStep()) {
                        nextBtn.click();
                    }
                }
            }
        }

        // Optimize modal for mobile
        const previewModal = document.getElementById('previewModal');
        if (previewModal) {
            previewModal.addEventListener('shown.bs.modal', function() {
                // Prevent body scroll when modal is open
                document.body.style.overflow = 'hidden';

                // Focus management for accessibility
                const firstFocusable = this.querySelector('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
                if (firstFocusable) {
                    firstFocusable.focus();
                }
            });

            previewModal.addEventListener('hidden.bs.modal', function() {
                // Restore body scroll
                document.body.style.overflow = '';
            });
        }
    }

    function optimizeFormScrolling() {
        // Smooth scroll to validation errors
        function scrollToError(element) {
            const offset = window.innerWidth <= 576 ? 100 : 80;
            const elementPosition = element.getBoundingClientRect().top + window.pageYOffset;
            const offsetPosition = elementPosition - offset;

            window.scrollTo({
                top: offsetPosition,
                behavior: 'smooth'
            });
        }

        // Override default validation scroll behavior
        const originalValidateCurrentStep = validateCurrentStep;
        validateCurrentStep = function() {
            const isValid = originalValidateCurrentStep();

            if (!isValid) {
                // Find first error and scroll to it
                const firstError = document.querySelector('.is-invalid, .alert-danger');
                if (firstError) {
                    setTimeout(() => scrollToError(firstError), 100);
                }
            }

            return isValid;
        };

        // Optimize wizard navigation scrolling
        const originalUpdateWizardUI = updateWizardUI;
        updateWizardUI = function() {
            originalUpdateWizardUI();

            // Scroll to top of form on step change (mobile)
            if (window.innerWidth <= 576) {
                setTimeout(() => {
                    const wizardProgress = document.querySelector('.wizard-progress');
                    if (wizardProgress) {
                        wizardProgress.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                }, 100);
            }
        };
    }

    function addHapticFeedback() {
        // Add haptic feedback for supported devices
        if ('vibrate' in navigator) {
            // Light haptic feedback for button taps
            const buttons = document.querySelectorAll('.btn, .step');
            buttons.forEach(button => {
                button.addEventListener('click', function() {
                    navigator.vibrate(10); // Very light vibration
                });
            });

            // Medium haptic feedback for step navigation
            nextBtn.addEventListener('click', function() {
                navigator.vibrate(20);
            });

            prevBtn.addEventListener('click', function() {
                navigator.vibrate(20);
            });

            // Strong haptic feedback for form submission
            const submitBtn = document.getElementById('submitBtn');
            if (submitBtn) {
                submitBtn.addEventListener('click', function() {
                    navigator.vibrate(50);
                });
            }
        }
    }

    // Initialize mobile optimizations
    initializeMobileOptimizations();

    // Re-initialize on window resize
    window.addEventListener('resize', function() {
        // Debounce resize events
        clearTimeout(window.resizeTimeout);
        window.resizeTimeout = setTimeout(function() {
            initializeMobileOptimizations();
        }, 250);
    });

    // ========================================
    // ENHANCED UI COMPONENTS FUNCTIONALITY
    // ========================================

    // Enhanced Multi-Select Dropdown
    function initializeEnhancedMultiSelect() {
        const container = document.getElementById('softwareMultiSelect');
        if (!container) return;

        const searchInput = document.getElementById('softwareSearch');
        const dropdown = document.getElementById('softwareDropdown');
        const toggle = document.getElementById('softwareToggle');
        const selectedContainer = document.getElementById('selectedSoftware');
        const hiddenInputsContainer = document.getElementById('softwareHiddenInputs');
        const options = dropdown.querySelectorAll('.multiselect-option');

        let selectedValues = [];

        // Initialize from old values
        const oldValues = @json(old('software_used', []));
        if (oldValues && oldValues.length > 0) {
            oldValues.forEach(value => {
                selectOption(value);
            });
        }

        // Toggle dropdown
        toggle.addEventListener('click', function() {
            dropdown.classList.toggle('show');
            if (dropdown.classList.contains('show')) {
                searchInput.focus();
            }
        });

        // Search functionality
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            options.forEach(option => {
                const text = option.querySelector('.option-text').textContent.toLowerCase();
                const desc = option.querySelector('.option-desc').textContent.toLowerCase();
                const matches = text.includes(searchTerm) || desc.includes(searchTerm);
                option.style.display = matches ? 'flex' : 'none';
            });
        });

        // Option selection
        options.forEach(option => {
            option.addEventListener('click', function() {
                const value = this.dataset.value;
                if (selectedValues.includes(value)) {
                    deselectOption(value);
                } else {
                    selectOption(value);
                }
            });
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!container.contains(e.target)) {
                dropdown.classList.remove('show');
            }
        });

        function selectOption(value) {
            if (selectedValues.includes(value)) return;

            selectedValues.push(value);

            // Find option element
            const option = Array.from(options).find(opt => opt.dataset.value === value);
            if (option) {
                option.classList.add('selected');

                // Create selected item
                const selectedItem = document.createElement('div');
                selectedItem.className = 'selected-item';
                selectedItem.dataset.value = value;

                const icon = option.querySelector('i').cloneNode(true);
                const text = option.querySelector('.option-text').textContent;

                selectedItem.innerHTML = `
                    ${icon.outerHTML}
                    <span>${text}</span>
                    <button type="button" class="selected-item-remove" onclick="removeSoftwareItem('${value}')">
                        <i class="fas fa-times"></i>
                    </button>
                `;

                selectedContainer.appendChild(selectedItem);

                // Create hidden input
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'software_used[]';
                hiddenInput.value = value;
                hiddenInput.dataset.value = value;
                hiddenInputsContainer.appendChild(hiddenInput);
            }

            updatePlaceholder();
        }

        function deselectOption(value) {
            const index = selectedValues.indexOf(value);
            if (index > -1) {
                selectedValues.splice(index, 1);

                // Remove from UI
                const selectedItem = selectedContainer.querySelector(`[data-value="${value}"]`);
                if (selectedItem) {
                    selectedItem.remove();
                }

                // Remove hidden input
                const hiddenInput = hiddenInputsContainer.querySelector(`[data-value="${value}"]`);
                if (hiddenInput) {
                    hiddenInput.remove();
                }

                // Update option state
                const option = Array.from(options).find(opt => opt.dataset.value === value);
                if (option) {
                    option.classList.remove('selected');
                }
            }

            updatePlaceholder();
        }

        function updatePlaceholder() {
            if (selectedValues.length === 0) {
                searchInput.placeholder = 'Tìm kiếm và chọn phần mềm...';
            } else {
                searchInput.placeholder = `${selectedValues.length} phần mềm đã chọn...`;
            }
        }

        // Global function for remove buttons
        window.removeSoftwareItem = function(value) {
            deselectOption(value);
        };
    }

    // Enhanced Tag Input
    function initializeEnhancedTagInput() {
        const container = document.getElementById('materialsTagInput');
        if (!container) return;

        const inputField = document.getElementById('materialsInputField');
        const dropdown = document.getElementById('materialsSuggestions');
        const toggle = document.getElementById('materialsToggle');
        const tagsContainer = document.getElementById('materialsTags');
        const hiddenInput = document.getElementById('materials');
        const suggestionItems = dropdown.querySelectorAll('.suggestion-item');

        let tags = [];

        // Initialize from old values
        const oldValue = hiddenInput.value;
        if (oldValue) {
            const oldTags = oldValue.split(',').map(tag => tag.trim()).filter(tag => tag);
            oldTags.forEach(tag => {
                addTag(tag);
            });
        }

        // Toggle suggestions
        toggle.addEventListener('click', function() {
            dropdown.classList.toggle('show');
            if (dropdown.classList.contains('show')) {
                inputField.focus();
            }
        });

        // Add tag on Enter
        inputField.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const value = this.value.trim();
                if (value) {
                    addTag(value);
                    this.value = '';
                }
            }
        });

        // Suggestion selection
        suggestionItems.forEach(item => {
            item.addEventListener('click', function() {
                const value = this.dataset.value;
                addTag(value);
                inputField.value = '';
                dropdown.classList.remove('show');
            });
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!container.contains(e.target)) {
                dropdown.classList.remove('show');
            }
        });

        function addTag(value) {
            if (tags.includes(value)) return;

            tags.push(value);

            // Create tag element
            const tagElement = document.createElement('div');
            tagElement.className = 'tag-item';
            tagElement.dataset.value = value;

            tagElement.innerHTML = `
                <span>${value}</span>
                <button type="button" class="tag-item-remove" onclick="removeMaterialTag('${value}')">
                    <i class="fas fa-times"></i>
                </button>
            `;

            tagsContainer.appendChild(tagElement);
            updateHiddenInput();
            updatePlaceholder();
        }

        function removeTag(value) {
            const index = tags.indexOf(value);
            if (index > -1) {
                tags.splice(index, 1);

                // Remove from UI
                const tagElement = tagsContainer.querySelector(`[data-value="${value}"]`);
                if (tagElement) {
                    tagElement.remove();
                }

                updateHiddenInput();
                updatePlaceholder();
            }
        }

        function updateHiddenInput() {
            hiddenInput.value = tags.join(', ');
        }

        function updatePlaceholder() {
            if (tags.length === 0) {
                inputField.placeholder = 'Nhập vật liệu và nhấn Enter...';
            } else {
                inputField.placeholder = `${tags.length} vật liệu đã thêm...`;
            }
        }

        // Global function for remove buttons
        window.removeMaterialTag = function(value) {
            removeTag(value);
        };
    }

    // Initialize enhanced UI components
    initializeEnhancedMultiSelect();
    initializeEnhancedTagInput();

    // Enhanced validation helper functions
    function showFieldError(field, message) {
        field.classList.add('is-invalid');

        // Remove existing error message
        const existingError = field.parentNode.querySelector('.invalid-feedback');
        if (existingError) {
            existingError.remove();
        }

        // Add new error message
        const errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback';
        errorDiv.innerHTML = '<i class="fas fa-exclamation-circle me-1"></i>' + message;
        field.parentNode.appendChild(errorDiv);
    }

    // Real-time validation for title
    document.getElementById('title').addEventListener('input', function() {
        const title = this.value.trim();
        this.classList.remove('is-invalid');

        if (title.length > 0 && title.length < 5) {
            this.classList.add('is-invalid');
            showFieldError(this, 'Tiêu đề phải có ít nhất 5 ký tự.');
        } else if (title.length > 255) {
            this.classList.add('is-invalid');
            showFieldError(this, 'Tiêu đề không được vượt quá 255 ký tự.');
        } else if (title.length > 0 && !/^[a-zA-Z0-9\s\-_àáạảãâầấậẩẫăằắặẳẵèéẹẻẽêềếệểễìíịỉĩòóọỏõôồốộổỗơờớợởỡùúụủũưừứựửữỳýỵỷỹđĐ]+$/.test(title)) {
            this.classList.add('is-invalid');
            showFieldError(this, 'Tiêu đề chỉ được chứa chữ cái, số, dấu gạch ngang và khoảng trắng.');
        } else if (title.length >= 5) {
            this.classList.remove('is-invalid');
            const errorDiv = this.parentNode.querySelector('.invalid-feedback');
            if (errorDiv) errorDiv.remove();
        }
    });

    // Real-time validation for description
    document.getElementById('description').addEventListener('input', function() {
        const description = this.value.replace(/<[^>]*>/g, '').trim();
        const wordCount = description.split(/\s+/).filter(word => word.length > 0).length;

        this.classList.remove('is-invalid');

        if (description.length > 0 && description.length < 50) {
            this.classList.add('is-invalid');
            showFieldError(this, 'Mô tả phải có ít nhất 50 ký tự.');
        } else if (description.length > 0 && wordCount < 20) {
            this.classList.add('is-invalid');
            showFieldError(this, 'Mô tả cần có ít nhất 20 từ.');
        } else if (description.length > 5000) {
            this.classList.add('is-invalid');
            showFieldError(this, 'Mô tả không được vượt quá 5000 ký tự.');
        } else if (description.length >= 50 && wordCount >= 20) {
            this.classList.remove('is-invalid');
            const errorDiv = this.parentNode.querySelector('.invalid-feedback');
            if (errorDiv) errorDiv.remove();
        }
    });

    // Real-time validation for cover image
    document.getElementById('cover_image').addEventListener('change', function() {
        this.classList.remove('is-invalid');

        if (this.files.length > 0) {
            const file = this.files[0];
            const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
            const maxSize = 5 * 1024 * 1024; // 5MB

            if (!allowedTypes.includes(file.type)) {
                this.classList.add('is-invalid');
                showFieldError(this, 'Hình ảnh phải có định dạng: JPEG, PNG, JPG, GIF, WebP.');
            } else if (file.size > maxSize) {
                this.classList.add('is-invalid');
                showFieldError(this, 'Hình ảnh không được vượt quá 5MB.');
            } else {
                this.classList.remove('is-invalid');
                const errorDiv = this.parentNode.querySelector('.invalid-feedback');
                if (errorDiv) errorDiv.remove();
            }
        }
    });

    // Enhanced form validation and submission
    document.getElementById('showcaseForm').addEventListener('submit', function(e) {
        let isValid = true;
        const errors = [];

        // Clear previous validation states
        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        document.querySelectorAll('.invalid-feedback').forEach(el => el.remove());

        // Validate title
        const title = document.getElementById('title');
        if (!title.value.trim()) {
            showFieldError(title, 'Tiêu đề showcase là bắt buộc.');
            isValid = false;
        } else if (title.value.trim().length < 5) {
            showFieldError(title, 'Tiêu đề phải có ít nhất 5 ký tự.');
            isValid = false;
        } else if (title.value.length > 255) {
            showFieldError(title, 'Tiêu đề không được vượt quá 255 ký tự.');
            isValid = false;
        } else if (!/^[a-zA-Z0-9\s\-_àáạảãâầấậẩẫăằắặẳẵèéẹẻẽêềếệểễìíịỉĩòóọỏõôồốộổỗơờớợởỡùúụủũưừứựửữỳýỵỷỹđĐ]+$/.test(title.value)) {
            showFieldError(title, 'Tiêu đề chỉ được chứa chữ cái, số, dấu gạch ngang và khoảng trắng.');
            isValid = false;
        }

        // Validate description
        const description = document.getElementById('description');
        if (!description.value.trim()) {
            showFieldError(description, 'Mô tả chi tiết là bắt buộc.');
            isValid = false;
        } else {
            const descriptionText = description.value.replace(/<[^>]*>/g, '').trim();
            const wordCount = descriptionText.split(/\s+/).filter(word => word.length > 0).length;

            if (descriptionText.length < 50) {
                showFieldError(description, 'Mô tả phải có ít nhất 50 ký tự để cung cấp thông tin đầy đủ.');
                isValid = false;
            } else if (wordCount < 20) {
                showFieldError(description, 'Mô tả cần có ít nhất 20 từ để cung cấp thông tin đầy đủ.');
                isValid = false;
            } else if (descriptionText.length > 5000) {
                showFieldError(description, 'Mô tả không được vượt quá 5000 ký tự.');
                isValid = false;
            }
        }

        // Validate cover image
        const coverImage = document.getElementById('cover_image');
        if (!coverImage.files.length) {
            showFieldError(coverImage, 'Hình ảnh đại diện là bắt buộc.');
            isValid = false;
        } else {
            const file = coverImage.files[0];
            const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
            const maxSize = 5 * 1024 * 1024; // 5MB

            if (!allowedTypes.includes(file.type)) {
                showFieldError(coverImage, 'Hình ảnh phải có định dạng: JPEG, PNG, JPG, GIF, WebP.');
                isValid = false;
            } else if (file.size > maxSize) {
                showFieldError(coverImage, 'Hình ảnh không được vượt quá 5MB.');
                isValid = false;
            }
        }

        // Validate technical information (at least one field required)
        const softwareUsed = document.querySelectorAll('input[name="software_used[]"]:checked');
        const materials = document.getElementById('materials');
        const manufacturingProcess = document.getElementById('manufacturing_process');

        if (softwareUsed.length === 0 && !materials.value.trim() && !manufacturingProcess.value) {
            const technicalSection = document.querySelector('.form-section h5:contains("Thông Tin Kỹ Thuật")');
            if (technicalSection) {
                const errorDiv = document.createElement('div');
                errorDiv.className = 'alert alert-danger mt-2';
                errorDiv.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i>Vui lòng điền ít nhất một thông tin kỹ thuật (phần mềm, vật liệu hoặc quy trình sản xuất).';
                technicalSection.parentNode.appendChild(errorDiv);
            }
            isValid = false;
        }

        // Validate file attachments consistency
        const hasCadFiles = document.getElementById('has_cad_files');
        if (hasCadFiles && hasCadFiles.checked && selectedAttachments.length === 0) {
            const fileSection = document.getElementById('fileAttachmentsUploadArea');
            showFieldError(fileSection, 'Bạn đã chọn "File CAD đính kèm" nhưng chưa upload file nào.');
            isValid = false;
        }

        if (!isValid) {
            e.preventDefault();
            // Scroll to first error
            const firstError = document.querySelector('.is-invalid');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
            return;
        }

        // Handle file attachments
        if (selectedAttachments.length > 0) {
            // Clear the original file input
            fileAttachmentsInput.value = '';

            // Create a new DataTransfer object to set files
            const dt = new DataTransfer();
            selectedAttachments.forEach(file => {
                dt.items.add(file);
            });

            // Set the files to the input
            fileAttachmentsInput.files = dt.files;
        }

        // Handle multiple images
        if (selectedImages.length > 0) {
            // Clear the original multiple images input
            multipleImagesInput.value = '';

            // Create a new DataTransfer object for images
            const imageDt = new DataTransfer();
            selectedImages.forEach(imageData => {
                imageDt.items.add(imageData.file);
            });

            // Set the files to the input
            multipleImagesInput.files = imageDt.files;
        }
    });
});
</script>
@endpush
