<?php

namespace App\Modules\Admin\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Modules\Products\Resources\ProductResource;

class AdminOrderDetailResource extends JsonResource
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
                'avatar_url' => $this->user->avatar_url,
                'category' => $this->when($this->user->userCategory, [
                    'id' => $this->user->userCategory?->id,
                    'name' => $this->user->userCategory?->name,
                    'discount_percentage' => $this->user->userCategory?->discount_percentage,
                    'min_total_amount' => $this->user->userCategory?->min_total_amount,
                ]),
                'total_orders' => $this->user->orders()->count(),
                'total_spent' => $this->user->orders()->where('status', 'delivered')->sum('total_amount'),
            ],
            'merchant' => [
                'id' => $this->merchant->id,
                'name' => $this->merchant->name,
                'phone' => $this->merchant->phone,
                'address' => $this->merchant->address,
                'is_open' => $this->merchant->is_open,
                'delivery_fee' => $this->merchant->delivery_fee,
                'estimated_delivery_time' => $this->merchant->estimated_delivery_time,
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
            'items' => $this->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product_name,
                    'product_image' => $item->product_image,
                    'product' => $item->product ? new ProductResource($item->product) : null,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'total_price' => $item->total_price,
                    'selling_type' => $item->selling_type,
                    'cartons_count' => $item->cartons_count,
                    'packages_count' => $item->packages_count,
                    'units_count' => $item->units_count,
                ];
            }),
            'tracking' => $this->trackings->map(function ($tracking) {
                return [
                    'id' => $tracking->id,
                    'status' => $tracking->status,
                    'status_text' => $tracking->status_text,
                    'location' => $tracking->location,
                    'driver_name' => $tracking->driver_name,
                    'driver_phone' => $tracking->driver_phone,
                    'notes' => $tracking->notes,
                    'timestamp' => $tracking->timestamp->toISOString(),
                    'time_ago' => $tracking->timestamp->diffForHumans(),
                ];
            }),
            'estimated_delivery_time' => $this->estimated_delivery_time?->toISOString(),
            'delivered_at' => $this->delivered_at?->toISOString(),
            'notes' => $this->notes,
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            'order_age_hours' => $this->created_at->diffInHours(now()),
            'is_overdue' => $this->estimated_delivery_time && now()->gt($this->estimated_delivery_time),
            'can_cancel' => in_array($this->status, ['pending', 'confirmed']),
            'can_update_status' => !in_array($this->status, ['delivered', 'cancelled']),
            'next_possible_statuses' => $this->getNextPossibleStatuses(),
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

    private function getNextPossibleStatuses(): array
    {
        $transitions = [
            'pending' => ['confirmed', 'cancelled'],
            'confirmed' => ['preparing', 'cancelled'],
            'preparing' => ['shipped', 'cancelled'],
            'shipped' => ['delivered'],
            'delivered' => [],
            'cancelled' => [],
        ];

        $possibleStatuses = $transitions[$this->status] ?? [];
        
        return collect($possibleStatuses)->map(function ($status) {
            return [
                'value' => $status,
                'text' => \App\Modules\Orders\Models\Order::STATUSES[$status] ?? $status,
            ];
        })->values()->toArray();
    }
}