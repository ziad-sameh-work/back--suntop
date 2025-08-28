<?php

namespace App\Modules\Loyalty\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LoyaltyPointsResource extends JsonResource
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
            'points' => $this->points,
            'formatted_points' => $this->formatted_points,
            'type' => $this->type,
            'type_name' => $this->getTypeName(),
            'description' => $this->description,
            'order' => $this->when($this->order, [
                'id' => $this->order->id,
                'order_number' => $this->order->order_number,
                'total_amount' => $this->order->total_amount,
            ]),
            'expires_at' => $this->expires_at ? $this->expires_at->toISOString() : null,
            'is_expired' => $this->is_expired,
            'metadata' => $this->metadata,
            'created_at' => $this->created_at->toISOString(),
            'days_until_expiry' => $this->expires_at ? max(0, now()->diffInDays($this->expires_at, false)) : null,
        ];
    }

    /**
     * Get type name in Arabic
     */
    private function getTypeName(): string
    {
        $types = [
            'earned' => 'مكتسبة',
            'redeemed' => 'مستبدلة',
            'admin_award' => 'مكافأة إدارية',
            'admin_deduct' => 'خصم إداري',
            'expired' => 'منتهية الصلاحية',
            'bonus' => 'مكافأة',
        ];

        return $types[$this->type] ?? $this->type;
    }
}
