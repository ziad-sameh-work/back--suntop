<?php

namespace App\Modules\Merchants\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Merchant extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'business_name',
        'business_type',
        'commission_rate',
        'description',
        'image_url',
        'logo_url',
        'logo',
        'category',
        'rating',
        'review_count',
        'address',
        'phone',
        'email',
        'latitude',
        'longitude',
        'city',
        'district',
        'is_open',
        'working_hours',
        'delivery_available',
        'minimum_order',
        'delivery_fee',
        'estimated_delivery_time',
        'is_active',
    ];

    protected $casts = [
        'working_hours' => 'array',
        'rating' => 'decimal:1',
        'commission_rate' => 'decimal:2',
        'minimum_order' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'is_open' => 'boolean',
        'delivery_available' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Orders relationship
     */
    public function orders(): HasMany
    {
        return $this->hasMany(\App\Modules\Orders\Models\Order::class);
    }

    /**
     * Products relationship
     */
    public function products(): HasMany
    {
        return $this->hasMany(\App\Modules\Products\Models\Product::class);
    }
}
