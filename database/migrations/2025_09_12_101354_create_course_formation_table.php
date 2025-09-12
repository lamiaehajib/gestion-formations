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
        Schema::create('course_formation', function (Blueprint $table) {
            // Un `id` pour la clé primaire de cette table, c'est une bonne pratique.
            $table->id();

            // Clé étrangère pour la table `courses`.
            $table->foreignId('course_id')->constrained()->onDelete('cascade');

            // Clé étrangère pour la table `formations`.
            $table->foreignId('formation_id')->constrained()->onDelete('cascade');

            // Rendre la combinaison des deux clés unique pour éviter les doublons.
            $table->unique(['course_id', 'formation_id']);
            
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
        Schema::dropIfExists('course_formation');
    }
};
