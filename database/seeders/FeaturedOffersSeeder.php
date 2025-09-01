<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Modules\Offers\Models\Offer;

class FeaturedOffersSeeder extends Seeder
{
    /**
     * Run the database seeder.
     *
     * @return void
     */
    public function run()
    {
        // تحقق من وجود عروض مميزة مسبقاً
        $existingCodes = ['CITRUS30', 'MANGO25', 'FREESHIP100', 'BUY3GET1', 'CASHBACK15'];
        $hasExistingOffers = Offer::whereIn('code', $existingCodes)->exists();
        
        if ($hasExistingOffers) {
            $this->command->info('العروض المميزة موجودة بالفعل، سيتم تحديث العروض الموجودة...');
            $this->updateExistingOffers();
            return;
        }

        $this->command->info('🌟 إنشاء العروض المميزة...');

        $featuredOffers = [
            [
                'title' => 'خصم 30% على عصائر الحمضيات',
                'description' => 'احصل على خصم هائل 30% على جميع عصائر سن توب الحمضية بما في ذلك البرتقال والليمون والجريب فروت',
                'short_description' => 'خصم 30% على جميع العصائر الحمضية',
                'code' => 'CITRUS30',
                'type' => 'percentage',
                'discount_percentage' => 30,
                'minimum_amount' => 50,
                'maximum_discount' => 100,
                'valid_from' => now(),
                'valid_until' => now()->addDays(30),
                'usage_limit' => 1000,
                'is_active' => true,
                'is_featured' => true,
                'background_color' => '#FF6B35',
                'text_color' => '#FFFFFF',
                'display_order' => 1,
                'offer_tag' => 'hot',
                'trend_score' => 85,
                'applicable_categories' => ['Citrus', 'حمضيات'],
            ],
            [
                'title' => 'عرض المانجو الاستوائي',
                'description' => 'تذوق نكهة المانجو الاستوائية الطبيعية مع خصم خاص 25% على جميع عصائر المانجو',
                'short_description' => 'خصم 25% على عصائر المانجو الاستوائية',
                'code' => 'MANGO25',
                'type' => 'percentage',
                'discount_percentage' => 25,
                'minimum_amount' => 30,
                'maximum_discount' => 75,
                'valid_from' => now(),
                'valid_until' => now()->addDays(45),
                'usage_limit' => 500,
                'is_active' => true,
                'is_featured' => true,
                'background_color' => '#FFA500',
                'text_color' => '#FFFFFF',
                'display_order' => 2,
                'offer_tag' => 'new',
                'trend_score' => 78,
                'applicable_categories' => ['Tropical', 'استوائي'],
            ],
            [
                'title' => 'شحن مجاني للطلبات +100 جنيه',
                'description' => 'احصل على توصيل مجاني تماماً لجميع الطلبات بقيمة 100 جنيه أو أكثر في جميع أنحاء الجمهورية',
                'short_description' => 'شحن مجاني للطلبات أكثر من 100 جنيه',
                'code' => 'FREESHIP100',
                'type' => 'fixed_amount',
                'min_purchase_amount' => 100,
                'valid_from' => now(),
                'valid_until' => now()->addDays(60),
                'is_active' => true,
                'is_featured' => true,
                'background_color' => '#28A745',
                'text_color' => '#FFFFFF',
                'display_order' => 3,
                'offer_tag' => 'exclusive',
                'trend_score' => 92,
            ],
            [
                'title' => 'اشتري 3 واحصل على 1 مجاناً',
                'description' => 'عرض رائع! اشتري 3 عبوات من أي نوع عصير سن توب واحصل على الرابعة مجاناً تماماً',
                'short_description' => 'اشتري 3 واحصل على 1 مجاناً',
                'code' => 'BUY3GET1',
                'type' => 'percentage',
                'discount_percentage' => 25,
                'minimum_amount' => 75,
                'valid_from' => now(),
                'valid_until' => now()->addDays(21),
                'usage_limit' => 300,
                'is_active' => true,
                'is_featured' => true,
                'background_color' => '#DC3545',
                'text_color' => '#FFFFFF',
                'display_order' => 4,
                'offer_tag' => 'limited',
                'trend_score' => 67,
            ],
            [
                'title' => 'استرداد نقدي 15%',
                'description' => 'احصل على استرداد نقدي بنسبة 15% من قيمة طلبك ليتم إضافته إلى محفظتك الرقمية',
                'short_description' => 'استرداد نقدي 15% في محفظتك',
                'code' => 'CASHBACK15',
                'type' => 'fixed_amount',
                'discount_percentage' => 15,
                'minimum_amount' => 150,
                'maximum_discount' => 200,
                'valid_from' => now(),
                'valid_until' => now()->addDays(35),
                'usage_limit' => 200,
                'is_active' => true,
                'is_featured' => true,
                'background_color' => '#6F42C1',
                'text_color' => '#FFFFFF',
                'display_order' => 5,
                'offer_tag' => 'weekend',
                'trend_score' => 71,
            ],
        ];

        foreach ($featuredOffers as $index => $offerData) {
            $offerData['used_count'] = rand(15, 85); // Random usage for demo
            Offer::create($offerData);
        }

        $this->command->info('✅ تم إنشاء ' . count($featuredOffers) . ' عرض مميز بنجاح');

        // Update trend scores
        $this->command->info('📈 تحديث نقاط الرواج...');
        $offers = Offer::where('is_featured', true)->get();
        foreach ($offers as $offer) {
            $offer->updateTrendScore();
        }

        $this->command->info('✅ تم تحديث نقاط الرواج بنجاح');
    }

    /**
     * Update existing featured offers
     */
    private function updateExistingOffers()
    {
        $this->command->info('🔄 تحديث العروض المميزة الموجودة...');

        $offerUpdates = [
            'CITRUS30' => [
                'title' => 'خصم 30% على عصائر الحمضيات',
                'valid_until' => now()->addDays(30),
                'is_active' => true,
                'is_featured' => true,
                'trend_score' => 85,
            ],
            'MANGO25' => [
                'title' => 'عرض المانجو الاستوائي',
                'valid_until' => now()->addDays(45),
                'is_active' => true,
                'is_featured' => true,
                'trend_score' => 78,
            ],
            'FREESHIP100' => [
                'title' => 'شحن مجاني للطلبات +100 جنيه',
                'valid_until' => now()->addDays(60),
                'is_active' => true,
                'is_featured' => true,
                'trend_score' => 92,
            ],
            'BUY3GET1' => [
                'title' => 'اشتري 3 واحصل على 1 مجاناً',
                'valid_until' => now()->addDays(21),
                'is_active' => true,
                'is_featured' => true,
                'trend_score' => 67,
            ],
            'CASHBACK15' => [
                'title' => 'استرداد نقدي 15%',
                'valid_until' => now()->addDays(35),
                'is_active' => true,
                'is_featured' => true,
                'trend_score' => 71,
            ],
        ];

        foreach ($offerUpdates as $code => $updateData) {
            $offer = Offer::where('code', $code)->first();
            if ($offer) {
                $offer->update($updateData);
                $this->command->info("✅ تم تحديث العرض: {$offer->title}");
            }
        }

        $this->command->info('🔄 تم تحديث جميع العروض المميزة بنجاح!');
    }
}

