<?php

namespace App\Modules\Admin\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user() && $this->user()->role === 'admin';
    }

    public function rules()
    {
        $productId = $this->route('id');
        
        return [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|nullable|string',
            'image_url' => 'sometimes|nullable|string|max:255',
            'gallery' => 'sometimes|nullable|array',
            'gallery.*' => 'string|max:255',
            'price' => 'sometimes|required|numeric|min:0|max:999999.99',
            'original_price' => 'sometimes|nullable|numeric|min:0|max:999999.99',
            'currency' => 'sometimes|required|string|max:3',
            'category' => 'sometimes|nullable|string|max:255',
            'size' => 'sometimes|nullable|string|max:255',
            'volume_category' => 'sometimes|nullable|string|max:255',
            'is_available' => 'sometimes|boolean',
            'stock_quantity' => 'sometimes|required|integer|min:0',
            'rating' => 'sometimes|nullable|numeric|min:0|max:5',
            'review_count' => 'sometimes|nullable|integer|min:0',
            'tags' => 'sometimes|nullable|array',
            'tags.*' => 'string|max:255',
            'ingredients' => 'sometimes|nullable|array',
            'ingredients.*' => 'string|max:255',
            'nutrition_facts' => 'sometimes|nullable|array',
            'storage_instructions' => 'sometimes|nullable|string',
            'expiry_info' => 'sometimes|nullable|string|max:255',
            'barcode' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
                Rule::unique('products', 'barcode')->ignore($productId)
            ],
            'is_featured' => 'sometimes|boolean',
            'sort_order' => 'sometimes|nullable|integer|min:0',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'اسم المنتج مطلوب',
            'name.max' => 'اسم المنتج يجب ألا يزيد عن 255 حرف',
            'price.required' => 'سعر المنتج مطلوب',
            'price.numeric' => 'سعر المنتج يجب أن يكون رقم',
            'price.min' => 'سعر المنتج يجب أن يكون أكبر من أو يساوي صفر',
            'price.max' => 'سعر المنتج يجب ألا يزيد عن 999999.99',
            'original_price.numeric' => 'السعر الأصلي يجب أن يكون رقم',
            'currency.required' => 'العملة مطلوبة',
            'currency.max' => 'العملة يجب ألا تزيد عن 3 أحرف',
            'stock_quantity.required' => 'كمية المخزون مطلوبة',
            'stock_quantity.integer' => 'كمية المخزون يجب أن تكون رقم صحيح',
            'stock_quantity.min' => 'كمية المخزون يجب أن تكون أكبر من أو تساوي صفر',
            'rating.numeric' => 'التقييم يجب أن يكون رقم',
            'rating.min' => 'التقييم يجب أن يكون أكبر من أو يساوي صفر',
            'rating.max' => 'التقييم يجب أن يكون أقل من أو يساوي 5',
            'barcode.unique' => 'الباركود موجود بالفعل',
            'gallery.array' => 'معرض الصور يجب أن يكون مصفوفة',
            'tags.array' => 'العلامات يجب أن تكون مصفوفة',
            'ingredients.array' => 'المكونات يجب أن تكون مصفوفة',
            'nutrition_facts.array' => 'القيم الغذائية يجب أن تكون مصفوفة',
        ];
    }
}
