<?php

namespace App\Modules\Loyalty\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RewardTierResource extends JsonResource
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
            'name' => $this->name,
            'display_name' => $this->display_name,
            'description' => $this->description,
            'points_required' => $this->points_required,
            'icon_url' => $this->icon_url ? url('storage/' . $this->icon_url) : $this->getDefaultIcon(),
            'color' => $this->color,
            'discount_percentage' => $this->discount_percentage,
            'bonus_multiplier' => $this->bonus_multiplier,
            'benefits' => $this->formatted_benefits,
            'is_active' => $this->is_active,
            'sort_order' => $this->sort_order,
        ];
    }

    /**
     * Get default icon based on tier name
     */
    private function getDefaultIcon(): string
    {
        $icons = [
            'bronze' => '🥉',
            'silver' => '🥈', 
            'gold' => '🥇',
            'platinum' => '💎',
            'diamond' => '💍',
        ];

        $tierName = strtolower($this->name);
        return $icons[$tierName] ?? '⭐';
    }
}
