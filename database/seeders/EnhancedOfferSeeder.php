<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Modules\Offers\Models\Offer;

class EnhancedOfferSeeder extends Seeder
{
    /**
     * Run the database seeder.
     *
     * @return void
     */
    public function run()
    {
        // تحقق من وجود عروض مسبقاً
        if (Offer::where('code', 'MORNING25')->exists()) {
            $this->command->info('العروض المحسّنة موجودة بالفعل، تم تخطي عملية إنشاء البيانات التجريبية.');
            return;
        }

        $this->command->info('🎁 إنشاء العروض المحسّنة...');

        $offers = [
            [
                'title' => 'عرض الصباح الطازج',
                'description' => 'خصم 25% على جميع عصائر سن توب الحمضية حتى الساعة 12 ظهراً',
                'code' => 'MORNING25',
                'type' => 'percentage',
                'discount_percentage' => 25,
                'minimum_amount' => 30,
                'maximum_discount' => 50,
                'valid_from' => now(),
                'valid_until' => now()->addDays(30),
                'usage_limit' => 500,
                'applicable_categories' => ['Citrus', 'حمضيات'],
                'is_active' => true,
            ],
            [
                'title' => 'اشتري 2 واحصل على 1 مجاناً',
                'description' => 'سن توب المزيج الاستوائي - اشتري عبوتين واحصل على الثالثة مجاناً',
                'code' => 'BOGO3',
                'type' => 'percentage',
                'discount_percentage' => 33.33,
                'valid_from' => now(),
                'valid_until' => now()->addDays(14),
                'usage_limit' => 200,
                'applicable_categories' => ['Tropical', 'استوائي'],
                'is_active' => true,
            ],
            [
                'title' => 'هدية مجانية مع كل طلب',
                'description' => 'احصل على عصير سن توب 250 مل مجاناً مع أي طلب بقيمة 100 جنيه أو أكثر',
                'code' => 'FREEGIFT',
                'type' => 'fixed_amount',
                'minimum_amount' => 100,
                'valid_from' => now(),
                'valid_until' => now()->addDays(21),
                'usage_limit' => 300,
                'is_active' => true,
            ],
            [
                'title' => 'استرداد نقدي 15%',
                'description' => 'احصل على استرداد نقدي بنسبة 15% من قيمة طلبك (حد أقصى 75 جنيه)',
                'code' => 'CASHBACK15',
                'type' => 'fixed_amount',
                'discount_percentage' => 15,
                'maximum_discount' => 75,
                'minimum_amount' => 150,
                'valid_from' => now(),
                'valid_until' => now()->addDays(45),
                'usage_limit' => 100,
                'is_active' => true,
            ],
            [
                'title' => 'عرض نهاية الأسبوع',
                'description' => 'خصم 30% على جميع منتجات سن توب خلال عطلة نهاية الأسبوع',
                'code' => 'WEEKEND30',
                'type' => 'percentage',
                'discount_percentage' => 30,
                'minimum_amount' => 75,
                'valid_from' => now()->next('Friday'),
                'valid_until' => now()->next('Sunday')->endOfDay(),
                'usage_limit' => 1000,
                'is_active' => true,
            ],
            [
                'title' => 'عرض العملاء الجدد',
                'description' => 'خصم 40% على طلبك الأول من سن توب - مرحباً بك في عائلتنا!',
                'code' => 'WELCOME40',
                'type' => 'percentage',
                'discount_percentage' => 40,
                'maximum_discount' => 100,
                'valid_from' => now(),
                'valid_until' => now()->addDays(60),
                'usage_limit' => 1000,
                'first_order_only' => true,
                'is_active' => true,
            ],
            [
                'title' => 'عرض الكمية الكبيرة',
                'description' => 'خصم 20% على الطلبات التي تحتوي على 10 عبوات أو أكثر',
                'code' => 'BULK20',
                'type' => 'percentage',
                'discount_percentage' => 20,
                'minimum_amount' => 200,
                'valid_from' => now(),
                'valid_until' => now()->addDays(90),
                'usage_limit' => 50,
                'is_active' => true,
            ],
            [
                'title' => 'عرض منتصف الليل',
                'description' => 'خصم 35% على الطلبات بين الساعة 11 مساءً و 3 صباحاً',
                'code' => 'MIDNIGHT35',
                'type' => 'percentage',
                'discount_percentage' => 35,
                'minimum_amount' => 50,
                'maximum_discount' => 80,
                'valid_from' => now(),
                'valid_until' => now()->addDays(30),
                'usage_limit' => 200,
                'is_active' => true,
            ],
            [
                'title' => 'مجموعة التذوق المجانية',
                'description' => 'احصل على مجموعة تذوق مكونة من 4 عصائر مختلفة مجاناً مع أي طلب',
                'code' => 'TASTING',
                'type' => 'fixed_amount',
                'minimum_amount' => 120,
                'valid_from' => now(),
                'valid_until' => now()->addDays(25),
                'usage_limit' => 150,
                'is_active' => true,
            ],
            [
                'title' => 'عرض الولاء الذهبي',
                'description' => 'خصم إضافي 10% للأعضاء الذهبيين فما فوق في برنامج الولاء',
                'code' => 'GOLDLOYALTY',
                'type' => 'percentage',
                'discount_percentage' => 10,
                'valid_from' => now(),
                'valid_until' => now()->addDays(365),
                'is_active' => true,
            ],
        ];

        foreach ($offers as $offerData) {
            Offer::create($offerData);
        }

        $this->command->info('✅ تم إنشاء ' . count($offers) . ' عرض محسّن بنجاح');
    }
}
