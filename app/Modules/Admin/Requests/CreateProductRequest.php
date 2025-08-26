<?php

namespace App\Modules\Admin\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateProductRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user() && $this->user()->role === 'admin';
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image_url' => 'nullable|string|max:255',
            'gallery' => 'nullable|array',
            'gallery.*' => 'string|max:255',
            'price' => 'required|numeric|min:0|max:999999.99',
            'original_price' => 'nullable|numeric|min:0|max:999999.99',
            'currency' => 'required|string|max:3',
            'category' => 'nullable|string|max:255',
            'size' => 'nullable|string|max:255',
            'volume_category' => 'nullable|string|max:255',
            'is_available' => 'boolean',
            'stock_quantity' => 'required|integer|min:0',
            'rating' => 'nullable|numeric|min:0|max:5',
            'review_count' => 'nullable|integer|min:0',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:255',
            'ingredients' => 'nullable|array',
            'ingredients.*' => 'string|max:255',
            'nutrition_facts' => 'nullable|array',
            'storage_instructions' => 'nullable|string',
            'expiry_info' => 'nullable|string|max:255',
            'barcode' => 'nullable|string|max:255|unique:products,barcode',
            'is_featured' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
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
