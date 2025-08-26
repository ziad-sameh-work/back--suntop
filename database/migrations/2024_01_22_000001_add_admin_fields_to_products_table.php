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
        Schema::table('products', function (Blueprint $table) {
            // Add new fields for admin product management
            $table->string('short_description', 500)->nullable()->after('description');
            $table->string('sku', 100)->unique()->nullable()->after('short_description');
            $table->string('slug', 255)->unique()->nullable()->after('sku');
            $table->json('images')->nullable()->after('gallery'); // Array of image paths
            $table->decimal('discount_price', 10, 2)->nullable()->after('price');
            $table->integer('min_quantity')->default(1)->after('stock_quantity');
            $table->decimal('weight', 8, 2)->nullable()->after('min_quantity');
            $table->string('dimensions', 100)->nullable()->after('weight');
            $table->foreignId('merchant_id')->nullable()->constrained('merchants')->onDelete('set null')->after('dimensions');
            $table->string('meta_title', 255)->nullable()->after('is_featured');
            $table->text('meta_description')->nullable()->after('meta_title');
            
            // Add indexes for better performance
            $table->index('sku');
            $table->index('slug');
            $table->index('merchant_id');
            $table->index('discount_price');
            $table->index('is_available');
            $table->index('is_featured');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            // Drop indexes first
            $table->dropIndex(['sku']);
            $table->dropIndex(['slug']);
            $table->dropIndex(['merchant_id']);
            $table->dropIndex(['discount_price']);
            $table->dropIndex(['is_available']);
            $table->dropIndex(['is_featured']);
            
            // Drop foreign key constraint
            $table->dropForeign(['merchant_id']);
            
            // Drop columns
            $table->dropColumn([
                'short_description',
                'sku',
                'slug',
                'images',
                'discount_price',
                'min_quantity',
                'weight',
                'dimensions',
                'merchant_id',
                'meta_title',
                'meta_description'
            ]);
        });
    }
};
