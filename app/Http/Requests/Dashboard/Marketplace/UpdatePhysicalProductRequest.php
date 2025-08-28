<?php

namespace App\Http\Requests\Dashboard\Marketplace;

use App\Services\UnifiedMarketplacePermissionService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePhysicalProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        $product = $this->route('product');
        $productType = $this->input('product_type');
        
        // Check if user can edit this product
        if ($product && $product->seller_id !== $user->id) {
            return false;
        }
        
        // Check if user can sell this type of physical product
        return UnifiedMarketplacePermissionService::canSell($user, $productType);
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
            
            // Product Type & Condition
            'product_type' => 'required|in:new_product,used_product',
            'condition' => 'required|in:new,like_new,good,fair,poor',
            
            // Pricing
            'price' => 'required|numeric|min:0|max:999999999',
            'sale_price' => 'nullable|numeric|min:0|lt:price',
            
            // Inventory Management
            'stock_quantity' => 'required|integer|min:0|max:999999',
            'manage_stock' => 'boolean',
            'low_stock_threshold' => 'nullable|integer|min:1|max:1000',
            
            // Physical Specifications
            'weight' => 'nullable|numeric|min:0|max:999999',
            'length' => 'nullable|numeric|min:0|max:999999',
            'width' => 'nullable|numeric|min:0|max:999999',
            'height' => 'nullable|numeric|min:0|max:999999',
            'material' => 'nullable|string|max:191',
            'manufacturing_process' => 'nullable|string|max:191',
            
            // Technical Information
            'technical_specs' => 'nullable|array|max:50',
            'technical_specs.*' => 'string|max:500',
            'mechanical_properties' => 'nullable|array|max:50',
            'mechanical_properties.*' => 'string|max:500',
            'standards_compliance' => 'nullable|array|max:20',
            'standards_compliance.*' => 'string|max:100',
            'industry_category' => 'nullable|string|max:191',
            
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
            
            // Remove existing images
            'remove_featured_image' => 'nullable|boolean',
            'remove_images' => 'nullable|string',
            
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
            
            'product_type.required' => 'Loại sản phẩm là bắt buộc.',
            'product_type.in' => 'Loại sản phẩm không hợp lệ.',
            'condition.required' => 'Tình trạng sản phẩm là bắt buộc.',
            'condition.in' => 'Tình trạng sản phẩm không hợp lệ.',
            
            'price.required' => 'Giá sản phẩm là bắt buộc.',
            'price.numeric' => 'Giá sản phẩm phải là số.',
            'price.min' => 'Giá sản phẩm không được âm.',
            'price.max' => 'Giá sản phẩm quá lớn.',
            'sale_price.numeric' => 'Giá khuyến mãi phải là số.',
            'sale_price.min' => 'Giá khuyến mãi không được âm.',
            'sale_price.lt' => 'Giá khuyến mãi phải nhỏ hơn giá gốc.',
            
            'stock_quantity.required' => 'Số lượng tồn kho là bắt buộc.',
            'stock_quantity.integer' => 'Số lượng tồn kho phải là số nguyên.',
            'stock_quantity.min' => 'Số lượng tồn kho không được âm.',
            'stock_quantity.max' => 'Số lượng tồn kho quá lớn.',
            'low_stock_threshold.integer' => 'Ngưỡng cảnh báo tồn kho phải là số nguyên.',
            'low_stock_threshold.min' => 'Ngưỡng cảnh báo tồn kho phải ít nhất 1.',
            'low_stock_threshold.max' => 'Ngưỡng cảnh báo tồn kho không được vượt quá 1000.',
            
            'weight.numeric' => 'Trọng lượng phải là số.',
            'weight.min' => 'Trọng lượng không được âm.',
            'weight.max' => 'Trọng lượng quá lớn.',
            'length.numeric' => 'Chiều dài phải là số.',
            'length.min' => 'Chiều dài không được âm.',
            'length.max' => 'Chiều dài quá lớn.',
            'width.numeric' => 'Chiều rộng phải là số.',
            'width.min' => 'Chiều rộng không được âm.',
            'width.max' => 'Chiều rộng quá lớn.',
            'height.numeric' => 'Chiều cao phải là số.',
            'height.min' => 'Chiều cao không được âm.',
            'height.max' => 'Chiều cao quá lớn.',
            'material.max' => 'Chất liệu không được vượt quá 191 ký tự.',
            'manufacturing_process.max' => 'Quy trình sản xuất không được vượt quá 191 ký tự.',
            
            'technical_specs.array' => 'Thông số kỹ thuật phải là danh sách.',
            'technical_specs.max' => 'Không được vượt quá 50 thông số kỹ thuật.',
            'technical_specs.*.max' => 'Mỗi thông số kỹ thuật không được vượt quá 500 ký tự.',
            'mechanical_properties.array' => 'Tính chất cơ học phải là danh sách.',
            'mechanical_properties.max' => 'Không được vượt quá 50 tính chất cơ học.',
            'mechanical_properties.*.max' => 'Mỗi tính chất cơ học không được vượt quá 500 ký tự.',
            'standards_compliance.array' => 'Tiêu chuẩn tuân thủ phải là danh sách.',
            'standards_compliance.max' => 'Không được vượt quá 20 tiêu chuẩn tuân thủ.',
            'standards_compliance.*.max' => 'Mỗi tiêu chuẩn tuân thủ không được vượt quá 100 ký tự.',
            'industry_category.max' => 'Danh mục ngành không được vượt quá 191 ký tự.',
            
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
            // Validate condition based on product type
            $productType = $this->input('product_type');
            $condition = $this->input('condition');
            
            if ($productType === 'new_product' && $condition !== 'new') {
                $validator->errors()->add('condition', 'Sản phẩm mới phải có tình trạng "Mới".');
            }
            
            if ($productType === 'used_product' && $condition === 'new') {
                $validator->errors()->add('condition', 'Sản phẩm cũ không thể có tình trạng "Mới".');
            }
            
            // Validate low stock threshold
            $stockQuantity = $this->input('stock_quantity');
            $lowStockThreshold = $this->input('low_stock_threshold');
            
            if ($lowStockThreshold && $lowStockThreshold > $stockQuantity) {
                $validator->errors()->add('low_stock_threshold', 'Ngưỡng cảnh báo không được lớn hơn số lượng tồn kho.');
            }
            
            // Validate dimensions consistency
            $length = $this->input('length');
            $width = $this->input('width');
            $height = $this->input('height');
            
            if (($length || $width || $height) && (!$length || !$width || !$height)) {
                $validator->errors()->add('dimensions', 'Nếu nhập kích thước, phải nhập đầy đủ chiều dài, rộng và cao.');
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
            'product_type' => 'loại sản phẩm',
            'condition' => 'tình trạng',
            'price' => 'giá',
            'sale_price' => 'giá khuyến mãi',
            'stock_quantity' => 'số lượng tồn kho',
            'low_stock_threshold' => 'ngưỡng cảnh báo tồn kho',
            'weight' => 'trọng lượng',
            'length' => 'chiều dài',
            'width' => 'chiều rộng',
            'height' => 'chiều cao',
            'material' => 'chất liệu',
            'manufacturing_process' => 'quy trình sản xuất',
            'technical_specs' => 'thông số kỹ thuật',
            'mechanical_properties' => 'tính chất cơ học',
            'standards_compliance' => 'tiêu chuẩn tuân thủ',
            'industry_category' => 'danh mục ngành',
            'images' => 'hình ảnh',
            'featured_image' => 'hình đại diện',
            'tags' => 'tags',
            'meta_title' => 'meta title',
            'meta_description' => 'meta description',
        ];
    }
}
