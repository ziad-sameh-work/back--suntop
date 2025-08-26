<?php

namespace App\Modules\Admin\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserCategoryResource extends JsonResource
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
            'display_name_en' => $this->display_name_en,
            'description' => $this->description,
            'min_purchase_amount' => $this->min_purchase_amount,
            'max_purchase_amount' => $this->max_purchase_amount,
            'discount_percentage' => $this->discount_percentage,
            'benefits' => $this->benefits,
            'formatted_benefits' => $this->formatted_benefits,
            'purchase_range' => $this->purchase_range,
            'is_active' => $this->is_active,
            'sort_order' => $this->sort_order,
            'users_count' => $this->whenLoaded('users', function () {
                return $this->users->count();
            }),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}
