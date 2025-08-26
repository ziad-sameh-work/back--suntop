<?php

namespace App\Modules\Products\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'image_url' => $this->image_full_url,
            'price' => (float) $this->price,
            'original_price' => $this->original_price ? (float) $this->original_price : null,
            'currency' => $this->currency ?? 'EGP',
            'category' => $this->category,
            'size' => $this->size,
            'volume_category' => $this->volume_category,
            'is_available' => $this->is_available,
            'stock_quantity' => $this->stock_quantity,
            'rating' => (float) $this->rating,
            'review_count' => $this->review_count,
            'tags' => $this->tags ?? [],
            'nutrition_facts' => $this->nutrition_facts ?? [],
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}
