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
        Schema::table('chat_messages', function (Blueprint $table) {
            // Check if columns don't exist before adding them
            if (!Schema::hasColumn('chat_messages', 'attachment_path')) {
                $table->string('attachment_path')->nullable()->after('message_type');
            }
            if (!Schema::hasColumn('chat_messages', 'attachment_name')) {
                $table->string('attachment_name')->nullable()->after('attachment_path');
            }
            
            // Remove attachments column if it exists
            if (Schema::hasColumn('chat_messages', 'attachments')) {
                $table->dropColumn('attachments');
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
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->dropColumn(['attachment_path', 'attachment_name']);
            $table->json('attachments')->nullable();
        });
    }
};
