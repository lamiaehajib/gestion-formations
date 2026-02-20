<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_security_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_attempt_id')->constrained('exam_attempts')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('activity_type'); // copy_attempt, right_click, tab_switch, etc.
            $table->integer('tab_switch_count')->default(0);
            $table->timestamp('activity_timestamp');
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
            
            $table->index(['exam_attempt_id', 'activity_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_security_logs');
    }
};