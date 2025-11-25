<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * جدول التطبيقات الخارجية
     */
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // مثال: UITS Admin
            $table->string('slug')->unique(); // مثال: uits-admin
            $table->string('url'); // https://uits-admin.ma
            $table->string('vps_location')->nullable(); // VPS 1, VPS 2
            $table->string('icon')->nullable(); // أيقونة التطبيق
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0); // ترتيب العرض
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};