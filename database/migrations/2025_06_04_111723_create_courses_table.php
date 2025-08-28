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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('formation_id')->constrained()->onDelete('cascade');
            
            // Add the consultant_id column
            // Assuming your consultants are in the 'users' table
            $table->foreignId('consultant_id')
                  ->nullable() // Make it nullable if a course can temporarily exist without a consultant
                  ->constrained('users') // References the 'users' table
                  ->onDelete('set null'); // If a consultant is deleted, set this field to null

            $table->string('title');
            $table->text('description')->nullable();
            $table->date('course_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('zoom_link')->nullable();
            $table->string('recording_url')->nullable();
            $table->json('documents')->nullable();
            
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
        Schema::dropIfExists('courses');
    }
};