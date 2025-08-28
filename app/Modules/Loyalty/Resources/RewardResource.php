<?php

namespace App\Modules\Loyalty\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RewardResource extends JsonResource
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
            'type' => $this->type,
            'type_name' => $this->type_name,
            'points_cost' => $this->points_cost,
            'formatted_points_cost' => $this->formatted_points_cost,
            'discount_percentage' => $this->discount_percentage,
            'discount_amount' => $this->discount_amount,
            'cashback_amount' => $this->cashback_amount,
            'bonus_points' => $this->bonus_points,
            'free_product_id' => $this->free_product_id,
            'image_url' => $this->image_url ? url('storage/' . $this->image_url) : null,
            'category' => $this->category,
            'expiry_days' => $this->expiry_days,
            'usage_limit' => $this->usage_limit,
            'used_count' => $this->used_count,
            'remaining_uses' => $this->usage_limit ? max(0, $this->usage_limit - $this->used_count) : null,
            'is_active' => $this->is_active,
            'is_available' => $this->isAvailable(),
            'applicable_categories' => $this->applicable_categories,
            'applicable_products' => $this->applicable_products,
            'minimum_order_amount' => $this->minimum_order_amount,
            'terms_conditions' => $this->terms_conditions,
            'created_at' => $this->created_at->toISOString(),
            'can_afford' => $this->when(isset($this->can_afford), (bool) $this->can_afford),
            'popularity_score' => $this->used_count,
            'value_score' => $this->calculateValueScore(),
        ];
    }

    /**
     * Calculate value score for sorting
     */
    private function calculateValueScore(): float
    {
        if ($this->type === 'discount' && $this->discount_percentage) {
            return $this->discount_percentage / max(1, $this->points_cost) * 100;
        }
        
        if ($this->type === 'cashback' && $this->cashback_amount) {
            return $this->cashback_amount / max(1, $this->points_cost) * 100;
        }
        
        if ($this->type === 'bonus_points' && $this->bonus_points) {
            return $this->bonus_points / max(1, $this->points_cost) * 100;
        }
        
        return 1.0;
    }
}
