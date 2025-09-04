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
        Schema::table('order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('order_items', 'selling_type')) {
                $table->string('selling_type', 50)->default('unit')->after('total_price');
            }
            if (!Schema::hasColumn('order_items', 'cartons_count')) {
                $table->integer('cartons_count')->default(0)->after('selling_type');
            }
            if (!Schema::hasColumn('order_items', 'packages_count')) {
                $table->integer('packages_count')->default(0)->after('cartons_count');
            }
            if (!Schema::hasColumn('order_items', 'units_count')) {
                $table->integer('units_count')->default(0)->after('packages_count');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['selling_type', 'cartons_count', 'packages_count', 'units_count']);
        });
    }
};
