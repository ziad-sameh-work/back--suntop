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
            'short_description' => $this->short_description,
            'image_url' => $this->getMainImageUrl(),
            'images' => $this->getAllImageUrls(),
            'gallery' => $this->getAllImageUrls(), // Legacy compatibility
            'price' => (float) $this->price,
            'discount_price' => $this->discount_price ? (float) $this->discount_price : null,
            'original_price' => $this->original_price ? (float) $this->original_price : null,
            'currency' => $this->currency ?? 'EGP',
            'category' => $this->getCategoryInfo(),
            'category_name' => $this->getCategoryName(),
            'category_id' => $this->category_id,
            'size' => $this->size,
            'volume_category' => $this->volume_category,
            'is_available' => $this->is_available,
            'stock_quantity' => $this->stock_quantity,
            'rating' => (float) $this->rating,
            'review_count' => $this->review_count,
            'tags' => $this->tags ?? [],
            'ingredients' => $this->ingredients ?? [],
            'nutrition_facts' => $this->nutrition_facts ?? [],
            'storage_instructions' => $this->storage_instructions,
            'expiry_info' => $this->expiry_info,
            'barcode' => $this->barcode,
            'sku' => $this->sku,
            'weight' => $this->weight,
            'dimensions' => $this->dimensions,
            'reviews' => $this->recent_reviews,
            'back_color' => $this->back_color ?? '#FFFFFF',
            'is_featured' => $this->is_featured,
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
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
            if (str_starts_with($firstImage, 'http')) {
                return $firstImage;
            }
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
        
        // Fallback to legacy string field
        $legacyCategory = $this->volume_category ?? $this->category ?? $this->size;
        if ($legacyCategory) {
            return [
                'id' => null,
                'name' => $legacyCategory,
                'display_name' => $legacyCategory,
                'description' => null,
                'icon' => null,
            ];
        }
        
        return null;
    }
}
