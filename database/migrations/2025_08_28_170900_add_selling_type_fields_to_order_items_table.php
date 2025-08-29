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
            // Add new fields for selling type and quantities
            $table->enum('selling_type', ['unit', 'package', 'carton'])->default('unit')->after('total_price');
            $table->integer('cartons_count')->default(0)->after('selling_type');
            $table->integer('packages_count')->default(0)->after('cartons_count');
            $table->integer('units_count')->default(0)->after('packages_count');
            
            // Add indexes for better performance
            $table->index('selling_type');
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
            $table->dropColumn([
                'selling_type',
                'cartons_count',
                'packages_count',
                'units_count'
            ]);
        });
    }
};
