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
        Schema::create('user_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 10)->unique(); // A, B, C, etc.
            $table->string('display_name'); // اسم الفئة باللغة العربية
            $table->string('display_name_en')->nullable(); // اسم الفئة بالإنجليزية
            $table->text('description')->nullable(); // وصف الفئة
            $table->decimal('min_purchase_amount', 12, 2)->default(0); // الحد الأدنى للشراء
            $table->decimal('max_purchase_amount', 12, 2)->nullable(); // الحد الأقصى للشراء (null = لا يوجد حد)
            $table->decimal('discount_percentage', 5, 2)->default(0); // نسبة الخصم للفئة
            $table->json('benefits')->nullable(); // مميزات إضافية للفئة
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0); // ترتيب الفئات
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
            $table->index(['min_purchase_amount', 'max_purchase_amount']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_categories');
    }
};
