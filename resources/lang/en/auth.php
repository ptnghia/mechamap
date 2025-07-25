<?php

return [

    'failed' => 'These credentials do not match our records.',
    'password' => 'The provided password is incorrect.',
    'throttle' => 'Too many login attempts. Please try again in :seconds seconds.',

    // Login
    'login' => [
        'title' => 'Login',
        'welcome_back' => 'Welcome back',
        'email_or_username' => 'Email or Username',
        'password' => 'Password',
        'remember' => 'Remember me',
        'submit' => 'Login',
        'forgot_password' => 'Forgot password?',
        'no_account' => 'Don\'t have an account?',
        'register_now' => 'Register now',
        'or_login_with' => 'Or login with',
        'google' => 'Login with Google',
        'facebook' => 'Login with Facebook',
    ],

    // Register
    'register' => [
        'title' => 'Register',
        'name' => 'Full Name',
        'email' => 'Email Address',
        'password' => 'Password',
        'password_confirmation' => 'Confirm Password',
        'submit' => 'Register',
        'have_account' => 'Already have an account?',
        'login_now' => 'Login now',
        'agree_terms' => 'I agree to the',
        'terms_of_service' => 'Terms of Service',
        'privacy_policy' => 'Privacy Policy',
        'join_community' => 'Join MechaMap Community',
        'create_account' => 'Create New Account',

        // Additional register keys
        'account_type_placeholder' => 'Select account type',
        'community_member_title' => 'Community Member',
        'member_role' => 'Member',
        'member_role_desc' => 'Full forum access, create posts and participate in discussions',
        'business_partner_title' => 'Business Partner',
        'manufacturer_role' => 'Manufacturer',
        'manufacturer_role_desc' => 'Manufacture and sell mechanical products, industrial equipment',
        'supplier_role' => 'Supplier',
        'supplier_role_desc' => 'Supply components, materials and support services',
        'brand_role' => 'Brand',
        'brand_role_desc' => 'Promote brand and products on marketplace',
        'account_type_help' => 'Select the account type that best fits your intended use',
        'terms_agreement' => 'I agree to the <a href="/terms" target="_blank">Terms of Service</a> and <a href="/privacy" target="_blank">Privacy Policy</a>',
        'already_have_account' => 'Already have an account?',
        'sign_in' => 'Sign in',

        // Wizard keys
        'step1_title' => 'Step 1: Personal Information',
        'wizard_title' => 'Business Account Registration',
        'step1_subtitle' => 'Create account and choose membership type',
        'continue_button' => 'Continue',
        'personal_info_title' => 'Personal Information',
        'personal_info_description' => 'Enter your personal information to create your account',
        'name_valid' => 'Name is valid',
        'username_available' => 'Username is available',
        'email_valid' => 'Email is valid',
        'email_help' => 'We will send a verification email to this address',
        'account_type_title' => 'Choose Account Type',
        'account_type_description' => 'Select the account type that best fits your intended use',
        'community_member_description' => 'Join the community to learn, share and connect',
        'recommended' => 'Recommended',
        'guest_role' => 'Guest',
        'guest_role_desc' => 'View-only access, cannot create posts or comments',
        'note_community' => 'You can upgrade your account after registration.',
        'business_partner_description' => 'For businesses looking to sell products or services',
        'note_business' => 'Business accounts require verification before full feature access.',

        // Additional wizard keys
        'step_indicator' => 'Step :current/:total',
        'step1_label' => 'Personal Information',
        'step2_label' => 'Verification',
        'progress_complete' => ':percent% complete',
        'security_note' => 'Your information is secure and encrypted',
        'auto_saving' => 'Auto saving...',
        'step_default' => 'Step :number',
        'errors_occurred' => 'Errors occurred',
    ],

    // Password Reset
    'forgot_password' => [
        'title' => 'Forgot Password',
        'description' => 'Enter your email to receive a password reset link',
        'email' => 'Email Address',
        'submit' => 'Send Reset Link',
        'back_to_login' => 'Back to Login',
        'reset_sent' => 'Password reset link has been sent!',
    ],

    'reset_password' => [
        'title' => 'Reset Password',
        'subtitle' => 'Create a new password for your account',
        'heading' => 'Create New Password',
        'description' => 'Please enter a new password for your account',
        'email' => 'Email Address',
        'password' => 'New Password',
        'new_password' => 'New Password',
        'password_confirmation' => 'Confirm New Password',
        'confirm_password' => 'Confirm New Password',
        'password_placeholder' => 'Enter new password',
        'confirm_placeholder' => 'Re-enter new password',
        'password_hint' => 'Use at least 8 characters with letters, numbers and symbols',
        'submit' => 'Reset Password',
        'update_password' => 'Update Password',
        'success' => 'Password has been reset successfully!',
        'password_match' => 'Passwords match',
        'password_mismatch' => 'Passwords do not match',
        'tips' => [
            'strong_title' => 'Strong Password',
            'strong_desc' => 'Use at least 8 characters including uppercase, lowercase, numbers and symbols',
            'avoid_personal_title' => 'Avoid Personal Information',
            'avoid_personal_desc' => 'Do not use names, birth dates, phone numbers in passwords',
            'unique_title' => 'Unique Password',
            'unique_desc' => 'Do not reuse passwords from other accounts',
        ],
    ],

    // Logout
    'logout' => [
        'title' => 'Logout',
        'confirm' => 'Are you sure you want to logout?',
        'success' => 'Successfully logged out',
    ],

    // Email Verification
    'verification' => [
        'title' => 'Email Verification',
        'description' => 'Please check your email and click the verification link',
        'resend' => 'Resend verification email',
        'verified' => 'Email has been verified successfully!',
        'not_verified' => 'Email not verified',
    ],

    // User Roles
    'roles' => [
        'admin' => 'Administrator',
        'moderator' => 'Moderator',
        'senior_member' => 'Senior Member',
        'member' => 'Member',
        'guest' => 'Guest',
        'verified_partner' => 'Verified Partner',
        'manufacturer' => 'Manufacturer',
        'supplier' => 'Supplier',
        'brand' => 'Brand',
    ],

    // Messages
    'messages' => [
        'login_success' => 'Login successful!',
        'login_failed' => 'Invalid login credentials',
        'register_success' => 'Registration successful! Please check your email to verify your account',
        'logout_success' => 'Successfully logged out',
        'password_reset_sent' => 'Password reset link has been sent to your email',
        'password_reset_success' => 'Password has been reset successfully',
        'email_verified' => 'Email has been verified successfully',
        'account_locked' => 'Account has been locked',
        'too_many_attempts' => 'Too many attempts. Please try again later',
    ],

    // Password Confirmation
    'confirm_password' => 'Confirm Password',
    'confirm' => 'Confirm',
    'secure_area_message' => 'This is a secure area of the application. Please confirm your password before continuing.',

    // Additional keys for Blade compatibility
    'email_or_username_label' => 'Email or Username',
    'password_label' => 'Password',
    'remember_login' => 'Remember me',
    'forgot_password_link' => 'Forgot password?',
    'login_button' => 'Login',
    'or_login_with' => 'or login with',
    'login_with_google' => 'Login with Google',
    'login_with_facebook' => 'Login with Facebook',
    'no_account' => 'Don\'t have an account?',
    'register_now' => 'Register now',

    // Community features
    'connect_engineers' => 'Connect Engineers',
    'join_discussions' => 'Join Discussions',
    'share_experience' => 'Share Experience',
    'marketplace_products' => 'Marketplace Products',

    // Registration additional keys
    'create_new_account' => 'Create New Account',
    'welcome_to_mechamap' => 'Welcome to MechaMap',
    'create_account_journey' => 'Create an account to start your engineering journey',
    'full_name_label' => 'Full Name',
    'full_name_placeholder' => 'Enter your full name',
    'username_label' => 'Username',
    'username_placeholder' => 'Choose a username',
    'username_help' => 'Username can only contain letters, numbers and underscores',
    'email_label' => 'Email Address',
    'email_placeholder' => 'Enter your email address',
    'password_placeholder' => 'Create a strong password',
    'password_help' => 'Password must be at least 8 characters with uppercase, lowercase and numbers',
    'confirm_password_label' => 'Confirm Password',
    'confirm_password_placeholder' => 'Re-enter your password',
    'account_type_label' => 'Account Type',

];
