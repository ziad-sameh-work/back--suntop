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
            // إضافة حقول إحصائيات الشراء إذا لم تكن موجودة بالفعل
            if (!Schema::hasColumn('users', 'total_cartons_purchased')) {
                $table->integer('total_cartons_purchased')->default(0)->after('user_category_id');
            }
            
            if (!Schema::hasColumn('users', 'total_packages_purchased')) {
                $table->integer('total_packages_purchased')->default(0)->after('total_cartons_purchased');
            }
            
            if (!Schema::hasColumn('users', 'total_units_purchased')) {
                $table->integer('total_units_purchased')->default(0)->after('total_packages_purchased');
            }
            
            if (!Schema::hasColumn('users', 'total_orders_count')) {
                $table->integer('total_orders_count')->default(0)->after('total_units_purchased');
            }
            
            // نتخطى هذا العمود لأنه موجود بالفعل
            // $table->decimal('total_purchase_amount', 12, 2)->default(0)->after('total_orders_count');
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
            $columns = [];
            
            if (Schema::hasColumn('users', 'total_cartons_purchased')) {
                $columns[] = 'total_cartons_purchased';
            }
            
            if (Schema::hasColumn('users', 'total_packages_purchased')) {
                $columns[] = 'total_packages_purchased';
            }
            
            if (Schema::hasColumn('users', 'total_units_purchased')) {
                $columns[] = 'total_units_purchased';
            }
            
            if (Schema::hasColumn('users', 'total_orders_count')) {
                $columns[] = 'total_orders_count';
            }
            
            // لا نحذف total_purchase_amount لأننا لم نضفه
            
            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
