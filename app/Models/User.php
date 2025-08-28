<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable,HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'birth_date',
        'cin',
        'avatar',
        
        'status',
         'promotion_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'birth_date' => 'date',
      
    ];

    // Relations
    public function formations()
    {
        return $this->hasMany(Formation::class, 'consultant_id');
    }

    public function inscriptions()
    {
        return $this->hasMany(Inscription::class);
    }

    public function reclamations()
    {
        return $this->hasMany(Reclamation::class);
    }

    public function assignedReclamations()
    {
        return $this->hasMany(Reclamation::class, 'assigned_to');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    public function evaluations()
    {
        return $this->hasMany(Evaluation::class);
    }

    public function forumPosts()
    {
        return $this->hasMany(ForumPost::class);
    }

    public function createdForums()
    {
        return $this->hasMany(Forum::class, 'created_by');
    }

      public function promotion()
    {
        return $this->belongsTo(Promotion::class);
    }
}