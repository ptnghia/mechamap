/**
 * TinyMCE Unified Configuration for MechaMap
 * Provides consistent rich text editing experience across the platform
 */

class TinyMCEConfig {
    constructor() {
        this.baseConfig = {
            // Core settings
            height: 300,
            readonly: false,
            menubar: false,
            branding: false,
            toolbar_mode: 'floating',

            // Language support
            language: 'vi',
            language_url: '/js/tinymce-lang/vi.js',

            // Content styling
            content_style: `
                body {
                    font-family: "Roboto Condensed", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
                    font-size: 14px;
                    line-height: 1.6;
                    color: #333;
                    margin: 8px;
                    background: #fff;
                }
                blockquote {
                    border-left: 4px solid #007bff;
                    margin: 16px 0;
                    padding: 12px 16px;
                    background: #f8f9fa;
                    font-style: italic;
                    border-radius: 4px;
                }
                code {
                    background: #f1f3f4;
                    padding: 2px 6px;
                    border-radius: 4px;
                    font-family: "Monaco", "Consolas", "Courier New", monospace;
                    font-size: 13px;
                    color: #e83e8c;
                }
                pre {
                    background: #f8f9fa;
                    padding: 12px;
                    border-radius: 6px;
                    overflow-x: auto;
                    border: 1px solid #e9ecef;
                    font-family: "Monaco", "Consolas", "Courier New", monospace;
                }
                img {
                    max-width: 100%;
                    height: auto;
                    border-radius: 4px;
                    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                }
                table {
                    border-collapse: collapse;
                    width: 100%;
                    margin: 16px 0;
                }
                table td, table th {
                    border: 1px solid #dee2e6;
                    padding: 8px 12px;
                }
                table th {
                    background: #f8f9fa;
                    font-weight: 600;
                }
                .mce-content-body[data-mce-placeholder]:not(.mce-visualblocks)::before {
                    color: #6c757d;
                    font-style: italic;
                }
            `,

            // Mobile responsiveness
            mobile: {
                theme: 'mobile',
                plugins: ['autosave', 'lists', 'autolink'],
                toolbar: ['undo', 'bold', 'italic', 'styleselect']
            },

            // Image upload configuration
            images_upload_url: '/api/tinymce/upload',
            images_upload_credentials: true,
            images_upload_handler: function (blobInfo, success, failure, progress) {
                return TinyMCEUploader.uploadImage(blobInfo, success, failure, progress);
            },

            // File picker for links and media
            file_picker_callback: function (callback, value, meta) {
                TinyMCEUploader.filePicker(callback, value, meta);
            },

            // Paste handling
            paste_data_images: true,
            paste_as_text: false,
            paste_webkit_styles: 'none',

            // Auto-save
            autosave_ask_before_unload: true,
            autosave_interval: '30s',
            autosave_prefix: 'tinymce-autosave-{path}{query}-{id}-',
            autosave_restore_when_empty: false,

            // Performance
            browser_spellcheck: true,
            contextmenu: false,

            // Security
            convert_urls: false,
            relative_urls: false,
            remove_script_host: false,
        };
    }

    /**
     * Get configuration for thread/comment forms
     */
    getCommentConfig(selector, options = {}) {
        return {
            ...this.baseConfig,
            selector: selector,
            height: options.height || 200,
            placeholder: options.placeholder || 'Nhập nội dung của bạn...',
            plugins: [
                'autolink', 'lists', 'link', 'emoticons', 'autosave'
            ],
            toolbar: 'bold italic underline | bullist numlist | blockquote | link emoticons',
            toolbar_mode: 'wrap',
            ...options
        };
    }

    /**
     * Get configuration for admin forms
     */
    getAdminConfig(selector, options = {}) {
        return {
            ...this.baseConfig,
            selector: selector,
            height: options.height || 400,
            placeholder: options.placeholder || 'Nhập nội dung...',
            plugins: [
                'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                'insertdatetime', 'media', 'table', 'help', 'wordcount', 'emoticons',
                'autosave', 'save'
            ],
            toolbar: [
                'undo redo | formatselect | bold italic underline strikethrough | forecolor backcolor',
                'alignleft aligncenter alignright alignjustify | bullist numlist | outdent indent',
                'blockquote | link image media | table | code fullscreen | save'
            ],
            ...options
        };
    }

    /**
     * Get configuration for showcase descriptions
     */
    getShowcaseConfig(selector, options = {}) {
        return {
            ...this.baseConfig,
            selector: selector,
            height: options.height || 250,
            placeholder: options.placeholder || 'Mô tả chi tiết về dự án của bạn...',
            plugins: [
                'advlist', 'autolink', 'lists', 'link', 'image', 'charmap',
                'searchreplace', 'visualblocks', 'code', 'fullscreen',
                'insertdatetime', 'table', 'wordcount', 'emoticons', 'autosave'
            ],
            toolbar: [
                'undo redo | formatselect | bold italic underline | alignleft aligncenter alignright',
                'bullist numlist | outdent indent | blockquote | link image | table | emoticons | fullscreen'
            ],
            ...options
        };
    }

    /**
     * Get minimal configuration for simple forms
     */
    getMinimalConfig(selector, options = {}) {
        return {
            ...this.baseConfig,
            selector: selector,
            height: options.height || 150,
            placeholder: options.placeholder || 'Nhập nội dung...',
            plugins: ['autolink', 'lists', 'link', 'emoticons'],
            toolbar: 'bold italic underline | bullist numlist | link emoticons',
            ...options
        };
    }

    /**
     * Initialize TinyMCE with custom setup
     */
    init(config, setupCallback = null) {
        const finalConfig = {
            ...config,
            setup: function(editor) {
                // Custom buttons
                TinyMCEConfig.addCustomButtons(editor);

                // Custom event handlers
                TinyMCEConfig.addEventHandlers(editor);

                // Call custom setup if provided
                if (setupCallback && typeof setupCallback === 'function') {
                    setupCallback(editor);
                }
            }
        };

        return tinymce.init(finalConfig);
    }

    /**
     * Add custom buttons to editor
     */
    static addCustomButtons(editor) {
        // Quote button
        editor.ui.registry.addButton('quote', {
            text: 'Trích dẫn',
            icon: 'quote',
            tooltip: 'Thêm trích dẫn',
            onAction: function() {
                editor.insertContent('<blockquote><p>Nội dung trích dẫn...</p></blockquote><p><br></p>');
            }
        });

        // Code block button
        editor.ui.registry.addButton('codeblock', {
            text: 'Code',
            icon: 'sourcecode',
            tooltip: 'Thêm khối code',
            onAction: function() {
                editor.insertContent('<pre><code>// Nhập code của bạn ở đây</code></pre><p><br></p>');
            }
        });
    }

    /**
     * Add event handlers
     */
    static addEventHandlers(editor) {
        // Handle content change for validation
        editor.on('input keyup change', function() {
            const content = editor.getContent().trim();
            const textarea = document.getElementById(editor.id);

            if (textarea) {
                // Update hidden textarea value
                textarea.value = content;

                // Trigger validation
                const event = new Event('input', { bubbles: true });
                textarea.dispatchEvent(event);

                // Remove validation errors if content exists
                if (content) {
                    textarea.classList.remove('is-invalid');
                    const errorDiv = document.querySelector(`#${editor.id}-error`);
                    if (errorDiv) {
                        errorDiv.style.display = 'none';
                    }

                    // Remove error styling from TinyMCE container
                    const tinyMCEContainer = editor.getContainer();
                    if (tinyMCEContainer) {
                        tinyMCEContainer.classList.remove('is-invalid');
                    }
                }
            }
        });

        // Handle initialization
        editor.on('init', function() {
            console.log(`TinyMCE initialized for: ${editor.id}`);
        });
    }
}

// Export for global use
window.TinyMCEConfig = TinyMCEConfig;
