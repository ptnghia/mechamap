<?php

namespace App\View\Components;

use Illuminate\View\Component;

class AdvancedFileUpload extends Component
{
    public $name;
    public $id;
    public $fileTypes;
    public $maxSize;
    public $maxFiles;
    public $multiple;
    public $dragDrop;
    public $showPreview;
    public $showProgress;
    public $required;
    public $accept;
    public $acceptDescription;
    public $uploadText;
    public $context;
    public $class;

    /**
     * Create a new component instance.
     */
    public function __construct(
        $name = 'files',
        $id = null,
        $fileTypes = ['jpg', 'png', 'gif', 'pdf'],
        $maxSize = '5MB',
        $maxFiles = 10,
        $multiple = true,
        $dragDrop = true,
        $showPreview = true,
        $showProgress = true,
        $required = false,
        $accept = null,
        $acceptDescription = null,
        $uploadText = null,
        $context = 'default',
        $class = ''
    ) {
        $this->name = $name;
        $this->id = $id;
        $this->fileTypes = is_array($fileTypes) ? $fileTypes : explode(',', $fileTypes);
        $this->maxSize = $maxSize;
        $this->maxFiles = $maxFiles;
        $this->multiple = $multiple;
        $this->dragDrop = $dragDrop;
        $this->showPreview = $showPreview;
        $this->showProgress = $showProgress;
        $this->required = $required;
        $this->accept = $accept;
        $this->acceptDescription = $acceptDescription;
        $this->uploadText = $uploadText;
        $this->context = $context;
        $this->class = $class;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.advanced-file-upload');
    }
}
