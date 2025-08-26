<?php

namespace App\Modules\Admin\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AdminOrderDetailResource extends JsonResource
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
                'full_name' => $this->user->full_name,
                'category' => $this->user->userCategory ? [
                    'id' => $this->user->userCategory->id,
                    'name' => $this->user->userCategory->name,
                    'display_name' => $this->user->userCategory->display_name,
                    'discount_percentage' => $this->user->userCategory->discount_percentage,
                ] : null,
                'total_orders' => $this->user->total_orders_count,
                'total_spent' => $this->user->total_purchase_amount,
            ],
            'merchant' => [
                'id' => $this->merchant->id,
                'name' => $this->merchant->name,
                'phone' => $this->merchant->phone,
                'email' => $this->merchant->email,
                'address' => $this->merchant->address,
                'delivery_fee' => $this->merchant->delivery_fee,
                'estimated_delivery_time' => $this->merchant->estimated_delivery_time,
            ],
            'items' => $this->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product_name,
                    'product_image' => $item->product_image ? url('storage/' . $item->product_image) : null,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'total_price' => $item->total_price,
                    'product' => $item->product ? [
                        'id' => $item->product->id,
                        'name' => $item->product->name,
                        'current_price' => $item->product->price,
                        'stock_quantity' => $item->product->stock_quantity,
                        'is_available' => $item->product->is_available,
                    ] : null,
                ];
            }),
            'financial_breakdown' => [
                'subtotal' => $this->subtotal,
                'delivery_fee' => $this->delivery_fee,
                'discount' => $this->discount,
                'category_discount' => $this->category_discount ?? 0,
                'loyalty_discount' => $this->loyalty_discount,
                'tax' => $this->tax,
                'total_amount' => $this->total_amount,
                'formatted_breakdown' => [
                    'subtotal' => number_format($this->subtotal, 2) . ' ' . $this->currency,
                    'delivery_fee' => number_format($this->delivery_fee, 2) . ' ' . $this->currency,
                    'discount' => number_format($this->discount, 2) . ' ' . $this->currency,
                    'category_discount' => number_format($this->category_discount ?? 0, 2) . ' ' . $this->currency,
                    'loyalty_discount' => number_format($this->loyalty_discount, 2) . ' ' . $this->currency,
                    'tax' => number_format($this->tax, 2) . ' ' . $this->currency,
                    'total_amount' => number_format($this->total_amount, 2) . ' ' . $this->currency,
                ],
            ],
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
            'tracking_history' => $this->trackings->map(function ($tracking) {
                return [
                    'id' => $tracking->id,
                    'status' => $tracking->status,
                    'status_text' => $tracking->status_text,
                    'location' => $tracking->location,
                    'driver_name' => $tracking->driver_name,
                    'driver_phone' => $tracking->driver_phone,
                    'notes' => $tracking->notes,
                    'timestamp' => $tracking->timestamp->toISOString(),
                    'timestamp_human' => $tracking->timestamp->diffForHumans(),
                ];
            }),
            'can_cancel' => in_array($this->status, ['pending', 'confirmed']),
            'can_update_status' => !in_array($this->status, ['delivered', 'cancelled']),
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
