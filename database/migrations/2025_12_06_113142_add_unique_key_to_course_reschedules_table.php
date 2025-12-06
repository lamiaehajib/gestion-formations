<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('course_reschedules', function (Blueprint $table) {
            // Ndiro compound unique key: course_id + original_date + new_date
            // Hadchi ghadi imn3 duplicate reschedule records
            $table->unique(['course_id', 'original_date', 'new_date'], 'unique_reschedule');
        });
    }

    public function down()
    {
        Schema::table('course_reschedules', function (Blueprint $table) {
            $table->dropUnique('unique_reschedule');
        });
    }
};