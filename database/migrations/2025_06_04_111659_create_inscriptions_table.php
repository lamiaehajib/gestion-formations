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
        Schema::create('inscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // الطالب اللي تسجل
            $table->foreignId('formation_id')->constrained()->onDelete('cascade'); // التكوين اللي تسجل فيه

            // حالة التسجيل: معلق، نشيط، مكتمل، ملغى
            $table->enum('status', ['pending', 'active', 'completed', 'cancelled'])->default('pending');
            
            $table->date('inscription_date'); // تاريخ التسجيل
            
            $table->decimal('total_amount', 10, 2); // المبلغ الإجمالي للتكوين
            $table->decimal('paid_amount', 10, 2)->default(0); // المبلغ المدفوع حتى الآن

            // خيارات الدفع بالتقسيط
            $table->integer('chosen_installments')->default(1); // عدد الأقساط اللي اختارها الطالب (1 يعني دفع كامل)
            $table->decimal('amount_per_installment', 10, 2)->nullable(); // مبلغ كل قسط (يمكن أن يكون فارغاً إذا كان دفعاً كاملاً)
            $table->integer('remaining_installments')->default(1); // عدد الأقساط المتبقية
$table->boolean('access_restricted')->default(false);
$table->date('next_installment_due_date')->nullable();
            // حقل JSON للوثائق (مثال: مسارات ملفات مرفقة، أو وثائق مطلوبة إضافية)
            $table->json('documents')->nullable(); 
            
            $table->text('notes')->nullable(); // ملاحظات إضافية على التسجيل

            $table->timestamps(); // created_at و updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inscriptions');
    }
};