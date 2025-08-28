<?php

namespace App\View\Components;

use Illuminate\View\Component;

class CommentImageUpload extends Component
{
    public $maxFiles;
    public $maxSize;
    public $context;
    public $uploadText;
    public $acceptDescription;
    public $showPreview;
    public $compact;
    public $uniqueId;

    /**
     * Create a new component instance.
     *
     * @param int $maxFiles Maximum number of files allowed
     * @param string $maxSize Maximum file size (e.g., '5MB')
     * @param string $context Context for upload (comment, reply)
     * @param string $uploadText Text to display in upload area
     * @param string $acceptDescription Description of accepted file types
     * @param bool $showPreview Whether to show image previews
     * @param bool $compact Whether to use compact layout
     */
    public function __construct(
        $maxFiles = 5,
        $maxSize = '5MB',
        $context = 'comment',
        $uploadText = 'Thêm hình ảnh mới',
        $acceptDescription = 'Tối đa 5 file • 5MB mỗi file • JPG, JPEG, PNG, GIF, WEBP',
        $showPreview = true,
        $compact = false
    ) {
        $this->maxFiles = $maxFiles;
        $this->maxSize = $maxSize;
        $this->context = $context;
        $this->uploadText = $uploadText;
        $this->acceptDescription = $acceptDescription;
        $this->showPreview = $showPreview;
        $this->compact = $compact;
        $this->uniqueId = 'comment-upload-' . uniqid();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.comment-image-upload');
    }
}
