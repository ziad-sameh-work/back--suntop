<?php

namespace App\Modules\Offers\Services;

use App\Modules\Core\BaseService;
use App\Modules\Offers\Models\Offer;

class OfferService extends BaseService
{
    public function __construct(Offer $offer)
    {
        $this->model = $offer;
    }

    /**
     * Apply offer discount
     */
    public function applyOffer(string $code, float $subtotal): float
    {
        $offer = $this->model->where('code', $code)
                            ->where('is_active', true)
                            ->where('valid_from', '<=', now())
                            ->where('valid_until', '>=', now())
                            ->first();

        if (!$offer) {
            throw new \Exception('كود العرض غير صالح أو منتهي الصلاحية');
        }

        if ($offer->usage_limit && $offer->used_count >= $offer->usage_limit) {
            throw new \Exception('تم استنفاد عدد استخدامات هذا العرض');
        }

        if ($offer->minimum_amount && $subtotal < $offer->minimum_amount) {
            throw new \Exception("الحد الأدنى للطلب هو {$offer->minimum_amount} جنيه");
        }

        $discount = 0;
        
        if ($offer->type === 'percentage') {
            $discount = $subtotal * ($offer->discount_percentage / 100);
            if ($offer->maximum_discount && $discount > $offer->maximum_discount) {
                $discount = $offer->maximum_discount;
            }
        } else {
            $discount = $offer->discount_amount;
        }

        // Update usage count
        $offer->increment('used_count');

        return $discount;
    }
}
