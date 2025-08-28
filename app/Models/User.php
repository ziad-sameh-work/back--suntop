<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Modules\Users\Models\UserCategory;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    // User roles constants
    const ROLE_USER = 'customer';
    const ROLE_ADMIN = 'admin';
    const ROLE_MERCHANT = 'merchant';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'full_name',
        'phone',
        'role',
        'user_category_id',
        'total_cartons_purchased',
        'total_packages_purchased', 
        'total_units_purchased',
        'total_orders_count',
        'category_updated_at',
        // Legacy field for backward compatibility
        'total_purchase_amount',
        'is_active',
        'profile_image',
        'last_login_at',
        'password_changed_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'password_changed_at' => 'datetime',
        'category_updated_at' => 'datetime',
        'is_active' => 'boolean',
        // Legacy cast for backward compatibility
        'total_purchase_amount' => 'decimal:2',
    ];

    /**
     * Scope for active users
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope by role
     */
    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    /**
     * User category relationship
     */
    public function userCategory(): BelongsTo
    {
        return $this->belongsTo(UserCategory::class);
    }

    /**
     * Orders relationship
     */
    public function orders(): HasMany
    {
        return $this->hasMany(\App\Modules\Orders\Models\Order::class);
    }

    /**
     * Loyalty points relationship
     */
    public function loyaltyPoints(): HasMany
    {
        return $this->hasMany(\App\Modules\Loyalty\Models\LoyaltyPoint::class);
    }

    public function chats(): HasMany
    {
        return $this->hasMany(\App\Models\Chat::class, 'customer_id');
    }

    public function assignedChats(): HasMany
    {
        return $this->hasMany(\App\Models\Chat::class, 'assigned_admin_id');
    }

    public function chatMessages(): HasMany
    {
        return $this->hasMany(\App\Models\ChatMessage::class, 'sender_id');
    }

    /**
     * Notifications relationship
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(\App\Models\Notification::class);
    }

    /**
     * Favorites relationship
     */
    public function favorites(): HasMany
    {
        return $this->hasMany(\App\Models\Favorite::class);
    }

    /**
     * Favorite products relationship
     */
    public function favoriteProducts()
    {
        return $this->belongsToMany(\App\Modules\Products\Models\Product::class, 'favorites', 'user_id', 'product_id')
                   ->withTimestamps()
                   ->withPivot('added_at');
    }

    /**
     * Scope by category
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('user_category_id', $categoryId);
    }

    /**
     * Update user purchase statistics and category
     */
    public function updatePurchaseStats(float $orderAmount, int $cartons = 0, int $packages = 0, int $units = 0): void
    {
        $this->increment('total_orders_count');
        $this->increment('total_cartons_purchased', $cartons);
        $this->increment('total_packages_purchased', $packages);
        $this->increment('total_units_purchased', $units);
        
        // Legacy support - still track total purchase amount
        $this->increment('total_purchase_amount', $orderAmount);
        
        // Update category based on new totals
        $this->updateCategory();
    }

    /**
     * Update user category based on carton/package purchase history
     */
    public function updateCategory(): void
    {
        $newCategory = UserCategory::getCategoryForUser($this);
        
        if ($newCategory && $this->user_category_id !== $newCategory->id) {
            $this->update([
                'user_category_id' => $newCategory->id,
                'category_updated_at' => now(),
            ]);
        }
    }

    /**
     * Get user's total package units (cartons + packages)
     */
    public function getTotalPackageUnitsAttribute(): int
    {
        return $this->total_cartons_purchased + $this->total_packages_purchased;
    }

    /**
     * Get category discount for specific selling type
     */
    public function getCategoryDiscountForSellingType(string $sellingType = 'unit'): float
    {
        return $this->userCategory?->getDiscountForSellingType($sellingType) ?? 0;
    }

    /**
     * Get user category discount percentage
     */
    public function getCategoryDiscountAttribute(): float
    {
        return $this->userCategory?->discount_percentage ?? 0;
    }

    /**
     * Get formatted purchase amount
     */
    public function getFormattedPurchaseAmountAttribute(): string
    {
        return number_format($this->total_purchase_amount, 2) . ' جنيه';
    }

    /**
     * Get category name
     */
    public function getCategoryNameAttribute(): ?string
    {
        return $this->userCategory?->name;
    }

    /**
     * Get category display name
     */
    public function getCategoryDisplayNameAttribute(): ?string
    {
        return $this->userCategory?->display_name;
    }
}
