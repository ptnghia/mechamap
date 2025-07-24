# Social Login với Account Type Selection - MechaMap

## Tổng quan

Tính năng này cải tiến quy trình Social Login để nhất quán với quy trình đăng ký thông thường, cho phép người dùng chọn loại tài khoản phù hợp thay vì tự động tạo với role mặc định.

## Quy trình mới

### 1. **User đã tồn tại**
- Đăng nhập trực tiếp
- Cập nhật/tạo social account
- Chuyển hướng đến dashboard

### 2. **User chưa tồn tại**
- Lưu thông tin social vào session (30 phút)
- Chuyển hướng đến trang chọn account type
- Cho phép chọn role và nhập username
- Tạo user sau khi hoàn tất

## Các thay đổi đã thực hiện

### 1. **SocialiteController Updates**

**Phương thức mới:**
- `handleNewSocialUser()` - Xử lý user mới
- `showAccountTypeSelection()` - Hiển thị trang chọn account type
- `processAccountTypeSelection()` - Xử lý form submission
- `updateOrCreateSocialAccount()` - Quản lý social accounts
- `getRedirectUrlByRole()` - Redirect theo role

**Session Management:**
```php
session([
    'social_registration' => [
        'provider' => $provider,
        'provider_id' => $socialUser->getId(),
        'name' => $socialUser->getName(),
        'email' => $socialUser->getEmail(),
        'avatar' => $socialUser->getAvatar(),
        'token' => $socialUser->token,
        'refresh_token' => $socialUser->refreshToken,
        'created_at' => now()->toISOString()
    ]
]);
```

### 2. **Routes mới**

```php
// Social Registration Routes
Route::middleware('guest')->group(function () {
    Route::get('/auth/social/account-type', [SocialiteController::class, 'showAccountTypeSelection'])
        ->name('auth.social.account-type');
    Route::post('/auth/social/account-type', [SocialiteController::class, 'processAccountTypeSelection']);
});
```

### 3. **View Template**

**File:** `resources/views/auth/social/account-type.blade.php`

**Features:**
- Hiển thị thông tin từ social provider
- Form chọn account type (member, verified_partner, manufacturer, supplier, brand)
- Input username với suggestion từ email
- Terms acceptance
- Responsive design với Bootstrap 5

### 4. **Request Validation**

**File:** `app/Http/Requests/Auth/SocialAccountTypeRequest.php`

**Validation Rules:**
- `account_type`: required, in allowed values
- `username`: required, unique, alpha_dash, min 3 chars
- `terms`: required, accepted

## Account Types Available

| Role | Mô tả | Redirect URL |
|------|-------|--------------|
| `member` | Thành viên cộng đồng (khuyến nghị) | `/dashboard` |
| `verified_partner` | Đối tác xác thực | `/partner/dashboard` |
| `manufacturer` | Nhà sản xuất | `/business/dashboard` |
| `supplier` | Nhà cung cấp | `/business/dashboard` |
| `brand` | Thương hiệu | `/business/dashboard` |

## Security Features

### 1. **Session Timeout**
- Social data expires sau 30 phút
- Tự động clear session khi hết hạn

### 2. **Validation**
- Username uniqueness check
- Account type validation
- Terms acceptance required

### 3. **Error Handling**
- Session expiry handling
- OAuth provider errors
- Database transaction safety

## User Experience

### 1. **Social Info Display**
- Avatar từ social provider
- Name và email
- Provider badge (Google/Facebook)

### 2. **Account Type Selection**
- Visual grouping (Community vs Business)
- Clear descriptions cho mỗi role
- Recommended badge cho member role

### 3. **Auto-suggestions**
- Username suggestion từ email
- Pre-filled social information

## Email Notifications

Sau khi tạo tài khoản thành công:
- Gửi email `WelcomeSocialUser`
- Chứa thông tin đăng nhập (username, password)
- Hướng dẫn sử dụng tài khoản

## Testing

### Manual Testing Flow:

1. **Truy cập:** `https://mechamap.test/login`
2. **Click:** "Login with Google" hoặc "Login with Facebook"
3. **OAuth:** Hoàn tất authentication với provider
4. **Account Type:** Chọn loại tài khoản và nhập username
5. **Complete:** Verify tài khoản được tạo và redirect đúng

### Test Cases:

```php
// Test existing user
$existingUser = User::factory()->create(['email' => 'test@example.com']);
// Should login directly

// Test new user
// Should redirect to account type selection

// Test session expiry
// Should redirect to login with error

// Test invalid account type
// Should show validation error
```

## Error Scenarios

| Scenario | Behavior |
|----------|----------|
| OAuth provider error | Redirect to login with error message |
| Session expired | Redirect to login, clear session |
| Username taken | Show validation error |
| Invalid account type | Show validation error |
| Database error | Show generic error, rollback |

## Future Enhancements

1. **Additional Providers**: GitHub, LinkedIn
2. **Profile Completion**: Step 2 for business accounts
3. **Avatar Import**: Save social avatar to user profile
4. **Account Linking**: Link multiple social accounts
5. **Progressive Registration**: Collect more info over time

## Migration Notes

- Existing social users không bị ảnh hưởng
- New social registrations sử dụng quy trình mới
- Session-based approach đảm bảo security
- Backward compatible với existing social accounts
