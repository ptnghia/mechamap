<?php

if (!function_exists('formatFileSize')) {
    /**
     * Format file size in human readable format
     *
     * @param int $bytes
     * @return string
     */
    function formatFileSize($bytes)
    {
        if ($bytes == 0) return '0 B';
        
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }
}

if (!function_exists('getFileIcon')) {
    /**
     * Get FontAwesome icon class for file extension
     *
     * @param string $extension
     * @return string
     */
    function getFileIcon($extension)
    {
        $extension = strtolower($extension);
        
        $icons = [
            // Documents
            'pdf' => 'file-pdf',
            'doc' => 'file-word', 
            'docx' => 'file-word',
            'xls' => 'file-excel', 
            'xlsx' => 'file-excel',
            'ppt' => 'file-powerpoint', 
            'pptx' => 'file-powerpoint',
            'txt' => 'file-alt',
            'rtf' => 'file-alt',
            
            // Archives
            'zip' => 'file-archive', 
            'rar' => 'file-archive',
            '7z' => 'file-archive',
            'tar' => 'file-archive',
            'gz' => 'file-archive',
            
            // CAD Files
            'dwg' => 'cube', 
            'dxf' => 'cube',
            'step' => 'cube', 
            'stp' => 'cube',
            'iges' => 'cube', 
            'igs' => 'cube',
            'stl' => 'cube',
            'obj' => 'cube',
            '3ds' => 'cube',
            'max' => 'cube',
            'blend' => 'cube',
            'skp' => 'cube',
            'ipt' => 'cube', // Inventor
            'iam' => 'cube', // Inventor Assembly
            'idw' => 'cube', // Inventor Drawing
            'sldprt' => 'cube', // SolidWorks Part
            'sldasm' => 'cube', // SolidWorks Assembly
            'slddrw' => 'cube', // SolidWorks Drawing
            'catpart' => 'cube', // CATIA
            'catproduct' => 'cube', // CATIA
            'prt' => 'cube', // Pro/E, NX
            'asm' => 'cube', // Pro/E, NX
            'drw' => 'cube', // Pro/E, NX
            
            // Images
            'jpg' => 'image', 
            'jpeg' => 'image',
            'png' => 'image', 
            'gif' => 'image',
            'bmp' => 'image',
            'svg' => 'image',
            'webp' => 'image',
            'tiff' => 'image',
            'tif' => 'image',
            'ico' => 'image',
            
            // Videos
            'mp4' => 'video', 
            'avi' => 'video',
            'mov' => 'video',
            'wmv' => 'video',
            'flv' => 'video',
            'webm' => 'video',
            'mkv' => 'video',
            '3gp' => 'video',
            
            // Audio
            'mp3' => 'music', 
            'wav' => 'music',
            'flac' => 'music',
            'aac' => 'music',
            'ogg' => 'music',
            'wma' => 'music',
            
            // Code
            'html' => 'code',
            'css' => 'code',
            'js' => 'code',
            'php' => 'code',
            'py' => 'code',
            'java' => 'code',
            'cpp' => 'code',
            'c' => 'code',
            'h' => 'code',
            'cs' => 'code',
            'xml' => 'code',
            'json' => 'code',
            'sql' => 'database',
            
            // Engineering specific
            'nc' => 'cogs', // CNC G-code
            'gcode' => 'cogs',
            'tap' => 'cogs',
            'cnc' => 'cogs',
            'cam' => 'cogs',
            'fem' => 'calculator', // FEA files
            'inp' => 'calculator', // ABAQUS
            'cdb' => 'calculator', // ANSYS
            'mph' => 'calculator', // COMSOL
            'mat' => 'calculator', // MATLAB
            'm' => 'calculator', // MATLAB
            
            // Simulation
            'sim' => 'chart-line',
            'res' => 'chart-line',
            'rst' => 'chart-line',
            'odb' => 'chart-line',
            
            // Manufacturing
            'mfg' => 'industry',
            'setup' => 'industry',
            'ops' => 'industry',
        ];
        
        return $icons[$extension] ?? 'file';
    }
}

if (!function_exists('getFileCategory')) {
    /**
     * Get file category based on extension
     *
     * @param string $extension
     * @return string
     */
    function getFileCategory($extension)
    {
        $extension = strtolower($extension);
        
        $categories = [
            'image' => ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp', 'tiff', 'tif', 'ico'],
            'video' => ['mp4', 'avi', 'mov', 'wmv', 'flv', 'webm', 'mkv', '3gp'],
            'audio' => ['mp3', 'wav', 'flac', 'aac', 'ogg', 'wma'],
            'document' => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'rtf'],
            'archive' => ['zip', 'rar', '7z', 'tar', 'gz'],
            'cad' => ['dwg', 'dxf', 'step', 'stp', 'iges', 'igs', 'stl', 'obj', '3ds', 'max', 'blend', 'skp', 'ipt', 'iam', 'idw', 'sldprt', 'sldasm', 'slddrw', 'catpart', 'catproduct', 'prt', 'asm', 'drw'],
            'code' => ['html', 'css', 'js', 'php', 'py', 'java', 'cpp', 'c', 'h', 'cs', 'xml', 'json', 'sql'],
            'engineering' => ['nc', 'gcode', 'tap', 'cnc', 'cam', 'fem', 'inp', 'cdb', 'mph', 'mat', 'm'],
            'simulation' => ['sim', 'res', 'rst', 'odb'],
            'manufacturing' => ['mfg', 'setup', 'ops'],
        ];
        
        foreach ($categories as $category => $extensions) {
            if (in_array($extension, $extensions)) {
                return $category;
            }
        }
        
        return 'other';
    }
}

if (!function_exists('isImageFile')) {
    /**
     * Check if file is an image
     *
     * @param string $mimeType
     * @return bool
     */
    function isImageFile($mimeType)
    {
        return strpos($mimeType, 'image/') === 0;
    }
}

if (!function_exists('isVideoFile')) {
    /**
     * Check if file is a video
     *
     * @param string $mimeType
     * @return bool
     */
    function isVideoFile($mimeType)
    {
        return strpos($mimeType, 'video/') === 0;
    }
}

if (!function_exists('isAudioFile')) {
    /**
     * Check if file is an audio file
     *
     * @param string $mimeType
     * @return bool
     */
    function isAudioFile($mimeType)
    {
        return strpos($mimeType, 'audio/') === 0;
    }
}

if (!function_exists('isCadFile')) {
    /**
     * Check if file is a CAD file based on extension
     *
     * @param string $extension
     * @return bool
     */
    function isCadFile($extension)
    {
        $cadExtensions = ['dwg', 'dxf', 'step', 'stp', 'iges', 'igs', 'stl', 'obj', '3ds', 'max', 'blend', 'skp', 'ipt', 'iam', 'idw', 'sldprt', 'sldasm', 'slddrw', 'catpart', 'catproduct', 'prt', 'asm', 'drw'];
        
        return in_array(strtolower($extension), $cadExtensions);
    }
}

if (!function_exists('getQualityBadge')) {
    /**
     * Get quality badge for image dimensions
     *
     * @param int|null $width
     * @param int|null $height
     * @return array
     */
    function getQualityBadge($width, $height)
    {
        if (!$width || !$height) {
            return ['class' => 'bg-secondary', 'text' => 'Unknown'];
        }
        
        if ($width >= 3840 && $height >= 2160) {
            return ['class' => 'bg-success', 'text' => '4K'];
        } elseif ($width >= 1920 && $height >= 1080) {
            return ['class' => 'bg-success', 'text' => 'HD'];
        } elseif ($width >= 1280 && $height >= 720) {
            return ['class' => 'bg-primary', 'text' => 'HD Ready'];
        } else {
            return ['class' => 'bg-secondary', 'text' => 'Standard'];
        }
    }
}
