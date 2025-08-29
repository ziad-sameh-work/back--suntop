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
        Schema::create('pusher_chats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['active', 'closed', 'archived'])->default('active');
            $table->string('title')->nullable();
            $table->timestamp('last_message_at')->nullable();
            $table->json('metadata')->nullable(); // For additional data like customer info
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
            $table->index(['status', 'last_message_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pusher_chats');
    }
};
