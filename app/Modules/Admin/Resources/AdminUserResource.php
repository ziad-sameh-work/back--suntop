<?php

namespace App\Modules\Admin\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AdminUserResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'full_name' => $this->full_name,
            'phone' => $this->phone,
            'role' => $this->role,
            'role_display' => $this->getRoleDisplay(),
            'is_active' => $this->is_active,
            'status' => $this->is_active ? 'مفعل' : 'غير مفعل',
            'profile_image' => $this->profile_image ? url('storage/' . $this->profile_image) : null,
            'user_category' => $this->whenLoaded('userCategory', function () {
                return [
                    'id' => $this->userCategory->id,
                    'name' => $this->userCategory->name,
                    'display_name' => $this->userCategory->display_name,
                    'discount_percentage' => $this->userCategory->discount_percentage,
                ];
            }),
            'total_purchase_amount' => $this->total_purchase_amount,
            'formatted_purchase_amount' => number_format($this->total_purchase_amount, 2) . ' جنيه',
            'total_orders_count' => $this->total_orders_count,
            'last_login_at' => $this->last_login_at?->toISOString(),
            'last_login_human' => $this->last_login_at?->diffForHumans(),
            'password_changed_at' => $this->password_changed_at?->toISOString(),
            'category_updated_at' => $this->category_updated_at?->toISOString(),
            'email_verified_at' => $this->email_verified_at?->toISOString(),
            'created_at' => $this->created_at->toISOString(),
            'created_at_human' => $this->created_at->diffForHumans(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }

    private function getRoleDisplay()
    {
        return match($this->role) {
            'admin' => 'مدير النظام',
            'merchant' => 'تاجر',
            'user' => 'عميل',
            default => $this->role,
        };
    }
}
