<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Crypt;

class ApplicationAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'crm_admin_id',
        'role_name',
        'username',
        'password',
        'notes',
        'last_used_at',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_used_at' => 'datetime',
    ];

    protected $hidden = [
        'password',
    ];

    /**
     * Application associée
     */
    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    /**
     * Admin CRM propriétaire
     */
    public function crmAdmin(): BelongsTo
    {
        return $this->belongsTo(CrmAdmin::class);
    }

    /**
     * Chiffrer le mot de passe automatiquement
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Crypt::encryptString($value);
    }

    /**
     * Déchiffrer le mot de passe
     */
    public function getDecryptedPasswordAttribute()
    {
        try {
            return Crypt::decryptString($this->attributes['password']);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Marquer comme utilisé
     */
    public function markAsUsed()
    {
        $this->update(['last_used_at' => now()]);
    }
}