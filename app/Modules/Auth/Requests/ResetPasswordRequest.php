<?php

namespace App\Modules\Auth\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'old_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
            'new_password_confirmation' => 'required|string',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        // Handle both confirm_password and new_password_confirmation
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
            'new_password.min' => 'كلمة المرور الجديدة يجب أن تحتوي على 8 أحرف على الأقل',
            'new_password.confirmed' => 'تأكيد كلمة المرور غير متطابق',
            'new_password_confirmation.required' => 'تأكيد كلمة المرور مطلوب',
        ];
    }
}
