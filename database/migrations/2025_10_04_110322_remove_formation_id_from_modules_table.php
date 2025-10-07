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
        Schema::table('modules', function (Blueprint $table) {
            // 🚨 الفحص الشرطي للعمود: لا تقم بأي شيء إذا لم يكن العمود موجوداً
            if (Schema::hasColumn('modules', 'formation_id')) {
                
                // 1. ✅ حذف القيد الأجنبي أولاً: 
                // نستخدم dropForeign مع اسم القيد الافتراضي الذي أنشأته Laravel (اسم_الجدول_اسم_العمود_foreign).
                try {
                    $table->dropForeign('modules_formation_id_foreign'); 
                } catch (\Exception $e) {
                    // إذا كان الاسم مختلفاً لسبب ما أو كان قد حُذف سابقاً، سنتجاوز هذا الخطأ ونستمر في حذف العمود.
                }
                
                // 2. ✅ حذف العمود (الآن سيتم حذفه بأمان)
                $table->dropColumn('formation_id');
            }
        });
    }


    public function down()
    {
        // ⚠️ يجب عليك وضع هذا الكود في ملف "down" ليتمكن من عكس العملية في المستقبل:
        Schema::table('modules', function (Blueprint $table) {
            // نستخدم 'after' لتحديد موقع العمود في حال التراجع
            $table->foreignId('formation_id')->constrained()->onDelete('cascade')->after('progress');
        });
    }
};
