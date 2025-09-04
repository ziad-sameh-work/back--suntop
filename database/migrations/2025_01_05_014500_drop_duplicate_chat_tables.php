<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Drop duplicate/unused chat-related tables
        // Keep only 'chats' and 'chat_messages' tables
        
        // Must drop pusher_messages first due to foreign key constraint
        Schema::dropIfExists('pusher_messages');
        Schema::dropIfExists('pusher_chats');
        
        // Note: We're keeping 'chats' and 'chat_messages' as they are the main tables
        // All real-time functionality will use these tables with Pusher events
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Recreate pusher_chats table if needed to rollback
        Schema::create('pusher_chats', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('subject');
            $table->enum('status', ['open', 'in_progress', 'resolved', 'closed'])->default('open');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->unsignedBigInteger('assigned_admin_id')->nullable();
            $table->integer('admin_unread_count')->default(0);
            $table->integer('customer_unread_count')->default(0);
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('assigned_admin_id')->references('id')->on('users')->onDelete('set null');
        });

        // Recreate pusher_messages table if needed to rollback
        Schema::create('pusher_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('chat_id');
            $table->unsignedBigInteger('sender_id');
            $table->enum('sender_type', ['customer', 'admin']);
            $table->text('message')->nullable();
            $table->enum('message_type', ['text', 'image', 'file'])->default('text');
            $table->string('attachment_path')->nullable();
            $table->string('attachment_name')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->foreign('chat_id')->references('id')->on('pusher_chats')->onDelete('cascade');
            $table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
