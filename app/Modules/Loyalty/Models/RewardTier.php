<?php

namespace App\Modules\Loyalty\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class RewardTier extends Model
{
    protected $fillable = [
        'name',
        'display_name',
        'description',
        'points_required',
        'icon_url',
        'color',
        'discount_percentage',
        'bonus_multiplier',
        'benefits',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'discount_percentage' => 'decimal:2',
        'benefits' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Scope for active tiers
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope ordered by points required
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('points_required', 'asc');
    }

    /**
     * Get users in this tier
     */
    public function users()
    {
        return User::whereHas('loyaltyPoints', function($query) {
            $lifetimePoints = LoyaltyPoint::selectRaw('SUM(CASE WHEN points > 0 THEN points ELSE 0 END)')
                                         ->where('user_id', User::raw('users.id'))
                                         ->toSql();
            
            $query->havingRaw("($lifetimePoints) >= ?", [$this->points_required]);
        });
    }

    /**
     * Get next tier
     */
    public function getNextTier()
    {
        return self::active()
                  ->where('points_required', '>', $this->points_required)
                  ->orderBy('points_required', 'asc')
                  ->first();
    }

    /**
     * Get previous tier
     */
    public function getPreviousTier()
    {
        return self::active()
                  ->where('points_required', '<', $this->points_required)
                  ->orderBy('points_required', 'desc')
                  ->first();
    }

    /**
     * Get tier for user based on points
     */
    public static function getTierForUser(User $user)
    {
        $lifetimePoints = LoyaltyPoint::getUserLifetimePoints($user->id);
        
        return self::active()
                  ->where('points_required', '<=', $lifetimePoints)
                  ->orderBy('points_required', 'desc')
                  ->first();
    }

    /**
     * Get next tier for user
     */
    public static function getNextTierForUser(User $user)
    {
        $lifetimePoints = LoyaltyPoint::getUserLifetimePoints($user->id);
        
        return self::active()
                  ->where('points_required', '>', $lifetimePoints)
                  ->orderBy('points_required', 'asc')
                  ->first();
    }

    /**
     * Calculate progress percentage to next tier
     */
    public function getProgressPercentage(User $user): float
    {
        $lifetimePoints = LoyaltyPoint::getUserLifetimePoints($user->id);
        $nextTier = $this->getNextTier();
        
        if (!$nextTier) {
            return 100.0; // Max tier reached
        }
        
        $currentTierPoints = $this->points_required;
        $nextTierPoints = $nextTier->points_required;
        $pointsInCurrentTier = $lifetimePoints - $currentTierPoints;
        $pointsNeededForNextTier = $nextTierPoints - $currentTierPoints;
        
        return min(100.0, max(0.0, ($pointsInCurrentTier / $pointsNeededForNextTier) * 100));
    }

    /**
     * Get formatted benefits list
     */
    public function getFormattedBenefitsAttribute(): array
    {
        if (!$this->benefits) {
            return [];
        }

        return array_map(function($benefit) {
            return is_array($benefit) ? $benefit : ['text' => $benefit];
        }, $this->benefits);
    }
}
