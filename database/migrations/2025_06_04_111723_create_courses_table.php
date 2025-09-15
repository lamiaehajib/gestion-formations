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
            
            
            $table->foreignId('consultant_id')
                  ->nullable() 
                  ->constrained('users') 
                  ->onDelete('set null'); 

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