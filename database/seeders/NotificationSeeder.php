<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Notification;
use App\Models\User;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeder.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('🔔 إنشاء إشعارات تجريبية...');

        // Get some users to create notifications for
        $users = User::where('role', 'customer')->take(5)->get();

        if ($users->isEmpty()) {
            $this->command->warn('⚠️ لا توجد مستخدمين لإنشاء إشعارات لهم');
            return;
        }

        $notificationsData = [];
        $now = now();

        foreach ($users as $user) {
            // Order status notifications
            $notificationsData[] = [
                'user_id' => $user->id,
                'title' => 'تم شحن طلبيتك',
                'message' => 'تم شحن طلبية منتجات سن توب الخاصة بك وستصل خلال يومين',
                'type' => Notification::TYPE_SHIPMENT,
                'priority' => Notification::PRIORITY_HIGH,
                'data' => json_encode([
                    'order_number' => 'ORD-2024-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT),
                    'tracking_number' => 'TRK' . strtoupper(uniqid()),
                ]),
                'action_url' => '/orders',
                'created_at' => $now->copy()->subHours(rand(1, 48)),
                'updated_at' => $now->copy()->subHours(rand(1, 48)),
            ];

            $notificationsData[] = [
                'user_id' => $user->id,
                'title' => 'تم تأكيد طلبيتك',
                'message' => 'تم تأكيد طلبك بنجاح وجاري التحضير',
                'type' => Notification::TYPE_ORDER_STATUS,
                'priority' => Notification::PRIORITY_MEDIUM,
                'data' => json_encode([
                    'order_number' => 'ORD-2024-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT),
                    'status' => 'confirmed',
                ]),
                'action_url' => '/orders',
                'read_at' => rand(0, 1) ? $now->copy()->subHours(rand(1, 24)) : null,
                'created_at' => $now->copy()->subDays(rand(1, 7)),
                'updated_at' => $now->copy()->subDays(rand(1, 7)),
            ];

            // Loyalty points notifications
            $notificationsData[] = [
                'user_id' => $user->id,
                'title' => 'تم إضافة نقاط ولاء',
                'message' => 'تم إضافة 25 نقطة ولاء إلى حسابك من شراء 5 كراتين',
                'type' => Notification::TYPE_REWARD,
                'priority' => Notification::PRIORITY_MEDIUM,
                'data' => json_encode([
                    'points' => 25,
                    'reason' => 'شراء 5 كراتين',
                    'type' => 'earned',
                ]),
                'action_url' => '/loyalty',
                'read_at' => rand(0, 1) ? $now->copy()->subHours(rand(1, 24)) : null,
                'created_at' => $now->copy()->subDays(rand(1, 5)),
                'updated_at' => $now->copy()->subDays(rand(1, 5)),
            ];

            // Offer notifications
            $notificationsData[] = [
                'user_id' => $user->id,
                'title' => 'عرض خاص: خصم 30%',
                'message' => 'احصل على خصم 30% على جميع منتجات سن توب لفترة محدودة!',
                'type' => Notification::TYPE_OFFER,
                'priority' => Notification::PRIORITY_HIGH,
                'data' => json_encode([
                    'offer_code' => 'SUMMER30',
                    'discount_percentage' => 30,
                    'valid_until' => $now->copy()->addDays(7)->toDateString(),
                ]),
                'action_url' => '/offers',
                'created_at' => $now->copy()->subHours(rand(1, 12)),
                'updated_at' => $now->copy()->subHours(rand(1, 12)),
            ];

            // General notifications
            $notificationsData[] = [
                'user_id' => $user->id,
                'title' => 'منتجات جديدة متاحة الآن',
                'message' => 'اكتشف مجموعة جديدة من عصائر سن توب الطبيعية',
                'type' => Notification::TYPE_GENERAL,
                'priority' => Notification::PRIORITY_LOW,
                'data' => json_encode([
                    'category' => 'منتجات جديدة',
                ]),
                'action_url' => '/products',
                'read_at' => rand(0, 1) ? $now->copy()->subHours(rand(1, 24)) : null,
                'created_at' => $now->copy()->subDays(rand(2, 10)),
                'updated_at' => $now->copy()->subDays(rand(2, 10)),
            ];

            // Payment notifications
            $notificationsData[] = [
                'user_id' => $user->id,
                'title' => 'تم استلام الدفعة',
                'message' => 'تم استلام دفعة طلبك بنجاح بقيمة 150.75 جنيه',
                'type' => Notification::TYPE_PAYMENT,
                'priority' => Notification::PRIORITY_MEDIUM,
                'data' => json_encode([
                    'amount' => 150.75,
                    'currency' => 'EGP',
                    'payment_method' => 'cash_on_delivery',
                ]),
                'action_url' => '/orders',
                'read_at' => $now->copy()->subHours(rand(1, 24)),
                'created_at' => $now->copy()->subDays(rand(1, 3)),
                'updated_at' => $now->copy()->subDays(rand(1, 3)),
            ];
        }

        // Insert all notifications
        Notification::insert($notificationsData);

        $totalNotifications = count($notificationsData);
        $this->command->info("✅ تم إنشاء {$totalNotifications} إشعار تجريبي بنجاح");
        
        // Show statistics
        $unreadCount = Notification::whereNull('read_at')->count();
        $byType = Notification::selectRaw('type, count(*) as count')
                             ->groupBy('type')
                             ->pluck('count', 'type')
                             ->toArray();

        $this->command->info("📊 الإحصائيات:");
        $this->command->info("   - إجمالي الإشعارات: {$totalNotifications}");
        $this->command->info("   - غير مقروءة: {$unreadCount}");
        
        foreach ($byType as $type => $count) {
            $typeName = Notification::TYPES[$type] ?? $type;
            $this->command->info("   - {$typeName}: {$count}");
        }
    }
}
