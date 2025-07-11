<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

/**
 * 🏢 Business Registration Request
 * 
 * Validates Step 2 of the registration wizard (business information)
 */
class BusinessRegistrationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'company_name' => [
                'required',
                'string',
                'max:255',
                'min:2',
                'regex:/^[\p{L}\p{N}\s\-\.\,\(\)&]+$/u' // Unicode letters, numbers, spaces, common business symbols
            ],
            'business_license' => [
                'required',
                'string',
                'max:100',
                'min:5',
                'regex:/^[A-Z0-9\-\/]+$/i' // Alphanumeric with hyphens and slashes
            ],
            'tax_code' => [
                'required',
                'string',
                'max:20',
                'min:10',
                'regex:/^[0-9]{10,13}$/', // 10-13 digits for Vietnamese tax codes
                'unique:users,tax_code'
            ],
            'business_description' => [
                'required',
                'string',
                'min:50',
                'max:1000'
            ],
            'business_categories' => [
                'required',
                'array',
                'min:1',
                'max:5'
            ],
            'business_categories.*' => [
                'string',
                'in:automotive,aerospace,manufacturing,materials,components,industrial,electronics,energy,construction,textiles,food,pharmaceutical,chemical,machinery,tools'
            ],
            'business_phone' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^[\+]?[0-9\s\-\(\)]+$/' // International phone format
            ],
            'business_email' => [
                'nullable',
                'email:rfc,dns',
                'max:255',
                'different:email' // Must be different from personal email
            ],
            'business_address' => [
                'nullable',
                'string',
                'max:500',
                'min:10'
            ],
            'verification_documents' => [
                'nullable',
                'array',
                'max:5' // Maximum 5 documents
            ],
            'verification_documents.*' => [
                'file',
                'mimes:pdf,jpg,jpeg,png,doc,docx',
                'max:5120', // 5MB per file
                'dimensions:max_width=4000,max_height=4000' // For images
            ]
        ];
    }

    /**
     * Get custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'company_name.required' => 'Vui lòng nhập tên công ty.',
            'company_name.min' => 'Tên công ty phải có ít nhất 2 ký tự.',
            'company_name.max' => 'Tên công ty không được vượt quá 255 ký tự.',
            'company_name.regex' => 'Tên công ty chứa ký tự không hợp lệ.',

            'business_license.required' => 'Vui lòng nhập số giấy phép kinh doanh.',
            'business_license.min' => 'Số giấy phép kinh doanh phải có ít nhất 5 ký tự.',
            'business_license.max' => 'Số giấy phép kinh doanh không được vượt quá 100 ký tự.',
            'business_license.regex' => 'Số giấy phép kinh doanh không đúng định dạng.',

            'tax_code.required' => 'Vui lòng nhập mã số thuế.',
            'tax_code.min' => 'Mã số thuế phải có ít nhất 10 số.',
            'tax_code.max' => 'Mã số thuế không được vượt quá 20 số.',
            'tax_code.regex' => 'Mã số thuế chỉ được chứa số và có độ dài 10-13 ký tự.',
            'tax_code.unique' => 'Mã số thuế đã được sử dụng.',

            'business_description.required' => 'Vui lòng nhập mô tả hoạt động kinh doanh.',
            'business_description.min' => 'Mô tả hoạt động kinh doanh phải có ít nhất 50 ký tự.',
            'business_description.max' => 'Mô tả hoạt động kinh doanh không được vượt quá 1000 ký tự.',

            'business_categories.required' => 'Vui lòng chọn ít nhất một lĩnh vực kinh doanh.',
            'business_categories.min' => 'Vui lòng chọn ít nhất một lĩnh vực kinh doanh.',
            'business_categories.max' => 'Chỉ được chọn tối đa 5 lĩnh vực kinh doanh.',
            'business_categories.*.in' => 'Lĩnh vực kinh doanh không hợp lệ.',

            'business_phone.regex' => 'Số điện thoại công ty không đúng định dạng.',
            'business_phone.max' => 'Số điện thoại công ty không được vượt quá 20 ký tự.',

            'business_email.email' => 'Email công ty không hợp lệ.',
            'business_email.max' => 'Email công ty không được vượt quá 255 ký tự.',
            'business_email.different' => 'Email công ty phải khác với email cá nhân.',

            'business_address.min' => 'Địa chỉ công ty phải có ít nhất 10 ký tự.',
            'business_address.max' => 'Địa chỉ công ty không được vượt quá 500 ký tự.',

            'verification_documents.max' => 'Chỉ được tải lên tối đa 5 tài liệu.',
            'verification_documents.*.file' => 'Tài liệu phải là file hợp lệ.',
            'verification_documents.*.mimes' => 'Tài liệu phải có định dạng: PDF, JPG, JPEG, PNG, DOC, DOCX.',
            'verification_documents.*.max' => 'Kích thước tài liệu không được vượt quá 5MB.',
            'verification_documents.*.dimensions' => 'Kích thước ảnh quá lớn (tối đa 4000x4000 pixels).',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'company_name' => 'tên công ty',
            'business_license' => 'giấy phép kinh doanh',
            'tax_code' => 'mã số thuế',
            'business_description' => 'mô tả hoạt động kinh doanh',
            'business_categories' => 'lĩnh vực kinh doanh',
            'business_phone' => 'số điện thoại công ty',
            'business_email' => 'email công ty',
            'business_address' => 'địa chỉ công ty',
            'verification_documents' => 'tài liệu xác minh',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'company_name' => trim($this->company_name),
            'business_license' => strtoupper(trim($this->business_license)),
            'tax_code' => preg_replace('/[^0-9]/', '', $this->tax_code), // Remove non-digits
            'business_description' => trim($this->business_description),
            'business_phone' => $this->business_phone ? trim($this->business_phone) : null,
            'business_email' => $this->business_email ? strtolower(trim($this->business_email)) : null,
            'business_address' => $this->business_address ? trim($this->business_address) : null,
        ]);
    }

    /**
     * Handle a passed validation attempt.
     */
    protected function passedValidation(): void
    {
        // Additional validation passed, can add custom logic here
        $this->merge([
            'company_name' => ucwords(strtolower($this->company_name)), // Proper case
        ]);
    }

    /**
     * Get available business categories
     */
    public static function getBusinessCategories(): array
    {
        return [
            'automotive' => 'Ô tô & Xe máy',
            'aerospace' => 'Hàng không & Vũ trụ',
            'manufacturing' => 'Sản xuất chế tạo',
            'materials' => 'Vật liệu & Kim loại',
            'components' => 'Linh kiện & Phụ tụng',
            'industrial' => 'Thiết bị công nghiệp',
            'electronics' => 'Điện tử & Điện lạnh',
            'energy' => 'Năng lượng & Dầu khí',
            'construction' => 'Xây dựng & Kiến trúc',
            'textiles' => 'Dệt may & Thời trang',
            'food' => 'Thực phẩm & Đồ uống',
            'pharmaceutical' => 'Dược phẩm & Y tế',
            'chemical' => 'Hóa chất & Nhựa',
            'machinery' => 'Máy móc & Thiết bị',
            'tools' => 'Dụng cụ & Công cụ',
        ];
    }

    /**
     * Get sanitized data for session storage
     */
    public function getSanitizedData(): array
    {
        return [
            'company_name' => $this->company_name,
            'business_license' => $this->business_license,
            'tax_code' => $this->tax_code,
            'business_description' => $this->business_description,
            'business_categories' => $this->business_categories,
            'business_phone' => $this->business_phone,
            'business_email' => $this->business_email,
            'business_address' => $this->business_address,
        ];
    }

    /**
     * Check if this is a valid business registration
     */
    public function isValidBusinessRegistration(): bool
    {
        $requiredFields = ['company_name', 'business_license', 'tax_code', 'business_description', 'business_categories'];
        
        foreach ($requiredFields as $field) {
            if (empty($this->$field)) {
                return false;
            }
        }

        return true;
    }
}
