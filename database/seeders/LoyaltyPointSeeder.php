<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\Loyalty\Models\LoyaltyPoint;
use App\Models\User;
use App\Modules\Orders\Models\Order;
use Carbon\Carbon;

class LoyaltyPointSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        // تحقق من وجود نقاط ولاء مسبقاً
        if (LoyaltyPoint::count() > 0) {
            $this->command->info('نقاط الولاء موجودة بالفعل، تم تخطي عملية إنشاء البيانات التجريبية.');
            return;
        }

        $this->command->info('بدء إنشاء بيانات نقاط الولاء التجريبية...');

        // الحصول على المستخدمين والطلبات
        $users = User::where('role', 'customer')->limit(10)->get();
        $orders = Order::with('user')->limit(20)->get();

        if ($users->isEmpty()) {
            $this->command->warn('لا توجد مستخدمين - تم تخطي إنشاء نقاط الولاء');
            return;
        }

        $totalTransactions = 0;

        foreach ($users as $user) {
            // نقاط ترحيبية للمستخدمين الجدد
            LoyaltyPoint::create([
                'user_id' => $user->id,
                'points' => 50,
                'type' => LoyaltyPoint::TYPE_BONUS,
                'description' => 'مكافأة الانضمام لبرنامج الولاء',
                'expires_at' => now()->addYear(),
                'metadata' => [
                    'signup_bonus' => true,
                    'created_by' => 'system'
                ]
            ]);
            $totalTransactions++;

            // إنشاء نقاط كسب عشوائية (من المشتريات)
            $earnTransactions = rand(2, 8);
            for ($i = 0; $i < $earnTransactions; $i++) {
                $orderAmount = rand(50, 500);
                $points = floor($orderAmount / 10); // 1 نقطة لكل 10 ج.م
                $createdAt = now()->subDays(rand(1, 90));
                
                LoyaltyPoint::create([
                    'user_id' => $user->id,
                    'points' => $points,
                    'type' => LoyaltyPoint::TYPE_EARNED,
                    'description' => "نقاط من شراء بقيمة {$orderAmount} ج.م",
                    'order_id' => $orders->where('user_id', $user->id)->first()?->id,
                    'expires_at' => $createdAt->addYear(),
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                    'metadata' => [
                        'order_amount' => $orderAmount,
                        'earn_rate' => 10
                    ]
                ]);
                $totalTransactions++;
            }

            // نقاط استرداد عشوائية
            if (rand(1, 100) <= 60) { // 60% احتمال وجود استرداد
                $redeemTransactions = rand(1, 3);
                for ($i = 0; $i < $redeemTransactions; $i++) {
                    $redeemedPoints = rand(50, 200);
                    $createdAt = now()->subDays(rand(1, 60));
                    
                    LoyaltyPoint::create([
                        'user_id' => $user->id,
                        'points' => -$redeemedPoints,
                        'type' => LoyaltyPoint::TYPE_REDEEMED,
                        'description' => "استرداد {$redeemedPoints} نقطة كخصم",
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                        'metadata' => [
                            'discount_amount' => $redeemedPoints / 100, // 100 نقطة = 1 ج.م
                            'redeem_rate' => 100
                        ]
                    ]);
                    $totalTransactions++;
                }
            }

            // مكافآت إدارية عشوائية
            if (rand(1, 100) <= 30) { // 30% احتمال وجود مكافأة إدارية
                $bonusPoints = rand(25, 100);
                $createdAt = now()->subDays(rand(1, 30));
                
                LoyaltyPoint::create([
                    'user_id' => $user->id,
                    'points' => $bonusPoints,
                    'type' => LoyaltyPoint::TYPE_ADMIN_AWARD,
                    'description' => $this->getRandomAdminAwardReason(),
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                    'metadata' => [
                        'admin_id' => 1,
                        'admin_name' => 'مدير النظام',
                        'reason' => 'admin_bonus'
                    ]
                ]);
                $totalTransactions++;
            }

            // نقاط مكافآت التقييم
            if (rand(1, 100) <= 40) { // 40% احتمال وجود مكافآت تقييم
                $reviewCount = rand(1, 3);
                for ($i = 0; $i < $reviewCount; $i++) {
                    $createdAt = now()->subDays(rand(1, 45));
                    
                    LoyaltyPoint::create([
                        'user_id' => $user->id,
                        'points' => 10,
                        'type' => LoyaltyPoint::TYPE_BONUS,
                        'description' => 'مكافأة تقييم منتج',
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                        'metadata' => [
                            'review_bonus' => true,
                            'product_id' => rand(1, 50)
                        ]
                    ]);
                    $totalTransactions++;
                }
            }

            // نقاط منتهية الصلاحية (للاختبار)
            if (rand(1, 100) <= 20) { // 20% احتمال وجود نقاط منتهية
                $expiredPoints = rand(20, 80);
                $createdAt = now()->subMonths(rand(13, 18));
                
                LoyaltyPoint::create([
                    'user_id' => $user->id,
                    'points' => $expiredPoints,
                    'type' => LoyaltyPoint::TYPE_EARNED,
                    'description' => 'نقاط من شراء قديم',
                    'expires_at' => $createdAt->addYear(), // منتهية بالفعل
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                    'metadata' => [
                        'expired' => true,
                        'order_amount' => $expiredPoints * 10
                    ]
                ]);

                // إضافة معاملة انتهاء الصلاحية
                LoyaltyPoint::create([
                    'user_id' => $user->id,
                    'points' => -$expiredPoints,
                    'type' => LoyaltyPoint::TYPE_EXPIRED,
                    'description' => "انتهاء صلاحية {$expiredPoints} نقطة",
                    'created_at' => $createdAt->addYear(),
                    'updated_at' => $createdAt->addYear(),
                    'metadata' => [
                        'auto_expired' => true,
                        'original_points' => $expiredPoints
                    ]
                ]);
                $totalTransactions += 2;
            }
        }

        // إضافة بعض المعاملات الحديثة
        $this->createRecentTransactions($users, 15);
        $totalTransactions += 15;

        $this->command->info("تم إنشاء {$totalTransactions} معاملة نقاط ولاء بنجاح!");
        $this->command->info('المعاملات تشمل: نقاط كسب، استرداد، مكافآت إدارية، مكافآت تقييم، ونقاط منتهية');
    }

    /**
     * إنشاء معاملات حديثة
     */
    private function createRecentTransactions($users, $count)
    {
        for ($i = 0; $i < $count; $i++) {
            $user = $users->random();
            $type = $this->getRandomTransactionType();
            $createdAt = now()->subHours(rand(1, 72));

            switch ($type) {
                case LoyaltyPoint::TYPE_EARNED:
                    $orderAmount = rand(100, 800);
                    $points = floor($orderAmount / 10);
                    
                    LoyaltyPoint::create([
                        'user_id' => $user->id,
                        'points' => $points,
                        'type' => $type,
                        'description' => "نقاط من شراء بقيمة {$orderAmount} ج.م",
                        'expires_at' => $createdAt->addYear(),
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                        'metadata' => [
                            'order_amount' => $orderAmount,
                            'recent_transaction' => true
                        ]
                    ]);
                    break;

                case LoyaltyPoint::TYPE_REDEEMED:
                    $points = rand(50, 150);
                    
                    LoyaltyPoint::create([
                        'user_id' => $user->id,
                        'points' => -$points,
                        'type' => $type,
                        'description' => "استرداد {$points} نقطة كخصم",
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                        'metadata' => [
                            'discount_amount' => $points / 100,
                            'recent_transaction' => true
                        ]
                    ]);
                    break;

                case LoyaltyPoint::TYPE_ADMIN_AWARD:
                    $points = rand(25, 75);
                    
                    LoyaltyPoint::create([
                        'user_id' => $user->id,
                        'points' => $points,
                        'type' => $type,
                        'description' => $this->getRandomAdminAwardReason(),
                        'expires_at' => $createdAt->addYear(),
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                        'metadata' => [
                            'admin_id' => 1,
                            'admin_name' => 'مدير النظام',
                            'recent_transaction' => true
                        ]
                    ]);
                    break;

                case LoyaltyPoint::TYPE_BONUS:
                    LoyaltyPoint::create([
                        'user_id' => $user->id,
                        'points' => 10,
                        'type' => $type,
                        'description' => 'مكافأة تقييم منتج',
                        'expires_at' => $createdAt->addYear(),
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                        'metadata' => [
                            'review_bonus' => true,
                            'recent_transaction' => true
                        ]
                    ]);
                    break;
            }
        }
    }

    /**
     * الحصول على نوع معاملة عشوائي
     */
    private function getRandomTransactionType()
    {
        $types = [
            LoyaltyPoint::TYPE_EARNED => 50,      // 50% احتمال
            LoyaltyPoint::TYPE_REDEEMED => 25,    // 25% احتمال
            LoyaltyPoint::TYPE_ADMIN_AWARD => 15, // 15% احتمال
            LoyaltyPoint::TYPE_BONUS => 10,       // 10% احتمال
        ];

        $rand = rand(1, 100);
        $cumulative = 0;

        foreach ($types as $type => $probability) {
            $cumulative += $probability;
            if ($rand <= $cumulative) {
                return $type;
            }
        }

        return LoyaltyPoint::TYPE_EARNED;
    }

    /**
     * الحصول على سبب عشوائي للمكافأة الإدارية
     */
    private function getRandomAdminAwardReason()
    {
        $reasons = [
            'مكافأة عميل مميز',
            'تعويض عن مشكلة في الطلب',
            'مكافأة الذكرى السنوية',
            'مكافأة العميل المثالي',
            'تقدير للولاء والثقة',
            'مكافأة خاصة من الإدارة',
            'مكافأة المشاركة في الاستطلاع',
            'تعويض عن التأخير في التوصيل',
            'مكافأة إحالة صديق',
            'مكافأة تفاعل على وسائل التواصل'
        ];

        return $reasons[array_rand($reasons)];
    }
}
