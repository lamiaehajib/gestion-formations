<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_rattrapages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('original_exam_id')->constrained('exams')->onDelete('cascade');
            $table->foreignId('rattrapage_exam_id')->constrained('exams')->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');

            // Eligibility criteria used when creating this rattrapage
            $table->boolean('include_absent')->default(true)->comment('Students with 0 attempts');
            $table->boolean('include_failed')->default(true)->comment('Students who attempted but failed');
            $table->decimal('score_threshold', 5, 2)->nullable()->comment('Max score to be eligible (e.g. < 50%)');

            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Pivot table: which students are allowed for this rattrapage
        Schema::create('exam_rattrapage_students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rattrapage_id')->constrained('exam_rattrapages')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('inscription_id')->constrained('inscriptions')->onDelete('cascade');
            $table->string('eligibility_reason')->nullable()->comment('absent|failed|manual');
            $table->decimal('original_score', 5, 2)->nullable();
            $table->timestamps();

            $table->unique(['rattrapage_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_rattrapage_students');
        Schema::dropIfExists('exam_rattrapages');
    }
};