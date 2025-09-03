# Translation API Guide - MechaMap

## Tổng quan

Hệ thống Translation API của MechaMap cung cấp khả năng dịch thuật text từ tiếng Việt sang các ngôn ngữ khác sử dụng API external tại `http://realtime.mechamap.com/`.

## Các chức năng chính

### 1. Dịch text từ tiếng Việt
- **Function**: `translateVietnamese(vietnameseText, targetLanguage, contentType)`
- **Mô tả**: Dịch text tiếng Việt sang ngôn ngữ đích
- **Tham số**:
  - `vietnameseText` (string): Text tiếng Việt cần dịch (tối đa 5000 ký tự)
  - `targetLanguage` (string): Mã ngôn ngữ đích (ví dụ: 'en', 'fr', 'de')
  - `contentType` (string, optional): Loại nội dung 'text' hoặc 'html' (mặc định: 'text')

### 2. Nhận diện ngôn ngữ
- **Function**: `detectLanguage(content)`
- **Mô tả**: Nhận diện ngôn ngữ của text
- **Tham số**:
  - `content` (string): Text cần nhận diện ngôn ngữ

### 3. Lấy danh sách ngôn ngữ hỗ trợ
- **Function**: `getSupportedLanguages()`
- **Mô tả**: Lấy danh sách tất cả ngôn ngữ được hỗ trợ

## Cách sử dụng

### 1. Sử dụng trong JavaScript

```javascript
// Dịch text thường
try {
    const result = await translateVietnamese(
        "Xin chào, tôi là một lập trình viên", 
        "en"
    );
    console.log(result); // "Hello, I'm a programmer"
} catch (error) {
    console.error('Translation error:', error);
}

// Dịch HTML content
try {
    const htmlResult = await translateVietnamese(
        "<p>Chào mừng bạn đến với <strong>MechaMap</strong>!</p>", 
        "en", 
        "html"
    );
    console.log(htmlResult); // "<p>Welcome to <strong>MechaMap</strong>!</p>"
} catch (error) {
    console.error('HTML translation error:', error);
}

// Nhận diện ngôn ngữ
try {
    const language = await detectLanguage("Hello world");
    console.log(language); // "en"
} catch (error) {
    console.error('Language detection error:', error);
}

// Lấy danh sách ngôn ngữ hỗ trợ
try {
    const languages = await getSupportedLanguages();
    console.log('Total languages:', languages.total);
    console.log('Target languages:', languages.targetLanguages);
} catch (error) {
    console.error('Get languages error:', error);
}
```

### 2. Sử dụng với helper functions

```javascript
// Dịch nhanh sang tiếng Anh
const englishText = await quickTranslateToEnglish("Xin chào");

// Dịch với fallback
const result = await translateWithFallback(
    "Text tiếng Việt", 
    "en", 
    "Fallback text if translation fails"
);
```

## API Endpoints

### POST /api/translate
Dịch text hoặc HTML content giữa các ngôn ngữ.

**Request Body:**
```json
{
  "sourceLanguage": "vi",
  "targetLanguage": "en",
  "content": "Xin chào, tôi là một lập trình viên",
  "contentType": "text"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Translation completed successfully",
  "data": {
    "originalText": "Xin chào, tôi là một lập trình viên",
    "translatedText": "Hello, I'm a programmer",
    "sourceLanguage": "vi",
    "targetLanguage": "en",
    "detectedLanguage": "vi"
  }
}
```

### POST /api/detect-language
Nhận diện ngôn ngữ của text.

**Request Body:**
```json
{
  "content": "Bonjour, comment allez-vous?"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Language detection completed successfully",
  "data": {
    "content": "Bonjour, comment allez-vous?",
    "detectedLanguage": "fr"
  }
}
```

### GET /api/supported-languages
Lấy danh sách ngôn ngữ được hỗ trợ.

**Response:**
```json
{
  "success": true,
  "message": "Supported languages retrieved successfully",
  "data": {
    "sourceLanguages": ["auto", "af", "sq", "am", "ar", ...],
    "targetLanguages": ["af", "sq", "am", "ar", "hy", ...],
    "total": 109
  }
}
```

## Ngôn ngữ được hỗ trợ

API hỗ trợ 109 ngôn ngữ, bao gồm các ngôn ngữ phổ biến:
- **en**: English
- **fr**: Français
- **de**: Deutsch
- **ja**: 日本語
- **ko**: 한국어
- **zh**: 中文
- **es**: Español
- **it**: Italiano
- **ru**: Русский
- **pt**: Português
- **ar**: العربية
- **hi**: हिन्दी
- **th**: ไทย
- **id**: Bahasa Indonesia
- **ms**: Bahasa Melayu

## Demo và Test

### 1. File demo HTML
Truy cập: `https://mechamap.test/translation-demo.html`

### 2. Console demo
```javascript
// Chạy tất cả demo
await runAllDemos();

// Chạy demo riêng lẻ
await demoTranslateVietnamese();
await demoDetectLanguage();
await demoGetSupportedLanguages();
```

## Xử lý lỗi

Tất cả functions đều throw Error khi có lỗi xảy ra. Các lỗi phổ biến:

- **Validation errors**: Thiếu tham số hoặc tham số không hợp lệ
- **Length errors**: Text vượt quá 5000 ký tự
- **Network errors**: Lỗi kết nối API
- **API errors**: Lỗi từ server API

```javascript
try {
    const result = await translateVietnamese(text, targetLang);
    // Xử lý kết quả thành công
} catch (error) {
    if (error.message.includes('exceeds maximum length')) {
        // Xử lý lỗi text quá dài
    } else if (error.message.includes('HTTP error')) {
        // Xử lý lỗi network
    } else {
        // Xử lý lỗi khác
    }
}
```

## Tích hợp vào dự án

### 1. Include scripts
```html
<script src="/js/translation-service.js"></script>
```

### 2. Sử dụng trong components
```javascript
// Trong Vue.js component
methods: {
    async translateContent(text, targetLang) {
        try {
            return await translateVietnamese(text, targetLang);
        } catch (error) {
            this.$toast.error('Lỗi dịch thuật: ' + error.message);
            return text;
        }
    }
}

// Trong React component
const translateContent = async (text, targetLang) => {
    try {
        return await translateVietnamese(text, targetLang);
    } catch (error) {
        toast.error('Lỗi dịch thuật: ' + error.message);
        return text;
    }
};
```

## Performance và Best Practices

1. **Cache kết quả**: Lưu cache kết quả dịch để tránh gọi API nhiều lần
2. **Batch processing**: Gộp nhiều text ngắn thành một request
3. **Error handling**: Luôn có fallback khi API lỗi
4. **Rate limiting**: Tránh gọi API quá nhiều trong thời gian ngắn
5. **Content validation**: Kiểm tra độ dài và định dạng trước khi gọi API

## Troubleshooting

### Lỗi CORS
Nếu gặp lỗi CORS, đảm bảo API server đã cấu hình đúng headers.

### Lỗi Network
Kiểm tra kết nối internet và trạng thái API server.

### Lỗi Rate Limit
Giảm tần suất gọi API hoặc implement retry mechanism.

---

**Cập nhật lần cuối**: 2025-09-03
**Phiên bản**: 1.0.0
