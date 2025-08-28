<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // الاسم الفني للفئة (مثل "1L", "250ml")
            $table->string('display_name'); // الاسم المعروض للمستخدم (مثل "1 لتر", "250 مل")
            $table->text('description')->nullable(); // وصف الفئة (اختياري)
            $table->string('icon')->nullable(); // أيقونة الفئة (اختياري)
            $table->integer('sort_order')->default(0); // ترتيب الفئة في القوائم
            $table->boolean('is_active')->default(true); // حالة نشاط الفئة
            $table->timestamps();
        });

        // إضافة حقل category_id إلى جدول المنتجات
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->after('merchant_id')
                  ->constrained('product_categories')->nullOnDelete();
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
            $table->dropConstrainedForeignId('category_id');
        });
        
        Schema::dropIfExists('product_categories');
    }
}
