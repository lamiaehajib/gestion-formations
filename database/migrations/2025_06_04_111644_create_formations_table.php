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
        Schema::create('formations', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->integer('duration_hours');
$table->enum('duration_unit', ['heures', 'jours', 'mois'])->default('heures'); // Changed 'heure' to 'heures'
            $table->integer('capacity');
            $table->enum('status', ['draft', 'published', 'completed'])->default('draft');
            $table->date('start_date');
            $table->date('end_date');
            
            // Foreign Keys
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            // تأكد أن جدول 'users' موجود ولديه حقول الأدوار (roles)
            $table->foreignId('consultant_id')->constrained('users')->onDelete('cascade');
            
            // Champs JSON pour Prérequis et Documents (optionnels)
            $table->json('prerequisites')->nullable();
            $table->json('documents_required')->nullable();
            
            // تم التعديل: حقل جديد لتخزين خيارات الدفع المتاحة كـ JSON Array
            // القيمة الافتراضية هي [1]، مما يعني أن الدفع الكامل متاح دائمًا
 $table->json('available_payment_options')->nullable();
            $table->timestamps(); // created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('formations');
    }
};