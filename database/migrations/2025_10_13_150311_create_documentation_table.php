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
        Schema::create('documentation', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->constrained()->onDelete('cascade');
            $table->foreignId('consultant_id')->constrained('users')->onDelete('cascade');
            
            // Documentation files/content
            $table->string('file_path')->nullable(); // Path lil file ila kan uploaded
            $table->text('description')->nullable(); // Description dyal documentation
            $table->json('files')->nullable(); // Multiple files (array of paths)
            
            // Verification by admin
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('admin_comment')->nullable(); // 3lach rejected/notes mn admin
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null'); // Admin li dar verification
            $table->timestamp('verified_at')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Index bach nsara3 queries
            $table->index(['module_id', 'consultant_id']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documentation');
    }
};