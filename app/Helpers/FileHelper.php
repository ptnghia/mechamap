<?php

if (!function_exists('getFileIcon')) {
    /**
     * Get Font Awesome icon class for file extension
     */
    function getFileIcon(string $filename): string
    {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        $iconMap = [
            // CAD Files
            'dwg' => 'fas fa-drafting-compass',
            'dxf' => 'fas fa-drafting-compass',
            'step' => 'fas fa-cube',
            'stp' => 'fas fa-cube',
            'iges' => 'fas fa-cube',
            'igs' => 'fas fa-cube',
            'stl' => 'fas fa-shapes',
            
            // Documents
            'pdf' => 'fas fa-file-pdf',
            'doc' => 'fas fa-file-word',
            'docx' => 'fas fa-file-word',
            'txt' => 'fas fa-file-alt',
            'rtf' => 'fas fa-file-alt',
            
            // Spreadsheets
            'xls' => 'fas fa-file-excel',
            'xlsx' => 'fas fa-file-excel',
            'csv' => 'fas fa-file-csv',
            
            // Presentations
            'ppt' => 'fas fa-file-powerpoint',
            'pptx' => 'fas fa-file-powerpoint',
            
            // Archives
            'zip' => 'fas fa-file-archive',
            'rar' => 'fas fa-file-archive',
            '7z' => 'fas fa-file-archive',
            'tar' => 'fas fa-file-archive',
            'gz' => 'fas fa-file-archive',
            
            // Images
            'jpg' => 'fas fa-file-image',
            'jpeg' => 'fas fa-file-image',
            'png' => 'fas fa-file-image',
            'gif' => 'fas fa-file-image',
            'bmp' => 'fas fa-file-image',
            'svg' => 'fas fa-file-image',
            'webp' => 'fas fa-file-image',
            
            // Videos
            'mp4' => 'fas fa-file-video',
            'avi' => 'fas fa-file-video',
            'mov' => 'fas fa-file-video',
            'wmv' => 'fas fa-file-video',
            'flv' => 'fas fa-file-video',
            'webm' => 'fas fa-file-video',
            
            // Audio
            'mp3' => 'fas fa-file-audio',
            'wav' => 'fas fa-file-audio',
            'flac' => 'fas fa-file-audio',
            'aac' => 'fas fa-file-audio',
            'ogg' => 'fas fa-file-audio',
            
            // Code
            'html' => 'fas fa-file-code',
            'css' => 'fas fa-file-code',
            'js' => 'fas fa-file-code',
            'php' => 'fas fa-file-code',
            'py' => 'fas fa-file-code',
            'java' => 'fas fa-file-code',
            'cpp' => 'fas fa-file-code',
            'c' => 'fas fa-file-code',
            'json' => 'fas fa-file-code',
            'xml' => 'fas fa-file-code',
        ];
        
        return $iconMap[$extension] ?? 'fas fa-file';
    }
}

if (!function_exists('formatFileSize')) {
    /**
     * Format file size in human readable format
     */
    function formatFileSize(int $bytes, int $precision = 2): string
    {
        if ($bytes === 0) {
            return '0 Bytes';
        }
        
        $units = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB'];
        $base = log($bytes, 1024);
        $index = floor($base);
        
        if ($index >= count($units)) {
            $index = count($units) - 1;
        }
        
        $size = round(pow(1024, $base - $index), $precision);
        
        return $size . ' ' . $units[$index];
    }
}

if (!function_exists('getFileTypeColor')) {
    /**
     * Get color class for file type
     */
    function getFileTypeColor(string $filename): string
    {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        $colorMap = [
            // CAD Files - Blue
            'dwg' => 'text-primary',
            'dxf' => 'text-primary',
            'step' => 'text-success',
            'stp' => 'text-success',
            'iges' => 'text-success',
            'igs' => 'text-success',
            'stl' => 'text-warning',
            
            // Documents - Blue
            'pdf' => 'text-danger',
            'doc' => 'text-primary',
            'docx' => 'text-primary',
            'txt' => 'text-secondary',
            
            // Archives - Gray
            'zip' => 'text-secondary',
            'rar' => 'text-secondary',
            '7z' => 'text-secondary',
            
            // Images - Purple
            'jpg' => 'text-purple',
            'jpeg' => 'text-purple',
            'png' => 'text-purple',
            'gif' => 'text-purple',
            
            // Videos - Red
            'mp4' => 'text-danger',
            'avi' => 'text-danger',
            'mov' => 'text-danger',
        ];
        
        return $colorMap[$extension] ?? 'text-muted';
    }
}

if (!function_exists('isCADFile')) {
    /**
     * Check if file is a CAD file
     */
    function isCADFile(string $filename): bool
    {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $cadExtensions = ['dwg', 'dxf', 'step', 'stp', 'iges', 'igs', 'stl'];
        
        return in_array($extension, $cadExtensions);
    }
}

if (!function_exists('isImageFile')) {
    /**
     * Check if file is an image
     */
    function isImageFile(string $filename): bool
    {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp'];
        
        return in_array($extension, $imageExtensions);
    }
}

if (!function_exists('isDocumentFile')) {
    /**
     * Check if file is a document
     */
    function isDocumentFile(string $filename): bool
    {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $documentExtensions = ['pdf', 'doc', 'docx', 'txt', 'rtf', 'xls', 'xlsx', 'ppt', 'pptx'];
        
        return in_array($extension, $documentExtensions);
    }
}

if (!function_exists('isArchiveFile')) {
    /**
     * Check if file is an archive
     */
    function isArchiveFile(string $filename): bool
    {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $archiveExtensions = ['zip', 'rar', '7z', 'tar', 'gz'];
        
        return in_array($extension, $archiveExtensions);
    }
}

if (!function_exists('getFileCategory')) {
    /**
     * Get file category based on extension
     */
    function getFileCategory(string $filename): string
    {
        if (isCADFile($filename)) {
            return 'cad';
        } elseif (isImageFile($filename)) {
            return 'image';
        } elseif (isDocumentFile($filename)) {
            return 'document';
        } elseif (isArchiveFile($filename)) {
            return 'archive';
        } else {
            return 'other';
        }
    }
}

if (!function_exists('getMimeTypeFromExtension')) {
    /**
     * Get MIME type from file extension
     */
    function getMimeTypeFromExtension(string $filename): string
    {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        $mimeMap = [
            // CAD Files
            'dwg' => 'application/acad',
            'dxf' => 'application/dxf',
            'step' => 'application/step',
            'stp' => 'application/step',
            'iges' => 'application/iges',
            'igs' => 'application/iges',
            'stl' => 'application/sla',
            
            // Documents
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'txt' => 'text/plain',
            'rtf' => 'application/rtf',
            
            // Spreadsheets
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'csv' => 'text/csv',
            
            // Presentations
            'ppt' => 'application/vnd.ms-powerpoint',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            
            // Archives
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            '7z' => 'application/x-7z-compressed',
            'tar' => 'application/x-tar',
            'gz' => 'application/gzip',
            
            // Images
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'svg' => 'image/svg+xml',
            'webp' => 'image/webp',
            
            // Videos
            'mp4' => 'video/mp4',
            'avi' => 'video/x-msvideo',
            'mov' => 'video/quicktime',
            'wmv' => 'video/x-ms-wmv',
            'flv' => 'video/x-flv',
            'webm' => 'video/webm',
            
            // Audio
            'mp3' => 'audio/mpeg',
            'wav' => 'audio/wav',
            'flac' => 'audio/flac',
            'aac' => 'audio/aac',
            'ogg' => 'audio/ogg',
        ];
        
        return $mimeMap[$extension] ?? 'application/octet-stream';
    }
}
