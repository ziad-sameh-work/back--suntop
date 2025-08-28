<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('🚀 بدء تشغيل جميع الـ Seeders...');
        $this->command->info('');

        // تشغيل جميع الـ Seeders بالترتيب
        $this->call([
            UserCategorySeeder::class,
            UserSeeder::class,
            MerchantSeeder::class,
            // إضافة فئات المنتجات أولاً
            ProductCategorySeeder::class,
            ProductSeeder::class,
            OfferSeeder::class,
            EnhancedOfferSeeder::class,
            FeaturedOffersSeeder::class,
            RewardTierSeeder::class,
            RewardSeeder::class,
            LoyaltyPointSeeder::class,
            OrderSeeder::class,
            ChatSeeder::class,
            NotificationSeeder::class,
            FavoriteSeeder::class,
        ]);

        $this->command->info('');
        $this->command->info('🎉 تم تشغيل جميع الـ Seeders بنجاح!');
        $this->command->info('');
        $this->command->info('📊 ملخص البيانات المضافة:');
        $this->command->info('🏷️ فئات المستخدمين: 4 فئات');
        $this->command->info('👥 المستخدمين: 10 مستخدم');
        $this->command->info('🏪 التجار: 10 تجار');
        $this->command->info('🍹 فئات المنتجات: 2 فئات (1 لتر و 250 مل)');
        $this->command->info('📦 المنتجات: 8 منتجات');
        $this->command->info('🎁 العروض: 25+ عرض (أساسية + محسّنة + مميزة)');
        $this->command->info('🏆 مستويات المكافآت: 4 مستويات (برونزي، فضي، ذهبي، بلاتيني)');
        $this->command->info('🎖️ المكافآت: 10 مكافآت متنوعة');
        $this->command->info('⭐ نقاط الولاء: معاملات متنوعة');
        $this->command->info('🛒 الطلبات: 60 طلب');
        $this->command->info('🔔 الإشعارات: إشعارات متنوعة');
        $this->command->info('❤️ المفضلة: مفضلة متنوعة للعملاء');
        $this->command->info('');
        $this->command->info('🔐 بيانات التيست السريع:');
        $this->command->info('Username: testuser | Password: password123');
        $this->command->info('Admin: admin | Password: admin123');
        $this->command->info('Merchant: merchant1 | Password: merchant123');
        $this->command->info('');
        $this->command->info('🚀 المشروع جاهز للتيست!');
    }
}
