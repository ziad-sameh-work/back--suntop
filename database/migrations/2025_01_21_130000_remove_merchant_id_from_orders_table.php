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
        Schema::table('orders', function (Blueprint $table) {
            // Drop foreign key constraint first if it exists
            try {
                $table->dropForeign(['merchant_id']);
            } catch (\Exception $e) {
                // Continue if constraint doesn't exist
            }
            
            // Drop the merchant_id column if it exists
            if (Schema::hasColumn('orders', 'merchant_id')) {
                $table->dropColumn('merchant_id');
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
        Schema::table('orders', function (Blueprint $table) {
            // Re-add merchant_id column (nullable for rollback safety)
            $table->unsignedBigInteger('merchant_id')->nullable()->after('user_id');
        });
    }
};
