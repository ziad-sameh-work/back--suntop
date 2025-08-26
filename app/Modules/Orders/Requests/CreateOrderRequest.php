<?php

namespace App\Modules\Orders\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'merchant_id' => 'required|string|exists:merchants,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|string|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'delivery_address' => 'required|array',
            'delivery_address.street' => 'required|string|max:255',
            'delivery_address.building' => 'required|string|max:100',
            'delivery_address.apartment' => 'nullable|string|max:100',
            'delivery_address.city' => 'required|string|max:100',
            'delivery_address.district' => 'required|string|max:100',
            'delivery_address.postal_code' => 'nullable|string|max:20',
            'delivery_address.phone' => 'required|string|max:20',
            'delivery_address.notes' => 'nullable|string|max:500',
            'payment_method' => 'required|string|in:cash_on_delivery,credit_card,wallet',
            'offer_code' => 'nullable|string|exists:offers,code',
            'use_loyalty_points' => 'nullable|integer|min:0',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'merchant_id.required' => 'معرف التاجر مطلوب',
            'merchant_id.exists' => 'التاجر غير موجود',
            'items.required' => 'عناصر الطلب مطلوبة',
            'items.min' => 'يجب أن يحتوي الطلب على عنصر واحد على الأقل',
            'items.*.product_id.required' => 'معرف المنتج مطلوب',
            'items.*.product_id.exists' => 'المنتج غير موجود',
            'items.*.quantity.required' => 'كمية المنتج مطلوبة',
            'items.*.quantity.min' => 'الكمية يجب أن تكون أكبر من صفر',
            'items.*.unit_price.required' => 'سعر المنتج مطلوب',
            'delivery_address.required' => 'عنوان التوصيل مطلوب',
            'delivery_address.street.required' => 'اسم الشارع مطلوب',
            'delivery_address.building.required' => 'رقم المبنى مطلوب',
            'delivery_address.city.required' => 'المدينة مطلوبة',
            'delivery_address.district.required' => 'المنطقة مطلوبة',
            'delivery_address.phone.required' => 'رقم الهاتف مطلوب',
            'payment_method.required' => 'طريقة الدفع مطلوبة',
            'payment_method.in' => 'طريقة الدفع غير صالحة',
            'offer_code.exists' => 'كود العرض غير صالح',
            'use_loyalty_points.min' => 'نقاط الولاء يجب أن تكون أكبر من صفر',
        ];
    }
}
