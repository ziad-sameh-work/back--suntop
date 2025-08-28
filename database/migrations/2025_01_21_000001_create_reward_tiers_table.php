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
        Schema::create('reward_tiers', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Bronze, Silver, Gold, Platinum
            $table->string('display_name');
            $table->text('description');
            $table->integer('points_required');
            $table->string('icon_url')->nullable();
            $table->string('color')->default('#CD7F32'); // Bronze color as default
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->integer('bonus_multiplier')->default(1);
            $table->json('benefits')->nullable(); // Array of benefits
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['is_active', 'points_required']);
            $table->unique(['name']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reward_tiers');
    }
};
