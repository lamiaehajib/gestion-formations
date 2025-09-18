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
        Schema::table('courses', function (Blueprint $table) {
            // Check if the column does not already exist before adding it
            if (!Schema::hasColumn('courses', 'formation_id')) {
                $table->foreignId('formation_id')
                    ->nullable()
                    ->constrained()
                    ->onDelete('cascade');
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
        Schema::table('courses', function (Blueprint $table) {
            // Check if the foreign key constraint exists before dropping it
            if (Schema::hasColumn('courses', 'formation_id')) {
                $table->dropForeign(['formation_id']);
                $table->dropColumn('formation_id');
            }
        });
    }
};