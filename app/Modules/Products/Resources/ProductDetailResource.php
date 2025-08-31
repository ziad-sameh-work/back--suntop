<?php

namespace App\Modules\Products\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductDetailResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'image_url' => $this->getMainImageUrl(),
            'images' => $this->getAllImageUrls(),
            'price' => (float) $this->price,
            'category' => $this->getCategoryInfo(),
            'category_name' => $this->getCategoryName(),
            'category_id' => $this->category_id,
            'is_available' => $this->is_available,
            'back_color' => $this->back_color ?? '#FF6B35',
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
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
        
        // Add legacy gallery images if no new images
        if (empty($allImages) && $this->gallery && is_array($this->gallery)) {
            foreach ($this->gallery as $image) {
                if (str_starts_with($image, 'http')) {
                    $allImages[] = $image;
                } else {
                    $allImages[] = url('storage/' . $image);
                }
            }
        }
        
        // Add legacy image_url if still no images
        if (empty($allImages) && $this->image_url) {
            if (str_starts_with($this->image_url, 'http')) {
                $allImages[] = $this->image_url;
            } else {
                $allImages[] = url('storage/' . $this->image_url);
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
    
    /**
     * Get full category information
     */
    private function getCategoryInfo(): ?array
    {
        // Try relationship first
        if ($this->relationLoaded('category') && $this->category) {
            return [
                'id' => $this->category->id,
                'name' => $this->category->name,
                'display_name' => $this->category->display_name,
                'description' => $this->category->description,
                'icon' => $this->category->icon,
            ];
        }
        
        // Try to load category if not loaded
        if ($this->category_id) {
            try {
                $category = $this->category;
                if ($category) {
                    return [
                        'id' => $category->id,
                        'name' => $category->name,
                        'display_name' => $category->display_name,
                        'description' => $category->description,
                        'icon' => $category->icon,
                    ];
                }
            } catch (\Exception $e) {
                // Category relationship might not exist
            }
        }
        
        return null;
    }
}
