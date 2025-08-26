<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

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
            ProductSeeder::class,
            OfferSeeder::class,
            LoyaltyPointSeeder::class,
            OrderSeeder::class,
            ChatSeeder::class,
        ]);

        $this->command->info('');
        $this->command->info('🎉 تم تشغيل جميع الـ Seeders بنجاح!');
        $this->command->info('');
        $this->command->info('📊 ملخص البيانات المضافة:');
        $this->command->info('🏷️ فئات المستخدمين: 4 فئات');
        $this->command->info('👥 المستخدمين: 10 مستخدم');
        $this->command->info('🏪 التجار: 10 تجار');
        $this->command->info('📦 المنتجات: 8 منتجات');
        $this->command->info('🎁 العروض: 10 عروض');
        $this->command->info('⭐ نقاط الولاء: معاملات متنوعة');
        $this->command->info('🛒 الطلبات: 60 طلب');
        $this->command->info('');
        $this->command->info('🔐 بيانات التيست السريع:');
        $this->command->info('Username: testuser | Password: password123');
        $this->command->info('Admin: admin | Password: admin123');
        $this->command->info('Merchant: merchant1 | Password: merchant123');
        $this->command->info('');
        $this->command->info('🚀 المشروع جاهز للتيست!');
    }
}
