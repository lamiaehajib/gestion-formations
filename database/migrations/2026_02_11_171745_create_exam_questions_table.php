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
        Schema::create('exam_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained()->onDelete('cascade');
            
            // Question type: qcm (choix unique), true_false, text (réponse courte), essay (réponse longue), checkbox (choix multiples)
            $table->enum('type', ['qcm', 'true_false', 'text', 'essay', 'checkbox', 'fill_blanks', 'matching', 'ordering', 'numeric'])->default('qcm');
            
            $table->text('question_text');
            $table->string('question_image')->nullable(); // Path pour une image optionnelle
            
            // Points pour cette question
            $table->decimal('points', 8, 2)->default(1.00);
            
            // Order/position dans l'examen
            $table->integer('order')->default(0);
            
            // Options pour QCM/Checkbox (JSON)
            // Format: [{"text": "Option A", "is_correct": true}, {"text": "Option B", "is_correct": false}]
            $table->json('options')->nullable();
            
            // Réponse correcte pour true_false ou text
            $table->json('correct_answer')->nullable();
            
            // Explanation optionnelle à afficher après
            $table->text('explanation')->nullable();
            
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('exam_id');
            $table->index(['exam_id', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_questions');
    }
};