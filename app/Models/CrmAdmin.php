<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CrmAdmin extends Authenticatable
{
    use HasFactory;

    protected $table = 'crm_admins';

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_active',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_login_at' => 'datetime',
    ];

    /**
     * Mettre à jour la date de dernière connexion
     */
    public function updateLastLogin(): void
    {
        $this->update(['last_login_at' => now()]);
    }
}