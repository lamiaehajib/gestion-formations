<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Module extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'description',
        'duration_hours',
        'number_seance',
        'order',
        'status',
        'content',
        'progress', // New: for the consultant's progress
        'formation_id',
        'user_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'content' => 'array', // Will automatically handle JSON serialization/deserialization
    ];

    /**
     * Get the formation that owns the module.
     */
    public function formation()
    {
        return $this->belongsTo(Formation::class);
    }

    public function courses()
    {
        return $this->hasMany(Course::class);
    }
    /**
     * Get the user (consultant) that is assigned to the module.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}