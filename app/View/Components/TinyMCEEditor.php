<?php

namespace App\View\Components;

use Illuminate\View\Component;

class TinyMCEEditor extends Component
{
    public string $name;
    public string $id;
    public string $value;
    public string $placeholder;
    public string $context;
    public int $height;
    public bool $required;
    public array $customAttributes;
    public string $class;

    /**
     * Create a new component instance.
     */
    public function __construct(
        string $name,
        string $id = '',
        ?string $value = '',
        string $placeholder = '',
        string $context = 'comment',
        int $height = 200,
        bool $required = false,
        string $class = '',
        array $customAttributes = []
    ) {
        $this->name = $name;
        $this->id = $id ?: $name;
        $this->value = $value ?? '';
        $this->placeholder = $placeholder ?: 'Nhập nội dung của bạn...';
        $this->context = $context;
        $this->height = $height;
        $this->required = $required;
        $this->class = $class;
        $this->customAttributes = $customAttributes;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.tinymce-editor');
    }

    /**
     * Get TinyMCE configuration based on context
     */
    public function getConfig(): array
    {
        $baseConfig = [
            'selector' => "#{$this->id}",
            'height' => $this->height,
            'placeholder' => $this->placeholder,
            'readonly' => false,
            'menubar' => false,
            'branding' => false,
            'toolbar_mode' => 'floating',
            'language' => 'vi',
            'language_url' => asset('js/tinymce-lang/vi.js'),
            'images_upload_url' => route('api.tinymce.upload'),
            'images_upload_credentials' => true,
            'paste_data_images' => true,
            'paste_as_text' => false,
            'autosave_ask_before_unload' => true,
            'autosave_interval' => '30s',
            'browser_spellcheck' => true,
            'contextmenu' => false,
            'convert_urls' => false,
            'relative_urls' => false,
            'content_style' => $this->getContentStyle()
        ];

        return match($this->context) {
            'admin' => array_merge($baseConfig, [
                'plugins' => [
                    'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                    'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                    'insertdatetime', 'media', 'table', 'help', 'wordcount', 'emoticons',
                    'autosave', 'save'
                ],
                'toolbar' => [
                    'undo redo | formatselect | bold italic underline strikethrough | forecolor backcolor',
                    'alignleft aligncenter alignright alignjustify | bullist numlist | outdent indent',
                    'blockquote | link image media | table | code fullscreen | save'
                ]
            ]),
            'showcase' => array_merge($baseConfig, [
                'plugins' => [
                    'advlist', 'autolink', 'lists', 'link', 'image', 'charmap',
                    'searchreplace', 'visualblocks', 'code', 'fullscreen',
                    'insertdatetime', 'table', 'wordcount', 'emoticons', 'autosave'
                ],
                'toolbar' => [
                    'undo redo | formatselect | bold italic underline | alignleft aligncenter alignright',
                    'bullist numlist | outdent indent | blockquote | link image | table | emoticons | fullscreen'
                ]
            ]),
            'minimal' => array_merge($baseConfig, [
                'plugins' => ['autolink', 'lists', 'link', 'emoticons'],
                'toolbar' => 'bold italic underline | bullist numlist | link emoticons'
            ]),
            default => array_merge($baseConfig, [ // comment context
                'plugins' => [
                    'advlist', 'autolink', 'lists', 'link', 'image', 'charmap',
                    'searchreplace', 'visualblocks', 'code', 'fullscreen',
                    'insertdatetime', 'table', 'wordcount', 'emoticons', 'autosave'
                ],
                'toolbar' => [
                    'undo redo | formatselect | bold italic underline | alignleft aligncenter alignright',
                    'bullist numlist | outdent indent | blockquote | link image | emoticons | code fullscreen'
                ]
            ])
        };
    }

    /**
     * Get content style for TinyMCE
     */
    private function getContentStyle(): string
    {
        return '
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
        ';
    }

    /**
     * Get validation attributes
     */
    public function getValidationAttributes(): array
    {
        $attrs = [];

        if ($this->required) {
            $attrs['required'] = true;
        }

        return array_merge($attrs, $this->customAttributes);
    }
}
