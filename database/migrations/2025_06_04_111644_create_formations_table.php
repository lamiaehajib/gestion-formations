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
        Schema::create('formations', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->integer('duration_hours');
            $table->enum('duration_unit', ['heures', 'jours', 'mois'])->default('heures'); // Changed 'heure' to 'heures'
            $table->integer('capacity');
            $table->enum('status', ['draft', 'published', 'completed'])->default('draft');
            $table->date('start_date');
            $table->date('end_date');
            
        
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
           
            $table->foreignId('consultant_id')->constrained('users')->onDelete('cascade');
            
           
            $table->json('prerequisites')->nullable();
            $table->json('documents_required')->nullable();
            
      
             $table->json('available_payment_options')->nullable();
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
        Schema::dropIfExists('formations');
    }
};