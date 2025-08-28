<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'inscription_id',
        'amount',
        'due_date', // هذا العمود أصبح nullable في Migration، احتفظ به إذا كنت لا تريد حذفه
        'paid_date',
        'payment_method',
        'status',
        'late_fee', // احتفظ به إذا كنت تستخدمه
        'reference',
        'transaction_id',
        'receipt_path', // <<< قم بإضافة هذا السطر
                'created_by_user_id', // Add this line

    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'late_fee' => 'decimal:2', // احتفظ به إذا كنت تستخدمه
        'due_date' => 'date', // احتفظ به إذا كنت تستخدمه
        'paid_date' => 'date',
        'receipt_path' => 'string', // <<< قم بإضافة هذا السطر
    ];

    // Relations
    public function inscription()
    {
        return $this->belongsTo(Inscription::class);
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }
}