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
        Schema::table('merchants', function (Blueprint $table) {
            // Add admin management fields
            $table->string('business_name')->nullable()->after('name');
            $table->string('business_type')->nullable()->after('business_name');
            $table->decimal('commission_rate', 5, 2)->default(5.0)->after('business_type');
            $table->string('logo')->nullable()->after('logo_url');
            
            // Add indexes for better performance
            $table->index('commission_rate');
            $table->index('business_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('merchants', function (Blueprint $table) {
            $table->dropIndex(['commission_rate']);
            $table->dropIndex(['business_type']);
            
            $table->dropColumn([
                'business_name',
                'business_type', 
                'commission_rate',
                'logo'
            ]);
        });
    }
};
