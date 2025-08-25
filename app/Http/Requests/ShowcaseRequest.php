<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ShowcaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [
            // Basic Information
            'title' => [
                'required',
                'string',
                'min:5',
                'max:255',
                'regex:/^[a-zA-Z0-9\s\-_àáạảãâầấậẩẫăằắặẳẵèéẹẻẽêềếệểễìíịỉĩòóọỏõôồốộổỗơờớợởỡùúụủũưừứựửữỳýỵỷỹđĐ]+$/'
            ],
            'description' => [
                'required',
                'string',
                'min:50',
                'max:5000'
            ],
            'location' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9\s\-_,àáạảãâầấậẩẫăằắặẳẵèéẹẻẽêềếệểễìíịỉĩòóọỏõôồốộổỗơờớợởỡùúụủũưừứựửữỳýỵỷỹđĐ]+$/'
            ],
            'usage' => [
                'nullable',
                'string',
                'max:500'
            ],
            'cover_image' => [
                'required',
                'image',
                'mimes:jpeg,png,jpg,gif,webp',
                'max:5120', // 5MB
                'dimensions:min_width=400,min_height=300,max_width=4000,max_height=4000'
            ],

            // Technical Information
            'software_used' => [
                'nullable',
                'array',
                'max:10'
            ],
            'software_used.*' => [
                'string',
                'max:100',
                Rule::in([
                    'SolidWorks', 'AutoCAD', 'ANSYS', 'MATLAB', 'CATIA', 'Fusion 360',
                    'Autodesk Inventor', 'Siemens NX', 'PTC Creo', 'Rhino 3D', 'Blender',
                    'SketchUp', 'COMSOL Multiphysics', 'Abaqus', 'Simulink', 'LabVIEW', 'Khác'
                ])
            ],
            'materials' => [
                'nullable',
                'string',
                'max:1000',
                'regex:/^[a-zA-Z0-9\s\-_,().àáạảãâầấậẩẫăằắặẳẵèéẹẻẽêềếệểễìíịỉĩòóọỏõôồốộổỗơờớợởỡùúụủũưừứựửữỳýỵỷỹđĐ]+$/'
            ],
            'manufacturing_process' => [
                'nullable',
                'string',
                Rule::in([
                    'CNC Machining', 'Manual Machining', 'Grinding', 'EDM', '3D Printing',
                    'Metal 3D Printing', 'Casting', 'Forging', 'Sheet Metal', 'Stamping',
                    'Deep Drawing', 'Welding', 'Brazing', 'Soldering', 'Riveting',
                    'Injection Molding', 'Blow Molding', 'Compression Molding', 'Assembly',
                    'Surface Treatment', 'Heat Treatment', 'Hybrid Process', 'Other'
                ])
            ],
            'complexity_level' => [
                'nullable',
                Rule::in(['Beginner', 'Intermediate', 'Advanced', 'Expert'])
            ],
            'industry_application' => [
                'nullable',
                'string',
                Rule::in([
                    'automotive', 'aerospace', 'shipbuilding', 'machinery', 'electronics',
                    'oil_gas', 'renewable_energy', 'power_generation', 'construction',
                    'chemical', 'pharmaceutical', 'food_beverage', 'textile',
                    'robotics', 'automation', 'iot_industry4', 'research_development',
                    'general_manufacturing', 'maintenance_repair', 'education_training', 'other'
                ])
            ],
            'floors' => [
                'nullable',
                'string',
                Rule::in(['Prototype/Demo', 'Pilot/Thử nghiệm', 'Sản xuất nhỏ', 'Sản xuất hàng loạt', 'Quy mô công nghiệp'])
            ],
            'category' => [
                'nullable',
                'string',
                Rule::in(['design', 'manufacturing', 'analysis', 'automation', 'robotics', 'cad_cam', 'plc_scada', 'materials', 'other'])
            ],

            // Project Features (Boolean fields)
            'has_tutorial' => 'nullable|boolean',
            'has_calculations' => 'nullable|boolean',
            'has_cad_files' => 'nullable|boolean',

            // Sharing Settings (Boolean fields)
            'is_public' => 'nullable|boolean',
            'allow_downloads' => 'nullable|boolean',
            'allow_comments' => 'nullable|boolean',

            // Advanced Technical Fields
            'technical_specs' => [
                'nullable',
                'array',
                'max:20' // Maximum 20 technical specifications
            ],
            'technical_specs.*.name' => [
                'required_with:technical_specs.*.value',
                'string',
                'max:100',
                'regex:/^[a-zA-Z0-9\s\-_().àáạảãâầấậẩẫăằắặẳẵèéẹẻẽêềếệểễìíịỉĩòóọỏõôồốộổỗơờớợởỡùúụủũưừứựửữỳýỵỷỹđĐ]+$/'
            ],
            'technical_specs.*.value' => [
                'required_with:technical_specs.*.name',
                'string',
                'max:200',
                'regex:/^[a-zA-Z0-9\s\-_().×x,àáạảãâầấậẩẫăằắặẳẵèéẹẻẽêềếệểễìíịỉĩòóọỏõôồốộổỗơờớợởỡùúụủũưừứựửữỳýỵỷỹđĐ]+$/'
            ],
            'technical_specs.*.unit' => [
                'nullable',
                'string',
                'max:50',
                'regex:/^[a-zA-Z0-9\s\-_().°%àáạảãâầấậẩẫăằắặẳẵèéẹẻẽêềếệểễìíịỉĩòóọỏõôồốộổỗơờớợởỡùúụủũưừứựửữỳýỵỷỹđĐ]+$/'
            ],

            'learning_objectives' => [
                'nullable',
                'array',
                'max:10' // Maximum 10 learning objectives
            ],
            'learning_objectives.*' => [
                'string',
                'min:10',
                'max:200',
                'regex:/^[a-zA-Z0-9\s\-_().,:;àáạảãâầấậẩẫăằắặẳẵèéẹẻẽêềếệểễìíịỉĩòóọỏõôồốộổỗơờớợởỡùúụủũưừứựửữỳýỵỷỹđĐ]+$/'
            ],

            // Multiple Images
            'multiple_images' => [
                'nullable',
                'array',
                'max:10'
            ],
            'multiple_images.*' => [
                'image',
                'mimes:jpeg,png,jpg,gif,webp',
                'max:10240', // 10MB per image
                'dimensions:min_width=200,min_height=200,max_width=4000,max_height=4000'
            ],

            // File Attachments - Enhanced Security
            'file_attachments' => [
                'nullable',
                'array',
                'max:10'
            ],
            'file_attachments.*' => [
                'file',
                'max:51200', // 50MB per file
                'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,dwg,step,stp,iges,igs,jpg,jpeg,png,gif,zip,rar,7z',
                new \App\Rules\SecureFileUpload(),
                new \App\Rules\VirusScanFile(),
                new \App\Rules\FileContentValidation()
            ]
        ];

        // Add conditional validation for update requests
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['cover_image'] = [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,gif,webp',
                'max:5120',
                'dimensions:min_width=400,min_height=300,max_width=4000,max_height=4000'
            ];
        }

        return $rules;
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            // Title messages
            'title.required' => 'Tiêu đề showcase là bắt buộc.',
            'title.min' => 'Tiêu đề phải có ít nhất 5 ký tự.',
            'title.max' => 'Tiêu đề không được vượt quá 255 ký tự.',
            'title.regex' => 'Tiêu đề chỉ được chứa chữ cái, số, dấu gạch ngang và khoảng trắng.',

            // Description messages
            'description.required' => 'Mô tả chi tiết là bắt buộc.',
            'description.min' => 'Mô tả phải có ít nhất 50 ký tự để cung cấp thông tin đầy đủ.',
            'description.max' => 'Mô tả không được vượt quá 5000 ký tự.',

            // Cover image messages
            'cover_image.required' => 'Hình ảnh đại diện là bắt buộc.',
            'cover_image.image' => 'File phải là hình ảnh.',
            'cover_image.mimes' => 'Hình ảnh phải có định dạng: JPEG, PNG, JPG, GIF, WebP.',
            'cover_image.max' => 'Hình ảnh không được vượt quá 5MB.',
            'cover_image.dimensions' => 'Hình ảnh phải có kích thước tối thiểu 400x300px và tối đa 4000x4000px.',

            // Location messages
            'location.max' => 'Địa điểm không được vượt quá 255 ký tự.',
            'location.regex' => 'Địa điểm chứa ký tự không hợp lệ.',

            // Usage messages
            'usage.max' => 'Lĩnh vực ứng dụng không được vượt quá 500 ký tự.',

            // Software used messages
            'software_used.array' => 'Phần mềm sử dụng phải là danh sách.',
            'software_used.max' => 'Chỉ được chọn tối đa 10 phần mềm.',
            'software_used.*.in' => 'Phần mềm được chọn không hợp lệ.',

            // Materials messages
            'materials.max' => 'Vật liệu không được vượt quá 1000 ký tự.',
            'materials.regex' => 'Vật liệu chứa ký tự không hợp lệ.',

            // Manufacturing process messages
            'manufacturing_process.in' => 'Quy trình sản xuất được chọn không hợp lệ.',

            // Complexity level messages
            'complexity_level.in' => 'Mức độ phức tạp được chọn không hợp lệ.',

            // Industry application messages
            'industry_application.in' => 'Ngành ứng dụng được chọn không hợp lệ.',

            // Floors messages
            'floors.in' => 'Quy mô dự án được chọn không hợp lệ.',

            // Category messages
            'category.in' => 'Danh mục được chọn không hợp lệ.',

            // Multiple images messages
            'multiple_images.array' => 'Hình ảnh phải là danh sách.',
            'multiple_images.max' => 'Chỉ được upload tối đa 10 hình ảnh.',
            'multiple_images.*.image' => 'Tất cả file phải là hình ảnh.',
            'multiple_images.*.mimes' => 'Hình ảnh phải có định dạng: JPEG, PNG, JPG, GIF, WebP.',
            'multiple_images.*.max' => 'Mỗi hình ảnh không được vượt quá 10MB.',
            'multiple_images.*.dimensions' => 'Hình ảnh phải có kích thước tối thiểu 200x200px và tối đa 4000x4000px.',

            // File attachments messages
            'file_attachments.array' => 'File đính kèm phải là danh sách.',
            'file_attachments.max' => 'Chỉ được upload tối đa 10 file đính kèm.',
            'file_attachments.*.file' => 'Tất cả phải là file hợp lệ.',
            'file_attachments.*.max' => 'Mỗi file không được vượt quá 50MB.',
            'file_attachments.*.mimes' => 'File phải có định dạng: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, DWG, STEP, STP, IGES, IGS, JPG, JPEG, PNG, GIF, ZIP, RAR, 7Z.',

            // Technical specifications messages
            'technical_specs.array' => 'Thông số kỹ thuật phải là danh sách.',
            'technical_specs.max' => 'Chỉ được thêm tối đa 20 thông số kỹ thuật.',
            'technical_specs.*.name.required_with' => 'Tên thông số kỹ thuật là bắt buộc khi có giá trị.',
            'technical_specs.*.name.max' => 'Tên thông số kỹ thuật không được vượt quá 100 ký tự.',
            'technical_specs.*.name.regex' => 'Tên thông số kỹ thuật chứa ký tự không hợp lệ.',
            'technical_specs.*.value.required_with' => 'Giá trị thông số kỹ thuật là bắt buộc khi có tên.',
            'technical_specs.*.value.max' => 'Giá trị thông số kỹ thuật không được vượt quá 200 ký tự.',
            'technical_specs.*.value.regex' => 'Giá trị thông số kỹ thuật chứa ký tự không hợp lệ.',
            'technical_specs.*.unit.max' => 'Đơn vị thông số kỹ thuật không được vượt quá 50 ký tự.',
            'technical_specs.*.unit.regex' => 'Đơn vị thông số kỹ thuật chứa ký tự không hợp lệ.',

            // Learning objectives messages
            'learning_objectives.array' => 'Mục tiêu học tập phải là danh sách.',
            'learning_objectives.max' => 'Chỉ được thêm tối đa 10 mục tiêu học tập.',
            'learning_objectives.*.min' => 'Mỗi mục tiêu học tập phải có ít nhất 10 ký tự.',
            'learning_objectives.*.max' => 'Mỗi mục tiêu học tập không được vượt quá 200 ký tự.',
            'learning_objectives.*.regex' => 'Mục tiêu học tập chứa ký tự không hợp lệ.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'title' => 'tiêu đề',
            'description' => 'mô tả',
            'location' => 'địa điểm',
            'usage' => 'lĩnh vực ứng dụng',
            'cover_image' => 'hình ảnh đại diện',
            'software_used' => 'phần mềm sử dụng',
            'materials' => 'vật liệu',
            'manufacturing_process' => 'quy trình sản xuất',
            'complexity_level' => 'mức độ phức tạp',
            'industry_application' => 'ứng dụng ngành',
            'floors' => 'quy mô dự án',
            'category' => 'danh mục',
            'multiple_images' => 'hình ảnh',
            'file_attachments' => 'file đính kèm',
            'technical_specs' => 'thông số kỹ thuật',
            'learning_objectives' => 'mục tiêu học tập',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Custom validation: Check if at least one technical field is filled
            if (!$this->software_used && !$this->materials && !$this->manufacturing_process) {
                $validator->errors()->add('technical_info', 'Vui lòng điền ít nhất một thông tin kỹ thuật (phần mềm, vật liệu hoặc quy trình sản xuất).');
            }

            // Custom validation: Check file attachments consistency with has_cad_files
            if ($this->has_cad_files && !$this->file_attachments) {
                $validator->errors()->add('file_attachments', 'Bạn đã chọn "File CAD đính kèm" nhưng chưa upload file nào.');
            }

            // Custom validation: Check description content quality
            if ($this->description) {
                $wordCount = str_word_count(strip_tags($this->description));
                if ($wordCount < 20) {
                    $validator->errors()->add('description', 'Mô tả cần có ít nhất 20 từ để cung cấp thông tin đầy đủ.');
                }
            }
        });
    }
}
