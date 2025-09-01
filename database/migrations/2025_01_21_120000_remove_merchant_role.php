<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // أولاً: حذف جميع المستخدمين من فئة التاجر
        DB::table('users')->where('role', 'merchant')->delete();
        
        // ثانياً: تحديث enum ليشمل فقط customer و admin
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('customer', 'admin') NOT NULL DEFAULT 'customer'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // إعادة إضافة merchant إلى enum (لكن بدون استرداد البيانات المحذوفة)
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('customer', 'merchant', 'admin') NOT NULL DEFAULT 'customer'");
    }
};
