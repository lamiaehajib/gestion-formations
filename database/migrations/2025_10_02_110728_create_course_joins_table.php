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
 public function up(): void
{
    Schema::create('course_joins', function (Blueprint $table) {
        $table->id();
        // Clé étrangère l'Course
        $table->foreignId('course_id')->constrained()->onDelete('cascade');
        // Clé étrangère l'User
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        // Bach n'assurerw belli kulla user ydir join mara wa7da l'nafs course
        $table->unique(['course_id', 'user_id']); 
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('course_joins');
    }
};
