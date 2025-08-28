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
            // Only try to drop columns if they exist
            $columnsToRemove = [];
            
            if (Schema::hasColumn('user_categories', 'carton_discount_percentage')) {
                $columnsToRemove[] = 'carton_discount_percentage';
            }
            
            if (Schema::hasColumn('user_categories', 'package_discount_percentage')) {
                $columnsToRemove[] = 'package_discount_percentage';
            }
            
            if (Schema::hasColumn('user_categories', 'unit_discount_percentage')) {
                $columnsToRemove[] = 'unit_discount_percentage';
            }
            
            if (!empty($columnsToRemove)) {
                $table->dropColumn($columnsToRemove);
            }
            
            // Add loyalty points fields if they don't exist
            if (!Schema::hasColumn('user_categories', 'carton_loyalty_points')) {
                $table->integer('carton_loyalty_points')->default(10)->after('max_cartons');
            }
            
            if (!Schema::hasColumn('user_categories', 'bonus_points_per_carton')) {
                $table->integer('bonus_points_per_carton')->default(0)->after('carton_loyalty_points');
            }
            
            if (!Schema::hasColumn('user_categories', 'monthly_bonus_points')) {
                $table->integer('monthly_bonus_points')->default(0)->after('bonus_points_per_carton');
            }
            
            if (!Schema::hasColumn('user_categories', 'signup_bonus_points')) {
                $table->integer('signup_bonus_points')->default(0)->after('monthly_bonus_points');
            }
            
            if (!Schema::hasColumn('user_categories', 'has_points_multiplier')) {
                $table->boolean('has_points_multiplier')->default(false)->after('signup_bonus_points');
            }
            
            if (!Schema::hasColumn('user_categories', 'points_multiplier')) {
                $table->decimal('points_multiplier', 3, 2)->default(1.00)->after('has_points_multiplier');
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
        Schema::table('user_categories', function (Blueprint $table) {
            // Remove loyalty points fields if they exist
            $columnsToRemove = [];
            
            $possibleColumns = [
                'carton_loyalty_points',
                'bonus_points_per_carton',
                'monthly_bonus_points',
                'signup_bonus_points',
                'has_points_multiplier',
                'points_multiplier'
            ];
            
            foreach ($possibleColumns as $column) {
                if (Schema::hasColumn('user_categories', $column)) {
                    $columnsToRemove[] = $column;
                }
            }
            
            if (!empty($columnsToRemove)) {
                $table->dropColumn($columnsToRemove);
            }
            
            // Re-add discount percentage fields if they don't exist
            if (!Schema::hasColumn('user_categories', 'carton_discount_percentage')) {
                $table->decimal('carton_discount_percentage', 5, 2)->default(0)->after('max_cartons');
            }
            
            if (!Schema::hasColumn('user_categories', 'package_discount_percentage')) {
                $table->decimal('package_discount_percentage', 5, 2)->default(0)->after('carton_discount_percentage');
            }
            
            if (!Schema::hasColumn('user_categories', 'unit_discount_percentage')) {
                $table->decimal('unit_discount_percentage', 5, 2)->default(0)->after('package_discount_percentage');
            }
        });
    }
};