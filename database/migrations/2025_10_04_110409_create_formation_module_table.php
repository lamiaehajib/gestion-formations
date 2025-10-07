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
    Schema::create('formation_module', function (Blueprint $table) {
        $table->id();
        // L'clé étrangère l'Formation
        $table->foreignId('formation_id')->constrained()->onDelete('cascade');
        // L'clé étrangère l'Module
        $table->foreignId('module_id')->constrained()->onDelete('cascade');
        // Khas ikon ghir Module wa7ed f Formation wa7da (unique key)
        $table->unique(['formation_id', 'module_id']); 
        
        // N9adro nzido chi colonnes khas bi had l'relation, b7al 'order'
        $table->integer('order')->default(1); 
        
        $table->timestamps();
    });
}

public function down()
{
    Schema::dropIfExists('formation_module');
}
};
