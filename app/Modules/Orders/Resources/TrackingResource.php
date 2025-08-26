<?php

namespace App\Modules\Orders\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TrackingResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'order_id' => $this->id,
            'tracking_number' => $this->tracking_number,
            'current_status' => $this->status,
            'estimated_delivery' => $this->estimated_delivery_time?->toISOString(),
            'tracking_history' => $this->trackings->map(function ($tracking) {
                return [
                    'status' => $tracking->status,
                    'status_text' => $tracking->status_text,
                    'timestamp' => $tracking->timestamp->toISOString(),
                    'location' => $tracking->location,
                    'driver_name' => $tracking->driver_name,
                    'driver_phone' => $tracking->driver_phone,
                    'notes' => $tracking->notes,
                ];
            }),
        ];
    }
}
