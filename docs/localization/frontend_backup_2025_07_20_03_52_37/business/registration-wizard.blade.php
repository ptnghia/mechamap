@extends('layouts.app')

@section('title', 'Business Registration - MechaMap')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Header -->
            <div class="text-center mb-5">
                <h1 class="display-5 fw-bold text-primary">Business Registration</h1>
                <p class="lead text-muted">Join MechaMap as a verified business partner</p>
            </div>

            <!-- Progress Steps -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-3">
                            <div class="step-indicator active" id="step1-indicator">
                                <div class="step-number">1</div>
                                <div class="step-title">Account</div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="step-indicator" id="step2-indicator">
                                <div class="step-number">2</div>
                                <div class="step-title">Business Info</div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="step-indicator" id="step3-indicator">
                                <div class="step-number">3</div>
                                <div class="step-title">Documents</div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="step-indicator" id="step4-indicator">
                                <div class="step-number">4</div>
                                <div class="step-title">Review</div>
                            </div>
                        </div>
                    </div>
                    <div class="progress mt-3" style="height: 4px;">
                        <div class="progress-bar bg-primary" id="progress-bar" style="width: 25%"></div>
                    </div>
                </div>
            </div>

            <!-- Registration Form -->
            <div class="card shadow">
                <div class="card-body p-5">
                    <!-- Step 1: Account Creation -->
                    <div class="step-content" id="step1">
                        <h3 class="mb-4">
                            <i class="fas fa-user-plus text-primary"></i>
                            Create Your Account
                        </h3>
                        <form id="step1-form">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="name" name="name" required>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="username" name="username" required>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control" id="email" name="email" required>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="phone" class="form-label">Phone Number</label>
                                        <input type="tel" class="form-control" id="phone" name="phone">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                        <input type="password" class="form-control" id="password" name="password" required>
                                        <div class="form-text">Minimum 8 characters</div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="password_confirmation" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="terms_accepted" name="terms_accepted" required>
                                    <label class="form-check-label" for="terms_accepted">
                                        I agree to the <a href="#" target="_blank">Terms of Service</a> <span class="text-danger">*</span>
                                    </label>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="privacy_accepted" name="privacy_accepted" required>
                                    <label class="form-check-label" for="privacy_accepted">
                                        I agree to the <a href="#" target="_blank">Privacy Policy</a> <span class="text-danger">*</span>
                                    </label>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Step 2: Business Information -->
                    <div class="step-content d-none" id="step2">
                        <h3 class="mb-4">
                            <i class="fas fa-building text-primary"></i>
                            Business Information
                        </h3>
                        <form id="step2-form">
                            <div class="mb-4">
                                <label for="application_type" class="form-label">Business Type <span class="text-danger">*</span></label>
                                <select class="form-select" id="application_type" name="application_type" required>
                                    <option value="">Select Business Type</option>
                                    <option value="manufacturer">Manufacturer - Nhà sản xuất</option>
                                    <option value="supplier">Supplier - Nhà cung cấp</option>
                                    <option value="brand">Brand - Nhãn hàng</option>
                                    <option value="verified_partner">Verified Partner - Đối tác xác thực</option>
                                </select>
                                <div class="form-text">Choose the type that best describes your business</div>
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="business_name" class="form-label">Business Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="business_name" name="business_name" required>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="business_type" class="form-label">Industry Type <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="business_type" name="business_type" 
                                               placeholder="e.g., Manufacturing, Trading, Services" required>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="tax_id" class="form-label">Tax ID / Business Registration Number <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="tax_id" name="tax_id" required>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="registration_number" class="form-label">Additional Registration Number</label>
                                        <input type="text" class="form-control" id="registration_number" name="registration_number">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="business_address" class="form-label">Business Address <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="business_address" name="business_address" rows="3" required></textarea>
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="business_phone" class="form-label">Business Phone</label>
                                        <input type="tel" class="form-control" id="business_phone" name="business_phone">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="business_email" class="form-label">Business Email</label>
                                        <input type="email" class="form-control" id="business_email" name="business_email">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="business_website" class="form-label">Business Website</label>
                                <input type="url" class="form-control" id="business_website" name="business_website" 
                                       placeholder="https://example.com">
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="mb-3">
                                <label for="business_description" class="form-label">Business Description</label>
                                <textarea class="form-control" id="business_description" name="business_description" rows="4" 
                                          placeholder="Describe your business activities, products, and services"></textarea>
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="years_in_business" class="form-label">Years in Business</label>
                                        <input type="number" class="form-control" id="years_in_business" name="years_in_business" min="0" max="100">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="employee_count" class="form-label">Number of Employees</label>
                                        <input type="number" class="form-control" id="employee_count" name="employee_count" min="1">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="annual_revenue" class="form-label">Annual Revenue (VND)</label>
                                        <input type="number" class="form-control" id="annual_revenue" name="annual_revenue" min="0">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Step 3: Document Upload -->
                    <div class="step-content d-none" id="step3">
                        <h3 class="mb-4">
                            <i class="fas fa-file-upload text-primary"></i>
                            Upload Verification Documents
                        </h3>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Required Documents:</strong>
                            <ul class="mb-0 mt-2" id="required-documents-list">
                                <li>Business License (Giấy phép kinh doanh)</li>
                                <li>Tax Certificate (Giấy chứng nhận thuế)</li>
                                <li>Identity Document (Giấy tờ tùy thân)</li>
                            </ul>
                        </div>

                        <div id="document-upload-area">
                            <!-- Document upload forms will be dynamically generated here -->
                        </div>

                        <div class="text-center mt-4">
                            <button type="button" class="btn btn-outline-primary" onclick="addDocumentUpload()">
                                <i class="fas fa-plus"></i> Add Another Document
                            </button>
                        </div>

                        <div id="uploaded-documents" class="mt-4">
                            <!-- Uploaded documents will be displayed here -->
                        </div>
                    </div>

                    <!-- Step 4: Review and Submit -->
                    <div class="step-content d-none" id="step4">
                        <h3 class="mb-4">
                            <i class="fas fa-check-circle text-primary"></i>
                            Review and Submit
                        </h3>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0">Account Information</h5>
                                    </div>
                                    <div class="card-body" id="account-review">
                                        <!-- Account info will be populated here -->
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0">Business Information</h5>
                                    </div>
                                    <div class="card-body" id="business-review">
                                        <!-- Business info will be populated here -->
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mt-4">
                            <div class="card-header">
                                <h5 class="mb-0">Uploaded Documents</h5>
                            </div>
                            <div class="card-body" id="documents-review">
                                <!-- Documents will be listed here -->
                            </div>
                        </div>

                        <form id="step4-form" class="mt-4">
                            <div class="mb-3">
                                <label for="preferred_language" class="form-label">Preferred Language</label>
                                <select class="form-select" id="preferred_language" name="preferred_language">
                                    <option value="vi">Tiếng Việt</option>
                                    <option value="en">English</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="email_notifications_enabled" 
                                           name="email_notifications_enabled" checked>
                                    <label class="form-check-label" for="email_notifications_enabled">
                                        Receive email notifications about application status
                                    </label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="sms_notifications_enabled" 
                                           name="sms_notifications_enabled">
                                    <label class="form-check-label" for="sms_notifications_enabled">
                                        Receive SMS notifications (optional)
                                    </label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="final_confirmation" 
                                           name="final_confirmation" required>
                                    <label class="form-check-label" for="final_confirmation">
                                        I confirm that all information provided is accurate and complete <span class="text-danger">*</span>
                                    </label>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Navigation Buttons -->
                    <div class="d-flex justify-content-between mt-5">
                        <button type="button" class="btn btn-outline-secondary" id="prev-btn" onclick="previousStep()" disabled>
                            <i class="fas fa-arrow-left"></i> Previous
                        </button>
                        <button type="button" class="btn btn-primary" id="next-btn" onclick="nextStep()">
                            Next <i class="fas fa-arrow-right"></i>
                        </button>
                        <button type="button" class="btn btn-success d-none" id="submit-btn" onclick="submitApplication()">
                            <i class="fas fa-paper-plane"></i> Submit Application
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Modal -->
<div class="modal fade" id="loadingModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center py-5">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <h5>Processing your application...</h5>
                <p class="text-muted mb-0">Please wait while we process your information.</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/frontend/business-registration.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('js/frontend/business-registration.js') }}"></script>
@endpush
