<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPromotionIdToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // إضافة العمود الجديد promotion_id
            $table->foreignId('promotion_id')->nullable()->after('status')->constrained('promotions')->onDelete('set null');
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
            // حذف المفتاح الخارجي أولاً
            $table->dropForeign(['promotion_id']);
            // ثم حذف العمود نفسه
            $table->dropColumn('promotion_id');
        });
    }
}