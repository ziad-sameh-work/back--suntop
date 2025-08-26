<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Modules\Users\Models\UserCategory;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeder.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('🔄 إنشاء المستخدمين التجريبيين مع بيانات الكراتين والعلب...');

        // Check if users already exist
        if (User::where('username', 'testuser')->exists()) {
            $this->command->info('⚠️ المستخدمين موجودون بالفعل، تحديث بياناتهم...');
            $this->updateExistingUsers();
            return;
        }

        // Get categories for assignment
        $categories = UserCategory::ordered()->get();
        $starterCategory = $categories->where('name', 'Starter')->first();
        $regularCategory = $categories->where('name', 'Regular')->first();
        $premiumCategory = $categories->where('name', 'Premium')->first();
        $wholesaleCategory = $categories->where('name', 'Wholesale')->first();

        // Create test users with carton/package data
        $users = [
            [
                'name' => 'Test User',
                'username' => 'testuser',
                'email' => 'test@suntop.com',
                'full_name' => 'مستخدم تجريبي',
                'phone' => '+20 109 999 9999',
                'password' => Hash::make('password123'),
                'role' => 'customer',
                'user_category_id' => $starterCategory?->id,
                'total_cartons_purchased' => 2,
                'total_packages_purchased' => 5,
                'total_units_purchased' => 8,
                'total_orders_count' => 3,
                'total_purchase_amount' => 65.75, // Legacy
                'is_active' => true,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Admin User',
                'username' => 'admin',
                'email' => 'admin@suntop.com',
                'full_name' => 'مدير النظام',
                'phone' => '+20 100 000 0001',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'user_category_id' => null, // Admins don't need categories
                'total_cartons_purchased' => 0,
                'total_packages_purchased' => 0,
                'total_units_purchased' => 0,
                'total_orders_count' => 0,
                'total_purchase_amount' => 0,
                'is_active' => true,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Regular Customer',
                'username' => 'regular_customer',
                'email' => 'regular@suntop.com',
                'full_name' => 'عميل عادي',
                'phone' => '+20 100 000 0003',
                'password' => Hash::make('password123'),
                'role' => 'customer',
                'user_category_id' => $regularCategory?->id,
                'total_cartons_purchased' => 12,
                'total_packages_purchased' => 25,
                'total_units_purchased' => 35,
                'total_orders_count' => 8,
                'total_purchase_amount' => 485.50,
                'is_active' => true,
                'email_verified_at' => now(),
                'created_at' => now()->subDays(30),
                'updated_at' => now(),
            ],
            [
                'name' => 'Premium Customer',
                'username' => 'premium_customer',
                'email' => 'premium@suntop.com',
                'full_name' => 'عميل مميز',
                'phone' => '+20 100 000 0004',
                'password' => Hash::make('password123'),
                'role' => 'customer',
                'user_category_id' => $premiumCategory?->id,
                'total_cartons_purchased' => 35,
                'total_packages_purchased' => 85,
                'total_units_purchased' => 120,
                'total_orders_count' => 18,
                'total_purchase_amount' => 1450.25,
                'is_active' => true,
                'email_verified_at' => now(),
                'created_at' => now()->subDays(60),
                'updated_at' => now(),
            ],
            [
                'name' => 'Wholesale Customer',
                'username' => 'wholesale_customer',
                'email' => 'wholesale@suntop.com',
                'full_name' => 'تاجر جملة',
                'phone' => '+20 100 000 0005',
                'password' => Hash::make('password123'),
                'role' => 'customer',
                'user_category_id' => $wholesaleCategory?->id,
                'total_cartons_purchased' => 125,
                'total_packages_purchased' => 250,
                'total_units_purchased' => 85,
                'total_orders_count' => 45,
                'total_purchase_amount' => 8750.75,
                'is_active' => true,
                'email_verified_at' => now(),
                'created_at' => now()->subDays(90),
                'updated_at' => now(),
            ],
            [
                'name' => 'Merchant User',
                'username' => 'merchant1',
                'email' => 'merchant@suntop.com',
                'full_name' => 'تاجر سن توب',
                'phone' => '+20 100 000 0002',
                'password' => Hash::make('merchant123'),
                'role' => 'merchant',
                'user_category_id' => null, // Merchants don't need categories
                'total_cartons_purchased' => 0,
                'total_packages_purchased' => 0,
                'total_units_purchased' => 0,
                'total_orders_count' => 0,
                'total_purchase_amount' => 0,
                'is_active' => true,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($users as $userData) {
            $user = User::create($userData);
            $this->command->info("✅ تم إنشاء المستخدم: {$userData['full_name']} ({$userData['role']})");
            
            // Show category info for customers
            if ($userData['role'] === 'customer' && $userData['user_category_id']) {
                $category = UserCategory::find($userData['user_category_id']);
                $this->command->info("   📋 الفئة: {$category->display_name} | كراتين: {$userData['total_cartons_purchased']} | علب: {$userData['total_packages_purchased']}");
            }
        }

        $this->command->info('🎉 تم إنشاء ' . count($users) . ' مستخدمين بنجاح!');
        $this->command->info('📊 الآن يمكن للمستخدمين الاستفادة من خصومات الكراتين والعلب حسب فئاتهم');
    }

    /**
     * Update existing users with carton/package data
     */
    private function updateExistingUsers()
    {
        // Get categories for assignment
        $categories = UserCategory::ordered()->get();
        $starterCategory = $categories->where('name', 'Starter')->first();
        $regularCategory = $categories->where('name', 'Regular')->first();
        $premiumCategory = $categories->where('name', 'Premium')->first();
        $wholesaleCategory = $categories->where('name', 'Wholesale')->first();

        $userUpdates = [
            'testuser' => [
                'user_category_id' => $starterCategory?->id,
                'total_cartons_purchased' => 2,
                'total_packages_purchased' => 5,
                'total_units_purchased' => 8,
                'total_orders_count' => 3,
            ],
            'regular_customer' => [
                'user_category_id' => $regularCategory?->id,
                'total_cartons_purchased' => 12,
                'total_packages_purchased' => 25,
                'total_units_purchased' => 35,
                'total_orders_count' => 8,
            ],
            'premium_customer' => [
                'user_category_id' => $premiumCategory?->id,
                'total_cartons_purchased' => 35,
                'total_packages_purchased' => 85,
                'total_units_purchased' => 120,
                'total_orders_count' => 18,
            ],
            'wholesale_customer' => [
                'user_category_id' => $wholesaleCategory?->id,
                'total_cartons_purchased' => 125,
                'total_packages_purchased' => 250,
                'total_units_purchased' => 85,
                'total_orders_count' => 45,
            ],
        ];

        foreach ($userUpdates as $username => $updateData) {
            $user = User::where('username', $username)->first();
            if ($user) {
                $user->update($updateData);
                $this->command->info("✅ تم تحديث المستخدم: {$user->full_name}");
                
                if ($updateData['user_category_id']) {
                    $category = UserCategory::find($updateData['user_category_id']);
                    $this->command->info("   📋 الفئة: {$category->display_name} | كراتين: {$updateData['total_cartons_purchased']} | علب: {$updateData['total_packages_purchased']}");
                }
            }
        }

        $this->command->info('🔄 تم تحديث المستخدمين الموجودين بنجاح!');
    }
}