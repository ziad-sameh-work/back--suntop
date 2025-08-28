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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('message');
            $table->enum('type', ['shipment', 'offer', 'reward', 'general', 'order_status', 'payment']);
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamp('read_at')->nullable();
            $table->json('data')->nullable(); // Additional data like order_id, offer_id, etc.
            $table->string('action_url')->nullable(); // URL to redirect when notification is clicked
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->boolean('is_sent')->default(false); // For push notifications
            $table->timestamp('scheduled_at')->nullable(); // For scheduled notifications
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['user_id', 'read_at']);
            $table->index(['type', 'created_at']);
            $table->index(['is_sent', 'scheduled_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notifications');
    }
};
