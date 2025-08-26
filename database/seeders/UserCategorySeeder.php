<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Modules\Users\Models\UserCategory;

class UserCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('🔄 إنشاء فئات المستخدمين الجديدة بناءً على الكراتين ونقاط الولاء...');

        // Clear existing categories safely (respecting foreign keys)
        if (UserCategory::count() > 0) {
            // Update all users to have no category first
            \DB::table('users')->update(['user_category_id' => null]);
            // Now we can safely delete categories
            UserCategory::query()->delete();
        }

        $categories = [
            [
                'name' => 'Starter',
                'display_name' => 'المبتدئ',
                'display_name_en' => 'Starter',
                'description' => 'العملاء الجدد - أقل من 5 كراتين',
                'min_cartons' => 0,
                'max_cartons' => 4,
                'min_packages' => 0,
                'max_packages' => 0,
                'carton_loyalty_points' => 10,
                'bonus_points_per_carton' => 0,
                'monthly_bonus_points' => 0,
                'signup_bonus_points' => 50,
                'has_points_multiplier' => false,
                'points_multiplier' => 1.0,
                'requires_carton_purchase' => false,
                'requires_package_purchase' => false,
                'benefits' => [
                    '10 نقاط لكل كرتون',
                    'مكافأة 50 نقطة عند التسجيل',
                    'شحن مجاني للطلبات أكثر من 200 جنيه',
                    'دعم عملاء على مدار الساعة'
                ],
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Regular',
                'display_name' => 'العادي',
                'display_name_en' => 'Regular',
                'description' => 'العملاء العاديين - من 5 إلى 19 كرتون',
                'min_cartons' => 5,
                'max_cartons' => 19,
                'min_packages' => 0,
                'max_packages' => 0,
                'carton_loyalty_points' => 12,
                'bonus_points_per_carton' => 3,
                'monthly_bonus_points' => 100,
                'signup_bonus_points' => 100,
                'has_points_multiplier' => false,
                'points_multiplier' => 1.0,
                'requires_carton_purchase' => false,
                'requires_package_purchase' => false,
                'benefits' => [
                    '15 نقطة لكل كرتون (12 أساسية + 3 إضافية)',
                    'مكافأة 100 نقطة شهرياً',
                    'مكافأة 100 نقطة عند الترقية',
                    'شحن مجاني للطلبات أكثر من 150 جنيه',
                    'أولوية في خدمة العملاء'
                ],
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Premium',
                'display_name' => 'المميز',
                'display_name_en' => 'Premium',
                'description' => 'العملاء المميزين - من 20 إلى 49 كرتون',
                'min_cartons' => 20,
                'max_cartons' => 49,
                'min_packages' => 0,
                'max_packages' => 0,
                'carton_loyalty_points' => 15,
                'bonus_points_per_carton' => 5,
                'monthly_bonus_points' => 200,
                'signup_bonus_points' => 200,
                'has_points_multiplier' => true,
                'points_multiplier' => 1.5,
                'requires_carton_purchase' => false,
                'requires_package_purchase' => false,
                'benefits' => [
                    '30 نقطة لكل كرتون (15 أساسية + 5 إضافية × 1.5)',
                    'مضاعف النقاط 1.5x',
                    'مكافأة 200 نقطة شهرياً',
                    'مكافأة 200 نقطة عند الترقية',
                    'شحن مجاني لجميع الطلبات',
                    'مندوب مبيعات مخصص',
                    'عروض خاصة شهرية'
                ],
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Wholesale',
                'display_name' => 'تاجر الجملة',
                'display_name_en' => 'Wholesale',
                'description' => 'تجار الجملة - أكثر من 50 كرتون',
                'min_cartons' => 50,
                'max_cartons' => null,
                'min_packages' => 0,
                'max_packages' => 0,
                'carton_loyalty_points' => 20,
                'bonus_points_per_carton' => 10,
                'monthly_bonus_points' => 500,
                'signup_bonus_points' => 500,
                'has_points_multiplier' => true,
                'points_multiplier' => 2.0,
                'requires_carton_purchase' => true,
                'requires_package_purchase' => false,
                'benefits' => [
                    '60 نقطة لكل كرتون (20 أساسية + 10 إضافية × 2.0)',
                    'مضاعف النقاط 2.0x',
                    'مكافأة 500 نقطة شهرياً',
                    'مكافأة 500 نقطة عند الترقية',
                    'شحن مجاني لجميع الطلبات',
                    'دفع آجل حتى 30 يوم',
                    'مندوب مبيعات مخصص',
                    'عروض خاصة أسبوعية',
                    'خصم إضافي على الطلبات الكبيرة'
                ],
                'is_active' => true,
                'sort_order' => 4,
            ],
        ];

        foreach ($categories as $category) {
            UserCategory::create($category);
            $this->command->info("✅ تم إنشاء فئة: {$category['display_name']}");
        }

        $this->command->info('🎉 تم إنشاء ' . count($categories) . ' فئات مستخدمين بنجاح!');
    }
}