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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('user_category_id')->nullable()->after('role')->constrained('user_categories')->onDelete('set null');
            $table->decimal('total_purchase_amount', 12, 2)->default(0)->after('user_category_id'); // إجمالي مبلغ الشراء
            $table->integer('total_orders_count')->default(0)->after('total_purchase_amount'); // عدد الطلبات الإجمالي
            $table->timestamp('category_updated_at')->nullable()->after('total_orders_count'); // آخر تحديث للفئة
            
            $table->index(['user_category_id', 'total_purchase_amount']);
            $table->index('total_purchase_amount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['user_category_id']);
            $table->dropColumn([
                'user_category_id',
                'total_purchase_amount',
                'total_orders_count',
                'category_updated_at'
            ]);
        });
    }
};
