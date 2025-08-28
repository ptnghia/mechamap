<?php

namespace App\View\Components;

use Illuminate\View\Component;

class AvatarUpload extends Component
{
    public $name;
    public $id;
    public $currentAvatar;
    public $size;
    public $maxSize;
    public $required;
    public $shape;
    public $showRemove;
    public $placeholderText;
    public $class;
    public $uploadUrl;
    public $previewOnly;

    /**
     * Create a new component instance.
     */
    public function __construct(
        $name = 'avatar',
        $id = null,
        $currentAvatar = null,
        $size = 120,
        $maxSize = '2MB',
        $required = false,
        $shape = 'circle',
        $showRemove = true,
        $placeholderText = 'Click to upload avatar',
        $class = '',
        $uploadUrl = null,
        $previewOnly = false
    ) {
        $this->name = $name;
        $this->id = $id;
        $this->currentAvatar = $currentAvatar;
        $this->size = $size;
        $this->maxSize = $maxSize;
        $this->required = $required;
        $this->shape = $shape;
        $this->showRemove = $showRemove;
        $this->placeholderText = $placeholderText;
        $this->class = $class;
        $this->uploadUrl = $uploadUrl;
        $this->previewOnly = $previewOnly;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.avatar-upload');
    }
}
