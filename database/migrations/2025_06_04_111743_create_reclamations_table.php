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
        Schema::create('reclamations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('formation_id')->constrained()->onDelete('cascade');
            $table->enum('category', ['paiement', 'contenu', 'technique', 'autre']);
            $table->string('subject');
            $table->text('description');
            $table->enum('status', ['ouverte', 'en_traitement', 'resolue', 'fermee'])->default('ouverte');
            $table->enum('priority', ['basse', 'moyenne', 'haute'])->default('moyenne');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->text('response')->nullable();
            $table->date('response_date')->nullable();
            $table->integer('satisfaction_rating')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('reclamations');
    }
};
