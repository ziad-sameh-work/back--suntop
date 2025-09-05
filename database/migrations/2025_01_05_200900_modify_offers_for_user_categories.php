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
        Schema::table('offers', function (Blueprint $table) {
            // Remove code-related fields
            $table->dropUnique(['code']);
            $table->dropIndex(['code']);
            $table->dropColumn('code');
            
            // Remove usage tracking fields
            $table->dropColumn(['usage_limit', 'used_count']);
            
            // Change type from enum to text - first drop the existing column
            $table->dropColumn('type');
        });
        
        // Add the new type column and user category in a separate schema call
        Schema::table('offers', function (Blueprint $table) {
            // Add new type column as text field for custom offer types
            $table->string('type')->after('offer_tag')->nullable();
            
            // Add user category relationship
            $table->unsignedBigInteger('user_category_id')->after('type')->nullable();
            $table->foreign('user_category_id')->references('id')->on('user_categories')->onDelete('cascade');
            
            // Add index for user category
            $table->index('user_category_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('offers', function (Blueprint $table) {
            // Restore code field
            $table->string('code')->unique()->after('short_description');
            
            // Restore usage tracking fields
            $table->integer('usage_limit')->nullable()->after('valid_until');
            $table->integer('used_count')->default(0)->after('usage_limit');
            
            // Restore type enum
            $table->dropColumn('type');
            $table->enum('type', ['percentage', 'fixed_amount'])->default('percentage')->after('offer_tag');
            
            // Remove user category relationship
            $table->dropForeign(['user_category_id']);
            $table->dropIndex(['user_category_id']);
            $table->dropColumn('user_category_id');
            
            // Restore indexes
            $table->index('code');
            $table->index('type');
        });
    }
};
