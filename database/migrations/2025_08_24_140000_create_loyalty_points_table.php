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
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('points'); // Can be positive (earned) or negative (redeemed)
            $table->enum('type', ['earned', 'redeemed', 'admin_award', 'admin_deduct', 'expired', 'bonus'])->default('earned');
            $table->text('description')->nullable();
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('set null');
            $table->datetime('expires_at')->nullable();
            $table->json('metadata')->nullable(); // Additional data like promotion info, etc.
            $table->string('reference_type')->nullable(); // Polymorphic reference type
            $table->unsignedBigInteger('reference_id')->nullable(); // Polymorphic reference id
            $table->boolean('is_processed')->default(true); // For batch processing
            $table->timestamps();

            $table->index(['user_id', 'type']);
            $table->index(['user_id', 'created_at']);
            $table->index(['expires_at']);
            $table->index(['type', 'created_at']);
            $table->index(['reference_type', 'reference_id']);
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
