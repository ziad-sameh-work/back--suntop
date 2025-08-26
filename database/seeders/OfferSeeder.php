<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\Offers\Models\Offer;
use Carbon\Carbon;

class OfferSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        // تحقق من وجود عروض مسبقاً
        if (Offer::count() > 0) {
            $this->command->info('العروض موجودة بالفعل، تم تخطي عملية إنشاء البيانات التجريبية.');
            return;
        }

        $this->command->info('بدء إنشاء بيانات العروض التجريبية...');

        $offers = [
            // عروض نشطة حالياً
            [
                'title' => 'خصم 20% على جميع المنتجات',
                'description' => 'عرض خاص لفترة محدودة! احصل على خصم 20% على جميع المنتجات في المتجر. العرض ساري على جميع الفئات ولا يتطلب حد أدنى للشراء.',
                'type' => 'percentage',
                'discount_percentage' => 20.00,
                'minimum_amount' => 0,
                'maximum_discount' => 500,
                'valid_from' => now()->subDays(5),
                'valid_until' => now()->addDays(25),
                'usage_limit' => 1000,
                'used_count' => 45,
                'is_active' => true,
                'first_order_only' => false,
                'applicable_categories' => null,
            ],
            [
                'title' => 'خصم 50 جنيه للعملاء الجدد',
                'description' => 'مرحباً بك في عائلة SunTop! احصل على خصم 50 جنيه على أول طلب لك. العرض صالح للعملاء الجدد فقط.',
                'type' => 'fixed_amount',
                'discount_amount' => 50.00,
                'minimum_amount' => 200,
                'valid_from' => now()->subDays(10),
                'valid_until' => now()->addDays(20),
                'usage_limit' => 500,
                'used_count' => 23,
                'is_active' => true,
                'first_order_only' => true,
                'applicable_categories' => null,
            ],
            [
                'title' => 'خصم 15% على الإلكترونيات',
                'description' => 'عرض خاص على جميع الأجهزة الإلكترونية والاكسسوارات. خصم 15% لفترة محدودة مع إمكانية الشحن المجاني.',
                'type' => 'percentage',
                'discount_percentage' => 15.00,
                'minimum_amount' => 500,
                'maximum_discount' => 300,
                'valid_from' => now()->subDays(3),
                'valid_until' => now()->addDays(17),
                'usage_limit' => 200,
                'used_count' => 12,
                'is_active' => true,
                'first_order_only' => false,
                'applicable_categories' => ['إلكترونيات', 'أجهزة', 'اكسسوارات'],
            ],
            [
                'title' => 'خصم 100 جنيه على الطلبات الكبيرة',
                'description' => 'للطلبات التي تزيد عن 1000 جنيه، احصل على خصم فوري 100 جنيه. عرض محدود للكميات الكبيرة.',
                'type' => 'fixed_amount',
                'discount_amount' => 100.00,
                'minimum_amount' => 1000,
                'valid_from' => now()->subDays(7),
                'valid_until' => now()->addDays(23),
                'usage_limit' => 150,
                'used_count' => 8,
                'is_active' => true,
                'first_order_only' => false,
                'applicable_categories' => null,
            ],

            // عروض ستبدأ قريباً
            [
                'title' => 'عرض الجمعة البيضاء - خصم 40%',
                'description' => 'استعد لأكبر عروض السنة! خصم يصل إلى 40% على آلاف المنتجات في عرض الجمعة البيضاء. لا تفوت الفرصة!',
                'type' => 'percentage',
                'discount_percentage' => 40.00,
                'minimum_amount' => 100,
                'maximum_discount' => 1000,
                'valid_from' => now()->addDays(5),
                'valid_until' => now()->addDays(7),
                'usage_limit' => 5000,
                'used_count' => 0,
                'is_active' => true,
                'first_order_only' => false,
                'applicable_categories' => null,
            ],
            [
                'title' => 'خصم 25% للعضوية الذهبية',
                'description' => 'عرض خاص لأعضاء العضوية الذهبية. خصم 25% على جميع المنتجات كمكافأة للولاء.',
                'type' => 'percentage',
                'discount_percentage' => 25.00,
                'minimum_amount' => 300,
                'maximum_discount' => 500,
                'valid_from' => now()->addDays(3),
                'valid_until' => now()->addDays(33),
                'usage_limit' => 300,
                'used_count' => 0,
                'is_active' => true,
                'first_order_only' => false,
                'applicable_categories' => null,
            ],

            // عروض منتهية الصلاحية
            [
                'title' => 'عرض رمضان الكريم - خصم 30%',
                'description' => 'عرض شهر رمضان المبارك مع خصم 30% على مختارات من المنتجات. عرض منتهي الصلاحية.',
                'type' => 'percentage',
                'discount_percentage' => 30.00,
                'minimum_amount' => 150,
                'maximum_discount' => 400,
                'valid_from' => now()->subDays(45),
                'valid_until' => now()->subDays(15),
                'usage_limit' => 800,
                'used_count' => 267,
                'is_active' => false,
                'first_order_only' => false,
                'applicable_categories' => ['طعام', 'مشروبات', 'حلويات'],
            ],
            [
                'title' => 'خصم 75 جنيه للطلبات السريعة',
                'description' => 'عرض منتهي: خصم 75 جنيه على الطلبات السريعة خلال ساعة من التسجيل.',
                'type' => 'fixed_amount',
                'discount_amount' => 75.00,
                'minimum_amount' => 400,
                'valid_from' => now()->subDays(30),
                'valid_until' => now()->subDays(5),
                'usage_limit' => 100,
                'used_count' => 89,
                'is_active' => false,
                'first_order_only' => false,
                'applicable_categories' => null,
            ],

            // عروض غير نشطة
            [
                'title' => 'خصم 10% للطلاب',
                'description' => 'عرض خاص للطلاب مع خصم 10% بعد تقديم إثبات الطالب. العرض متوقف حالياً.',
                'type' => 'percentage',
                'discount_percentage' => 10.00,
                'minimum_amount' => 100,
                'maximum_discount' => 150,
                'valid_from' => now()->subDays(20),
                'valid_until' => now()->addDays(40),
                'usage_limit' => 200,
                'used_count' => 5,
                'is_active' => false,
                'first_order_only' => false,
                'applicable_categories' => ['كتب', 'قرطاسية', 'إلكترونيات'],
            ],
            [
                'title' => 'خصم 35 جنيه على الشحن',
                'description' => 'وفر في مصاريف الشحن! احصل على خصم 35 جنيه على رسوم التوصيل. العرض متوقف مؤقتاً.',
                'type' => 'fixed_amount',
                'discount_amount' => 35.00,
                'minimum_amount' => 250,
                'valid_from' => now()->subDays(12),
                'valid_until' => now()->addDays(18),
                'usage_limit' => 300,
                'used_count' => 67,
                'is_active' => false,
                'first_order_only' => false,
                'applicable_categories' => null,
            ],
        ];

        foreach ($offers as $offerData) {
            // إنشاء كود فريد لكل عرض
            $offerData['code'] = Offer::generateCode();
            
            Offer::create($offerData);
        }

        $this->command->info('تم إنشاء ' . count($offers) . ' عرض تجريبي بنجاح!');
        $this->command->info('العروض تشمل: عروض نشطة، عروض قادمة، عروض منتهية، وعروض متوقفة');
    }
}
