<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Illuminate->Support->Facades->DB; // هذا الـ import ليس ضروريا في هذا الموديل بعد الآن إذا تم حذف DB::beginTransaction/rollback

class Inscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'formation_id',
        'status',
        'inscription_date',
        'total_amount',
        'paid_amount',
        'chosen_installments',
        'amount_per_installment',
        'remaining_installments',
        'documents',
        'notes',
        'access_restricted',      // أضف هذا
        'next_installment_due_date', // أضف هذا
    ];

    protected $casts = [
        'inscription_date' => 'date',
        'documents' => 'array',
        'access_restricted' => 'boolean', // يجب تحويلها إلى نوع بولياني
        'next_installment_due_date' => 'date', // يجب تحويلها إلى نوع تاريخ
    ];

    public function getRemainingAmountAttribute()
    {
        return $this->total_amount - $this->paid_amount;
    }

    public function getPaymentTypeAttribute()
    {
        if ($this->chosen_installments === 1) {
            return 'Paiement Complet';
        }
        return "Paiement en {$this->chosen_installments} Versements";
    }

    public function getPaymentStatusLabelAttribute()
    {
        if ($this->remaining_amount <= 0) {
            return 'Payé';
        } elseif ($this->paid_amount > 0 && $this->remaining_amount > 0) {
            return 'Partiellement payé';
        }
        return 'Non payé';
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function formation()
    {
        return $this->belongsTo(Formation::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Adds a new payment record to this inscription and updates inscription's paid_amount and remaining_installments.
     * يجب أن يتم استدعاء هذه الدالة داخل معاملة (DB::beginTransaction) في Controller.
     *
     * @param float $amount The amount paid in this payment.
     * @param string $method The payment method (e.g., 'cash', 'bank_transfer').
     * @param string|null $reference Optional reference for the payment.
     * @param string|null $receiptPath Optional path to the uploaded receipt.
     * @return \App\Models\Payment The created payment record.
     * @throws \Exception If payment recording fails.
     */
   // ... داخل الكلاس Inscription
public function addPayment($amount, $method = 'cash', $reference = null, $receiptPath = null)
{
    try {
        $payment = $this->payments()->create([
            'amount' => $amount,
            'payment_method' => $method,
            'reference' => $reference,
            'paid_date' => now(), 
            'status' => 'paid',
            'receipt_path' => $receiptPath,
        ]);

        $this->paid_amount += $amount; // أضف المبلغ الجديد إلى paid_amount الحالي

        // -----------------------------------------------------------
        // هذا هو الجزء الذي يجب تعديله لتحسين حساب الأقساط المتبقية
        // -----------------------------------------------------------
        if ($this->chosen_installments > 1 && $this->amount_per_installment > 0) {
            // نستخدم Epsilon لمعالجة مشاكل الأرقام العشرية في المقارنات
            $epsilon = 0.01; // قيمة صغيرة جداً للمقارنة (1 سنت)

            // إعادة حساب الأقساط المتبقية بناءً على المبلغ المدفوع الكلي
            // وعدد الأقساط المفترض دفعها
            $paidInstallmentsCount = floor($this->paid_amount / $this->amount_per_installment + $epsilon);
            
            // تأكد أن عدد الأقساط المدفوعة لا يتجاوز العدد الكلي للأقساط المختارة
            $paidInstallmentsCount = min($paidInstallmentsCount, $this->chosen_installments);

            $this->remaining_installments = max(0, $this->chosen_installments - $paidInstallmentsCount);
            
            // إذا تم دفع المبلغ بالكامل (أو تقريباً بالكامل)
            if ($this->remaining_amount <= $epsilon) {
                $this->remaining_installments = 0;
            }
        } else { // إذا كان دفعاً كاملاً (قسط واحد)
            if ($this->remaining_amount <= $epsilon) {
                $this->remaining_installments = 0;
            }
        }
        // -----------------------------------------------------------
        // نهاية التعديل على حساب الأقساط المتبقية
        // -----------------------------------------------------------

        // تحديث حالة التسجيل إذا تم دفع المبلغ بالكامل
        if ($this->remaining_amount <= $epsilon) { // استخدام epsilon هنا أيضاً
            if ($this->status !== 'completed' && $this->status !== 'cancelled') {
                $this->status = 'active'; // أو 'completed' حسب سير العمل لديك
            }
        }
        $this->save(); // حفظ التغييرات على سجل التسجيل Inscription

        return $payment;

    } catch (\Exception $e) {
        \Log::error('Error adding payment to inscription model: ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());
        throw new \Exception('Failed to add payment within model: ' . $e->getMessage());
    }

    }
}