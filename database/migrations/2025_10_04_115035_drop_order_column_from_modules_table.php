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
        Schema::table('modules', function (Blueprint $table) {
            
            // ✅ حذف عمود order فقط
            if (Schema::hasColumn('modules', 'order')) {
                $table->dropColumn('order');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('modules', function (Blueprint $table) {
            // إعادة إضافة عمود order في حالة التراجع عن الـ Migration
            $table->integer('order')->nullable();
        });
    }
};
