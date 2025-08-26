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
            $table->string('username')->unique()->after('name');
            $table->string('full_name')->nullable()->after('username');
            $table->string('phone')->nullable()->after('email');
            $table->enum('role', ['customer', 'merchant', 'admin'])->default('customer')->after('phone');
            $table->boolean('is_active')->default(true)->after('role');
            $table->string('profile_image')->nullable()->after('is_active');
            $table->timestamp('last_login_at')->nullable()->after('profile_image');
            $table->timestamp('password_changed_at')->nullable()->after('last_login_at');
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
            $table->dropColumn([
                'username',
                'full_name',
                'phone',
                'role',
                'is_active',
                'profile_image',
                'last_login_at',
                'password_changed_at',
            ]);
        });
    }
};
