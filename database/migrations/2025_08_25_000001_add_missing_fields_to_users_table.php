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
            // Add missing total_purchase_amount field for backward compatibility
            if (!Schema::hasColumn('users', 'total_purchase_amount')) {
                $table->decimal('total_purchase_amount', 10, 2)->default(0)->after('total_units_purchased');
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
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'total_purchase_amount')) {
                $table->dropColumn('total_purchase_amount');
            }
        });
    }
};
