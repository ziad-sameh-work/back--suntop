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
        Schema::table('products', function (Blueprint $table) {
            // إضافة الحقول المفقودة المتعلقة بالكراتين والعلب
            if (!Schema::hasColumn('products', 'carton_size')) {
                $table->integer('carton_size')->nullable()->after('is_featured');
            }
            
            if (!Schema::hasColumn('products', 'carton_price')) {
                $table->decimal('carton_price', 10, 2)->nullable()->after('carton_size');
            }
            
            if (!Schema::hasColumn('products', 'is_full_carton')) {
                $table->boolean('is_full_carton')->default(false)->after('carton_price');
            }
            
            if (!Schema::hasColumn('products', 'package_size')) {
                $table->integer('package_size')->nullable()->after('is_full_carton');
            }
            
            if (!Schema::hasColumn('products', 'package_price')) {
                $table->decimal('package_price', 10, 2)->nullable()->after('package_size');
            }
            
            if (!Schema::hasColumn('products', 'is_full_package')) {
                $table->boolean('is_full_package')->default(false)->after('package_price');
            }
            
            if (!Schema::hasColumn('products', 'allow_individual_units')) {
                $table->boolean('allow_individual_units')->default(true)->after('is_full_package');
            }
            
            if (!Schema::hasColumn('products', 'carton_loyalty_points')) {
                $table->integer('carton_loyalty_points')->default(0)->after('allow_individual_units');
            }
            
            if (!Schema::hasColumn('products', 'package_loyalty_points')) {
                $table->integer('package_loyalty_points')->default(0)->after('carton_loyalty_points');
            }
            
            if (!Schema::hasColumn('products', 'unit_loyalty_points')) {
                $table->integer('unit_loyalty_points')->default(0)->after('package_loyalty_points');
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
        Schema::table('products', function (Blueprint $table) {
            $columns = [];
            
            $possibleColumns = [
                'carton_size',
                'carton_price',
                'is_full_carton',
                'package_size',
                'package_price',
                'is_full_package',
                'allow_individual_units',
                'carton_loyalty_points',
                'package_loyalty_points',
                'unit_loyalty_points'
            ];
            
            foreach ($possibleColumns as $column) {
                if (Schema::hasColumn('products', $column)) {
                    $columns[] = $column;
                }
            }
            
            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
