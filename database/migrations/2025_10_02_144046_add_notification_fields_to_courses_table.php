<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            // الحقل الأول: لتسجيل آخر وقت تم فيه إرسال الإشعار
            $table->timestamp('last_notification_time')->nullable()->after('recording_url');
            
            // الحقل الثاني: لتسجيل عدد الإشعارات المرسلة (القيمة الافتراضية 0)
            $table->unsignedTinyInteger('notification_count')->default(0)->after('last_notification_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            // الدالة down ضرورية لحذف الحقول في حالة التراجع عن الـ migration
            $table->dropColumn('last_notification_time');
            $table->dropColumn('notification_count');
        });
    }
};