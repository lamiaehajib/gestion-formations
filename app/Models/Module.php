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
      
        'status',
        'content',
        'progress', // New: for the consultant's progress
       
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
    public function formations() 
    {
        // Had l-methode sse77i7a: belongsToMany w withPivot('order') bach njibo tartib.
        return $this->belongsToMany(Formation::class)->withPivot('order'); 
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


    // ========================================
    // ✨ NOUVELLE RELATION - Documentation
    // ========================================

    /**
     * Documentation dyal had module
     */
    public function documentations()
    {
        return $this->hasMany(Documentation::class);
    }

    /**
     * Documentation pending dyal had module
     */
    public function pendingDocumentations()
    {
        return $this->hasMany(Documentation::class)->where('status', 'pending');
    }

    /**
     * Documentation approved dyal had module
     */
    public function approvedDocumentations()
    {
        return $this->hasMany(Documentation::class)->where('status', 'approved');
    }

    /**
     * Get documentation dyal consultant m3ayen
     */
    public function getConsultantDocumentation($consultantId)
    {
        return $this->documentations()->where('consultant_id', $consultantId)->first();
    }
}