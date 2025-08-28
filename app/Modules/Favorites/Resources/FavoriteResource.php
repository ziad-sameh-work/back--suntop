<?php

namespace App\Modules\Favorites\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Modules\Products\Resources\ProductResource;

class FavoriteResource extends JsonResource
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
            'user_id' => $this->user_id,
            'product_id' => $this->product_id,
            'product' => new ProductResource($this->whenLoaded('product')),
            'added_at' => $this->added_at->toISOString(),
            'created_at' => $this->created_at->toISOString(),
            'days_since_added' => $this->added_at->diffInDays(now()),
            'is_product_available' => $this->when(
                $this->relationLoaded('product'),
                $this->product ? $this->product->is_available : null
            ),
        ];
    }
}
