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
        Schema::table('user_categories', function (Blueprint $table) {
            // Remove discount percentage fields and add loyalty points fields
            $table->dropColumn([
                'carton_discount_percentage',
                'package_discount_percentage', 
                'unit_discount_percentage',
                'discount_percentage'
            ]);
            
            // Add loyalty points fields
            $table->integer('carton_loyalty_points')->default(10)->after('max_cartons');
            $table->integer('bonus_points_per_carton')->default(0)->after('carton_loyalty_points');
            $table->integer('monthly_bonus_points')->default(0)->after('bonus_points_per_carton');
            $table->integer('signup_bonus_points')->default(0)->after('monthly_bonus_points');
            $table->boolean('has_points_multiplier')->default(false)->after('signup_bonus_points');
            $table->decimal('points_multiplier', 3, 2)->default(1.00)->after('has_points_multiplier');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_categories', function (Blueprint $table) {
            // Remove loyalty points fields
            $table->dropColumn([
                'carton_loyalty_points',
                'bonus_points_per_carton',
                'monthly_bonus_points',
                'signup_bonus_points',
                'has_points_multiplier',
                'points_multiplier'
            ]);
            
            // Re-add discount percentage fields
            $table->decimal('carton_discount_percentage', 5, 2)->default(0)->after('max_cartons');
            $table->decimal('package_discount_percentage', 5, 2)->default(0)->after('carton_discount_percentage');
            $table->decimal('unit_discount_percentage', 5, 2)->default(0)->after('package_discount_percentage');
            $table->decimal('discount_percentage', 5, 2)->default(0)->after('unit_discount_percentage');
        });
    }
};