<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'installments_count',
        'down_payment_percentage',
        'schedule_template',
        'is_active',
    ];

    protected $casts = [
        'down_payment_percentage' => 'decimal:2',
        'schedule_template' => 'array',
        'is_active' => 'boolean',
    ];
}