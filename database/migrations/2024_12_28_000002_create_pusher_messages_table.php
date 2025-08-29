<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pusher_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_id')->constrained('pusher_chats')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->text('message');
            $table->enum('sender_type', ['customer', 'admin'])->default('customer');
            $table->boolean('is_read')->default(false);
            $table->json('metadata')->nullable(); // For attachments, message type, etc.
            $table->timestamps();
            
            $table->index(['chat_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index(['sender_type', 'is_read']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pusher_messages');
    }
};
