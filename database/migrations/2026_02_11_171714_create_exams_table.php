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
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            
            // Duration in minutes
            $table->integer('duration_minutes');
            
            // Exam settings
            $table->integer('passing_score')->default(50); // Score minimum pour réussir (%)
            $table->integer('max_attempts')->default(1); // Nombre max de tentatives
            $table->boolean('shuffle_questions')->default(false); // Mélanger les questions
            $table->boolean('show_results_immediately')->default(true); // Afficher résultats directement
            $table->boolean('show_correct_answers')->default(true); // Afficher les bonnes réponses
            
            // Availability
            $table->dateTime('available_from')->nullable();
            $table->dateTime('available_until')->nullable();
            
            // Status
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};
