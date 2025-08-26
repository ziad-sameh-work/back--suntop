<?php

namespace App\Modules\Admin\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AdminOrderResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'tracking_number' => $this->tracking_number,
            'status' => $this->status,
            'status_text' => $this->status_text,
            'status_badge_color' => $this->getStatusBadgeColor(),
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
                'phone' => $this->user->phone,
                'category' => $this->user->userCategory ? [
                    'name' => $this->user->userCategory->name,
                    'display_name' => $this->user->userCategory->display_name,
                ] : null,
            ],
            'merchant' => [
                'id' => $this->merchant->id,
                'name' => $this->merchant->name,
                'phone' => $this->merchant->phone,
            ],
            'items_count' => $this->items_count ?? $this->items->count(),
            'subtotal' => $this->subtotal,
            'delivery_fee' => $this->delivery_fee,
            'discount' => $this->discount,
            'category_discount' => $this->category_discount ?? 0,
            'loyalty_discount' => $this->loyalty_discount,
            'tax' => $this->tax,
            'total_amount' => $this->total_amount,
            'formatted_total' => number_format($this->total_amount, 2) . ' ' . $this->currency,
            'currency' => $this->currency,
            'payment_method' => $this->payment_method,
            'payment_method_text' => $this->getPaymentMethodText(),
            'payment_status' => $this->payment_status,
            'payment_status_text' => $this->getPaymentStatusText(),
            'delivery_address' => $this->delivery_address,
            'estimated_delivery_time' => $this->estimated_delivery_time?->toISOString(),
            'estimated_delivery_human' => $this->estimated_delivery_time?->diffForHumans(),
            'delivered_at' => $this->delivered_at?->toISOString(),
            'delivered_at_human' => $this->delivered_at?->diffForHumans(),
            'notes' => $this->notes,
            'created_at' => $this->created_at->toISOString(),
            'created_at_human' => $this->created_at->diffForHumans(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }

    private function getStatusBadgeColor()
    {
        return match($this->status) {
            'pending' => 'warning',
            'confirmed' => 'info',
            'preparing' => 'primary',
            'shipped' => 'secondary',
            'delivered' => 'success',
            'cancelled' => 'danger',
            default => 'secondary',
        };
    }

    private function getPaymentMethodText()
    {
        return match($this->payment_method) {
            'cash_on_delivery' => 'الدفع عند الاستلام',
            'credit_card' => 'بطاقة ائتمان',
            'wallet' => 'محفظة إلكترونية',
            default => $this->payment_method,
        };
    }

    private function getPaymentStatusText()
    {
        return match($this->payment_status) {
            'pending' => 'في انتظار الدفع',
            'paid' => 'مدفوع',
            'failed' => 'فشل الدفع',
            'refunded' => 'مسترد',
            default => $this->payment_status,
        };
    }
}
