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
        Schema::create('formation_messages', function (Blueprint $table) {
            $table->id();
            $table->string('subject'); // موضوع الرسالة
            $table->text('message')->nullable(); 
            $table->string('audio_path')->nullable(); // ✨ NOUVEAU: Chemin du fichier audio
            $table->integer('audio_duration')->nullable(); 
            $table->enum('priority', ['normal', 'urgent', 'important'])->default('normal'); // أولوية الرسالة
            $table->enum('status', ['draft', 'sent', 'scheduled'])->default('draft'); // حالة الرسالة
            $table->timestamp('scheduled_at')->nullable(); // موعد الإرسال المجدول
            $table->timestamp('sent_at')->nullable(); // تاريخ الإرسال الفعلي
            $table->foreignId('sent_by')->constrained('users')->onDelete('cascade'); // من أرسل الرسالة
            $table->integer('recipients_count')->default(0); // عدد المستلمين
            $table->timestamps();
            $table->softDeletes();
        });

        // جدول وسيط للربط بين الرسائل والتكوينات (Many to Many)
        Schema::create('formation_message_formations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('formation_message_id')->constrained('formation_messages')->onDelete('cascade');
            $table->foreignId('formation_id')->constrained('formations')->onDelete('cascade');
            $table->timestamps();
            
            // منع التكرار
            $table->unique(['formation_message_id', 'formation_id'], 'message_formation_unique');
        });

        // جدول لتتبع استلام كل طالب للرسالة
        Schema::create('formation_message_recipients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('formation_message_id')->constrained('formation_messages')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // الطالب المستلم
            $table->foreignId('inscription_id')->constrained('inscriptions')->onDelete('cascade'); // التسجيل المرتبط
            $table->boolean('is_read')->default(false); // هل قرأ الرسالة
            $table->timestamp('read_at')->nullable(); // متى قرأها
            $table->timestamps();
            
            // منع إرسال نفس الرسالة لنفس الطالب مرتين
            $table->unique(['formation_message_id', 'user_id'], 'message_user_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('formation_message_recipients');
        Schema::dropIfExists('formation_message_formations');
        Schema::dropIfExists('formation_messages');
    }
};