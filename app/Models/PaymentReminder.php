<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentReminder extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'expiry_date',
        'is_active',
        'sent_at',
        'sent_by'
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'sent_at' => 'datetime',
        'is_active' => 'boolean'
    ];

    /**
     * Relation avec User (l'étudiant qui reçoit le rappel)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relation avec User (l'admin qui a envoyé le rappel)
     */
    public function sentBy()
    {
        return $this->belongsTo(User::class, 'sent_by');
    }

    /**
     * Scope pour les rappels actifs
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope pour les rappels non expirés
     */
    public function scopeNotExpired($query)
    {
        return $query->where('expiry_date', '>=', now());
    }
}