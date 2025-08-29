<?php

namespace App\Http\Controllers;

use App\Services\FirebaseRealtimeService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TestFirebaseController extends Controller
{
    private $firebaseService;

    public function __construct(FirebaseRealtimeService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    /**
     * اختبار الاتصال مع Firebase
     */
    public function testConnection(): JsonResponse
    {
        $result = $this->firebaseService->testConnection();
        
        return response()->json([
            'firebase_test' => $result,
            'env_check' => [
                'firebase_url' => env('FIREBASE_DATABASE_URL'),
                'is_configured' => !empty(env('FIREBASE_DATABASE_URL'))
            ],
            'recommendations' => $this->getRecommendations($result)
        ], $result['success'] ? 200 : 500);
    }

    /**
     * اختبار شامل للشات الفوري
     */
    public function testFullChat(): JsonResponse
    {
        $results = [];
        
        // 1. اختبار الاتصال الأساسي
        $connectionTest = $this->firebaseService->testConnection();
        $results['connection_test'] = $connectionTest;
        
        if (!$connectionTest['success']) {
            return response()->json([
                'success' => false,
                'message' => 'Firebase connection failed',
                'results' => $results,
                'recommendations' => $this->getRecommendations($connectionTest)
            ], 500);
        }
        
        // 2. اختبار إنشاء شات وهمي
        $testChatData = [
            'customer_id' => 1,
            'customer_name' => 'Test Customer',
            'subject' => 'Test Chat',
            'status' => 'open',
            'priority' => 'medium',
            'created_at' => now()->toISOString(),
            'customer_unread_count' => 0,
            'admin_unread_count' => 0
        ];
        
        $chatTest = $this->firebaseService->createOrUpdateChat(999, $testChatData);
        $results['chat_creation_test'] = [
            'success' => $chatTest,
            'message' => $chatTest ? 'Chat creation successful' : 'Chat creation failed'
        ];
        
        // 3. اختبار إرسال رسالة وهمية
        $testMessageData = [
            'id' => 999,
            'sender_id' => 1,
            'sender_name' => 'Test Customer',
            'sender_type' => 'customer',
            'message' => 'Test message for Firebase real-time chat',
            'message_type' => 'text',
            'metadata' => ['test' => true]
        ];
        
        $messageTest = $this->firebaseService->sendMessage(999, $testMessageData);
        $results['message_test'] = [
            'success' => $messageTest,
            'message' => $messageTest ? 'Message sending successful' : 'Message sending failed'
        ];
        
        // 4. اختبار الإشعارات
        $notificationTest = $this->firebaseService->notifyAdmins(999, [
            'type' => 'new_message',
            'customer_name' => 'Test Customer',
            'message' => 'Test notification message'
        ]);
        
        $results['notification_test'] = [
            'success' => $notificationTest,
            'message' => $notificationTest ? 'Notification sending successful' : 'Notification sending failed'
        ];
        
        // 5. تنظيف البيانات التجريبية
        $this->firebaseService->deleteChat(999);
        
        $allPassed = $connectionTest['success'] && $chatTest && $messageTest && $notificationTest;
        
        return response()->json([
            'success' => $allPassed,
            'message' => $allPassed ? 'All Firebase chat tests passed! Real-time chat is working.' : 'Some tests failed.',
            'results' => $results,
            'firebase_url' => env('FIREBASE_DATABASE_URL'),
            'recommendations' => $allPassed ? [
                'Your real-time chat system is properly configured!',
                'Firebase Real-time Database is working correctly.',
                'Chat messages will be delivered in real-time.',
                'Admin notifications are working.',
                'You can now use the chat system in your Flutter app.'
            ] : $this->getRecommendations($results)
        ], $allPassed ? 200 : 500);
    }

    /**
     * احصل على توصيات بناءً على نتائج الاختبار
     */
    private function getRecommendations($testResult): array
    {
        $recommendations = [];
        
        if (empty(env('FIREBASE_DATABASE_URL'))) {
            $recommendations[] = 'Add FIREBASE_DATABASE_URL to your .env file';
        }
        
        if (is_array($testResult) && isset($testResult['success']) && !$testResult['success']) {
            $recommendations[] = 'Check your Firebase Database URL';
            $recommendations[] = 'Ensure Firebase Database has public read/write rules for testing';
            $recommendations[] = 'Verify your internet connection';
            $recommendations[] = 'Check Firebase project settings and database region';
        }
        
        if (empty($recommendations)) {
            $recommendations[] = 'Firebase is configured correctly!';
        }
        
        return $recommendations;
    }
}
