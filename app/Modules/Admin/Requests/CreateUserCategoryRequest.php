<?php

namespace App\Modules\Admin\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateUserCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user() && $this->user()->role === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:10|unique:user_categories,name',
            'display_name' => 'required|string|max:255',
            'display_name_en' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'min_purchase_amount' => 'required|numeric|min:0|max:999999999.99',
            'max_purchase_amount' => 'nullable|numeric|min:0|max:999999999.99|gt:min_purchase_amount',
            'discount_percentage' => 'required|numeric|min:0|max:100',
            'benefits' => 'nullable|array',
            'benefits.*' => 'string|max:255',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
        ];
    }

    /**
     * Get custom validation messages
     */
    public function messages()
    {
        return [
            'name.required' => 'اسم الفئة مطلوب',
            'name.unique' => 'اسم الفئة موجود بالفعل',
            'name.max' => 'اسم الفئة يجب ألا يزيد عن 10 أحرف',
            'display_name.required' => 'الاسم المعروض مطلوب',
            'display_name.max' => 'الاسم المعروض يجب ألا يزيد عن 255 حرف',
            'min_purchase_amount.required' => 'الحد الأدنى للشراء مطلوب',
            'min_purchase_amount.numeric' => 'الحد الأدنى للشراء يجب أن يكون رقم',
            'min_purchase_amount.min' => 'الحد الأدنى للشراء يجب أن يكون أكبر من أو يساوي صفر',
            'max_purchase_amount.numeric' => 'الحد الأقصى للشراء يجب أن يكون رقم',
            'max_purchase_amount.gt' => 'الحد الأقصى للشراء يجب أن يكون أكبر من الحد الأدنى',
            'discount_percentage.required' => 'نسبة الخصم مطلوبة',
            'discount_percentage.numeric' => 'نسبة الخصم يجب أن تكون رقم',
            'discount_percentage.min' => 'نسبة الخصم يجب أن تكون أكبر من أو تساوي صفر',
            'discount_percentage.max' => 'نسبة الخصم يجب أن تكون أقل من أو تساوي 100',
            'benefits.array' => 'المميزات يجب أن تكون مصفوفة',
            'benefits.*.string' => 'كل ميزة يجب أن تكون نص',
            'benefits.*.max' => 'كل ميزة يجب ألا تزيد عن 255 حرف',
        ];
    }
}
