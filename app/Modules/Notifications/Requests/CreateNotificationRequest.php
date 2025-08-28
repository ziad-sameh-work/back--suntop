<?php

namespace App\Modules\Notifications\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateNotificationRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
            'type' => 'required|in:shipment,offer,reward,general,order_status,payment',
            'priority' => 'sometimes|in:low,medium,high',
            'data' => 'sometimes|array',
            'action_url' => 'sometimes|string|max:500',
            'scheduled_at' => 'sometimes|date|after:now',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages()
    {
        return [
            'user_id.required' => 'معرف المستخدم مطلوب',
            'user_id.exists' => 'المستخدم غير موجود',
            'title.required' => 'عنوان الإشعار مطلوب',
            'title.max' => 'عنوان الإشعار لا يجب أن يتجاوز 255 حرف',
            'message.required' => 'محتوى الإشعار مطلوب',
            'message.max' => 'محتوى الإشعار لا يجب أن يتجاوز 1000 حرف',
            'type.required' => 'نوع الإشعار مطلوب',
            'type.in' => 'نوع الإشعار غير صحيح',
            'priority.in' => 'أولوية الإشعار غير صحيحة',
            'action_url.max' => 'رابط الإجراء لا يجب أن يتجاوز 500 حرف',
            'scheduled_at.date' => 'تاريخ الجدولة غير صحيح',
            'scheduled_at.after' => 'تاريخ الجدولة يجب أن يكون في المستقبل',
        ];
    }
}
