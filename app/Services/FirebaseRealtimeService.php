<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FirebaseRealtimeService
{
    private $databaseUrl;
    private $timeout;

    public function __construct()
    {
        $this->databaseUrl = env('FIREBASE_DATABASE_URL', 'https://suntop-609f9-default-rtdb.europe-west1.firebasedatabase.app');
        $this->timeout = 30;
        
        // التحقق من تكوين Firebase
        if (empty($this->databaseUrl) || $this->databaseUrl === 'https://suntop-609f9-default-rtdb.europe-west1.firebasedatabase.app') {
            Log::warning('Firebase Database URL not properly configured in .env file');
        }
    }

    /**
     * إرسال رسالة جديدة إلى Firebase
     */
    public function sendMessage(int $chatId, array $messageData): bool
    {
        try {
            $timestamp = now()->timestamp;
            $messageKey = $timestamp . '_' . uniqid();
            
            $firebaseData = [
                'id' => $messageData['id'],
                'chat_id' => $chatId,
                'sender_id' => $messageData['sender_id'],
                'sender_name' => $messageData['sender_name'],
                'sender_type' => $messageData['sender_type'],
                'message' => $messageData['message'],
                'message_type' => $messageData['message_type'] ?? 'text',
                'attachment_url' => $messageData['attachment_url'] ?? null,
                'attachment_name' => $messageData['attachment_name'] ?? null,
                'timestamp' => $timestamp,
                'created_at' => now()->toISOString(),
                'is_read' => false,
                'metadata' => $messageData['metadata'] ?? []
            ];

            $response = Http::timeout($this->timeout)
                ->put("{$this->databaseUrl}/chats/{$chatId}/messages/{$messageKey}.json", $firebaseData);

            if ($response->successful()) {
                // تحديث آخر رسالة في معلومات الشات
                $this->updateChatLastMessage($chatId, $firebaseData);
                return true;
            }

            Log::error('Firebase send message failed', [
                'chat_id' => $chatId,
                'response' => $response->body(),
                'status' => $response->status()
            ]);

            return false;

        } catch (Exception $e) {
            Log::error('Firebase send message error: ' . $e->getMessage(), [
                'chat_id' => $chatId,
                'message_data' => $messageData
            ]);
            return false;
        }
    }

    /**
     * إنشاء أو تحديث معلومات الشات في Firebase
     */
    public function createOrUpdateChat(int $chatId, array $chatData): bool
    {
        try {
            $firebaseData = [
                'id' => $chatId,
                'customer_id' => $chatData['customer_id'],
                'customer_name' => $chatData['customer_name'],
                'subject' => $chatData['subject'],
                'status' => $chatData['status'],
                'priority' => $chatData['priority'],
                'assigned_admin_id' => $chatData['assigned_admin_id'] ?? null,
                'assigned_admin_name' => $chatData['assigned_admin_name'] ?? null,
                'created_at' => $chatData['created_at'],
                'updated_at' => now()->toISOString(),
                'customer_unread_count' => $chatData['customer_unread_count'] ?? 0,
                'admin_unread_count' => $chatData['admin_unread_count'] ?? 0,
                'last_message' => $chatData['last_message'] ?? null,
                'last_message_at' => $chatData['last_message_at'] ?? null
            ];

            $response = Http::timeout($this->timeout)
                ->put("{$this->databaseUrl}/chats/{$chatId}/info.json", $firebaseData);

            return $response->successful();

        } catch (Exception $e) {
            Log::error('Firebase create/update chat error: ' . $e->getMessage(), [
                'chat_id' => $chatId,
                'chat_data' => $chatData
            ]);
            return false;
        }
    }

    /**
     * تحديث آخر رسالة في معلومات الشات
     */
    private function updateChatLastMessage(int $chatId, array $messageData): bool
    {
        try {
            $lastMessageData = [
                'message' => $messageData['message'],
                'sender_name' => $messageData['sender_name'],
                'sender_type' => $messageData['sender_type'],
                'timestamp' => $messageData['timestamp'],
                'created_at' => $messageData['created_at']
            ];

            $response = Http::timeout($this->timeout)
                ->patch("{$this->databaseUrl}/chats/{$chatId}/info.json", [
                    'last_message' => $lastMessageData,
                    'last_message_at' => $messageData['created_at'],
                    'updated_at' => now()->toISOString()
                ]);

            return $response->successful();

        } catch (Exception $e) {
            Log::error('Firebase update last message error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * تحديث عدد الرسائل غير المقروءة
     */
    public function updateUnreadCount(int $chatId, string $userType, int $count): bool
    {
        try {
            $field = $userType === 'admin' ? 'admin_unread_count' : 'customer_unread_count';
            
            $response = Http::timeout($this->timeout)
                ->patch("{$this->databaseUrl}/chats/{$chatId}/info.json", [
                    $field => $count,
                    'updated_at' => now()->toISOString()
                ]);

            return $response->successful();

        } catch (Exception $e) {
            Log::error('Firebase update unread count error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * تحديث حالة الشات
     */
    public function updateChatStatus(int $chatId, string $status, ?int $assignedAdminId = null, ?string $assignedAdminName = null): bool
    {
        try {
            $updateData = [
                'status' => $status,
                'updated_at' => now()->toISOString()
            ];

            if ($assignedAdminId !== null) {
                $updateData['assigned_admin_id'] = $assignedAdminId;
                $updateData['assigned_admin_name'] = $assignedAdminName;
            }

            $response = Http::timeout($this->timeout)
                ->patch("{$this->databaseUrl}/chats/{$chatId}/info.json", $updateData);

            return $response->successful();

        } catch (Exception $e) {
            Log::error('Firebase update chat status error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * تمييز الرسائل كمقروءة
     */
    public function markMessagesAsRead(int $chatId, string $userType): bool
    {
        try {
            // تحديث عدد الرسائل غير المقروءة إلى صفر
            $this->updateUnreadCount($chatId, $userType, 0);

            // يمكن إضافة المزيد من المنطق هنا إذا لزم الأمر
            return true;

        } catch (Exception $e) {
            Log::error('Firebase mark messages as read error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * حذف الشات من Firebase
     */
    public function deleteChat(int $chatId): bool
    {
        try {
            $response = Http::timeout($this->timeout)
                ->delete("{$this->databaseUrl}/chats/{$chatId}.json");

            return $response->successful();

        } catch (Exception $e) {
            Log::error('Firebase delete chat error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * إرسال إشعار Real-time للإدارة عن شات جديد أو رسالة جديدة
     */
    public function notifyAdmins(int $chatId, array $notificationData): bool
    {
        try {
            $timestamp = now()->timestamp;
            $notificationKey = $timestamp . '_' . uniqid();

            $firebaseData = [
                'type' => $notificationData['type'], // 'new_chat', 'new_message', 'status_change'
                'chat_id' => $chatId,
                'customer_name' => $notificationData['customer_name'],
                'message' => $notificationData['message'],
                'timestamp' => $timestamp,
                'created_at' => now()->toISOString(),
                'is_read' => false
            ];

            $response = Http::timeout($this->timeout)
                ->put("{$this->databaseUrl}/admin_notifications/{$notificationKey}.json", $firebaseData);

            return $response->successful();

        } catch (Exception $e) {
            Log::error('Firebase notify admins error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * إرسال إشعار Real-time للعميل
     */
    public function notifyCustomer(int $customerId, array $notificationData): bool
    {
        try {
            $timestamp = now()->timestamp;
            $notificationKey = $timestamp . '_' . uniqid();

            $firebaseData = [
                'type' => $notificationData['type'], // 'admin_reply', 'status_change', 'chat_assigned'
                'chat_id' => $notificationData['chat_id'],
                'admin_name' => $notificationData['admin_name'] ?? 'فريق الدعم',
                'message' => $notificationData['message'],
                'timestamp' => $timestamp,
                'created_at' => now()->toISOString(),
                'is_read' => false
            ];

            $response = Http::timeout($this->timeout)
                ->put("{$this->databaseUrl}/customer_notifications/{$customerId}/{$notificationKey}.json", $firebaseData);

            return $response->successful();

        } catch (Exception $e) {
            Log::error('Firebase notify customer error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * اختبار الاتصال مع Firebase
     */
    public function testConnection(): array
    {
        try {
            // إنشاء اختبار بسيط
            $testData = [
                'test' => true,
                'timestamp' => now()->timestamp,
                'message' => 'Test connection from Laravel',
                'created_at' => now()->toISOString()
            ];

            $response = Http::timeout($this->timeout)
                ->put("{$this->databaseUrl}/test_connection.json", $testData);

            if ($response->successful()) {
                // محاولة قراءة البيانات للتأكد
                $readResponse = Http::timeout($this->timeout)
                    ->get("{$this->databaseUrl}/test_connection.json");

                if ($readResponse->successful()) {
                    $responseData = $readResponse->json();
                    
                    // حذف بيانات الاختبار
                    Http::timeout($this->timeout)
                        ->delete("{$this->databaseUrl}/test_connection.json");

                    return [
                        'success' => true,
                        'message' => 'Firebase connection successful',
                        'data' => [
                            'database_url' => $this->databaseUrl,
                            'response_data' => $responseData,
                            'write_success' => true,
                            'read_success' => true,
                            'delete_success' => true
                        ]
                    ];
                } else {
                    return [
                        'success' => false,
                        'message' => 'Failed to read from Firebase',
                        'data' => [
                            'database_url' => $this->databaseUrl,
                            'write_success' => true,
                            'read_success' => false,
                            'error' => $readResponse->body()
                        ]
                    ];
                }
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to write to Firebase',
                    'data' => [
                        'database_url' => $this->databaseUrl,
                        'write_success' => false,
                        'status_code' => $response->status(),
                        'error' => $response->body()
                    ]
                ];
            }

        } catch (Exception $e) {
            Log::error('Firebase connection test error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Firebase connection test failed: ' . $e->getMessage(),
                'data' => [
                    'database_url' => $this->databaseUrl,
                    'exception' => $e->getMessage()
                ]
            ];
        }
    }

    /**
     * الحصول على معلومات الشات من Firebase
     */
    public function getChat(int $chatId): ?array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->get("{$this->databaseUrl}/chats/{$chatId}.json");

            if ($response->successful()) {
                return $response->json();
            }

            return null;

        } catch (Exception $e) {
            Log::error('Firebase get chat error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * إرسال مؤشر كتابة من الأدمن
     */
    public function sendAdminTypingIndicator(int $chatId, int $adminId, string $adminName, bool $isTyping): bool
    {
        try {
            $typingData = [
                'admin_id' => $adminId,
                'admin_name' => $adminName,
                'is_typing' => $isTyping,
                'timestamp' => now()->timestamp,
                'created_at' => now()->toISOString()
            ];

            $response = Http::timeout($this->timeout)
                ->put("{$this->databaseUrl}/chats/{$chatId}/admin_typing.json", $typingData);

            return $response->successful();

        } catch (Exception $e) {
            Log::error('Firebase admin typing indicator error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * التحقق من اتصال الأدمن
     */
    public function isAdminOnline(int $adminId): bool
    {
        try {
            $response = Http::timeout($this->timeout)
                ->get("{$this->databaseUrl}/admin_presence/{$adminId}.json");

            if ($response->successful()) {
                $presenceData = $response->json();
                if ($presenceData && isset($presenceData['lastSeen'])) {
                    $lastSeen = $presenceData['lastSeen'];
                    $currentTime = now()->timestamp * 1000; // تحويل إلى milliseconds
                    $timeDiff = $currentTime - $lastSeen;
                    
                    // اعتبار الأدمن متصل إذا كان آخر نشاط خلال 5 دقائق
                    return $timeDiff < (5 * 60 * 1000);
                }
            }

            return false;

        } catch (Exception $e) {
            Log::error('Firebase check admin online error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * تسجيل إداري كمتصل
     */
    public function registerAdminPresence(int $adminId, string $adminName): bool
    {
        try {
            $presenceData = [
                'name' => $adminName,
                'timestamp' => now()->timestamp,
                'status' => 'online',
                'last_seen' => now()->toISOString()
            ];

            $response = Http::timeout($this->timeout)
                ->put("{$this->databaseUrl}/admin_presence/{$adminId}.json", $presenceData);

            return $response->successful();

        } catch (Exception $e) {
            Log::error('Firebase register admin presence error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * إزالة إداري من قائمة المتصلين
     */
    public function removeAdminPresence(int $adminId): bool
    {
        try {
            $response = Http::timeout($this->timeout)
                ->delete("{$this->databaseUrl}/admin_presence/{$adminId}.json");

            return $response->successful();

        } catch (Exception $e) {
            Log::error('Firebase remove admin presence error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * إرسال typing indicator
     */
    public function sendTypingIndicator(int $chatId, string $userType, string $userName, bool $isTyping): bool
    {
        try {
            $typingData = $isTyping ? [
                'user_type' => $userType,
                'user_name' => $userName,
                'timestamp' => now()->timestamp
            ] : null;

            $response = Http::timeout($this->timeout)
                ->put("{$this->databaseUrl}/chats/{$chatId}/typing.json", $typingData);

            return $response->successful();

        } catch (Exception $e) {
            Log::error('Firebase typing indicator error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * تحديث إحصائيات المحادثات في الوقت الفعلي
     */
    public function updateChatStats(array $stats): bool
    {
        try {
            $statsData = array_merge($stats, [
                'last_updated' => now()->toISOString(),
                'timestamp' => now()->timestamp
            ]);

            $response = Http::timeout($this->timeout)
                ->put("{$this->databaseUrl}/chat_stats.json", $statsData);

            return $response->successful();

        } catch (Exception $e) {
            Log::error('Firebase update chat stats error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * إرسال إشعار فوري للإدارة عن محادثة جديدة
     */
    public function sendNewChatAlert(int $chatId, array $chatData): bool
    {
        try {
            $alertKey = now()->timestamp . '_' . uniqid();
            
            $alertData = [
                'type' => 'new_chat_alert',
                'chat_id' => $chatId,
                'customer_name' => $chatData['customer_name'],
                'subject' => $chatData['subject'],
                'priority' => $chatData['priority'],
                'message' => "شات جديد من {$chatData['customer_name']}: {$chatData['subject']}",
                'timestamp' => now()->timestamp,
                'created_at' => now()->toISOString(),
                'requires_attention' => true
            ];

            $response = Http::timeout($this->timeout)
                ->put("{$this->databaseUrl}/live_alerts/{$alertKey}.json", $alertData);

            return $response->successful();

        } catch (Exception $e) {
            Log::error('Firebase new chat alert error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * إرسال إشعار فوري برسالة عاجلة
     */
    public function sendUrgentMessageAlert(int $chatId, array $messageData): bool
    {
        try {
            $alertKey = now()->timestamp . '_' . uniqid();
            
            $alertData = [
                'type' => 'urgent_message',
                'chat_id' => $chatId,
                'sender_name' => $messageData['sender_name'],
                'message_preview' => substr($messageData['message'], 0, 100),
                'message' => "رسالة عاجلة من {$messageData['sender_name']}",
                'timestamp' => now()->timestamp,
                'created_at' => now()->toISOString(),
                'requires_attention' => true
            ];

            $response = Http::timeout($this->timeout)
                ->put("{$this->databaseUrl}/live_alerts/{$alertKey}.json", $alertData);

            return $response->successful();

        } catch (Exception $e) {
            Log::error('Firebase urgent message alert error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * تحديث آخر نشاط لإداري
     */
    public function updateAdminActivity(int $adminId, string $activity): bool
    {
        try {
            $activityData = [
                'activity' => $activity,
                'timestamp' => now()->timestamp,
                'last_seen' => now()->toISOString()
            ];

            $response = Http::timeout($this->timeout)
                ->patch("{$this->databaseUrl}/admin_presence/{$adminId}.json", $activityData);

            return $response->successful();

        } catch (Exception $e) {
            Log::error('Firebase update admin activity error: ' . $e->getMessage());
            return false;
        }
    }

}
