<?php

use Illuminate\Support\Facades\Route;
use App\Models\Chat;
use App\Models\ChatMessage;
use App\Events\NewChatMessage;

/*
|--------------------------------------------------------------------------
| Test Real-time Routes - للاختبار المباشر
|--------------------------------------------------------------------------
*/

// اختبار سريع بدون أي معقدات
Route::get('/test-realtime-simple/{chatId?}', function($chatId = 1) {
    try {
        echo "<h1>🔥 اختبار Real-time البسيط</h1>\n";
        echo "<pre>\n";
        
        echo "1️⃣ البحث عن محادثة...\n";
        $chat = Chat::find($chatId);
        
        if (!$chat) {
            echo "❌ المحادثة غير موجودة، سأنشئ واحدة جديدة...\n";
            $chat = Chat::create([
                'customer_id' => 1,
                'subject' => 'اختبار Real-time',
                'status' => 'open',
                'priority' => 'medium'
            ]);
            echo "✅ تم إنشاء محادثة جديدة ID: {$chat->id}\n";
        } else {
            echo "✅ تم العثور على المحادثة ID: {$chat->id}\n";
        }
        
        echo "\n2️⃣ إنشاء رسالة اختبار...\n";
        $message = ChatMessage::create([
            'chat_id' => $chat->id,
            'sender_id' => 1,
            'sender_type' => 'admin',
            'message' => '🔥 رسالة اختبار Real-time - ' . now()->format('H:i:s'),
            'message_type' => 'text',
            'metadata' => [
                'test' => true,
                'timestamp' => now()->toISOString()
            ]
        ]);
        
        $message->load('sender');
        echo "✅ تم إنشاء الرسالة ID: {$message->id}\n";
        
        echo "\n3️⃣ إرسال الحدث الفوري...\n";
        
        // إرسال الحدث
        event(new NewChatMessage($message));
        
        echo "✅ تم إرسال الحدث بنجاح!\n";
        echo "\n📡 الحدث تم بثه على القنوات:\n";
        echo "   - chat.{$chat->id}\n";
        echo "   - mobile-chat.{$chat->id}\n";
        echo "   - private-admin.chats\n";
        echo "   - admin-chats-public\n";
        
        echo "\n🔗 للاختبار في المتصفح:\n";
        echo "   افتح: /test-mobile-chat.html\n";
        echo "   ثم أدخل Chat ID: {$chat->id}\n";
        echo "   واضغط 'تهيئة Pusher' ثم 'الاشتراك في القناة'\n";
        echo "   ثم اضغط F5 على هذه الصفحة لإرسال رسالة جديدة\n";
        
        echo "\n📱 للاختبار في Flutter:\n";
        echo "   Channel: mobile-chat.{$chat->id}\n";
        echo "   Event: message.new\n";
        echo "   Pusher Key: f546bf192457a6d47ed5\n";
        echo "   Cluster: eu\n";
        
        echo "\n✅ الاختبار مكتمل!\n";
        echo "</pre>\n";
        
        return response('<script>setTimeout(() => location.reload(), 5000);</script>', 200, ['Content-Type' => 'text/html']);
        
    } catch (Exception $e) {
        echo "<h1>❌ خطأ في الاختبار</h1>\n";
        echo "<pre>Error: " . $e->getMessage() . "\n";
        echo "Trace: " . $e->getTraceAsString() . "</pre>\n";
        return response('', 500);
    }
});

// إرسال رسالة بسيط جداً
Route::post('/send-test-message/{chatId}', function($chatId) {
    try {
        $chat = Chat::findOrFail($chatId);
        
        $message = ChatMessage::create([
            'chat_id' => $chat->id,
            'sender_id' => 1,
            'sender_type' => 'admin',
            'message' => request('message', 'رسالة اختبار - ' . now()->format('H:i:s')),
            'message_type' => 'text'
        ]);
        
        $message->load('sender');
        
        // إرسال الحدث الفوري
        event(new NewChatMessage($message));
        
        return response()->json([
            'success' => true,
            'message' => 'تم إرسال الرسالة والحدث الفوري',
            'data' => [
                'message_id' => $message->id,
                'chat_id' => $chat->id,
                'channels' => [
                    'chat.' . $chat->id,
                    'mobile-chat.' . $chat->id,
                    'private-admin.chats',
                    'admin-chats-public'
                ]
            ]
        ]);
        
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
});

// فحص إعدادات Pusher بسيط
Route::get('/check-pusher-simple', function() {
    try {
        $config = config('broadcasting.connections.pusher');
        
        $pusher = new \Pusher\Pusher(
            $config['key'],
            $config['secret'], 
            $config['app_id'],
            $config['options'] ?? []
        );
        
        // اختبار إرسال حدث
        $result = $pusher->trigger('test-channel', 'test-event', [
            'message' => 'اختبار',
            'time' => now()->toISOString()
        ]);
        
        return response()->json([
            'success' => true,
            'pusher_config' => [
                'key' => $config['key'],
                'cluster' => $config['options']['cluster'] ?? 'unknown',
                'app_id' => $config['app_id']
            ],
            'test_result' => $result ? 'نجح' : 'فشل',
            'message' => 'إعدادات Pusher تعمل بشكل صحيح'
        ]);
        
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'message' => 'خطأ في إعدادات Pusher'
        ], 500);
    }
});
