<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use App\Mail\Auth\VerifyEmailMail;
use App\Mail\Auth\WelcomeMail;
use App\Mail\Business\VerificationStatusMail;
use App\Models\User;

// Test routes for email templates (only in development)
if (app()->environment(['local', 'development'])) {
    
    Route::prefix('test-email')->group(function () {
        
        // Test email verification template
        Route::get('/verify', function () {
            $user = User::first() ?? User::factory()->make([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'role' => 'member'
            ]);
            
            $verificationUrl = 'https://mechamap.test/verify-email/123/hash123';
            $stats = [
                'users' => '64+',
                'discussions' => '118+',
                'showcases' => '25+'
            ];
            
            return new VerifyEmailMail($user, $verificationUrl, $stats);
        });
        
        // Test welcome email template
        Route::get('/welcome', function () {
            $user = User::first() ?? User::factory()->make([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'role' => 'member'
            ]);
            
            return new WelcomeMail($user);
        });
        
        // Test business verification email template - approved
        Route::get('/business-approved', function () {
            $user = User::first() ?? User::factory()->make([
                'name' => 'Công ty TNHH Test',
                'email' => 'business@example.com',
                'role' => 'manufacturer'
            ]);
            
            $businessInfo = [
                'company_name' => 'Công ty TNHH Cơ Khí Precision',
                'tax_code' => '0123456789001'
            ];
            
            return new VerificationStatusMail($user, 'approved', $businessInfo);
        });
        
        // Test business verification email template - rejected
        Route::get('/business-rejected', function () {
            $user = User::first() ?? User::factory()->make([
                'name' => 'Công ty TNHH Test',
                'email' => 'business@example.com',
                'role' => 'manufacturer'
            ]);
            
            $businessInfo = [
                'company_name' => 'Công ty TNHH Cơ Khí Precision',
                'tax_code' => '0123456789001'
            ];
            
            $rejectionReason = 'Giấy phép kinh doanh không rõ ràng và thông tin liên hệ chưa chính xác.';
            
            return new VerificationStatusMail($user, 'rejected', $businessInfo, $rejectionReason);
        });
        
        // Test business verification email template - pending
        Route::get('/business-pending', function () {
            $user = User::first() ?? User::factory()->make([
                'name' => 'Công ty TNHH Test',
                'email' => 'business@example.com',
                'role' => 'manufacturer'
            ]);
            
            $businessInfo = [
                'company_name' => 'Công ty TNHH Cơ Khí Precision',
                'tax_code' => '0123456789001'
            ];
            
            return new VerificationStatusMail($user, 'pending', $businessInfo);
        });
        
        // Send actual test email
        Route::get('/send/{type}', function ($type) {
            $testEmail = 'test@lifetech-media.vn'; // Your test email
            
            $user = User::factory()->make([
                'name' => 'Test User',
                'email' => $testEmail,
                'role' => 'member'
            ]);
            
            switch ($type) {
                case 'verify':
                    $verificationUrl = 'https://mechamap.test/verify-email/123/hash123';
                    $stats = ['users' => '64+', 'discussions' => '118+', 'showcases' => '25+'];
                    Mail::to($testEmail)->send(new VerifyEmailMail($user, $verificationUrl, $stats));
                    break;
                    
                case 'welcome':
                    Mail::to($testEmail)->send(new WelcomeMail($user));
                    break;
                    
                case 'business-approved':
                    $user->role = 'manufacturer';
                    $businessInfo = [
                        'company_name' => 'Công ty TNHH Cơ Khí Precision',
                        'tax_code' => '0123456789001'
                    ];
                    Mail::to($testEmail)->send(new VerificationStatusMail($user, 'approved', $businessInfo));
                    break;
                    
                default:
                    return 'Invalid email type';
            }
            
            return "Test email '{$type}' sent to {$testEmail}";
        });
        
    });
    
}
