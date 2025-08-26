<?php

namespace App\Modules\Orders\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'status' => $this->status,
            'status_text' => $this->status_text,
            'merchant_name' => $this->merchant->name,
            'total_amount' => (float) $this->total_amount,
            'currency' => $this->currency,
            'items_count' => $this->items->count(),
            'tracking_number' => $this->tracking_number,
            'estimated_delivery' => $this->estimated_delivery_time?->toISOString(),
            'created_at' => $this->created_at->toISOString(),
        ];
    }
}
