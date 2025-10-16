<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Exécute les migrations (crée la table).
     */
    public function up(): void
    {
        Schema::create('satisfaction_surveys', function (Blueprint $table) {
            $table->id();

            // Clés étrangères (Relations)
            // L'étudiant, la formation et l'inscription concernés par le sondage
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // L'étudiant (Student)
            $table->foreignId('formation_id')->constrained()->onDelete('cascade'); // La formation (Training/Course)
            $table->foreignId('inscription_id')->constrained()->onDelete('cascade'); // L'inscription (Enrollment)
            
            // Questions d'évaluation (Notes de 1 à 5 étoiles)
            $table->tinyInteger('content_quality')->nullable()->comment('Qualité du contenu'); // 1-5 étoiles
            $table->tinyInteger('instructor_rating')->nullable()->comment('Évaluation du formateur'); // 1-5 étoiles
            $table->tinyInteger('organization_rating')->nullable()->comment('Organisation'); // 1-5 étoiles
            $table->tinyInteger('support_rating')->nullable()->comment('Support et assistance'); // 1-5 étoiles
            $table->tinyInteger('overall_satisfaction')->nullable()->comment('Satisfaction générale'); // 1-5 étoiles
            
            // Questions ouvertes
            $table->text('positive_feedback')->nullable()->comment('Ce qui a été le plus apprécié');
            $table->text('improvement_suggestions')->nullable()->comment('Suggestions d\'amélioration');
            $table->text('additional_comments')->nullable()->comment('Remarques additionnelles');
            
            // Question de recommandation
            $table->boolean('would_recommend')->default(false)->comment('Recommanderiez-vous cette formation ?');
            
            // État du questionnaire
            $table->enum('status', ['pending', 'submitted', 'reviewed'])->default('pending')->comment('Statut du questionnaire: en attente, soumis, révisé');
            
            // Dates et Suivi
            $table->timestamp('submitted_at')->nullable()->comment('Date de soumission du questionnaire');
            $table->timestamp('reviewed_at')->nullable()->comment('Date de révision du questionnaire');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete()->comment('Utilisateur ayant révisé le questionnaire');
            
            $table->timestamps(); // colonnes created_at et updated_at
            $table->softDeletes(); // colonne deleted_at (suppression "douce")
            
            // Index unique pour s'assurer qu'un étudiant ne peut évaluer une inscription/formation qu'une seule fois
            $table->unique(['user_id', 'formation_id', 'inscription_id'], 'unique_survey_per_enrollment');
        });
    }

    /**
     * Annule les migrations (supprime la table).
     */
    public function down(): void
    {
        Schema::dropIfExists('satisfaction_surveys');
    }
};