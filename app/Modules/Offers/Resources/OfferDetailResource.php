<?php

namespace App\Modules\Offers\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OfferDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'code' => $this->code,
            'type' => $this->type,
            'type_name' => \App\Modules\Offers\Models\Offer::TYPES[$this->type] ?? $this->type,
            'discount_percentage' => $this->discount_percentage,
            'discount_amount' => $this->discount_amount,
            'minimum_amount' => $this->minimum_amount,
            'maximum_discount' => $this->maximum_discount,
            'image_url' => $this->image_url ? url('storage/' . $this->image_url) : null,
            'valid_from' => $this->valid_from->toISOString(),
            'valid_until' => $this->valid_until->toISOString(),
            'usage_limit' => $this->usage_limit,
            'used_count' => $this->used_count,
            'remaining_uses' => $this->usage_limit ? max(0, $this->usage_limit - $this->used_count) : null,
            'is_active' => $this->is_active,
            'is_valid' => $this->isValid(),
            'applicable_categories' => $this->applicable_categories,
            'applicable_products' => $this->applicable_products,
            'first_order_only' => $this->first_order_only,
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            'expires_in_days' => $this->valid_until ? max(0, now()->diffInDays($this->valid_until, false)) : null,
            'expires_in_hours' => $this->valid_until ? max(0, now()->diffInHours($this->valid_until, false)) : null,
            'terms_conditions' => $this->when($this->terms_conditions, $this->terms_conditions),
            'usage_statistics' => [
                'usage_rate' => $this->usage_limit ? round(($this->used_count / $this->usage_limit) * 100, 2) : 0,
                'popularity_score' => $this->used_count,
            ],
        ];
    }
}
