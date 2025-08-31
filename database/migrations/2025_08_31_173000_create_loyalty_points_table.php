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
        Schema::create('loyalty_points', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->integer('points'); // Positive for earned, negative for redeemed
            $table->string('type')->default('earned'); // earned, redeemed, admin_award, admin_deduct, expired, bonus
            $table->text('description')->nullable();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->datetime('expires_at')->nullable();
            $table->json('metadata')->nullable();
            $table->string('reference_type')->nullable(); // For polymorphic relationship
            $table->unsignedBigInteger('reference_id')->nullable(); // For polymorphic relationship
            $table->boolean('is_processed')->default(true);
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('set null');

            // Indexes for better performance
            $table->index(['user_id', 'type']);
            $table->index(['user_id', 'expires_at']);
            $table->index(['reference_type', 'reference_id']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('loyalty_points');
    }
};
