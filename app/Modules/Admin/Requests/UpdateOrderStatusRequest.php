<?php

namespace App\Modules\Admin\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => 'required|string|in:pending,confirmed,preparing,shipped,delivered,cancelled',
            'location' => 'nullable|string|max:255',
            'driver_name' => 'nullable|string|max:255',
            'driver_phone' => 'nullable|string|max:20',
            'notes' => 'nullable|string|max:500',
            'estimated_delivery_minutes' => 'nullable|integer|min:1|max:300',
            'send_notification' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'حالة الطلب مطلوبة',
            'status.in' => 'حالة الطلب غير صالحة',
            'location.max' => 'الموقع يجب ألا يزيد عن 255 حرف',
            'driver_name.max' => 'اسم السائق يجب ألا يزيد عن 255 حرف',
            'driver_phone.max' => 'رقم هاتف السائق يجب ألا يزيد عن 20 رقم',
            'notes.max' => 'الملاحظات يجب ألا تزيد عن 500 حرف',
            'estimated_delivery_minutes.integer' => 'وقت التوصيل المتوقع يجب أن يكون رقم',
            'estimated_delivery_minutes.min' => 'وقت التوصيل المتوقع يجب أن يكون أكبر من دقيقة',
            'estimated_delivery_minutes.max' => 'وقت التوصيل المتوقع يجب ألا يزيد عن 5 ساعات',
        ];
    }
}