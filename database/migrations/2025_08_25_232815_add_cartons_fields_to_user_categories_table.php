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
            // Add carton and package fields if they don't exist
            if (!Schema::hasColumn('user_categories', 'min_cartons')) {
                $table->integer('min_cartons')->default(0)->after('max_purchase_amount');
            }
            
            if (!Schema::hasColumn('user_categories', 'max_cartons')) {
                $table->integer('max_cartons')->nullable()->after('min_cartons');
            }
            
            if (!Schema::hasColumn('user_categories', 'min_packages')) {
                $table->integer('min_packages')->default(0)->after('max_cartons');
            }
            
            if (!Schema::hasColumn('user_categories', 'max_packages')) {
                $table->integer('max_packages')->nullable()->after('min_packages');
            }
            
            if (!Schema::hasColumn('user_categories', 'requires_carton_purchase')) {
                $table->boolean('requires_carton_purchase')->default(false)->after('max_packages');
            }
            
            if (!Schema::hasColumn('user_categories', 'requires_package_purchase')) {
                $table->boolean('requires_package_purchase')->default(false)->after('requires_carton_purchase');
            }
            
            // Add discount percentages for different purchase types if they don't exist
            if (!Schema::hasColumn('user_categories', 'carton_discount_percentage')) {
                $table->decimal('carton_discount_percentage', 5, 2)->default(0)->after('requires_package_purchase');
            }
            
            if (!Schema::hasColumn('user_categories', 'package_discount_percentage')) {
                $table->decimal('package_discount_percentage', 5, 2)->default(0)->after('carton_discount_percentage');
            }
            
            if (!Schema::hasColumn('user_categories', 'unit_discount_percentage')) {
                $table->decimal('unit_discount_percentage', 5, 2)->default(0)->after('package_discount_percentage');
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
            $columns = [];
            
            $possibleColumns = [
                'min_cartons',
                'max_cartons',
                'min_packages',
                'max_packages',
                'requires_carton_purchase',
                'requires_package_purchase',
                'carton_discount_percentage',
                'package_discount_percentage',
                'unit_discount_percentage'
            ];
            
            foreach ($possibleColumns as $column) {
                if (Schema::hasColumn('user_categories', $column)) {
                    $columns[] = $column;
                }
            }
            
            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
