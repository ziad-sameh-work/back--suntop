<?php

namespace App\Modules\Admin\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOrderStatusRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user() && $this->user()->role === 'admin';
    }

    public function rules()
    {
        return [
            'status' => [
                'required',
                Rule::in(['pending', 'confirmed', 'preparing', 'shipped', 'delivered', 'cancelled'])
            ],
            'location' => 'nullable|string|max:255',
            'driver_name' => 'nullable|string|max:255',
            'driver_phone' => 'nullable|string|max:20',
            'notes' => 'nullable|string|max:500',
        ];
    }

    public function messages()
    {
        return [
            'status.required' => 'حالة الطلب مطلوبة',
            'status.in' => 'حالة الطلب غير صحيحة',
            'location.max' => 'الموقع يجب ألا يزيد عن 255 حرف',
            'driver_name.max' => 'اسم السائق يجب ألا يزيد عن 255 حرف',
            'driver_phone.max' => 'رقم هاتف السائق يجب ألا يزيد عن 20 حرف',
            'notes.max' => 'الملاحظات يجب ألا تزيد عن 500 حرف',
        ];
    }
}
