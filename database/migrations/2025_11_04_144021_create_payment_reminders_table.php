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
        Schema::create('payment_reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // L'étudiant
            $table->date('expiry_date'); // Date d'échéance (05 novembre par exemple)
            $table->boolean('is_active')->default(true); // Rappel actif ou non
            $table->timestamp('sent_at')->nullable(); // Quand le rappel a été envoyé
            $table->foreignId('sent_by')->nullable()->constrained('users')->onDelete('set null'); // Admin qui a envoyé
            $table->timestamps();

            // Index pour performance
            $table->index(['user_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_reminders');
    }
};