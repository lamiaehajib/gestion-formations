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
        Schema::create('exam_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('inscription_id')->nullable()->constrained()->onDelete('set null');
            
            // Attempt info
            $table->integer('attempt_number')->default(1);
            $table->dateTime('started_at');
            $table->dateTime('submitted_at')->nullable();
            $table->dateTime('time_limit_at'); // Heure limite basée sur duration
            
            // Scoring
            $table->decimal('score', 8, 2)->nullable(); // Score obtenu (%)
            $table->decimal('total_points', 8, 2)->nullable(); // Points totaux obtenus
            $table->decimal('max_points', 8, 2)->nullable(); // Points maximums possibles
            
            // Status: in_progress, submitted, timed_out, graded
            $table->enum('status', ['in_progress', 'submitted', 'timed_out', 'graded'])->default('in_progress');
            
            // Passed or failed
            $table->boolean('passed')->nullable();
            
            // JSON للإجابات: {question_id: answer}
            $table->json('answers')->nullable();
            
            // JSON للنتائج التفصيلية: {question_id: {is_correct, points_earned, feedback}}
            $table->json('results')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Index pour performance
            $table->index(['user_id', 'exam_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_attempts');
    }
};