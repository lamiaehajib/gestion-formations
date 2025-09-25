<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Illuminate->Support->Facades->DB; // هذا الـ import ليس ضروريا في هذا الموديل بعد الآن إذا تم حذف DB::beginTransaction/rollback
use Illuminate\Database\Eloquent\SoftDeletes; 
class Inscription extends Model
{
    use HasFactory, SoftDeletes;

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
        'inscrit_par',
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
        // ... (كود إنشاء الدفع - يبقى كما هو)

        $payment = $this->payments()->create([
            'amount' => $amount,
            'payment_method' => $method,
            'reference' => $reference,
            'paid_date' => now(), 
            'status' => 'paid',
            'receipt_path' => $receiptPath,
        ]);

        $this->paid_amount += $amount; 

        // -----------------------------------------------------------
        // (كود إعادة حساب الأقساط المتبقية - يبقى كما هو)
        // -----------------------------------------------------------
        $epsilon = 0.01;
        if ($this->chosen_installments > 1 && $this->amount_per_installment > 0) {
            $paidInstallmentsCount = floor($this->paid_amount / $this->amount_per_installment + $epsilon);
            $paidInstallmentsCount = min($paidInstallmentsCount, $this->chosen_installments);
            $this->remaining_installments = max(0, $this->chosen_installments - $paidInstallmentsCount);
             
            if ($this->remaining_amount <= $epsilon) {
                $this->remaining_installments = 0;
            }
        } else {
            if ($this->remaining_amount <= $epsilon) {
                $this->remaining_installments = 0;
            }
        }
        // -----------------------------------------------------------
        
        // ✨ المنطق الجديد لحساب 'next_installment_due_date' 
        // تحقق من نوع الـ formation فقط إذا كان هناك أقساط متبقية
        if ($this->remaining_installments > 0) {
            $formationCategoryName = optional($this->formation)->category->name ?? null;

            if (in_array($formationCategoryName, ['Master Professionnelle', 'Licence Professionnelle'])) {
                // دالة Carbon::now() كتعطي التاريخ الحالي 
                $nextDueDate = Carbon::now()->addMonth()->startOfMonth()->day(1);
                
                // ولكن باش نضمنو أن التاريخ كيبدا من الشهر الجاي (ماشي الشهر اللي فيه دفع الطالب)
                // خاص نشوفو واش التاريخ ديال الدفعة الحالية فات 01 ديال هذا الشهر
                // وإذا كانت أول دفعة، خاص يكون التاريخ ديال الشهر الجاي.
                
                // غادي نديرو تاريخ الإستحقاق هو 01 من الشهر المقبل
                $this->next_installment_due_date = Carbon::parse($payment->paid_date)->addMonthNoOverflow()->day(1);

            } else {
                // إذا كان نوع الـ formation ماشي professionnelle، نخليوه فارغ ولا نحدوه بطريقة أخرى
                // لكن غالباً غنعتامدو عليه فقط إذا كان فيه نظام الأقساط
                $this->next_installment_due_date = null; // أو نديرو منطق افتراضي آخر إذا كان ضروري
            }
        } else {
            // إذا ما بقاوش أقساط، كنمسحو التاريخ
            $this->next_installment_due_date = null;
        }


        // تحديث حالة التسجيل إذا تم دفع المبلغ بالكامل
        if ($this->remaining_amount <= $epsilon) {
            if ($this->status !== 'completed' && $this->status !== 'cancelled') {
                $this->status = 'active'; // أو 'completed' حسب سير العمل لديك
            }
            $this->next_installment_due_date = null; // نأكدوا أنه كيتلغى إذا سالا الدفع
        }
        
        $this->save(); // حفظ التغييرات على سجل التسجيل Inscription

        return $payment;

    } catch (\Exception $e) {
        \Log::error('Error adding payment to inscription model: ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());
        throw new \Exception('Failed to add payment within model: ' . $e->getMessage());
    }

    }
}