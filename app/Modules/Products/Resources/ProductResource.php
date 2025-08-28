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
            'price' => (float) $this->price,
            'image_url' => $this->image_full_url,
            'category' => $this->volume_category, // سيعرض 250ml أو 1L
        ];
    }
}
