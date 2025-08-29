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
            'image_url' => $this->getMainImageUrl(),
            'images' => $this->getAllImageUrls(),
            'category' => $this->getCategoryName(),
            'category_id' => $this->category_id,
            'back_color' => $this->back_color ?? '#FFFFFF',
            'is_available' => $this->is_available,
            'stock_quantity' => $this->stock_quantity,
        ];
    }
    
    /**
     * Get main image URL with fallbacks
     */
    private function getMainImageUrl(): ?string
    {
        // Try new images array first
        if ($this->images && is_array($this->images) && count($this->images) > 0) {
            $firstImage = $this->images[0];
            // If it starts with http, it's already a full URL
            if (str_starts_with($firstImage, 'http')) {
                return $firstImage;
            }
            // Otherwise, build the URL
            return url($firstImage);
        }
        
        // Fallback to legacy image_url
        if ($this->image_url) {
            if (str_starts_with($this->image_url, 'http')) {
                return $this->image_url;
            }
            return url('storage/' . $this->image_url);
        }
        
        // Fallback to legacy gallery
        if ($this->gallery && is_array($this->gallery) && count($this->gallery) > 0) {
            $firstGalleryImage = $this->gallery[0];
            if (str_starts_with($firstGalleryImage, 'http')) {
                return $firstGalleryImage;
            }
            return url('storage/' . $firstGalleryImage);
        }
        
        // Default placeholder
        return url('images/no-product.png');
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
        
        // Fallback to legacy fields
        return $this->volume_category ?? $this->category ?? $this->size;
    }
}
