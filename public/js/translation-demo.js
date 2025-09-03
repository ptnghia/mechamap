/**
 * Demo và test functions cho Translation API
 * Sử dụng để kiểm tra các chức năng dịch thuật
 */

// Demo function để test dịch từ tiếng Việt sang ngôn ngữ khác
async function demoTranslateVietnamese() {
    try {
        console.log('=== Demo Translation API ===');
        
        // Test dịch text thường
        const vietnameseText = "Xin chào, tôi là một lập trình viên. Tôi đang phát triển ứng dụng web.";
        console.log('Original Vietnamese text:', vietnameseText);
        
        // Dịch sang tiếng Anh
        const englishText = await translateVietnamese(vietnameseText, 'en');
        console.log('English translation:', englishText);
        
        // Dịch sang tiếng Pháp
        const frenchText = await translateVietnamese(vietnameseText, 'fr');
        console.log('French translation:', frenchText);
        
        // Dịch sang tiếng Nhật
        const japaneseText = await translateVietnamese(vietnameseText, 'ja');
        console.log('Japanese translation:', japaneseText);
        
        // Test dịch HTML content
        const htmlContent = "<p>Chào mừng bạn đến với <strong>MechaMap</strong>!</p>";
        console.log('\nOriginal HTML:', htmlContent);
        
        const htmlEnglish = await translateVietnamese(htmlContent, 'en', 'html');
        console.log('HTML English translation:', htmlEnglish);
        
    } catch (error) {
        console.error('Demo translation error:', error);
    }
}

// Demo function để test detect language
async function demoDetectLanguage() {
    try {
        console.log('\n=== Demo Language Detection ===');
        
        const texts = [
            "Xin chào, tôi là người Việt Nam",
            "Hello, I am from Vietnam",
            "Bonjour, je suis du Vietnam",
            "こんにちは、私はベトナム出身です"
        ];
        
        for (const text of texts) {
            const detectedLang = await detectLanguage(text);
            console.log(`Text: "${text}" -> Detected language: ${detectedLang}`);
        }
        
    } catch (error) {
        console.error('Demo language detection error:', error);
    }
}

// Demo function để test get supported languages
async function demoGetSupportedLanguages() {
    try {
        console.log('\n=== Demo Supported Languages ===');
        
        const languages = await getSupportedLanguages();
        console.log('Total supported languages:', languages.total);
        console.log('Source languages count:', languages.sourceLanguages.length);
        console.log('Target languages count:', languages.targetLanguages.length);
        
        // Hiển thị một số ngôn ngữ phổ biến
        const popularLanguages = ['en', 'fr', 'de', 'ja', 'ko', 'zh', 'es', 'it'];
        console.log('\nPopular target languages available:');
        popularLanguages.forEach(lang => {
            if (languages.targetLanguages.includes(lang)) {
                console.log(`✓ ${lang}`);
            } else {
                console.log(`✗ ${lang} (not available)`);
            }
        });
        
    } catch (error) {
        console.error('Demo supported languages error:', error);
    }
}

// Function tiện ích để dịch nhanh từ tiếng Việt sang tiếng Anh
async function quickTranslateToEnglish(vietnameseText) {
    try {
        return await translateVietnamese(vietnameseText, 'en');
    } catch (error) {
        console.error('Quick translate error:', error);
        return vietnameseText; // Trả về text gốc nếu lỗi
    }
}

// Function tiện ích để dịch với fallback
async function translateWithFallback(vietnameseText, targetLanguage, fallbackText = null) {
    try {
        return await translateVietnamese(vietnameseText, targetLanguage);
    } catch (error) {
        console.error('Translation with fallback error:', error);
        return fallbackText || vietnameseText;
    }
}

// Function để chạy tất cả demo
async function runAllDemos() {
    console.log('🚀 Starting Translation API Demos...\n');
    
    await demoTranslateVietnamese();
    await demoDetectLanguage();
    await demoGetSupportedLanguages();
    
    console.log('\n✅ All demos completed!');
}

// Export functions để sử dụng ở nơi khác
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        demoTranslateVietnamese,
        demoDetectLanguage,
        demoGetSupportedLanguages,
        quickTranslateToEnglish,
        translateWithFallback,
        runAllDemos
    };
}

// Auto-run demo khi load trang (chỉ khi có console)
if (typeof window !== 'undefined' && window.console) {
    // Thêm button để test trong browser
    document.addEventListener('DOMContentLoaded', function() {
        // Tạo button test nếu đang ở development mode
        if (window.location.hostname === 'mechamap.test' || window.location.hostname === 'localhost') {
            const testButton = document.createElement('button');
            testButton.textContent = 'Test Translation API';
            testButton.style.cssText = `
                position: fixed;
                top: 10px;
                right: 10px;
                z-index: 9999;
                padding: 10px;
                background: #007bff;
                color: white;
                border: none;
                border-radius: 5px;
                cursor: pointer;
                font-size: 12px;
            `;
            testButton.onclick = runAllDemos;
            document.body.appendChild(testButton);
        }
    });
}

console.log('Translation Demo loaded. Use runAllDemos() to test all functions.');
