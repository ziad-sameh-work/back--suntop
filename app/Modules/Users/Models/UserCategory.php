<?php

namespace App\Modules\Users\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserCategory extends Model
{
    protected $fillable = [
        'name',
        'display_name',
        'display_name_en',
        'description',
        'min_cartons',
        'max_cartons',
        'min_packages',
        'max_packages',
        'carton_loyalty_points',
        'bonus_points_per_carton',
        'monthly_bonus_points',
        'signup_bonus_points',
        'has_points_multiplier',
        'points_multiplier',
        'requires_carton_purchase',
        'requires_package_purchase',
        'benefits',
        'is_active',
        'sort_order',
        // Legacy fields for backward compatibility
        'discount_percentage',
    ];

    protected $casts = [
        'carton_loyalty_points' => 'integer',
        'bonus_points_per_carton' => 'integer',
        'monthly_bonus_points' => 'integer',
        'signup_bonus_points' => 'integer',
        'has_points_multiplier' => 'boolean',
        'points_multiplier' => 'decimal:2',
        'requires_carton_purchase' => 'boolean',
        'requires_package_purchase' => 'boolean',
        'benefits' => 'array',
        'is_active' => 'boolean',
        // Legacy casts for backward compatibility
        'discount_percentage' => 'decimal:2',
    ];

    /**
     * Users relationship
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Scope for active categories
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for ordered categories
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('min_cartons');
    }

    /**
     * Check if carton count qualifies for this category
     */
    public function qualifiesForCartons(int $cartons): bool
    {
        if ($cartons < $this->min_cartons) {
            return false;
        }

        if ($this->max_cartons && $cartons > $this->max_cartons) {
            return false;
        }

        return true;
    }

    /**
     * Check if package count qualifies for this category
     */
    public function qualifiesForPackages(int $packages): bool
    {
        if ($packages < $this->min_packages) {
            return false;
        }

        if ($this->max_packages && $packages > $this->max_packages) {
            return false;
        }

        return true;
    }

    /**
     * Check if user qualifies for this category based on their purchase history
     */
    public function qualifiesForUser(User $user): bool
    {
        // If category requires carton purchases, check cartons
        if ($this->requires_carton_purchase) {
            return $this->qualifiesForCartons($user->total_cartons_purchased);
        }

        // If category requires package purchases, check packages  
        if ($this->requires_package_purchase) {
            return $this->qualifiesForPackages($user->total_packages_purchased);
        }

        // Otherwise, check both cartons and packages (OR logic)
        return $this->qualifiesForCartons($user->total_cartons_purchased) || 
               $this->qualifiesForPackages($user->total_packages_purchased);
    }

    /**
     * Get loyalty points for carton purchase
     */
    public function getLoyaltyPointsForCarton(): int
    {
        $basePoints = $this->carton_loyalty_points + $this->bonus_points_per_carton;
        
        if ($this->has_points_multiplier) {
            return (int) ($basePoints * $this->points_multiplier);
        }
        
        return $basePoints;
    }

    /**
     * Calculate total points for carton purchase including category bonuses
     */
    public function calculatePointsForCartons(int $cartonCount): int
    {
        $pointsPerCarton = $this->getLoyaltyPointsForCarton();
        return $cartonCount * $pointsPerCarton;
    }

    /**
     * Get formatted points info
     */
    public function getFormattedPointsInfoAttribute(): string
    {
        $points = $this->getLoyaltyPointsForCarton();
        $info = "{$points} نقطة لكل كرتون";
        
        if ($this->has_points_multiplier && $this->points_multiplier > 1) {
            $info .= " (مضاعف {$this->points_multiplier}x)";
        }
        
        return $info;
    }

    /**
     * Get category for specific carton/package count
     */
    public static function getCategoryForUser(User $user): ?UserCategory
    {
        return static::active()
                    ->ordered()
                    ->get()
                    ->first(function ($category) use ($user) {
                        return $category->qualifiesForUser($user);
                    });
    }

    /**
     * Legacy method for backward compatibility
     * Get category for specific purchase amount
     */
    public static function getCategoryForAmount(float $amount): ?self
    {
        return static::active()
            ->where('min_purchase_amount', '<=', $amount)
            ->where(function ($query) use ($amount) {
                $query->whereNull('max_purchase_amount')
                      ->orWhere('max_purchase_amount', '>=', $amount);
            })
            ->orderBy('min_purchase_amount', 'desc')
            ->first();
    }

    /**
     * Get all categories ordered by purchase range
     */
    public static function getAllOrdered()
    {
        return static::active()->ordered()->get();
    }

    /**
     * Get category display name based on locale
     */
    public function getDisplayNameAttribute(): string
    {
        $locale = app()->getLocale();
        
        if ($locale === 'en' && $this->display_name_en) {
            return $this->display_name_en;
        }
        
        return $this->attributes['display_name'];
    }

    /**
     * Get formatted purchase range
     */
    public function getPurchaseRangeAttribute(): string
    {
        $min = number_format($this->min_purchase_amount, 0);
        
        if ($this->max_purchase_amount) {
            $max = number_format($this->max_purchase_amount, 0);
            return "{$min} - {$max} جنيه";
        }
        
        return "من {$min} جنيه فأكثر";
    }

    /**
     * Get benefits as formatted list
     */
    public function getFormattedBenefitsAttribute(): array
    {
        $benefits = $this->benefits ?? [];
        
        // Add discount percentage as a benefit if exists
        if ($this->discount_percentage > 0) {
            array_unshift($benefits, "خصم {$this->discount_percentage}% على جميع الطلبات");
        }
        
        return $benefits;
    }
}
