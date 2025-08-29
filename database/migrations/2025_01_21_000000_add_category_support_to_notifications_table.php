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
        Schema::table('notifications', function (Blueprint $table) {
            // Add category support for bulk notifications
            $table->foreignId('user_category_id')->nullable()->after('user_id')->constrained('user_categories')->onDelete('cascade');
            
            // Add notification target types: 'user', 'category', 'all'
            $table->enum('target_type', ['user', 'category', 'all'])->default('user')->after('user_category_id');
            
            // Add alert style types for the admin interface
            $table->enum('alert_type', ['info', 'success', 'warning', 'error'])->default('info')->after('type');
            
            // Add body field for longer notification content
            $table->text('body')->nullable()->after('message');
            
            // Add indexes
            $table->index(['user_category_id', 'target_type']);
            $table->index(['target_type', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropForeign(['user_category_id']);
            $table->dropIndex(['user_category_id', 'target_type']);
            $table->dropIndex(['target_type', 'created_at']);
            $table->dropColumn(['user_category_id', 'target_type', 'alert_type', 'body']);
        });
    }
};
