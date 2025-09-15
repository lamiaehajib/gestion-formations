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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inscription_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->date('due_date')->nullable(); 
            $table->date('paid_date')->nullable();
            
         
            $table->enum('payment_method', ['cash', 'credit_card', 'bank_transfer', 'online', 'cheque', 'other'])->default('cash'); 
            
           
            $table->enum('status', ['pending', 'paid', 'refunded', 'failed'])->default('pending');
            
            $table->decimal('late_fee', 10, 2)->default(0);
            $table->string('reference')->nullable();
            $table->string('transaction_id')->nullable();
            
            
            $table->string('receipt_path')->nullable(); 

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
        Schema::dropIfExists('payments');
    }
};