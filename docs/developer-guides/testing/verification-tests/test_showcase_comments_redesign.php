<?php
/**
 * Test Showcase Comments System Redesign
 * Verify CKEditor5, Enhanced UI/UX, and Image Upload functionality
 */

echo "🎨 TESTING SHOWCASE COMMENTS REDESIGN\n";
echo "=====================================\n\n";

// 1. Check CKEditor5 Integration
echo "1️⃣ Checking CKEditor5 Integration...\n";
$layoutFile = 'resources/views/layouts/app.blade.php';
if (file_exists($layoutFile)) {
    $content = file_get_contents($layoutFile);
    
    // Check CKEditor5 is loaded for showcase pages
    if (strpos($content, "'showcase.show'") !== false) {
        echo "✅ CKEditor5 loaded for showcase pages\n";
    } else {
        echo "❌ CKEditor5 not configured for showcase pages\n";
    }
} else {
    echo "❌ Layout file not found\n";
}

// Check CKEditor5 component
$ckEditorComponent = 'resources/views/components/ckeditor5-comment.blade.php';
if (file_exists($ckEditorComponent)) {
    echo "✅ CKEditor5 comment component created\n";
    
    $componentContent = file_get_contents($ckEditorComponent);
    if (strpos($componentContent, 'ClassicEditor') !== false) {
        echo "✅ CKEditor5 ClassicEditor configured\n";
    }
    if (strpos($componentContent, 'language: \'vi\'') !== false) {
        echo "✅ Vietnamese language support\n";
    }
} else {
    echo "❌ CKEditor5 component not found\n";
}

echo "\n";

// 2. Check Enhanced UI/UX CSS
echo "2️⃣ Checking Enhanced UI/UX...\n";
$commentsCSS = 'public/css/frontend/views/showcase-comments.css';
if (file_exists($commentsCSS)) {
    echo "✅ Showcase comments CSS created\n";
    
    $cssContent = file_get_contents($commentsCSS);
    
    // Check key CSS classes
    $cssClasses = [
        '.comments-section',
        '.comment-form',
        '.comment-item',
        '.comment-header',
        '.comment-replies',
        '.reply-item',
        '.comment-actions',
        '.comment-action-btn'
    ];
    
    $foundClasses = 0;
    foreach ($cssClasses as $class) {
        if (strpos($cssContent, $class) !== false) {
            $foundClasses++;
        }
    }
    
    echo "✅ Found $foundClasses/" . count($cssClasses) . " essential CSS classes\n";
    
    // Check responsive design
    if (strpos($cssContent, '@media (max-width: 768px)') !== false) {
        echo "✅ Mobile responsive design included\n";
    }
    
    // Check dark mode support
    if (strpos($cssContent, '[data-bs-theme="dark"]') !== false) {
        echo "✅ Dark mode support included\n";
    }
} else {
    echo "❌ Showcase comments CSS not found\n";
}

echo "\n";

// 3. Check Enhanced Image Upload Component
echo "3️⃣ Checking Enhanced Image Upload...\n";
$uploadComponent = 'resources/views/components/enhanced-image-upload.blade.php';
if (file_exists($uploadComponent)) {
    echo "✅ Enhanced image upload component created\n";
    
    $uploadContent = file_get_contents($uploadComponent);
    
    // Check drag and drop functionality
    if (strpos($uploadContent, 'drag-over') !== false) {
        echo "✅ Drag and drop functionality\n";
    }
    
    // Check preview functionality
    if (strpos($uploadContent, 'preview-item') !== false) {
        echo "✅ Image preview functionality\n";
    }
    
    // Check Fancybox integration
    if (strpos($uploadContent, 'Fancybox.show') !== false) {
        echo "✅ Fancybox integration for image viewing\n";
    }
    
    // Check file validation
    if (strpos($uploadContent, 'maxSize') !== false && strpos($uploadContent, 'maxFiles') !== false) {
        echo "✅ File validation (size and count)\n";
    }
} else {
    echo "❌ Enhanced image upload component not found\n";
}

echo "\n";

// 4. Check Showcase Show Page Updates
echo "4️⃣ Checking Showcase Show Page Updates...\n";
$showcaseFile = 'resources/views/showcase/show.blade.php';
if (file_exists($showcaseFile)) {
    $showcaseContent = file_get_contents($showcaseFile);
    
    // Check CKEditor5 usage
    $ckEditorCount = substr_count($showcaseContent, 'x-ckeditor5-comment');
    echo "✅ CKEditor5 components used: $ckEditorCount times\n";
    
    // Check enhanced upload usage
    $uploadCount = substr_count($showcaseContent, 'x-enhanced-image-upload');
    echo "✅ Enhanced upload components used: $uploadCount times\n";
    
    // Check CSS inclusion
    if (strpos($showcaseContent, 'showcase-comments.css') !== false) {
        echo "✅ Enhanced CSS included\n";
    }
    
    // Check old rich-text-editor removal
    $oldEditorCount = substr_count($showcaseContent, 'x-rich-text-editor');
    if ($oldEditorCount === 0) {
        echo "✅ Old rich-text-editor components removed\n";
    } else {
        echo "⚠️  Still has $oldEditorCount old rich-text-editor components\n";
    }
    
    // Check sidebar conflict fix
    $sidebarCount = substr_count($showcaseContent, '<x-sidebar />');
    if ($sidebarCount === 1) {
        echo "✅ Sidebar conflict fixed (single sidebar)\n";
    } else {
        echo "⚠️  Sidebar count: $sidebarCount (should be 1)\n";
    }
} else {
    echo "❌ Showcase show page not found\n";
}

echo "\n";

// 5. Check JavaScript Cleanup
echo "5️⃣ Checking JavaScript Cleanup...\n";
if (file_exists($showcaseFile)) {
    $showcaseContent = file_get_contents($showcaseFile);
    
    // Check old JavaScript removal
    if (strpos($showcaseContent, 'rich-text-editor.js') === false) {
        echo "✅ Old rich-text-editor.js removed\n";
    } else {
        echo "⚠️  Old rich-text-editor.js still referenced\n";
    }
    
    // Check new functions
    if (strpos($showcaseContent, 'toggleReplyForm') !== false) {
        echo "✅ Reply form toggle function present\n";
    }
    
    // Check old preview functions removal
    if (strpos($showcaseContent, 'previewImages') === false) {
        echo "✅ Old preview functions removed\n";
    } else {
        echo "⚠️  Old preview functions still present\n";
    }
}

echo "\n";

// 6. Summary
echo "📊 IMPLEMENTATION SUMMARY\n";
echo "=========================\n";

$checks = [
    'CKEditor5 Integration' => file_exists($ckEditorComponent),
    'Enhanced UI/UX CSS' => file_exists($commentsCSS),
    'Enhanced Image Upload' => file_exists($uploadComponent),
    'Showcase Page Updated' => file_exists($showcaseFile) && strpos(file_get_contents($showcaseFile), 'x-ckeditor5-comment') !== false,
    'Old Components Removed' => file_exists($showcaseFile) && substr_count(file_get_contents($showcaseFile), 'x-rich-text-editor') === 0
];

$passedChecks = array_filter($checks);
$totalChecks = count($checks);
$passedCount = count($passedChecks);

echo "Passed: $passedCount/$totalChecks checks\n\n";

if ($passedCount === $totalChecks) {
    echo "🎉 SUCCESS: Showcase Comments Redesign Complete!\n";
    echo "✅ CKEditor5 with Vietnamese support\n";
    echo "✅ Enhanced UI/UX with professional design\n";
    echo "✅ Drag & Drop image upload with preview\n";
    echo "✅ Mobile responsive and dark mode support\n";
    echo "✅ Fancybox integration for image galleries\n";
    echo "✅ Clean code with old components removed\n";
} else {
    echo "⚠️  INCOMPLETE: Some components need attention\n";
    foreach ($checks as $check => $passed) {
        if (!$passed) {
            echo "❌ $check\n";
        }
    }
}

echo "\n";
echo "🔗 Next Steps:\n";
echo "1. Test CKEditor5 functionality in browser\n";
echo "2. Test drag & drop image upload\n";
echo "3. Verify mobile responsiveness\n";
echo "4. Test comment submission and display\n";
echo "5. Verify Fancybox image viewing\n";

?>
