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
        Schema::create('inscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); 
            $table->foreignId('formation_id')->constrained()->onDelete('cascade'); 

           
            $table->enum('status', ['pending', 'active', 'completed', 'cancelled'])->default('pending');
            
            $table->date('inscription_date'); 
            
            $table->decimal('total_amount', 10, 2); 
            $table->decimal('paid_amount', 10, 2)->default(0);

            // خيارات الدفع بالتقسيط
            $table->integer('chosen_installments')->default(1); 
            $table->decimal('amount_per_installment', 10, 2)->nullable(); 
            $table->integer('remaining_installments')->default(1); 
           $table->boolean('access_restricted')->default(false);
        $table->date('next_installment_due_date')->nullable();
        
            $table->json('documents')->nullable(); 
            
            $table->text('notes')->nullable();

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
        Schema::dropIfExists('inscriptions');
    }
};