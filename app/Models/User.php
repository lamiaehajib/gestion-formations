<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Auth\Events\Login; // Import the Login event

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'birth_date',
        'cin',
        'avatar',
        'documents', 
        'status',
        'promotion_id',
        'last_login_at', // Add last_login_at to fillable
        'login_count',   // Add login_count to fillable
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'birth_date' => 'date',
        'documents' => 'array',
        'last_login_at' => 'datetime', // Cast last_login_at to a datetime object
    ];

    /**
     * The "booted" method of the model.
     * This is where we hook into events.
     *
     * @return void
     */
    protected static function booted()
    {
        // Listen for the Laravel Auth Login event
        static::updated(function ($user) {
            // This event is fired after the user is authenticated.
            // We use a simple eloquent `updated` event listener as
            // the `Login` event is more complex to listen to
            // and this is simpler to maintain.
        });
    }

    /**
     * Record the user's login date and increment the login count.
     *
     * @return void
     */
    public function recordLogin()
    {
        $this->last_login_at = now();
        $this->login_count = $this->login_count + 1; // Increment the count
        $this->save();
    }
    
    // Relations
    public function formations()
    {
        return $this->hasMany(Formation::class, 'consultant_id');
    }

    public function modules()
    {
        return $this->hasMany(Module::class);
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
