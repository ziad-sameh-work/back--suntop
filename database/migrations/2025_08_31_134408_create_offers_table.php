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
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('short_description')->nullable();
            $table->string('code')->unique();
            $table->string('offer_tag')->nullable(); // "جديد", "حصري", "محدود"
            $table->enum('type', ['percentage', 'fixed_amount'])->default('percentage');
            $table->decimal('discount_percentage', 5, 2)->nullable();
            $table->decimal('discount_amount', 10, 2)->nullable();
            $table->decimal('minimum_amount', 10, 2)->default(0);
            $table->decimal('min_purchase_amount', 10, 2)->nullable();
            $table->decimal('maximum_discount', 10, 2)->nullable();
            $table->datetime('valid_from');
            $table->datetime('valid_until');
            $table->integer('usage_limit')->nullable();
            $table->integer('used_count')->default(0);
            $table->integer('trend_score')->default(0); // للعروض الرائجة
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->string('image_url')->nullable();
            $table->string('background_color')->default('#FF6B35');
            $table->string('text_color')->default('#FFFFFF');
            $table->integer('display_order')->default(0);
            $table->json('applicable_categories')->nullable(); // Categories this offer applies to
            $table->json('applicable_products')->nullable(); // Specific products this offer applies to
            $table->boolean('first_order_only')->default(false); // Only for first-time customers
            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_active', 'valid_from', 'valid_until']);
            $table->index(['is_featured', 'is_active']);
            $table->index(['trend_score', 'is_active']);
            $table->index(['display_order', 'is_featured']);
            $table->index('code');
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('offers');
    }
};
