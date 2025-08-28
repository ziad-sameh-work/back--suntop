<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Modules\Loyalty\Models\RewardTier;

class RewardTierSeeder extends Seeder
{
    /**
     * Run the database seeder.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('🏆 إنشاء مستويات المكافآت...');

        $tiers = [
            [
                'name' => 'bronze',
                'display_name' => 'البرونزي',
                'description' => 'مستوى البداية مع مكافآت أساسية',
                'points_required' => 0,
                'color' => '#CD7F32',
                'discount_percentage' => 0,
                'bonus_multiplier' => 1,
                'benefits' => [
                    'نقاط ولاء مع كل عملية شراء',
                    'إشعارات العروض الخاصة',
                    'دعم العملاء المتميز',
                ],
                'sort_order' => 1,
            ],
            [
                'name' => 'silver',
                'display_name' => 'الفضي',
                'description' => 'مستوى متقدم مع مكافآت محسّنة',
                'points_required' => 500,
                'color' => '#C0C0C0',
                'discount_percentage' => 5,
                'bonus_multiplier' => 1.2,
                'benefits' => [
                    'خصم 5% على جميع المنتجات',
                    'نقاط إضافية 20% مع كل شراء',
                    'عروض حصرية للأعضاء الفضيين',
                    'شحن مجاني للطلبات فوق 100 جنيه',
                ],
                'sort_order' => 2,
            ],
            [
                'name' => 'gold',
                'display_name' => 'الذهبي',
                'description' => 'مستوى مميز مع مكافآت رائعة',
                'points_required' => 1000,
                'color' => '#FFD700',
                'discount_percentage' => 10,
                'bonus_multiplier' => 1.5,
                'benefits' => [
                    'خصم 10% على جميع المنتجات',
                    'نقاط إضافية 50% مع كل شراء',
                    'عروض حصرية للأعضاء الذهبيين',
                    'شحن مجاني على جميع الطلبات',
                    'أولوية في خدمة العملاء',
                    'هدايا مجانية مع الطلبات الكبيرة',
                ],
                'sort_order' => 3,
            ],
            [
                'name' => 'platinum',
                'display_name' => 'البلاتيني',
                'description' => 'المستوى الأعلى مع أفضل المكافآت',
                'points_required' => 2000,
                'color' => '#E5E4E2',
                'discount_percentage' => 15,
                'bonus_multiplier' => 2,
                'benefits' => [
                    'خصم 15% على جميع المنتجات',
                    'مضاعفة النقاط مع كل شراء',
                    'وصول مبكر للمنتجات الجديدة',
                    'عروض حصرية للأعضاء البلاتينيين',
                    'شحن مجاني سريع',
                    'مدير حساب شخصي',
                    'هدايا مجانية شهرية',
                    'دعوات لفعاليات خاصة',
                ],
                'sort_order' => 4,
            ],
        ];

        foreach ($tiers as $tierData) {
            RewardTier::create($tierData);
        }

        $this->command->info('✅ تم إنشاء ' . count($tiers) . ' مستوى مكافآت بنجاح');
    }
}
