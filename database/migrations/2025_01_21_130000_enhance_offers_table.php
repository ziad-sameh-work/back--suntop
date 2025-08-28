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
        Schema::table('offers', function (Blueprint $table) {
            $table->boolean('is_featured')->default(false)->after('is_active');
            $table->string('background_color')->default('#FF6B35')->after('image_url');
            $table->string('text_color')->default('#FFFFFF')->after('background_color');
            $table->integer('display_order')->default(0)->after('is_featured');
            $table->text('short_description')->nullable()->after('description');
            $table->decimal('min_purchase_amount', 10, 2)->nullable()->after('minimum_amount');
            $table->string('offer_tag')->nullable()->after('code'); // "جديد", "حصري", "محدود"
            $table->integer('trend_score')->default(0)->after('used_count'); // للعروض الرائجة
            
            // Indexes for better performance
            $table->index(['is_featured', 'is_active']);
            $table->index(['trend_score', 'is_active']);
            $table->index(['display_order', 'is_featured']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('offers', function (Blueprint $table) {
            $table->dropIndex(['is_featured', 'is_active']);
            $table->dropIndex(['trend_score', 'is_active']);
            $table->dropIndex(['display_order', 'is_featured']);
            
            $table->dropColumn([
                'is_featured',
                'background_color',
                'text_color',
                'display_order',
                'short_description',
                'min_purchase_amount',
                'offer_tag',
                'trend_score'
            ]);
        });
    }
};

