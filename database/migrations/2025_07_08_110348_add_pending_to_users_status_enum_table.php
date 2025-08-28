<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // استيراد DB

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // الطريقة الأكثر أماناً هي استخدام SQL Raw لـ ENUM
        // تأكد من أن الترتيب صحيح: 'pending' يجب أن يأتي أولاً إذا أردت أن يكون هو القيمة الافتراضية للتسجيل الجديد
        // أو أضفه في النهاية إذا كان default 'active' مازال هو الأساسي
        DB::statement("ALTER TABLE users CHANGE status status ENUM('pending', 'active', 'inactive', 'suspended') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // عكس التغيير، قم بإزالة 'pending'
        // يجب أن تتعامل مع البيانات التي قد تكون 'pending' قبل عكسها
        // مثلاً، تحديث الحالات 'pending' إلى 'inactive' قبل الحذف
        DB::statement("UPDATE users SET status = 'inactive' WHERE status = 'pending'");
        DB::statement("ALTER TABLE users CHANGE status status ENUM('active', 'inactive', 'suspended') DEFAULT 'active'");
    }
};