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
            // On supprime la clé étrangère pour éviter les erreurs avant de supprimer la colonne
            $table->dropForeign(['formation_id']);
            // Maintenant, on peut supprimer la colonne 'formation_id'
            $table->dropColumn('formation_id');
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
            // On recrée la colonne 'formation_id' si on veut faire un rollback de la migration
            $table->foreignId('formation_id')->constrained()->onDelete('cascade');
        });
    }
};
