<?php

namespace App\Modules\Admin\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Modules\Orders\Models\Order;

class AdminOrderResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'tracking_number' => $this->tracking_number,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
                'phone' => $this->user->phone,
                'category' => $this->user->userCategory ? [
                    'name' => $this->user->userCategory->name,
                    'discount_percentage' => $this->user->userCategory->discount_percentage,
                ] : null,
            ],
            'merchant' => [
                'id' => $this->merchant->id,
                'name' => $this->merchant->name,
                'phone' => $this->merchant->phone,
                'is_open' => $this->merchant->is_open,
            ],
            'status' => $this->status,
            'status_text' => $this->status_text,
            'payment_method' => $this->payment_method,
            'payment_method_text' => $this->getPaymentMethodText(),
            'payment_status' => $this->payment_status,
            'payment_status_text' => $this->getPaymentStatusText(),
            'financial' => [
                'subtotal' => $this->subtotal,
                'delivery_fee' => $this->delivery_fee,
                'discount' => $this->discount,
                'category_discount' => $this->category_discount,
                'loyalty_discount' => $this->loyalty_discount,
                'tax' => $this->tax,
                'total_amount' => $this->total_amount,
                'currency' => $this->currency,
            ],
            'delivery_address' => $this->delivery_address,
            'items_count' => $this->items_count ?? $this->items->count(),
            'estimated_delivery_time' => $this->estimated_delivery_time?->toISOString(),
            'delivered_at' => $this->delivered_at?->toISOString(),
            'notes' => $this->notes,
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            'order_age_hours' => $this->created_at->diffInHours(now()),
            'is_overdue' => $this->estimated_delivery_time && now()->gt($this->estimated_delivery_time),
        ];
    }

    private function getPaymentMethodText(): string
    {
        return match($this->payment_method) {
            'cash_on_delivery' => 'الدفع عند الاستلام',
            'credit_card' => 'بطاقة ائتمان',
            'wallet' => 'محفظة إلكترونية',
            default => $this->payment_method,
        };
    }

    private function getPaymentStatusText(): string
    {
        return match($this->payment_status) {
            'pending' => 'في انتظار الدفع',
            'paid' => 'تم الدفع',
            'failed' => 'فشل الدفع',
            'refunded' => 'تم الاسترداد',
            default => $this->payment_status,
        };
    }
}