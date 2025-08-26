<?php

namespace App\Modules\Users\Services;

use App\Modules\Core\BaseService;
use App\Modules\Users\Models\UserCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class UserCategoryService extends BaseService
{
    public function __construct(UserCategory $userCategory)
    {
        $this->model = $userCategory;
    }

    /**
     * Get all active categories ordered
     */
    public function getAllActive(): Collection
    {
        return $this->model->active()->ordered()->get();
    }

    /**
     * Create new category
     */
    public function createCategory(array $data): UserCategory
    {
        // Validate that ranges don't overlap
        $this->validateCategoryRanges($data);
        
        return $this->create($data);
    }

    /**
     * Update category
     */
    public function updateCategory(int $categoryId, array $data): UserCategory
    {
        // Validate that ranges don't overlap (excluding current category)
        $this->validateCategoryRanges($data, $categoryId);
        
        $category = $this->update($categoryId, $data);
        
        // Update all users in this category
        $this->recalculateAllUserCategories();
        
        return $category;
    }

    /**
     * Delete category
     */
    public function deleteCategory(int $categoryId): bool
    {
        $category = $this->findByIdOrFail($categoryId);
        
        // Move users to appropriate categories before deletion
        $this->reassignUsersFromCategory($categoryId);
        
        return $this->delete($categoryId);
    }

    /**
     * Get category for specific purchase amount
     */
    public function getCategoryForAmount(float $amount): ?UserCategory
    {
        return UserCategory::getCategoryForAmount($amount);
    }

    /**
     * Get category for specific user based on carton/package count
     */
    public function getCategoryForUser(User $user): ?UserCategory
    {
        return UserCategory::getCategoryForUser($user);
    }

    /**
     * Update user category based on carton/package purchase history
     */
    public function updateUserCategory(User $user): void
    {
        $user->updateCategory();
    }

    /**
     * Apply category discount to order items based on selling type
     */
    public function applyCategoryDiscountToItems(User $user, array $orderItems): float
    {
        $totalDiscount = 0;
        
        foreach ($orderItems as $item) {
            // Only apply discount to cartons and packages
            if (in_array($item['selling_type'], ['carton', 'package'])) {
                $discountPercentage = $user->getCategoryDiscountForSellingType($item['selling_type']);
                
                if ($discountPercentage > 0) {
                    $itemDiscount = $item['total_price'] * ($discountPercentage / 100);
                    $totalDiscount += $itemDiscount;
                }
            }
        }
        
        return $totalDiscount;
    }

    /**
     * Check if user qualifies for carton-based discounts
     */
    public function userQualifiesForCartonDiscount(User $user): bool
    {
        $category = $user->userCategory;
        return $category && $category->carton_discount_percentage > 0;
    }

    /**
     * Check if user qualifies for package-based discounts
     */
    public function userQualifiesForPackageDiscount(User $user): bool
    {
        $category = $user->userCategory;
        return $category && $category->package_discount_percentage > 0;
    }

    /**
     * Recalculate categories for all users
     */
    public function recalculateAllUserCategories(): void
    {
        User::chunk(100, function ($users) {
            foreach ($users as $user) {
                $user->updateCategory();
            }
        });
    }

    /**
     * Get category statistics
     */
    public function getCategoryStatistics(): array
    {
        $categories = $this->getAllActive();
        $stats = [];

        foreach ($categories as $category) {
            $userCount = User::byCategory($category->id)->count();
            $totalCartons = User::byCategory($category->id)->sum('total_cartons_purchased');
            $totalPackages = User::byCategory($category->id)->sum('total_packages_purchased');
            $totalUnits = User::byCategory($category->id)->sum('total_units_purchased');
            $totalPurchases = User::byCategory($category->id)->sum('total_purchase_amount'); // Legacy
            
            $stats[] = [
                'category' => $category,
                'user_count' => $userCount,
                'total_cartons' => $totalCartons,
                'total_packages' => $totalPackages,
                'total_units' => $totalUnits,
                'average_cartons' => $userCount > 0 ? $totalCartons / $userCount : 0,
                'average_packages' => $userCount > 0 ? $totalPackages / $userCount : 0,
                // Legacy fields
                'total_purchases' => $totalPurchases,
                'average_purchase' => $userCount > 0 ? $totalPurchases / $userCount : 0,
            ];
        }

        return $stats;
    }

    /**
     * Get users by category
     */
    public function getUsersByCategory(int $categoryId, int $perPage = 20)
    {
        return User::with('userCategory')
                   ->byCategory($categoryId)
                   ->orderByRaw('(total_cartons_purchased + total_packages_purchased) DESC')
                   ->orderBy('total_purchase_amount', 'desc') // Secondary sort for legacy
                   ->paginate($perPage);
    }

    /**
     * Apply category discount to order
     */
    public function applyCategoryDiscount(User $user, float $orderAmount): float
    {
        $discountPercentage = $user->category_discount;
        
        if ($discountPercentage <= 0) {
            return 0;
        }

        return $orderAmount * ($discountPercentage / 100);
    }

    /**
     * Validate category ranges don't overlap
     */
    private function validateCategoryRanges(array $data, ?int $excludeCategoryId = null): void
    {
        $minAmount = $data['min_purchase_amount'] ?? 0;
        $maxAmount = $data['max_purchase_amount'] ?? null;

        $query = $this->model->active();
        
        if ($excludeCategoryId) {
            $query->where('id', '!=', $excludeCategoryId);
        }

        $existingCategories = $query->get();

        foreach ($existingCategories as $category) {
            // Check for overlap
            $categoryMin = $category->min_purchase_amount;
            $categoryMax = $category->max_purchase_amount;

            // Case 1: New range starts within existing range
            if ($minAmount >= $categoryMin && ($categoryMax === null || $minAmount <= $categoryMax)) {
                throw new \Exception("نطاق المبلغ يتداخل مع فئة {$category->name}");
            }

            // Case 2: New range ends within existing range
            if ($maxAmount !== null && $maxAmount >= $categoryMin && ($categoryMax === null || $maxAmount <= $categoryMax)) {
                throw new \Exception("نطاق المبلغ يتداخل مع فئة {$category->name}");
            }

            // Case 3: New range encompasses existing range
            if ($minAmount <= $categoryMin && ($maxAmount === null || ($categoryMax !== null && $maxAmount >= $categoryMax))) {
                throw new \Exception("نطاق المبلغ يتداخل مع فئة {$category->name}");
            }
        }
    }

    /**
     * Reassign users from deleted category
     */
    private function reassignUsersFromCategory(int $categoryId): void
    {
        $users = User::byCategory($categoryId)->get();
        
        foreach ($users as $user) {
            $user->update(['user_category_id' => null]);
            $user->updateCategory();
        }
    }

    /**
     * Get category upgrade/downgrade history for user
     */
    public function getUserCategoryHistory(User $user): array
    {
        // This would require a separate history table in a full implementation
        // For now, return basic info
        return [
            'current_category' => $user->userCategory,
            'total_purchase_amount' => $user->total_purchase_amount,
            'total_orders_count' => $user->total_orders_count,
            'category_updated_at' => $user->category_updated_at,
        ];
    }

    /**
     * Check if user qualifies for category upgrade
     */
    public function checkForCategoryUpgrade(User $user): ?UserCategory
    {
        $currentCategory = $user->userCategory;
        $possibleCategory = $this->getCategoryForAmount($user->total_purchase_amount);
        
        if (!$currentCategory || !$possibleCategory) {
            return $possibleCategory;
        }

        // Check if new category has higher benefits (higher discount percentage)
        if ($possibleCategory->discount_percentage > $currentCategory->discount_percentage) {
            return $possibleCategory;
        }

        return null;
    }
}
