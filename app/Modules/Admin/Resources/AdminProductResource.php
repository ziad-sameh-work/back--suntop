<?php

namespace App\Modules\Admin\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AdminProductResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'image_url' => $this->image_url,
            'image_full_url' => $this->image_full_url,
            'gallery' => $this->gallery,
            'gallery_full_urls' => $this->gallery_full_urls,
            'price' => $this->price,
            'formatted_price' => number_format($this->price, 2) . ' ' . $this->currency,
            'original_price' => $this->original_price,
            'formatted_original_price' => $this->original_price ? number_format($this->original_price, 2) . ' ' . $this->currency : null,
            'currency' => $this->currency,
            'category' => $this->category,
            'size' => $this->size,
            'volume_category' => $this->volume_category,
            'is_available' => $this->is_available,
            'availability_status' => $this->getAvailabilityStatus(),
            'stock_quantity' => $this->stock_quantity,
            'stock_status' => $this->getStockStatus(),
            'rating' => $this->rating,
            'review_count' => $this->review_count,
            'tags' => $this->tags,
            'ingredients' => $this->ingredients,
            'nutrition_facts' => $this->nutrition_facts,
            'storage_instructions' => $this->storage_instructions,
            'expiry_info' => $this->expiry_info,
            'barcode' => $this->barcode,
            'is_featured' => $this->is_featured,
            'featured_status' => $this->is_featured ? 'مميز' : 'عادي',
            'sort_order' => $this->sort_order,
            'back_color' => $this->back_color ?? '#FFFFFF',
            'total_sold' => $this->whenLoaded('orderItems', function () {
                return $this->orderItems->sum('quantity');
            }),
            'total_revenue' => $this->whenLoaded('orderItems', function () {
                return $this->orderItems->sum('total_price');
            }),
            'reviews' => $this->whenLoaded('reviews', function () {
                return $this->reviews->map(function ($review) {
                    return [
                        'id' => $review->id,
                        'user_name' => $review->user->name,
                        'rating' => $review->rating,
                        'comment' => $review->comment,
                        'created_at' => $review->created_at->toISOString(),
                    ];
                });
            }),
            'created_at' => $this->created_at->toISOString(),
            'created_at_human' => $this->created_at->diffForHumans(),
            'updated_at' => $this->updated_at->toISOString(),
            'deleted_at' => $this->deleted_at?->toISOString(),
            'is_deleted' => $this->trashed(),
        ];
    }

    private function getAvailabilityStatus()
    {
        if (!$this->is_available) {
            return 'غير متاح';
        }
        
        if ($this->stock_quantity <= 0) {
            return 'نفد المخزون';
        }
        
        return 'متاح';
    }

    private function getStockStatus()
    {
        if ($this->stock_quantity <= 0) {
            return 'نفد المخزون';
        } elseif ($this->stock_quantity <= 10) {
            return 'مخزون منخفض';
        } else {
            return 'متوفر';
        }
    }
}
