<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * جدول رموز SSO للدخول التلقائي
     */
    public function up(): void
    {
        Schema::create('sso_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('application_account_id')->constrained('application_accounts')->onDelete('cascade');
            
            $table->string('token', 64)->unique(); // رمز فريد
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            
            $table->timestamp('expires_at'); // تاريخ انتهاء الصلاحية (15 دقيقة)
            $table->timestamp('used_at')->nullable(); // متى تم استخدامه
            $table->boolean('is_used')->default(false);
            
            $table->timestamps();
            
            $table->index(['token', 'is_used']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sso_tokens');
    }
};