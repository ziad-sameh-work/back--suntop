<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Chat;
use App\Models\ChatMessage;
use App\Models\User;
use Carbon\Carbon;

class ChatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get some users
        $customers = User::where('role', 'customer')->take(5)->get();
        $admins = User::where('role', 'admin')->get();
        
        if ($customers->isEmpty()) {
            // Create some customer users if they don't exist
            for ($i = 1; $i <= 5; $i++) {
                User::create([
                    'name' => "عميل رقم {$i}",
                    'username' => "customer{$i}",
                    'email' => "customer{$i}@example.com",
                    'phone' => "+20 100 000 000{$i}",
                    'password' => bcrypt('password'),
                    'role' => 'customer',
                    'is_active' => true,
                    'email_verified_at' => now(),
                ]);
            }
            $customers = User::where('role', 'customer')->take(5)->get();
        }

        $subjects = [
            'مشكلة في الطلب رقم #1001',
            'استفسار عن المنتجات',
            'شكوى من جودة المنتج',
            'طلب استرداد',
            'مشكلة في الدفع',
            'استفسار عن التوصيل',
            'طلب تغيير العنوان',
            'مشكلة تقنية في التطبيق',
            'استفسار عن العروض',
            'طلب دعم فني'
        ];

        $priorities = ['low', 'medium', 'high', 'urgent'];
        $statuses = ['open', 'in_progress', 'resolved', 'closed'];

        $messages = [
            // Customer messages
            'مرحباً، أحتاج مساعدة في طلبي',
            'لم أستلم الطلب حتى الآن',
            'المنتج وصل معطوب',
            'أريد إلغاء الطلب',
            'كيف يمكنني تتبع الطلب؟',
            'هل يمكن تغيير عنوان التوصيل؟',
            'المنتج مختلف عن الصورة',
            'أريد استرداد أموالي',
            'التطبيق لا يعمل بشكل صحيح',
            'ما هي العروض المتاحة؟',
            
            // Admin messages
            'مرحباً، كيف يمكنني مساعدتك؟',
            'سأقوم بمراجعة طلبك الآن',
            'تم إرسال الطلب وسيصل خلال يومين',
            'نعتذر عن هذه المشكلة، سنحلها فوراً',
            'تم إلغاء الطلب واسترداد المبلغ',
            'يمكنك تتبع الطلب من خلال الرابط المرسل',
            'تم تحديث عنوان التوصيل',
            'سنقوم باستبدال المنتج',
            'فريق الدعم التقني سيتواصل معك',
            'إليك قائمة بأفضل العروض المتاحة'
        ];

        // Create chats
        foreach ($customers as $index => $customer) {
            for ($i = 0; $i < rand(1, 3); $i++) {
                $chat = Chat::create([
                    'customer_id' => $customer->id,
                    'subject' => $subjects[array_rand($subjects)],
                    'status' => $statuses[array_rand($statuses)],
                    'priority' => $priorities[array_rand($priorities)],
                    'assigned_admin_id' => rand(0, 1) ? $admins->random()->id ?? null : null,
                    'last_message_at' => Carbon::now()->subHours(rand(1, 48)),
                    'customer_unread_count' => rand(0, 3),
                    'admin_unread_count' => rand(0, 5),
                    'metadata' => [
                        'created_from' => 'seeder',
                        'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                        'ip_address' => '192.168.1.' . rand(1, 254)
                    ],
                    'created_at' => Carbon::now()->subDays(rand(1, 30)),
                ]);

                // Create messages for each chat
                $messageCount = rand(2, 8);
                for ($j = 0; $j < $messageCount; $j++) {
                    $isFromCustomer = $j % 2 == 0; // Alternate between customer and admin
                    $senderId = $isFromCustomer ? $customer->id : ($admins->isNotEmpty() ? $admins->random()->id : $customer->id);
                    $senderType = $isFromCustomer ? 'customer' : 'admin';
                    
                    ChatMessage::create([
                        'chat_id' => $chat->id,
                        'sender_id' => $senderId,
                        'sender_type' => $senderType,
                        'message' => $messages[array_rand($messages)],
                        'message_type' => 'text',
                        'is_read' => rand(0, 1),
                        'read_at' => rand(0, 1) ? Carbon::now()->subHours(rand(1, 24)) : null,
                        'metadata' => [
                            'sent_from' => 'seeder',
                            'ip_address' => '192.168.1.' . rand(1, 254)
                        ],
                        'created_at' => $chat->created_at->addMinutes($j * rand(10, 120)),
                    ]);
                }

                // Update chat's last message time to match the last message
                $chat->update([
                    'last_message_at' => $chat->messages()->latest()->first()->created_at ?? $chat->created_at
                ]);
            }
        }

        $this->command->info('تم إنشاء ' . Chat::count() . ' محادثة و ' . ChatMessage::count() . ' رسالة بنجاح!');
    }
}
