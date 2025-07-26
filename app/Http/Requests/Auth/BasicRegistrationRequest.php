<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

/**
 * 📝 Basic Registration Request
 *
 * Validates Step 1 of the registration wizard (basic information)
 */
class BasicRegistrationRequest extends FormRequest
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
            'name' => [
                'required',
                'string',
                'max:255',
                'min:2',
                'regex:/^[\p{L}\s\-\.\']+$/u' // Unicode letters, spaces, hyphens, dots, apostrophes
            ],
            'username' => [
                'required',
                'string',
                'max:255',
                'min:3',
                'unique:users,username',
                'alpha_dash',
                'not_in:admin,root,api,www,test,null,undefined,system,support,help,info,contact,about,home,index,main,default'
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users,email'
            ],
            'password' => [
                'required',
                'confirmed',
                Password::defaults()
                    ->min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
            ],
            'account_type' => [
                'required',
                'string',
                'in:member,guest,manufacturer,supplier,brand'
            ],
            'terms' => [
                'required',
                'accepted'
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
            'name.required' => 'Vui lòng nhập họ và tên.',
            'name.min' => 'Họ và tên phải có ít nhất 2 ký tự.',
            'name.max' => 'Họ và tên không được vượt quá 255 ký tự.',
            'name.regex' => 'Họ và tên chỉ được chứa chữ cái, khoảng trắng, dấu gạch ngang, dấu chấm và dấu nháy đơn.',

            'username.required' => 'Vui lòng nhập tên đăng nhập.',
            'username.min' => 'Tên đăng nhập phải có ít nhất 3 ký tự.',
            'username.max' => 'Tên đăng nhập không được vượt quá 255 ký tự.',
            'username.unique' => 'Tên đăng nhập đã được sử dụng. Vui lòng chọn tên khác.',
            'username.alpha_dash' => 'Tên đăng nhập chỉ được chứa chữ cái, số, dấu gạch ngang (-) và gạch dưới (_).',
            'username.not_in' => 'Tên đăng nhập này không được phép sử dụng. Vui lòng chọn tên khác.',

            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'email.email' => 'Địa chỉ email không đúng định dạng. Vui lòng kiểm tra lại (ví dụ: user@example.com).',
            'email.unique' => 'Địa chỉ email này đã được đăng ký. Vui lòng sử dụng email khác hoặc đăng nhập.',
            'email.max' => 'Địa chỉ email không được vượt quá 255 ký tự.',

            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp. Vui lòng nhập lại.',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'password.mixed' => 'Mật khẩu phải chứa cả chữ hoa và chữ thường.',
            'password.letters' => 'Mật khẩu phải chứa ít nhất một chữ cái.',
            'password.numbers' => 'Mật khẩu phải chứa ít nhất một số.',

            'account_type.required' => 'Vui lòng chọn loại tài khoản.',
            'account_type.in' => 'Loại tài khoản không hợp lệ.',

            'terms.required' => 'Bạn phải đồng ý với điều khoản sử dụng.',
            'terms.accepted' => 'Bạn phải đồng ý với điều khoản sử dụng.',
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
            'name' => 'họ và tên',
            'username' => 'tên đăng nhập',
            'email' => 'email',
            'password' => 'mật khẩu',
            'password_confirmation' => 'xác nhận mật khẩu',
            'account_type' => 'loại tài khoản',
            'terms' => 'điều khoản sử dụng',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => strtolower($this->email),
            'username' => strtolower($this->username),
            'name' => trim($this->name),
        ]);
    }

    /**
     * Handle a passed validation attempt.
     */
    protected function passedValidation(): void
    {
        // Additional validation passed, can add custom logic here
        $this->merge([
            'name' => ucwords(strtolower($this->name)), // Proper case
        ]);
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function getValidationErrorsForField(string $field): array
    {
        $validator = \Validator::make($this->all(), $this->rules(), $this->messages());

        if ($validator->fails()) {
            return $validator->errors()->get($field);
        }

        return [];
    }

    /**
     * Check if account type requires business information
     */
    public function requiresBusinessInfo(): bool
    {
        return in_array($this->account_type, ['manufacturer', 'supplier', 'brand']);
    }

    /**
     * Get sanitized data for session storage
     */
    public function getSanitizedData(): array
    {
        return [
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'password' => $this->password, // Will be hashed in service
            'account_type' => $this->account_type,
            'terms_accepted' => $this->boolean('terms'),
        ];
    }
}
