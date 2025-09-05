<?php

namespace App\Modules\Products\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray($request): array
    {
        $originalPrice = (float) $this->price;
        $finalPrice = $this->calculateDiscountedPrice($request);
        
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $finalPrice,
            'original_price' => $originalPrice !== $finalPrice ? $originalPrice : null,
            'discount_percentage' => $originalPrice !== $finalPrice ? round((($originalPrice - $finalPrice) / $originalPrice) * 100, 1) : null,
            'image_url' => $this->getMainImageUrl(),
            'images' => $this->getAllImageUrls(),
            'category' => $this->getCategoryName(),
            'category_id' => $this->category_id,
            'back_color' => $this->back_color ?? '#FF6B35',
            'is_available' => $this->is_available,
        ];
    }
    
    /**
     * Calculate discounted price based on user category offers
     */
    private function calculateDiscountedPrice($request): float
    {
        $originalPrice = (float) $this->price;
        
        // Get authenticated user
        $user = $request->user();
        if (!$user || !$user->user_category_id) {
            return $originalPrice;
        }
        
        // Get active offers for user's category
        $offers = \App\Modules\Offers\Models\Offer::where('is_active', true)
            ->where('user_category_id', $user->user_category_id)
            ->where('valid_from', '<=', now())
            ->where('valid_until', '>=', now())
            ->get();
        
        $bestDiscount = 0;
        
        foreach ($offers as $offer) {
            $discount = 0;
            
            if ($offer->discount_percentage) {
                // Percentage discount
                $discount = ($originalPrice * $offer->discount_percentage) / 100;
            } elseif ($offer->discount_amount) {
                // Fixed amount discount
                $discount = min($offer->discount_amount, $originalPrice);
            }
            
            // Keep the best discount
            if ($discount > $bestDiscount) {
                $bestDiscount = $discount;
            }
        }
        
        return max(0, $originalPrice - $bestDiscount);
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
     * Get all image URLs from storage
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
                    $allImages[] = \Storage::disk('public')->url($image);
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
