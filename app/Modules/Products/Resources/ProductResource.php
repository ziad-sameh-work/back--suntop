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
            'price' => (float) $this->price,
            'image_url' => $this->getMainImageUrl(),
            'images' => $this->getAllImageUrls(),
            'category' => $this->getCategoryName(),
            'category_id' => $this->category_id,
            'back_color' => $this->back_color ?? '#FF6B35',
            'is_available' => $this->is_available,
        ];
    }
    
    /**
     * Get main image URL with fallbacks
     */
    private function getMainImageUrl(): ?string
    {
        // Use the model's first_image attribute which handles all the logic
        return $this->first_image;
    }
    
    /**
     * Get all image URLs
     */
    private function getAllImageUrls(): array
    {
        $allImages = [];
        
        // Add images from new images array
        if ($this->images && is_array($this->images)) {
            foreach ($this->images as $image) {
                if (str_starts_with($image, 'http')) {
                    $allImages[] = $image;
                } else {
                    $allImages[] = url($image);
                }
            }
        }
        
        return $allImages;
    }
    
    /**
     * Get category name with fallbacks
     */
    private function getCategoryName(): ?string
    {
        // Try relationship first
        if ($this->relationLoaded('category') && $this->category) {
            return $this->category->display_name ?? $this->category->name;
        }
        
        // Try to load category if not loaded
        if ($this->category_id) {
            try {
                $category = $this->category;
                if ($category) {
                    return $category->display_name ?? $category->name;
                }
            } catch (\Exception $e) {
                // Category relationship might not exist
            }
        }
        
        // Return null if no category found
        return null;
    }
}
