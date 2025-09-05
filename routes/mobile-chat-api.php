<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MobileChatController;

/*
|--------------------------------------------------------------------------
| Mobile Chat API Routes - Real-time Chat for Mobile Apps
|--------------------------------------------------------------------------
|
| هذه الـ routes مخصصة للموبايل مع دعم Real-time كامل
| 
*/

Route::middleware('auth:sanctum')->prefix('mobile-chat')->group(function () {
    
    // 1. بدء/الحصول على محادثة جديدة
    Route::get('/start', [MobileChatController::class, 'startChat']);
    
    // 2. إرسال رسالة جديدة مع Real-time
    Route::post('/send', [MobileChatController::class, 'sendMessage']);
    
    // 3. الحصول على الرسائل
    Route::get('/{chatId}/messages', [MobileChatController::class, 'getMessages']);
    
    // 4. تاريخ المحادثات
    Route::get('/history', [MobileChatController::class, 'getChatHistory']);
    
    // 5. تعليم الرسائل كمقروءة
    Route::post('/{chatId}/read', [MobileChatController::class, 'markAsRead']);
    
    // 6. معلومات Pusher للاتصال
    Route::get('/pusher-config', [MobileChatController::class, 'getPusherConfig']);
    
    // 7. اختبار الـ Real-time
    Route::post('/test-broadcast/{chatId}', [MobileChatController::class, 'testBroadcast']);
});

// Routes بدون Authentication للاختبار
Route::prefix('test-mobile-chat')->group(function () {
    Route::get('/pusher-config', [MobileChatController::class, 'getPusherConfig']);
    Route::post('/test-send/{chatId}', [MobileChatController::class, 'testSendMessage']);
});
