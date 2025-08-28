<?php

namespace App\Http\Requests\Dashboard\Marketplace;

use App\Services\UnifiedMarketplacePermissionService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDigitalProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        
        // Check if user can sell digital products
        return UnifiedMarketplacePermissionService::canSell($user, 'digital');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            // Basic Information
            'name' => 'required|string|max:191',
            'description' => 'required|string|min:50',
            'short_description' => 'nullable|string|max:500',
            'product_category_id' => 'required|exists:product_categories,id',
            
            // Pricing
            'price' => 'required|numeric|min:0|max:999999999',
            'sale_price' => 'nullable|numeric|min:0|lt:price',
            
            // Digital Product Specific
            'download_limit' => 'nullable|integer|min:1|max:1000',
            'file_formats' => 'nullable|array|max:20',
            'file_formats.*' => 'string|max:50|regex:/^[a-zA-Z0-9\-_\.]+$/',
            'software_compatibility' => 'nullable|array|max:20',
            'software_compatibility.*' => 'string|max:100',
            
            // Digital Files (Required for digital products)
            'digital_files' => 'required|array|min:1|max:10',
            'digital_files.*' => [
                'required',
                'file',
                'mimes:dwg,dxf,step,stp,iges,igs,stl,pdf,doc,docx,zip,rar,7z,3dm,skp,f3d,ipt,iam,prt,asm,sldprt,sldasm',
                'max:51200', // 50MB
            ],
            
            // Images
            'images' => 'nullable|array|max:10',
            'images.*' => [
                'image',
                'mimes:jpeg,png,jpg,gif,webp',
                'max:2048', // 2MB
                'dimensions:min_width=300,min_height=300,max_width=4000,max_height=4000'
            ],
            'featured_image' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,gif,webp',
                'max:2048',
                'dimensions:min_width=400,min_height=400,max_width=4000,max_height=4000'
            ],
            
            // SEO & Meta
            'tags' => 'nullable|string|max:500',
            'meta_title' => 'nullable|string|max:191',
            'meta_description' => 'nullable|string|max:500',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Tên sản phẩm là bắt buộc.',
            'name.max' => 'Tên sản phẩm không được vượt quá 191 ký tự.',
            'description.required' => 'Mô tả sản phẩm là bắt buộc.',
            'description.min' => 'Mô tả sản phẩm phải có ít nhất 50 ký tự.',
            'product_category_id.required' => 'Danh mục sản phẩm là bắt buộc.',
            'product_category_id.exists' => 'Danh mục sản phẩm không hợp lệ.',
            
            'price.required' => 'Giá sản phẩm là bắt buộc.',
            'price.numeric' => 'Giá sản phẩm phải là số.',
            'price.min' => 'Giá sản phẩm không được âm.',
            'price.max' => 'Giá sản phẩm quá lớn.',
            'sale_price.numeric' => 'Giá khuyến mãi phải là số.',
            'sale_price.min' => 'Giá khuyến mãi không được âm.',
            'sale_price.lt' => 'Giá khuyến mãi phải nhỏ hơn giá gốc.',
            
            'download_limit.integer' => 'Giới hạn tải xuống phải là số nguyên.',
            'download_limit.min' => 'Giới hạn tải xuống phải ít nhất 1.',
            'download_limit.max' => 'Giới hạn tải xuống không được vượt quá 1000.',
            
            'file_formats.array' => 'Định dạng file phải là danh sách.',
            'file_formats.max' => 'Không được vượt quá 20 định dạng file.',
            'file_formats.*.regex' => 'Định dạng file chỉ được chứa chữ cái, số, dấu gạch ngang và dấu chấm.',
            
            'software_compatibility.array' => 'Phần mềm tương thích phải là danh sách.',
            'software_compatibility.max' => 'Không được vượt quá 20 phần mềm tương thích.',
            
            'digital_files.required' => 'Phải upload ít nhất một file digital.',
            'digital_files.array' => 'Digital files phải là danh sách file.',
            'digital_files.min' => 'Phải upload ít nhất một file digital.',
            'digital_files.max' => 'Không được upload quá 10 file digital.',
            'digital_files.*.required' => 'File digital là bắt buộc.',
            'digital_files.*.file' => 'Digital files phải là file hợp lệ.',
            'digital_files.*.mimes' => 'File digital phải có định dạng: dwg, dxf, step, stp, iges, igs, stl, pdf, doc, docx, zip, rar, 7z, 3dm, skp, f3d, ipt, iam, prt, asm, sldprt, sldasm.',
            'digital_files.*.max' => 'Mỗi file digital không được vượt quá 50MB.',
            
            'images.array' => 'Hình ảnh phải là danh sách.',
            'images.max' => 'Không được upload quá 10 hình ảnh.',
            'images.*.image' => 'File phải là hình ảnh.',
            'images.*.mimes' => 'Hình ảnh phải có định dạng: jpeg, png, jpg, gif, webp.',
            'images.*.max' => 'Mỗi hình ảnh không được vượt quá 2MB.',
            'images.*.dimensions' => 'Hình ảnh phải có kích thước từ 300x300 đến 4000x4000 pixels.',
            
            'featured_image.image' => 'Hình đại diện phải là hình ảnh.',
            'featured_image.mimes' => 'Hình đại diện phải có định dạng: jpeg, png, jpg, gif, webp.',
            'featured_image.max' => 'Hình đại diện không được vượt quá 2MB.',
            'featured_image.dimensions' => 'Hình đại diện phải có kích thước từ 400x400 đến 4000x4000 pixels.',
            
            'tags.max' => 'Tags không được vượt quá 500 ký tự.',
            'meta_title.max' => 'Meta title không được vượt quá 191 ký tự.',
            'meta_description.max' => 'Meta description không được vượt quá 500 ký tự.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Validate total file size
            if ($this->hasFile('digital_files')) {
                $totalSize = 0;
                foreach ($this->file('digital_files') as $file) {
                    $totalSize += $file->getSize();
                }
                
                // Max total size: 500MB
                if ($totalSize > 524288000) {
                    $validator->errors()->add('digital_files', 'Tổng dung lượng các file digital không được vượt quá 500MB.');
                }
            }
            
            // Validate file name duplicates
            if ($this->hasFile('digital_files')) {
                $fileNames = [];
                foreach ($this->file('digital_files') as $file) {
                    $originalName = $file->getClientOriginalName();
                    if (in_array($originalName, $fileNames)) {
                        $validator->errors()->add('digital_files', 'Không được upload các file có tên trùng nhau: ' . $originalName);
                        break;
                    }
                    $fileNames[] = $originalName;
                }
            }
            
            // Validate tags format
            if ($this->filled('tags')) {
                $tags = explode(',', $this->input('tags'));
                if (count($tags) > 20) {
                    $validator->errors()->add('tags', 'Không được có quá 20 tags.');
                }
                
                foreach ($tags as $tag) {
                    $tag = trim($tag);
                    if (strlen($tag) > 50) {
                        $validator->errors()->add('tags', 'Mỗi tag không được vượt quá 50 ký tự.');
                        break;
                    }
                }
            }
        });
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'tên sản phẩm',
            'description' => 'mô tả',
            'short_description' => 'mô tả ngắn',
            'product_category_id' => 'danh mục',
            'price' => 'giá',
            'sale_price' => 'giá khuyến mãi',
            'download_limit' => 'giới hạn tải xuống',
            'file_formats' => 'định dạng file',
            'software_compatibility' => 'phần mềm tương thích',
            'digital_files' => 'file digital',
            'images' => 'hình ảnh',
            'featured_image' => 'hình đại diện',
            'tags' => 'tags',
            'meta_title' => 'meta title',
            'meta_description' => 'meta description',
        ];
    }
}
