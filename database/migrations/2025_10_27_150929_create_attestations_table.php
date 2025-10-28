<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('attestations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('inscription_id')->constrained()->onDelete('cascade');
            $table->date('birth_date'); // تاريخ الميلاد ديال الطالب
            $table->string('academic_year'); // 2024-2025 مثلاً
            $table->enum('status', ['pending', 'en_traitement', 'termine'])->default('pending');
            $table->string('signed_document_path')->nullable(); // المسار ديال الملف الموقع
            $table->text('admin_message')->nullable(); // رسالة من الأدمن للطالب
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete(); // الأدمن لي دار المعالجة
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('attestations');
    }
};