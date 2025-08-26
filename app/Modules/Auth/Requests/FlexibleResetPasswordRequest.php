<?php

namespace App\Modules\Auth\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FlexibleResetPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'old_password' => 'required|string',
            'new_password' => 'required|string|min:6',
            'confirm_password' => 'sometimes|string|same:new_password',
            'new_password_confirmation' => 'sometimes|string|same:new_password',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $newPassword = $this->input('new_password');
            $confirmPassword = $this->input('confirm_password') ?: $this->input('new_password_confirmation');
            
            if (!$confirmPassword) {
                $validator->errors()->add('confirm_password', 'تأكيد كلمة المرور مطلوب');
                return;
            }
            
            if ($newPassword !== $confirmPassword) {
                $validator->errors()->add('confirm_password', 'تأكيد كلمة المرور غير متطابق');
            }
        });
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        // Normalize field names for flexibility
        if ($this->has('confirm_password') && !$this->has('new_password_confirmation')) {
            $this->merge([
                'new_password_confirmation' => $this->confirm_password
            ]);
        }
    }

    public function messages(): array
    {
        return [
            'old_password.required' => 'كلمة المرور الحالية مطلوبة',
            'new_password.required' => 'كلمة المرور الجديدة مطلوبة',
            'new_password.min' => 'كلمة المرور الجديدة يجب أن تحتوي على 6 أحرف على الأقل',
            'confirm_password.same' => 'تأكيد كلمة المرور غير متطابق',
            'new_password_confirmation.same' => 'تأكيد كلمة المرور غير متطابق',
            'confirm_password.required' => 'تأكيد كلمة المرور مطلوب',
            'new_password_confirmation.required' => 'تأكيد كلمة المرور مطلوب',
        ];
    }
}
