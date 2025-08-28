<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Modules\Loyalty\Models\Reward;

class RewardSeeder extends Seeder
{
    /**
     * Run the database seeder.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('🎁 إنشاء المكافآت...');

        $rewards = [
            [
                'title' => 'خصم 10% على الطلب القادم',
                'description' => 'احصل على خصم 10% على طلبك القادم من عصائر سن توب',
                'type' => 'discount',
                'points_cost' => 100,
                'discount_percentage' => 10,
                'category' => 'خصومات',
                'expiry_days' => 30,
                'terms_conditions' => 'صالح لمرة واحدة فقط، لا يمكن دمجه مع عروض أخرى',
            ],
            [
                'title' => 'عصير سن توب مجاني 500 مل',
                'description' => 'احصل على عصير سن توب بحجم 500 مل مجاناً',
                'type' => 'free_product',
                'points_cost' => 200,
                'category' => 'منتجات مجانية',
                'expiry_days' => 14,
                'terms_conditions' => 'يشمل جميع النكهات المتاحة',
            ],
            [
                'title' => 'استرداد نقدي 25 جنيه',
                'description' => 'احصل على استرداد نقدي بقيمة 25 جنيه في محفظتك',
                'type' => 'cashback',
                'points_cost' => 250,
                'cashback_amount' => 25.00,
                'category' => 'استرداد نقدي',
                'expiry_days' => 60,
                'terms_conditions' => 'يتم إضافة المبلغ لمحفظتك خلال 24 ساعة',
            ],
            [
                'title' => '50 نقطة ولاء إضافية',
                'description' => 'احصل على 50 نقطة ولاء إضافية فوراً',
                'type' => 'bonus_points',
                'points_cost' => 150,
                'bonus_points' => 50,
                'category' => 'نقاط إضافية',
                'expiry_days' => 7,
                'terms_conditions' => 'النقاط الإضافية صالحة لمدة سنة واحدة',
            ],
            [
                'title' => 'خصم 20% على العصائر الحمضية',
                'description' => 'خصم خاص 20% على جميع عصائر سن توب الحمضية',
                'type' => 'discount',
                'points_cost' => 300,
                'discount_percentage' => 20,
                'category' => 'خصومات متخصصة',
                'applicable_categories' => ['Citrus', 'حمضيات'],
                'expiry_days' => 21,
                'minimum_order_amount' => 50.00,
                'terms_conditions' => 'الحد الأدنى للطلب 50 جنيه، صالح على العصائر الحمضية فقط',
            ],
            [
                'title' => 'شحن مجاني للشهر القادم',
                'description' => 'احصل على شحن مجاني لجميع طلباتك خلال الشهر القادم',
                'type' => 'free_product',
                'points_cost' => 400,
                'category' => 'خدمات مجانية',
                'expiry_days' => 30,
                'terms_conditions' => 'صالح لعدد غير محدود من الطلبات خلال 30 يوم',
            ],
            [
                'title' => 'استرداد نقدي 50 جنيه',
                'description' => 'استرداد نقدي كبير بقيمة 50 جنيه',
                'type' => 'cashback',
                'points_cost' => 500,
                'cashback_amount' => 50.00,
                'category' => 'استرداد نقدي',
                'expiry_days' => 90,
                'terms_conditions' => 'يتم إضافة المبلغ لمحفظتك خلال 24 ساعة',
            ],
            [
                'title' => 'مجموعة عصائر سن توب المتنوعة',
                'description' => 'احصل على مجموعة من 6 عصائر بنكهات مختلفة',
                'type' => 'free_product',
                'points_cost' => 600,
                'category' => 'منتجات مجانية',
                'expiry_days' => 14,
                'terms_conditions' => 'تشمل 6 عصائر بأحجام 250 مل بنكهات متنوعة',
            ],
            [
                'title' => 'خصم 25% على الطلبات الكبيرة',
                'description' => 'خصم استثنائي 25% على الطلبات بقيمة 200 جنيه أو أكثر',
                'type' => 'discount',
                'points_cost' => 750,
                'discount_percentage' => 25,
                'category' => 'خصومات متخصصة',
                'minimum_order_amount' => 200.00,
                'expiry_days' => 45,
                'terms_conditions' => 'الحد الأدنى للطلب 200 جنيه، صالح لمرة واحدة فقط',
            ],
            [
                'title' => '200 نقطة ولاء فورية',
                'description' => 'احصل على 200 نقطة ولاء تُضاف فوراً لحسابك',
                'type' => 'bonus_points',
                'points_cost' => 800,
                'bonus_points' => 200,
                'category' => 'نقاط إضافية',
                'expiry_days' => 7,
                'terms_conditions' => 'النقاط الإضافية صالحة لمدة سنة واحدة من تاريخ الإضافة',
            ],
        ];

        foreach ($rewards as $rewardData) {
            Reward::create($rewardData);
        }

        $this->command->info('✅ تم إنشاء ' . count($rewards) . ' مكافأة بنجاح');
    }
}
