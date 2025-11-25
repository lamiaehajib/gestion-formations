<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Table des comptes d'applications
     * Liée aux crm_admins (propriétaires des apps)
     */
    public function up(): void
    {
        
        Schema::create('application_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->onDelete('cascade');
            
            // ✅ Lié à crm_admins au lieu de users
            $table->foreignId('crm_admin_id')->constrained('crm_admins')->onDelete('cascade');
            
            $table->string('role_name');
            $table->string('username');
            $table->text('password'); // Chiffré avec Crypt
            
            $table->text('notes')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
            
            // Index pour améliorer les performances (non problématique)
            $table->index(['application_id', 'crm_admin_id']);
            
            // Correction : Nommage explicite et court de l'index unique pour éviter la limite MySQL (64 caractères)
            $table->unique(['application_id', 'crm_admin_id', 'role_name'], 'app_admin_role_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application_accounts');
    }
};