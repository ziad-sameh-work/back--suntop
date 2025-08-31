<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * إزالة جميع الحقول غير المطلوبة من جدول المنتجات
     */
    public function up()
    {
        // إزالة foreign key constraints والفهارس قبل حذف الأعمدة
        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'products' 
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ");
        
        foreach ($foreignKeys as $fk) {
            try {
                DB::statement("ALTER TABLE products DROP FOREIGN KEY {$fk->CONSTRAINT_NAME}");
            } catch (\Exception $e) {
                // Continue
            }
        }
        
        // إزالة الفهارس
        $indexes = DB::select("
            SELECT DISTINCT INDEX_NAME 
            FROM information_schema.STATISTICS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'products' 
            AND INDEX_NAME != 'PRIMARY'
        ");
        
        foreach ($indexes as $index) {
            try {
                DB::statement("ALTER TABLE products DROP INDEX {$index->INDEX_NAME}");
            } catch (\Exception $e) {
                // Continue
            }
        }

        Schema::table('products', function (Blueprint $table) {
            // إزالة الحقول غير المطلوبة
            $columnsToRemove = [
                'short_description',
                'sku', 
                'slug',
                'discount_price',
                'stock_quantity',
                'min_quantity',
                'weight',
                'dimensions',
                'merchant_id',
                'meta_title',
                'meta_description',
                'carton_size',
                'carton_price',
                'is_full_carton',
                'package_size', 
                'package_price',
                'is_full_package',
                'allow_individual_units',
                'carton_loyalty_points',
                'package_loyalty_points',
                'unit_loyalty_points',
                'original_price',
                'currency',
                'category',
                'size',
                'volume_category',
                'rating',
                'review_count',
                'tags',
                'ingredients',
                'nutrition_facts',
                'storage_instructions',
                'expiry_info',
                'barcode',
                'is_featured',
                'sort_order',
                'image_url',
                'gallery'
            ];

            foreach ($columnsToRemove as $column) {
                if (Schema::hasColumn('products', $column)) {
                    $table->dropColumn($column);
                }
            }

            // إضافة category_id إذا لم يكن موجوداً
            if (!Schema::hasColumn('products', 'category_id')) {
                $table->foreignId('category_id')
                      ->nullable()
                      ->constrained('product_categories')
                      ->onDelete('set null')
                      ->after('description');
            }

            // إضافة back_color إذا لم يكن موجوداً
            if (!Schema::hasColumn('products', 'back_color')) {
                $table->string('back_color', 20)->default('#FF6B35')->after('price');
            }

            // إضافة images كـ JSON إذا لم يكن موجوداً
            if (!Schema::hasColumn('products', 'images')) {
                $table->json('images')->nullable()->after('back_color');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        // لا نريد إرجاع الحقول المحذوفة
        // هذا التنظيف نهائي
    }
};
